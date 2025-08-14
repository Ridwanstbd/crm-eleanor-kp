<?php

namespace App\Http\Controllers;

use App\Models\CampaignCustomerGroup;
use App\Models\Customer;
use App\Models\MessageLogs;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Product;
use App\Models\MessageTemplate;
use App\Models\CustomerGroup;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
 
class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $sortField = $request->input('sort', 'name'); 
        $sortDirection = $request->input('direction', 'desc');
        
        $allowedSortFields = ['name','schedule'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'name';
        }
        
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';
        
        $campaigns = Campaign::when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate(10);

        $campaigns->appends(request()->query());
        return view('pages.Admin.Campaign.index', compact('campaigns', 'search', 'sortField', 'sortDirection'));
    }

    public function create()
    {
        $products = Product::all();
        $templates = MessageTemplate::all();
        $customers = Customer::paginate(10);
                
        return view('pages.Admin.Campaign.create', compact("products","templates","customers"));
    }

    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'product' => 'nullable|exists:products,id',
            'template' => 'required|exists:message_templates,id',
            'tanggal_terjual' => 'nullable|date',
            'time_send' => 'nullable|date_format:H:i',
        ];

        $validationMessages = [
            'name.required' => 'Nama kampanye wajib diisi.',
            'template.required' => 'Template pesan wajib dipilih.',
            'template.exists' => 'Template pesan yang dipilih tidak valid.',
            'product.exists' => 'Produk yang dipilih tidak valid.',
            'tanggal_terjual.date' => 'Format tanggal tidak valid.',
            'time_send.date_format' => 'Format waktu kirim tidak valid. Gunakan format HH:MM.',
        ];

        if (!$request->has('new_audiens') && !$request->has('customer')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Pilih minimal satu target audiens (Audiens Baru atau Pelanggan).');
        }

        if ($request->has('new_audiens') && $request->new_audiens == '1') {
            $validationRules['name_group_customer'] = 'required|string|max:255';
            $validationRules['csv_file'] = 'required|file|mimes:csv,txt|max:2048';
            
            $validationMessages['name_group_customer.required'] = 'Nama grup pelanggan wajib diisi untuk audiens baru.';
            $validationMessages['csv_file.required'] = 'File CSV wajib diupload untuk audiens baru.';
            $validationMessages['csv_file.mimes'] = 'File harus berformat CSV.';
            $validationMessages['csv_file.max'] = 'Ukuran file maksimal 2MB.';
        }

        if ($request->has('customer') && $request->customer == '1') {
            $validationRules['selected_customers'] = 'required|array|min:1';
            $validationRules['selected_customers.*'] = 'exists:customers,id';
            
            $validationMessages['selected_customers.required'] = 'Pilih minimal satu pelanggan.';
            $validationMessages['selected_customers.min'] = 'Pilih minimal satu pelanggan.';
            $validationMessages['selected_customers.*.exists'] = 'Pelanggan yang dipilih tidak valid.';
            
            if ($request->filled('product')) {
                $validationRules['customer_quantities'] = 'required|array';
                $validationRules['customer_quantities.*'] = 'required|integer|min:1|max:999';
                
                $validationMessages['customer_quantities.required'] = 'Jumlah pembelian untuk setiap pelanggan wajib diisi.';
                $validationMessages['customer_quantities.*.required'] = 'Jumlah pembelian wajib diisi.';
                $validationMessages['customer_quantities.*.min'] = 'Jumlah pembelian minimal adalah 1.';
                $validationMessages['customer_quantities.*.max'] = 'Jumlah pembelian maksimal adalah 999.';
            }
        }

        try {
            $request->validate($validationRules, $validationMessages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }

        DB::beginTransaction();
        
        try {
            $campaign = Campaign::create([
                'name' => $request->name,
                'user_id' => Auth::id(),
                'product_id' => $request->product,
                'message_template_id' => $request->template,
                'schedule' => $request->tanggal_terjual,
                'time_send' => $request->time_send,
            ]);

            $customerGroups = [];

            if ($request->has('new_audiens') && $request->new_audiens == '1') {
                $customerGroups[] = $this->handleNewAudience($request, $campaign);
            }

            if ($request->has('customer') && $request->customer == '1') {
                $customerGroups[] = $this->handleExistingCustomers($request, $campaign);
            }

            $this->scheduleReminderMessages($campaign, $customerGroups, $request->tanggal_terjual, $request->time_send);

            DB::commit();
            
            return redirect()->route('campaigns.edit',$campaign->id)
                ->with('success', 'Kampanye berhasil dibuat dan pesan telah dijadwalkan.');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function handleExistingCustomers(Request $request, Campaign $campaign)
    {
        $selectedCustomers = $request->selected_customers;
        $customerQuantities = $request->customer_quantities ?? [];
                
        $customerGroup = CustomerGroup::create([
            'name' => 'Pelanggan Terpilih - ' . $campaign->name,
        ]);
        
        $campaign->customerGroups()->attach($customerGroup->id);
        $customerGroup->customers()->sync($selectedCustomers);

        if ($campaign->product_id) {
            foreach ($selectedCustomers as $customerId) {
                $customer = Customer::find($customerId);
                if (!$customer) {
                    continue;
                }
                
                $purchaseQuantity = isset($customerQuantities[$customerId]) ? 
                    intval($customerQuantities[$customerId]) : 1;
                
                if ($purchaseQuantity < 1 || $purchaseQuantity > 999) {
                    $purchaseQuantity = 1;
                }
                
                $customer->purchases()->syncWithoutDetaching([
                    $campaign->product_id => [
                        'campaign_id' => $campaign->id,
                        'last_purchase_quantity' => $purchaseQuantity
                        ]
                ]);
            }
        }

        return $customerGroup;
    }

    private function handleNewAudience(Request $request, Campaign $campaign)
    {
        $csvFile = $request->file('csv_file');
        $csvPath = $csvFile->store('csv_uploads', 'public');
        
        try {
            $customerGroup = CustomerGroup::create([
                'name' => $request->name_group_customer,
            ]);

            $campaign->customerGroups()->attach($customerGroup->id);

            $csvData = array_map('str_getcsv', file(storage_path('app/public/' . $csvPath)));
            
            $firstRow = $csvData[0] ?? [];
            $hasHeaders = false;
            
            if (!empty($firstRow)) {
                $firstRow[0] = trim(str_replace("\xEF\xBB\xBF", '', $firstRow[0]));
            }
            
            if (!empty($firstRow) && count($firstRow) >= 1) {
                $firstCol = strtolower(trim($firstRow[0]));
                if (in_array($firstCol, ['phone', 'nama', 'name', 'telepon', 'hp', 'nomor'])) {
                    $hasHeaders = true;
                }
            }
            
            if ($hasHeaders) {
                $headers = array_shift($csvData);
                $headers = array_map(function($header) {
                    return trim(str_replace("\xEF\xBB\xBF", '', strtolower($header)));
                }, $headers);
            } else {
                $headers = ['nomor', 'jumlah_beli', 'nama'];
                while (count($headers) < count($firstRow)) {
                    $headers[] = 'extra_' . count($headers);
                }
            }
            
            $processedCustomers = [];
            $totalQuantity = 0;
            $errors = [];

            foreach ($csvData as $rowIndex => $row) {
                if (count($row) >= 1 && !empty(trim($row[0]))) {
                    $row = array_pad($row, count($headers), '');
                    $customerData = array_combine($headers, $row);
                    
                    $phone = $this->getPhoneFromData($customerData);
                    $name = $this->getNameFromData($customerData, $rowIndex + 1);
                    $purchaseQuantity = $this->getQuantityFromData($customerData);
                    
                    if (empty($phone)) {
                        $errors[] = "Baris " . ($rowIndex + 1) . ": Nomor telepon kosong atau tidak valid";
                        continue;
                    }
                    
                    if ($purchaseQuantity < 1 || $purchaseQuantity > 999) {
                        $purchaseQuantity = 1;
                    }
                    
                    try {
                        $cleanPhone = $this->cleanPhoneNumber($phone);
                        
                        $customer = Customer::firstOrCreate([
                            'phone' => $cleanPhone,
                        ], [
                            'name' => $name,
                        ]);

                        if (empty($customer->name) || $customer->name === 'Unknown') {
                            $customer->update(['name' => $name]);
                        }

                        $customerGroup->customers()->syncWithoutDetaching([$customer->id]);
                        
                        $processedCustomers[] = $customer->id;
                        $totalQuantity += $purchaseQuantity;

                        if ($campaign->product_id) {
                            $customer->purchases()->syncWithoutDetaching([
                                $campaign->product_id => [
                                    'campaign_id' => $campaign->id,
                                    'last_purchase_quantity' => $purchaseQuantity
                                    ]
                            ]);
                        }
                        
                    } catch (\Exception $e) {
                        $errors[] = "Baris " . ($rowIndex + 1) . ": Error processing customer - " . $e->getMessage();
                    }
                } else {
                    Log::info("Row " . ($rowIndex + 1) . ": Empty row, skipping.");
                }
            }

            if (!empty($errors) && count($processedCustomers) == 0) {
                throw new \Exception("Tidak ada customer yang berhasil diproses. Errors: " . implode(', ', array_slice($errors, 0, 3)));
            }

            $customerGroup->update([
                'total_customers' => count($processedCustomers),
                'description' => $customerGroup->description . " | Total customers: " . count($processedCustomers) . " | Total quantity: " . $totalQuantity,
            ]);

            return $customerGroup;

        } catch (\Exception $e) {
            Storage::disk('public')->delete($csvPath);
            throw new \Exception('Error processing CSV file: ' . $e->getMessage());
        }
    }

    private function scheduleReminderMessages(Campaign $campaign, array $customerGroups, $tanggalTerjual = null, $timeSend = null)
    {
        $fonnteToken = User::find(auth()->id())->fonnte_token;
        
        $messageTemplateContent = $campaign->messageTemplate->content;
        $targetProduct = Product::find($campaign->product_id);

        if (!$fonnteToken || !$targetProduct) {
            Log::warning('Missing Fonnte token or product for campaign: ' . $campaign->id);
            return;
        }

        $baseDate = $tanggalTerjual ? \Carbon\Carbon::parse($tanggalTerjual) : now();
        
        if ($timeSend) {
            $timeParts = explode(':', $timeSend);
            $hour = intval($timeParts[0]);
            $minute = intval($timeParts[1]);
            $baseDate->setTime($hour, $minute, 0);
        }
        
        $messagesToSend = [];

        foreach ($customerGroups as $customerGroup) {
            $customers = $customerGroup->customers;

            foreach ($customers as $customer) {
                $purchaseData = $customer->purchases()->where('product_id', $targetProduct->id)->first();
                $purchaseQuantity = $purchaseData ? $purchaseData->pivot->last_purchase_quantity : 1;

                $estimationDays = 0;
                
                if (isset($targetProduct->default_estimation_days_per_unit) && $targetProduct->default_estimation_days_per_unit == 0) {
                    $estimationDays = 0;
                    $scheduledDate = now(); 
                    
                    if ($timeSend) {
                        $timeParts = explode(':', $timeSend);
                        $hour = intval($timeParts[0]);
                        $minute = intval($timeParts[1]);
                        $scheduledDate->setTime($hour, $minute, 0);
                    } else {
                        $scheduledDate->setTime(9, 0, 0);
                    }
                    if ($scheduledDate->isPast()) {
                        $scheduledDate = now();
                    }
                } elseif (isset($targetProduct->default_estimation_days_per_unit) && $targetProduct->default_estimation_days_per_unit > 0) {
                    $estimationDays = $targetProduct->default_estimation_days_per_unit * $purchaseQuantity;
                    $scheduledDate = $baseDate->copy()->addDays($estimationDays);
                    
                    if (!$timeSend) {
                        $scheduledDate->setTime(9, 0, 0);
                    }
                } else {
                    $estimationDays = 7 * $purchaseQuantity;
                    $scheduledDate = $baseDate->copy()->addDays($estimationDays);
                    
                    if (!$timeSend) {
                        $scheduledDate->setTime(9, 0, 0);
                    }
                }
                
                $scheduleTimestamp = $scheduledDate->timestamp;
                $formattedEstimationDate = $scheduledDate->format('d M Y');

                $personalizedMessage = str_replace(
                    [
                        '{name}', 
                        '{product_name}',
                        '{quantity_purchased}',
                        '{estimated_finish_date}',
                    ], 
                    [
                        $customer->name, 
                        $targetProduct->name,            
                        $purchaseQuantity,            
                        $formattedEstimationDate,
                    ],
                    $messageTemplateContent
                );

                $messagesToSend[] = [
                    "target" => $this->formatPhoneForFonnte($customer->phone),
                    "message" => $personalizedMessage,
                    "schedule" => $scheduleTimestamp, 
                    "delay" => "3", 
                ];
            }
        }

        if (!empty($messagesToSend)) {
            $this->sendToFonnte($messagesToSend, $fonnteToken, $campaign);
        }
    }

    private function sendToFonnte(array $messages, string $fonnteToken, Campaign $campaign)
    {
        $payload = [
            "data" => json_encode($messages),
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => $fonnteToken,
            ])->post('https://api.fonnte.com/send', $payload);

            $res = $response->json();

            if ($response->successful() && isset($res['id']) && is_array($res['id'])) {
                $rawDevice = $res['quota'] ?? null;
                $device = is_array($rawDevice) ? ($rawDevice[0] ?? null) : $rawDevice;

                $processStatus = $res['process'] ?? 'pending';

                foreach ($res['id'] as $k => $reportId) {
                    $target = $res['target'][$k] ?? null;
                    $messageContent = $messages[$k]['message'] ?? null;

                    if ($target && $messageContent) {
                        $customer = Customer::where('phone', $target)->first();
                        $customerId = $customer->id ?? null;

                        MessageLogs::create([
                            'report_id' => $reportId,
                            'target' => $target,
                            'message' => $messageContent,
                            'status' => $processStatus,
                            'device' => $device, 
                            'state_id' => null,
                            'state' => null,
                            'customer_id' => $customerId,
                            'campaign_id' => $campaign->id,
                        ]);
                    }
                }
            } else {
                Log::error('Fonnte API responded with an error or invalid data for campaign: ' . $campaign->id, [
                    'response' => $res,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Exception when sending messages for campaign: ' . $campaign->id, [
                'error' => $e->getMessage() . " (Connection: " . DB::connection()->getDatabaseName() . ")",
            ]);
        }
    }

    private function formatPhoneForFonnte($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) === '0') {
            return '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            return '62' . $phone;
        }
        
        return $phone;
    }
    
    private function getPhoneFromData($data)
    {
        $phoneFields = ['phone', 'telepon', 'hp', 'nomor', 'no_hp', 'no_telepon'];
        
        foreach ($phoneFields as $field) {
            if (isset($data[$field]) && !empty(trim($data[$field]))) {
                return trim($data[$field]);
            }
        }
        
        $values = array_values($data);
        return isset($values[0]) ? trim($values[0]) : '';
    }
    
    private function getNameFromData($data, $rowNumber)
    {
        $nameFields = ['name', 'nama', 'customer_name', 'nama_customer'];
        
        foreach ($nameFields as $field) {
            if (isset($data[$field]) && !empty(trim($data[$field]))) {
                return trim($data[$field]);
            }
        }
        
        $values = array_values($data);
        if (isset($values[1]) && !empty(trim($values[1]))) {
            return trim($values[1]);
        }
        
        return 'Customer ' . $rowNumber;
    }
    
    private function getQuantityFromData($data)
    {
        $quantityFields = ['purchase_quantity', 'quantity', 'qty', 'jumlah', 'jumlah_beli'];
        
        foreach ($quantityFields as $field) {
            if (isset($data[$field]) && !empty(trim($data[$field]))) {
                return intval($data[$field]);
            }
        }
        
        $values = array_values($data);
        if (isset($values[2]) && !empty(trim($values[2]))) {
            return intval($values[2]);
        }
        
        return 1;
    }

    private function cleanPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } 
        elseif (substr($phone, 0, 1) === '8') {
            $phone = '62' . $phone;
        }
        elseif (substr($phone, 0, 3) === '+62') {
            $phone = substr($phone, 1); 
        }
        
        return $phone;
    }

    public function edit(Campaign $campaign)
    {
        $customerGroups = $campaign->customerGroups;
        $customerIds = collect();
        foreach ($customerGroups as $customerGroup) {
            $customerIds = $customerIds->merge($customerGroup->customers->pluck('id'));
        }
        
        $customerIds = $customerIds->unique();
        
        $customers = Customer::whereIn('id', $customerIds)
            ->with(['purchases' => function ($query) use ($campaign) {
                $query->wherePivot('campaign_id', $campaign->id)
                    ->wherePivot('product_id', $campaign->product_id);
            }])
            ->get();
        
        $customers->transform(function ($customer) use ($campaign) {
            $purchaseData = $customer->purchases->first();
            $customer->purchase_quantity = $purchaseData ? $purchaseData->pivot->last_purchase_quantity : 1;
            return $customer;
        });

        $product = Product::find($campaign->product_id);         
        $template = MessageTemplate::find($campaign->message_template_id);
        
        $messageLogs = $campaign->messageLogs()
            ->with(['customer' => function($query) use ($campaign) {
                $query->with(['purchases' => function ($subQuery) use ($campaign) {
                    $subQuery->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('product_id', $campaign->product_id);
                }]);
            }])
            ->paginate(10);
        
        foreach ($messageLogs as $messageLog) {
            if ($messageLog->customer) {
                $purchaseData = $messageLog->customer->purchases->first();
                $messageLog->customer->purchase_quantity = $purchaseData ? $purchaseData->pivot->last_purchase_quantity : 1;
            }
        }
        
        return view('pages.Admin.Campaign.show', compact('campaign','customers', 'customerGroups','product','template','messageLogs'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $request->validate([
            'name' => 'required',
            'schedule' => 'required|date',
        ]);

        $campaign->update($request->only('name', 'schedule'));

        return redirect()->route('campaigns.index')->with('success', 'Kampanye berhasil diperbarui.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', 'Kampanye berhasil dihapus.');
    }

    public function downloadCsvTemplate()
    {
        $sampleData = [
            ['85704412510', '1', 'Ridwan Setio Budi'],
            ['82337440435', '2', 'Davindra'],
        ];
        
        $filename = 'customer_template.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($sampleData) {
            $file = fopen('php://output', 'w');
            
            fwrite($file, "\xEF\xBB\xBF");
            
            fputcsv($file, ['nomor', 'jumlah_beli', 'nama']);
            
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

}