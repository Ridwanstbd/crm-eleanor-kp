<?php

namespace App\Http\Controllers;

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

        $sortField = $request->input('sort', 'schedule');
        $sortDirection = $request->input('direction', 'desc');

        $allowedSortFields = ['name','schedule'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'name';
        }

        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'desc';

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
        $customerGroups= CustomerGroup::withCount('customers')->get();
        $customers = Customer::with('groups')->get();

        return view('pages.Admin.Campaign.create', compact("products","templates","customerGroups","customers"));
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

            // Only require quantities if product is selected
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
                'product_id' => $request->filled('product') ? $request->product : null,
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
                        'last_purchase_quantity' => $purchaseQuantity,
                        'created_at' => now(),
                        'updated_at' => now()
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

            $csvContent = file_get_contents(storage_path('app/public/' . $csvPath));
            $csvContent = str_replace("\xEF\xBB\xBF", '', $csvContent);

            $lines = explode("\n", $csvContent);
            $csvData = [];

            foreach ($lines as $line) {
                $line = trim((string) $line);
                if (!empty($line)) {
                    $row = str_getcsv($line, ',');
                    if (count($row) == 1) {
                        $row = str_getcsv($line, ';');
                    }
                    if (count($row) == 1) {
                        $row = str_getcsv($line, "\t");
                    }
                    $row = array_map(function($item) {
                        return (string) $item;
                    }, $row);
                    $csvData[] = $row;
                }
            }

            if (empty($csvData)) {
                throw new \Exception('File CSV kosong atau tidak dapat dibaca');
            }

            $firstRow = $csvData[0] ?? [];
            $hasHeaders = false;

            if (!empty($firstRow)) {
                $firstRow = array_map(function($item) {
                    return trim((string) $item);
                }, $firstRow);
            }

            if (!empty($firstRow) && count($firstRow) >= 2) {
                $firstCol = strtolower(trim($firstRow[0]));
                $secondCol = strtolower(trim($firstRow[1]));
                $thirdCol = isset($firstRow[2]) ? strtolower(trim($firstRow[2])) : '';

                $phoneHeaders = ['nomor', 'phone', 'telepon', 'nomor telepon', 'no_hp', 'hp', 'whatsapp', 'wa'];
                $nameHeaders = ['nama', 'name', 'customer', 'pelanggan', 'nama customer'];
                $quantityHeaders = ['jumlah', 'quantity', 'qty', 'jumlah_beli', 'jumlah beli', 'amount'];

                if (in_array($firstCol, $phoneHeaders) ||
                    in_array($secondCol, $nameHeaders) ||
                    in_array($thirdCol, $quantityHeaders)) {
                    $hasHeaders = true;
                }
            }

            if ($hasHeaders) {
                array_shift($csvData);
            }

            $totalQuantity = 0;
            $errors = [];
            $successCount = 0;

            foreach ($csvData as $rowIndex => $row) {
                if (empty(array_filter($row, function($value) { return !empty(trim($value)); }))) {
                    continue;
                }

                if (count($row) < 2) {
                    $errors[] = "Baris " . ($rowIndex + 1) . ": Data tidak lengkap (minimal nomor dan nama)";
                    continue;
                }

                $phone = isset($row[0]) ? trim((string) $row[0]) : '';
                $name = isset($row[1]) ? trim((string) $row[1]) : '';
                $purchaseQuantity = ($campaign->product_id && isset($row[2]) && !empty(trim((string) $row[2])))
                    ? intval(trim((string) $row[2]))
                    : 1;

                if (empty($phone) || empty($name)) {
                    $errors[] = "Baris " . ($rowIndex + 1) . ": Nomor telepon atau nama kosong";
                    continue;
                }

                if ($purchaseQuantity < 1 || $purchaseQuantity > 999) {
                    $purchaseQuantity = 1;
                }

                try {
                    $cleanPhone = $this->cleanPhoneNumber($phone);

                    if (!$this->isValidPhoneNumber($cleanPhone)) {
                        $errors[] = "Baris " . ($rowIndex + 1) . ": Format nomor telepon tidak valid ($phone)";
                        continue;
                    }

                    $customer = Customer::where('phone', $cleanPhone)->first();

                    if ($customer) {
                        if (empty($customer->name) || $customer->name === 'Unknown' || strlen($name) > strlen($customer->name)) {
                            $customer->update(['name' => $name]);
                        }
                    } else {
                        $customer = Customer::create([
                            'phone' => $cleanPhone,
                            'name' => $name,
                        ]);
                    }

                    $customerGroup->customers()->syncWithoutDetaching([$customer->id]);

                    $processedCustomers[] = $customer->id;
                    $totalQuantity += $purchaseQuantity;
                    $successCount++;

                    if ($campaign->product_id) {
                        $customer->purchases()->syncWithoutDetaching([
                            $campaign->product_id => [
                                'campaign_id' => $campaign->id,
                                'last_purchase_quantity' => $purchaseQuantity,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        ]);
                    }

                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($rowIndex + 1) . ": Error - " . $e->getMessage();
                    Log::error("Error processing customer row " . ($rowIndex + 1), [
                        'phone' => $phone,
                        'name' => $name,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if ($successCount == 0) {
                $errorMsg = "Tidak ada customer yang berhasil diproses.";
                if (!empty($errors)) {
                    $errorMsg .= " Errors: " . implode('; ', array_slice($errors, 0, 5));
                }
                throw new \Exception($errorMsg);
            }

            $customerGroup->update([
                'total_customers' => $successCount,
                'description' => "Imported from CSV | Total customers: $successCount | Total quantity: $totalQuantity" .
                            (!empty($errors) ? " | Errors: " . count($errors) : ""),
            ]);

            if (isset($csvPath) && Storage::disk('public')->exists($csvPath)) {
                Storage::disk('public')->delete($csvPath);
            }

            return $customerGroup;

        } catch (\Exception $e) {
            if (isset($csvPath) && Storage::disk('public')->exists($csvPath)) {
                Storage::disk('public')->delete($csvPath);
            }
            throw new \Exception('Error processing CSV file: ' . $e->getMessage());
        }
    }

    private function scheduleReminderMessages(Campaign $campaign, array $customerGroups, $tanggalTerjual = null, $timeSend = null)
    {
        $user = User::find(auth()->id());
        $fonnteToken = $user->fonnte_token ?? null;

        if (!$fonnteToken) {
            throw new \Exception('Fonnte token tidak ditemukan. Silakan konfigurasi token Fonnte terlebih dahulu.');
        }

        $messageTemplateContent = $campaign->messageTemplate->content ?? null;
        if (!$messageTemplateContent) {
            throw new \Exception('Template pesan tidak ditemukan.');
        }

        $targetProduct = null;
        if ($campaign->product_id) {
            $targetProduct = Product::find($campaign->product_id);
            if (!$targetProduct) {
                Log::warning('Product not found for campaign: ' . $campaign->id);
            }
        }

        $baseDate = $tanggalTerjual ? \Carbon\Carbon::parse($tanggalTerjual) : now();

        if ($timeSend) {
            try {
                $timeParts = explode(':', $timeSend);
                $hour = intval($timeParts[0]);
                $minute = intval($timeParts[1]);
                $baseDate->setTime($hour, $minute, 0);
            } catch (\Exception $e) {
                Log::warning('Invalid time format for campaign ' . $campaign->id . ': ' . $timeSend);
                $baseDate->setTime(9, 0, 0);
            }
        }

        $messagesToSend = [];
        $scheduleData = [];
        $totalCustomers = 0;

        foreach ($customerGroups as $customerGroup) {
            $customers = $customerGroup->customers()->get();
            $totalCustomers += $customers->count();

            foreach ($customers as $customer) {
                try {
                    $purchaseQuantity = 1;

                    if ($targetProduct) {
                        $purchaseData = $customer->purchases()
                            ->where('product_id', $targetProduct->id)
                            ->wherePivot('campaign_id', $campaign->id)
                            ->first();

                        if ($purchaseData) {
                            $purchaseQuantity = $purchaseData->pivot->last_purchase_quantity ?? 1;
                        }
                    }

                    $scheduledDate = $this->calculateScheduleDate($baseDate, $targetProduct, $purchaseQuantity, $timeSend);

                    $scheduleTimestamp = $scheduledDate->timestamp;
                    $formattedEstimationDate = $scheduledDate->format('d M Y');

                    $personalizedMessage = $this->personalizeMessage(
                        $messageTemplateContent,
                        $customer,
                        $targetProduct,
                        $purchaseQuantity,
                        $formattedEstimationDate
                    );

                    $formattedPhone = $this->formatPhoneForFonnte($customer->phone);
                    if (!$this->isValidPhoneNumber($formattedPhone)) {
                        Log::warning("Invalid phone number for customer " . $customer->id . ": " . $customer->phone);
                        continue;
                    }

                    $delay = ($campaign->product_id === null) ? "3" : "3";

                    $messagesToSend[] = [
                        "target" => $formattedPhone,
                        "message" => $personalizedMessage,
                        "schedule" => $scheduleTimestamp,
                        "delay" => $delay,
                    ];

                    $scheduleData[] = [
                        'target' => $formattedPhone,
                        'customer_id' => $customer->id,
                        'scheduled_at' => $scheduledDate,
                        'message' => $personalizedMessage
                    ];

                } catch (\Exception $e) {
                    Log::error("Error preparing message for customer " . $customer->id . ": " . $e->getMessage());
                }
            }
        }

        if (empty($messagesToSend)) {
            throw new \Exception('Tidak ada pesan yang dapat dikirim. Periksa data customer dan nomor telepon.');
        }

        $this->sendToFonnte($messagesToSend, $fonnteToken, $campaign, $scheduleData);
    }

    private function calculateScheduleDate($baseDate, $targetProduct, $purchaseQuantity, $timeSend)
    {
        $estimationDays = 0;

        if ($targetProduct && isset($targetProduct->default_estimation_days_per_unit)) {
            if ($targetProduct->default_estimation_days_per_unit == 0) {
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
            } else {
                $estimationDays = $targetProduct->default_estimation_days_per_unit * $purchaseQuantity;
                $scheduledDate = $baseDate->copy()->addDays($estimationDays);

                if (!$timeSend) {
                    $scheduledDate->setTime(9, 0, 0);
                }
            }
        } else {
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
        }

        return $scheduledDate;
    }

    private function personalizeMessage($messageTemplate, $customer, $product, $quantity, $estimationDate)
    {
        $customerName = (string) ($customer->name ?? 'Customer');
        $productName = $product ? (string) ($product->name ?? 'Product') : '';
        $quantityStr = (string) $quantity;
        $estimationDateStr = (string) $estimationDate;

        $replacements = [
            '{name}' => $customerName,
            '{customer_name}' => $customerName,
            '{quantity_purchased}' => $quantityStr,
            '{estimated_finish_date}' => $estimationDateStr,
        ];

        if ($product) {
            $replacements['{product_name}'] = $productName;
            $replacements['{product}'] = $productName;
        } else {
            $replacements['{product_name}'] = '';
            $replacements['{product}'] = '';
        }

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            (string) $messageTemplate
        );
    }

    private function sendToFonnte(array $messages, string $fonnteToken, Campaign $campaign, array $scheduleData = [])
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

                        $scheduleInfo = null;
                        foreach ($scheduleData as $data) {
                            if ($data['target'] === $target) {
                                $scheduleInfo = $data;
                                break;
                            }
                        }

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
                            'scheduled_at' => $scheduleInfo ? $scheduleInfo['scheduled_at'] : null,
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
        $phone = (string) $phone;
        $cleanPhone = $this->cleanPhoneNumber($phone);

        if (substr($cleanPhone, 0, 2) !== '62') {
            if (substr($cleanPhone, 0, 1) === '0') {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            } elseif (substr($cleanPhone, 0, 1) === '8') {
                $cleanPhone = '62' . $cleanPhone;
            } else {
                $cleanPhone = '62' . $cleanPhone;
            }
        }

        return $cleanPhone;
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
        $quantityFields = ['purchase_quantity', 'quantity', 'qty', 'jumlah', 'jumlah beli'];

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
        $phone = (string) $phone;
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

    private function isValidPhoneNumber($phone)
    {
        if (!preg_match('/^62[0-9]{8,13}$/', $phone)) {
            return false;
        }

        $validPrefixes = ['628', '629', '627', '626', '625', '624', '623', '622', '621'];

        foreach ($validPrefixes as $prefix) {
            if (substr($phone, 0, strlen($prefix)) === $prefix) {
                return true;
            }
        }

        return false;
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
                if ($campaign->product_id) {
                    $query->wherePivot('campaign_id', $campaign->id)
                        ->wherePivot('product_id', $campaign->product_id);
                }
            }])
            ->get();

        $customers->transform(function ($customer) use ($campaign) {
            if ($campaign->product_id) {
                $purchaseData = $customer->purchases->first();
                $customer->purchase_quantity = $purchaseData ? $purchaseData->pivot->last_purchase_quantity : 1;
            } else {
                $customer->purchase_quantity = 1;
            }
            return $customer;
        });

        $product = $campaign->product_id ? Product::find($campaign->product_id) : null;
        $template = MessageTemplate::find($campaign->message_template_id);

        $messageLogs = $campaign->messageLogs()
            ->with(['customer' => function($query) use ($campaign) {
                if ($campaign->product_id) {
                    $query->with(['purchases' => function ($subQuery) use ($campaign) {
                        $subQuery->wherePivot('campaign_id', $campaign->id)
                            ->wherePivot('product_id', $campaign->product_id);
                    }]);
                }
            }])
            ->paginate(10);

        foreach ($messageLogs as $messageLog) {
            if ($messageLog->customer) {
                if ($campaign->product_id) {
                    $purchaseData = $messageLog->customer->purchases->first();
                    $messageLog->customer->purchase_quantity = $purchaseData ? $purchaseData->pivot->last_purchase_quantity : 1;
                } else {
                    $messageLog->customer->purchase_quantity = 1;
                }
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
            [ 'nomor','nama', 'jumlah_beli'],
            ['6285704412510','Ridwan Setio Budi',  '1'],
            ['6282337440435', 'Davindra', '2'],
        ];

        $filename = 'template_customer.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
        ];

        $callback = function() use ($sampleData) {
            $output = fopen('php://output', 'w');

            fwrite($output, "\xEF\xBB\xBF");

            foreach ($sampleData as $row) {
                fputcsv($output, $row, ';');
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

}
