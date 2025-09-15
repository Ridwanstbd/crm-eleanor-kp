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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');

        $allowedSortFields = ['name','schedule'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
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

    public function create(Request $request)
    {
        $products = Product::all();
        $templates = MessageTemplate::all();
        $customerGroups = CustomerGroup::withCount('customers')->get();
        $customers = Customer::with('groups')->get();

        $reuseData = null;
        if ($request->has('reuse') && $request->reuse) {
            $campaign = Campaign::find($request->reuse);
            if ($campaign) {
                $reuseData = [
                    'template_id' => $campaign->message_template_id,
                    'product_id' => $campaign->product_id,
                    'name' => 'Copy - ' . $campaign->name,
                    'schedule' => $campaign->schedule,
                    'time_send' => $campaign->time_send,
                    'original_campaign_id' => $campaign->id
                ];
            }
        }

        return view('pages.Admin.Campaign.create', compact(
            'products', 
            'templates', 
            'customerGroups', 
            'customers',
            'reuseData'
        ));
    }

    public function store(Request $request)
    {
        if (!$request->has('new_audiens') && !$request->has('customer')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Pilih minimal satu target audiens (Audiens Baru atau Pelanggan).');
        }

        $validationRules = [
            'name' => 'required|string|max:255',
            'product' => 'nullable|exists:products,id',
            'template' => 'required|exists:message_templates,id',
            'schedule' => 'nullable|date',
            'time_send' => 'nullable|date_format:H:i',
        ];

        $validationMessages = [
            'name.required' => 'Nama kampanye wajib diisi.',
            'template.required' => 'Template pesan wajib dipilih.',
            'template.exists' => 'Template pesan yang dipilih tidak valid.',
            'product.exists' => 'Produk yang dipilih tidak valid.',
            'schedule.date' => 'Format tanggal tidak valid.',
            'time_send.date_format' => 'Format waktu kirim tidak valid. Gunakan format HH:MM.',
        ];

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

            $validationRules['customer_schedules'] = 'nullable|array';
            $validationRules['customer_schedules.*'] = 'nullable|date';
            $validationRules['customer_time_sends'] = 'nullable|array';
            $validationRules['customer_time_sends.*'] = 'nullable|date_format:H:i';

            $validationMessages['selected_customers.required'] = 'Pilih minimal satu pelanggan.';
            $validationMessages['selected_customers.min'] = 'Pilih minimal satu pelanggan.';
            $validationMessages['selected_customers.*.exists'] = 'Pelanggan yang dipilih tidak valid.';
            $validationMessages['customer_schedules.*.date'] = 'Format tanggal tidak valid untuk customer tertentu.';
            $validationMessages['customer_time_sends.*.date_format'] = 'Format waktu tidak valid untuk customer tertentu. Gunakan format HH:MM.';

            if ($request->filled('product')) {
                $validationRules['customer_quantities'] = 'required|array';
                $validationRules['customer_quantities.*'] = 'required|integer|min:1|max:999';
                $validationRules['customer_receipts'] = 'nullable|array';
                $validationRules['customer_receipts.*'] = 'nullable|string|max:255'; 
                
                $validationMessages['customer_quantities.required'] = 'Jumlah pembelian untuk setiap pelanggan wajib diisi.';
                $validationMessages['customer_quantities.*.required'] = 'Jumlah pembelian wajib diisi.';
                $validationMessages['customer_quantities.*.min'] = 'Jumlah pembelian minimal adalah 1.';
                $validationMessages['customer_quantities.*.max'] = 'Jumlah pembelian maksimal adalah 999.';
                $validationMessages['customer_receipts.*.max'] = 'Nomor resi maksimal 255 karakter.';
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
            ]);

            $customerGroups = [];

            if ($request->has('new_audiens') && $request->new_audiens == '1') {
                $customerGroups[] = $this->handleNewAudience($request, $campaign);
            }

            if ($request->has('customer') && $request->customer == '1') {
                $customerGroups[] = $this->handleExistingCustomers($request, $campaign);
            }

            $this->scheduleReminderMessages($campaign, $customerGroups);

            DB::commit();

            return redirect()->route('campaigns.edit', $campaign->id)
                ->with('success', 'Kampanye berhasil dibuat dan pesan telah dijadwalkan.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Campaign creation failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function handleExistingCustomers(Request $request, Campaign $campaign)
    {
        $selectedCustomers = $request->selected_customers;
        $customerQuantities = $request->customer_quantities ?? [];
        $customerReceipts = $request->customer_receipts ?? [];
        $customerSchedules = $request->customer_schedules ?? []; 
        $customerTimeSends = $request->customer_time_sends ?? []; 

        $customerGroup = CustomerGroup::create([
            'name' => 'Pelanggan Terpilih - ' . $campaign->name,
        ]);

        $campaign->customerGroups()->attach($customerGroup->id);
        $customerGroup->customers()->sync($selectedCustomers);

        $processedCustomers = [];

        $defaultSchedule = !empty($request->schedule) ? $request->schedule : null;
        $defaultTimeSend = !empty($request->time_send) ? $request->time_send : null;

        if ($campaign->product_id) {
            foreach ($selectedCustomers as $customerId) {
                $customer = Customer::find($customerId);
                if (!$customer) {
                    continue;
                }

                $purchaseQuantity = isset($customerQuantities[$customerId]) ?
                    intval($customerQuantities[$customerId]) : 1;
                
                $receipt = $customerReceipts[$customerId] ?? null;

                $customerSchedule = $customerSchedules[$customerId] ?? null;
                $customerTimeSend = $customerTimeSends[$customerId] ?? null;

                $finalSchedule = null;
                $finalTimeSend = null;

                if (!empty($customerSchedule)) {
                    $finalSchedule = $customerSchedule;
                } elseif (!empty($defaultSchedule)) {
                    $finalSchedule = $defaultSchedule;
                }

                if (!empty($customerTimeSend)) {
                    $finalTimeSend = $customerTimeSend;
                } elseif (!empty($defaultTimeSend)) {
                    $finalTimeSend = $defaultTimeSend;
                }

                $isScheduleInPast = false;
                if ($finalSchedule || $finalTimeSend) {
                    $scheduleToCheck = $finalSchedule ?: now()->format('Y-m-d');
                    $timeToCheck = $finalTimeSend ?: now()->format('H:i');
                    
                    try {
                        $scheduledDateTime = \Carbon\Carbon::parse($scheduleToCheck . ' ' . $timeToCheck);
                        $isScheduleInPast = $scheduledDateTime->isPast();
                    } catch (\Exception $e) {
                        $isScheduleInPast = false;
                    }
                }

                $hasAnyScheduleInput = !empty($customerSchedule) || !empty($customerTimeSend) || 
                                    !empty($defaultSchedule) || !empty($defaultTimeSend);
                
                $targetProduct = Product::find($campaign->product_id);
                $hasEstimation = $targetProduct && $targetProduct->default_estimation_days_per_unit > 0;
                
                $sendNow = (!$hasAnyScheduleInput && !$hasEstimation) || 
                        ($isScheduleInPast && !$hasEstimation);

                $schedule = $finalSchedule ?: now()->format('Y-m-d');
                $timeSend = $finalTimeSend;

                try {
                    $parsedSchedule = \Carbon\Carbon::parse($schedule);
                    $schedule = $parsedSchedule->format('Y-m-d');
                } catch (\Exception $e) {
                    $schedule = now()->format('Y-m-d');
                    if (!$hasEstimation) {
                        $sendNow = true;
                    }
                }

                if ($timeSend && !preg_match('/^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$/', $timeSend)) {
                    $timeSend = null;
                }

                if ($purchaseQuantity < 1 || $purchaseQuantity > 999) {
                    $purchaseQuantity = 1;
                }

                $customer->purchases()->syncWithoutDetaching([
                    $campaign->product_id => [
                        'campaign_id' => $campaign->id,
                        'last_purchase_quantity' => $purchaseQuantity,
                        'receipt' => $receipt,
                        'schedule' => $schedule,
                        'time_send' => $timeSend,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]);

                $processedCustomers[] = [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'customer_phone' => $customer->phone,
                    'schedule' => $schedule,
                    'time_send' => $timeSend,
                    'purchase_quantity' => $purchaseQuantity,
                    'receipt' => $receipt,
                    'send_now' => $sendNow
                ];
            }
        } else {
            foreach ($selectedCustomers as $customerId) {
                $customer = Customer::find($customerId);
                if (!$customer) {
                    continue;
                }

                $customerSchedule = $customerSchedules[$customerId] ?? null;
                $customerTimeSend = $customerTimeSends[$customerId] ?? null;

                $finalSchedule = null;
                $finalTimeSend = null;

                if (!empty($customerSchedule)) {
                    $finalSchedule = $customerSchedule;
                } elseif (!empty($defaultSchedule)) {
                    $finalSchedule = $defaultSchedule;
                }

                if (!empty($customerTimeSend)) {
                    $finalTimeSend = $customerTimeSend;
                } elseif (!empty($defaultTimeSend)) {
                    $finalTimeSend = $defaultTimeSend;
                }

                $isScheduleInPast = false;
                if ($finalSchedule || $finalTimeSend) {
                    $scheduleToCheck = $finalSchedule ?: now()->format('Y-m-d');
                    $timeToCheck = $finalTimeSend ?: now()->format('H:i');
                    
                    try {
                        $scheduledDateTime = \Carbon\Carbon::parse($scheduleToCheck . ' ' . $timeToCheck);
                        $isScheduleInPast = $scheduledDateTime->isPast();
                    } catch (\Exception $e) {
                        $isScheduleInPast = false;
                    }
                }

                $sendNow = (empty($customerSchedule) && empty($customerTimeSend) && 
                        empty($defaultSchedule) && empty($defaultTimeSend)) || $isScheduleInPast;

                $schedule = $finalSchedule ?: now()->format('Y-m-d');
                $timeSend = $finalTimeSend;

                try {
                    $parsedSchedule = \Carbon\Carbon::parse($schedule);
                    $schedule = $parsedSchedule->format('Y-m-d');
                } catch (\Exception $e) {
                    $schedule = now()->format('Y-m-d');
                    $sendNow = true;
                }

                if ($timeSend && !preg_match('/^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$/', $timeSend)) {
                    $timeSend = null;
                }

                $processedCustomers[] = [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'customer_phone' => $customer->phone,
                    'schedule' => $schedule,
                    'time_send' => $timeSend,
                    'purchase_quantity' => 1,
                    'receipt' => null,
                    'send_now' => $sendNow
                ];
            }
        }

        $jsonData = json_encode($processedCustomers);
        
        $customerGroup->update([
            'total_customers' => count($processedCustomers),
            'processed_customers_data' => $jsonData
        ]);

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

            foreach ($lines as $lineIndex => $line) {
                $line = trim((string) $line);
                if (!empty($line)) {
                    $hasComma = strpos($line, ',') !== false;
                    $hasSemicolon = strpos($line, ';') !== false;
                    $hasTab = strpos($line, "\t") !== false;
                    
                    $row = [];
                    
                    if ($hasSemicolon) {
                        $row = str_getcsv($line, ';');
                    } elseif ($hasComma || $hasTab) {
                        $delimiters = [',', "\t"];
                        $maxColumns = 1;
                        $bestRow = [trim($line)];
                        
                        foreach ($delimiters as $delimiter) {
                            if (($delimiter === ',' && $hasComma) || 
                                ($delimiter === "\t" && $hasTab)) {
                                
                                $testRow = str_getcsv($line, $delimiter);
                                
                                if (count($testRow) > $maxColumns) {
                                    $maxColumns = count($testRow);
                                    $bestRow = $testRow;
                                }
                            }
                        }
                        $row = $bestRow;
                    } else {
                        $row = [trim($line)];
                    }
                    
                    $row = array_map(function($item) {
                        return trim((string) $item);
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

            if (!empty($firstRow) && count($firstRow) >= 1) {
                $firstCol = strtolower(trim($firstRow[0]));
                $secondCol = isset($firstRow[1]) ? strtolower(trim($firstRow[1])) : '';
                $thirdCol = isset($firstRow[2]) ? strtolower(trim($firstRow[2])) : '';
                $fourthCol = isset($firstRow[3]) ? strtolower(trim($firstRow[3])) : '';
                $fifthCol = isset($firstRow[4]) ? strtolower(trim($firstRow[4])) : '';
                $sixthCol = isset($firstRow[5]) ? strtolower(trim($firstRow[5])) : '';

                $phoneHeaders = ['nomor', 'phone', 'telepon', 'nomor telepon', 'no_hp', 'hp', 'whatsapp', 'wa'];
                $nameHeaders = ['nama', 'name', 'customer', 'pelanggan', 'nama customer'];
                $quantityHeaders = ['jumlah', 'quantity', 'qty', 'jumlah_beli', 'jumlah beli', 'amount'];
                $receiptHeaders = ['resi', 'receipt', 'nomor resi', 'no resi'];
                $scheduleHeaders = ['schedule', 'tanggal', 'date', 'jadwal', 'tanggal kirim'];
                $timeHeaders = ['time', 'waktu', 'jam', 'time_send', 'waktu kirim'];

                if (in_array($firstCol, $phoneHeaders) ||
                    in_array($secondCol, $nameHeaders) ||
                    in_array($thirdCol, $quantityHeaders) ||
                    in_array($fourthCol, $receiptHeaders) ||
                    in_array($fifthCol, $scheduleHeaders) ||
                    in_array($sixthCol, $timeHeaders)) {
                    $hasHeaders = true;
                }
            }

            if ($hasHeaders) {
                array_shift($csvData);
            }

            $targetProduct = null;
            if ($campaign->product_id) {
                $targetProduct = Product::find($campaign->product_id);
            }

            $totalQuantity = 0;
            $errors = [];
            $successCount = 0;
            $processedCustomers = [];

            foreach ($csvData as $rowIndex => $row) {
                if (empty(array_filter($row, function($value) { return !empty(trim($value)); }))) {
                    continue;
                }

                if (count($row) < 1) {
                    $error = "Baris " . ($rowIndex + 1) . ": Data tidak lengkap (minimal nomor telepon)";
                    $errors[] = $error;
                    continue;
                }

                $phone = isset($row[0]) ? trim((string) $row[0]) : '';
                $name = isset($row[1]) ? trim((string) $row[1]) : '';
                $purchaseQuantity = ($campaign->product_id && isset($row[2]) && !empty(trim((string) $row[2])))
                    ? intval(trim((string) $row[2]))
                    : 1;
                $receipt = ($campaign->product_id && isset($row[3])) ? trim((string) $row[3]) : null;
                
                $csvSchedule = isset($row[4]) ? trim((string) $row[4]) : '';
                $csvTimeSend = isset($row[5]) ? trim((string) $row[5]) : '';

                $isScheduleInPast = false;
                if (!empty($csvSchedule) || !empty($csvTimeSend)) {
                    $scheduleToCheck = !empty($csvSchedule) ? $csvSchedule : now()->format('Y-m-d');
                    $timeToCheck = !empty($csvTimeSend) ? $csvTimeSend : now()->format('H:i');
                    
                    try {
                        $parsedSchedule = null;
                        if (!empty($csvSchedule)) {
                            $dateFormats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y', 'Y/m/d'];
                            foreach ($dateFormats as $format) {
                                try {
                                    $parsedDate = \Carbon\Carbon::createFromFormat($format, $csvSchedule);
                                    if ($parsedDate) {
                                        $parsedSchedule = $parsedDate->format('Y-m-d');
                                        break;
                                    }
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                            
                            if (!$parsedSchedule) {
                                try {
                                    $parsedDate = \Carbon\Carbon::parse($csvSchedule);
                                    $parsedSchedule = $parsedDate->format('Y-m-d');
                                } catch (\Exception $e) {
                                    $parsedSchedule = now()->format('Y-m-d');
                                }
                            }
                            $scheduleToCheck = $parsedSchedule;
                        }
                        
                        if (!empty($csvTimeSend) && preg_match('/^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$/', $csvTimeSend)) {
                            $timeToCheck = $csvTimeSend;
                        } else {
                            $timeToCheck = now()->format('H:i');
                        }
                        
                        $scheduledDateTime = \Carbon\Carbon::parse($scheduleToCheck . ' ' . $timeToCheck);
                        $isScheduleInPast = $scheduledDateTime->isPast();
                    } catch (\Exception $e) {
                        $isScheduleInPast = false;
                    }
                }

                $hasScheduleData = !empty($csvSchedule) || !empty($csvTimeSend);
                $hasEstimation = $targetProduct && $targetProduct->default_estimation_days_per_unit > 0;
                
                $sendNow = (!$hasScheduleData && !$hasEstimation) || 
                        ($isScheduleInPast && !$hasEstimation);

                $schedule = null;
                if (!empty($csvSchedule)) {
                    $dateFormats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y', 'Y/m/d'];
                    foreach ($dateFormats as $format) {
                        try {
                            $parsedDate = \Carbon\Carbon::createFromFormat($format, $csvSchedule);
                            if ($parsedDate) {
                                $schedule = $parsedDate->format('Y-m-d');
                                break;
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                    
                    if (!$schedule) {
                        try {
                            $parsedDate = \Carbon\Carbon::parse($csvSchedule);
                            $schedule = $parsedDate->format('Y-m-d');
                        } catch (\Exception $e) {
                            $schedule = now()->format('Y-m-d');
                            if (!$hasEstimation) {
                                $sendNow = true;
                            }
                            $errors[] = "Baris " . ($rowIndex + 1) . ": Format tanggal tidak valid ($csvSchedule), akan menggunakan jadwal default";
                        }
                    }
                } else {
                    $schedule = now()->format('Y-m-d');
                }

                $timeSend = null;
                if (!empty($csvTimeSend)) {
                    if (preg_match('/^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$/', $csvTimeSend)) {
                        $timeSend = $csvTimeSend;
                    } else {
                        $errors[] = "Baris " . ($rowIndex + 1) . ": Format waktu tidak valid ($csvTimeSend), akan menggunakan waktu sekarang";
                    }
                }

                if (empty($phone)) {
                    $error = "Baris " . ($rowIndex + 1) . ": Nomor telepon kosong";
                    $errors[] = $error;
                    continue;
                }

                if (empty($name)) {
                    $name = 'Customer ' . ($rowIndex + 1);
                }

                if ($purchaseQuantity < 1 || $purchaseQuantity > 999) {
                    $purchaseQuantity = 1;
                }

                try {
                    $cleanPhone = $this->cleanPhoneNumber($phone);

                    if (!$this->isValidPhoneNumber($cleanPhone)) {
                        $error = "Baris " . ($rowIndex + 1) . ": Format nomor telepon tidak valid ($phone)";
                        $errors[] = $error;
                        continue;
                    }

                    $customer = Customer::where('phone', $cleanPhone)->first();
                    
                    if ($customer) {
                        if (!empty($name) && 
                            !str_contains($name, 'Customer ') && 
                            (empty($customer->name) || 
                            str_contains($customer->name, 'Customer ') || 
                            $customer->name === 'Unknown' || 
                            strlen(trim($name)) > strlen(trim($customer->name)))) {
                            $customer->update(['name' => $name]);
                        }
                    } else {
                        $customer = Customer::create([
                            'phone' => $cleanPhone,
                            'name' => $name,
                        ]);
                    }

                    $customerGroup->customers()->syncWithoutDetaching([$customer->id]);

                    $processedCustomers[] = [
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name,
                        'customer_phone' => $customer->phone,
                        'schedule' => $schedule,
                        'time_send' => $timeSend,
                        'purchase_quantity' => $purchaseQuantity,
                        'receipt' => $receipt,
                        'csv_schedule' => $csvSchedule,
                        'csv_time_send' => $csvTimeSend,
                        'send_now' => $sendNow
                    ];
                    
                    $totalQuantity += $purchaseQuantity;
                    $successCount++;

                    if ($campaign->product_id) {
                        $customer->purchases()->syncWithoutDetaching([
                            $campaign->product_id => [
                                'campaign_id' => $campaign->id,
                                'last_purchase_quantity' => $purchaseQuantity,
                                'receipt' => $receipt,
                                'schedule' => $schedule,
                                'time_send' => $timeSend,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        ]);
                    }

                } catch (\Exception $e) {
                    $error = "Baris " . ($rowIndex + 1) . ": Error - " . $e->getMessage();
                    $errors[] = $error;
                    Log::error("Error processing CSV row: " . $e->getMessage());
                }
            }

            if ($successCount == 0) {
                $errorMsg = "Tidak ada customer yang berhasil diproses.";
                if (!empty($errors)) {
                    $errorMsg .= " Errors: " . implode('; ', array_slice($errors, 0, 5));
                }
                throw new \Exception($errorMsg);
            }

            $jsonData = json_encode($processedCustomers);
            
            $customerGroup->update([
                'total_customers' => $successCount,
                'processed_customers_data' => $jsonData
            ]);

            if (isset($csvPath) && Storage::disk('public')->exists($csvPath)) {
                Storage::disk('public')->delete($csvPath);
            }

            return $customerGroup;

        } catch (\Exception $e) {
            if (isset($csvPath) && Storage::disk('public')->exists($csvPath)) {
                Storage::disk('public')->delete($csvPath);
            }
            Log::error("handleNewAudience failed: " . $e->getMessage());
            throw new \Exception('Error processing CSV file: ' . $e->getMessage());
        }
    }

    private function scheduleReminderMessages(Campaign $campaign, array $customerGroups)
    {
        try {
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
            }

            $messagesToSend = [];
            $scheduleData = [];
            $totalCustomers = 0;

            foreach ($customerGroups as $groupIndex => $customerGroup) {
                if (!$customerGroup) {
                    continue;
                }

                $processedCustomersData = json_decode($customerGroup->processed_customers_data ?? '[]', true);
                
                if (!empty($processedCustomersData)) {
                    foreach ($processedCustomersData as $customerData) {
                        $customerId = $customerData['customer_id'];
                        $schedule = $customerData['schedule'] ?? now()->format('Y-m-d');
                        $timeSend = $customerData['time_send'] ?? '15:00';
                        $purchaseQuantity = $customerData['purchase_quantity'] ?? 1;
                        $receipt = $customerData['receipt'] ?? null;
                        $sendNow = $customerData['send_now'] ?? false;

                        $totalCustomers++;

                        try {
                            $customerObj = Customer::find($customerId);
                            if (!$customerObj) continue;

                            $scheduledDate = $this->calculateScheduleDate($schedule, $timeSend, $targetProduct, $purchaseQuantity);
                            
                            if ($sendNow && (!$targetProduct || $targetProduct->default_estimation_days_per_unit == 0)) {
                                $scheduledDate = now();
                            }

                            $formattedEstimationDate = $scheduledDate->format('d M Y');

                            $personalizedMessage = $this->personalizeMessage(
                                $messageTemplateContent,
                                $customerObj,
                                $targetProduct,
                                $purchaseQuantity,
                                $formattedEstimationDate,
                                $receipt 
                            );

                            $formattedPhone = $this->formatPhoneForFonnte($customerObj->phone);
                            if (!$this->isValidPhoneNumber($formattedPhone)) {
                                continue;
                            }
                            
                            $delay = (string) ($user->delay_message ?? '3');
                            
                            $messageData = [
                                "target" => $formattedPhone,
                                "message" => $personalizedMessage,
                                "delay" => $delay,
                            ];

                            if ($scheduledDate->isFuture()) {
                                $scheduleTimestamp = $scheduledDate->timestamp;
                                $messageData["schedule"] = $scheduleTimestamp;
                            }

                            $messagesToSend[] = $messageData;

                            $scheduleData[] = [
                                'target' => $formattedPhone,
                                'customer_id' => $customerObj->id,
                                'scheduled_at' => $scheduledDate,
                                'message' => $personalizedMessage
                            ];

                        } catch (\Exception $e) {
                            Log::error('Error processing customer ' . ($customerId ?? 'unknown') . ': ' . $e->getMessage());
                        }
                    }
                }
            }

            if (empty($messagesToSend)) {
                throw new \Exception('Tidak ada pesan yang dapat dijadwalkan.');
            }

            $this->sendToFonnte($messagesToSend, $fonnteToken, $campaign, $scheduleData);
        
        } catch (\Exception $e) {
            Log::error("scheduleReminderMessages failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function calculateScheduleDate($scheduleDate, $timeSend, $targetProduct, $purchaseQuantity, $sendNow = false)
    {
        if ($sendNow && (!$targetProduct || $targetProduct->default_estimation_days_per_unit == 0)) {
            return now();
        }

        try {
            $baseDate = \Carbon\Carbon::parse($scheduleDate)->startOfDay();
        } catch (\Exception $e) {
            $baseDate = now()->startOfDay();
        }

        $currentTime = now();
        $hour = $currentTime->hour;
        $minute = $currentTime->minute;
        
        if ($timeSend && preg_match('/^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$/', $timeSend)) {
            $timeParts = explode(':', $timeSend);
            $hour = intval($timeParts[0]);
            $minute = intval($timeParts[1]);
        }
        
        $baseDate->setTime($hour, $minute, 0);
        $finalDate = $baseDate->copy();

        if ($targetProduct && $targetProduct->default_estimation_days_per_unit > 0) {
            $estimationDays = $targetProduct->default_estimation_days_per_unit * $purchaseQuantity;
            $finalDate->addDays($estimationDays);
        }

        if (!$sendNow && $finalDate->isPast()) {
            if ($finalDate->isToday()) {
                $finalDate->addDay();
            } else {
                $finalDate = now()->addDay()->setTime($hour, $minute, 0);
                
                if ($targetProduct && $targetProduct->default_estimation_days_per_unit > 0) {
                    $estimationDays = $targetProduct->default_estimation_days_per_unit * $purchaseQuantity;
                    $finalDate->addDays($estimationDays);
                }
            }
        } elseif ($sendNow && $finalDate->isPast()) {
            return now();
        }

        return $finalDate;
    }

    private function personalizeMessage($messageTemplate, $customer, $product, $quantity, $estimationDate, $receipt = null)
    {
        $customerName = (string) ($customer->name ?? 'Customer');
        $productName = $product ? (string) ($product->name ?? 'Product') : '';
        $quantityStr = (string) $quantity;
        $estimationDateStr = (string) $estimationDate;
        $receiptStr = (string) ($receipt ?? '');

        $replacements = [
            '{customer_name}' => $customerName,
            '{product_name}' => $productName,
            '{quantity_purchased}' => $quantityStr,
            '{estimated_finish_date}' => $estimationDateStr,
            '{receipt}' => $receiptStr,
        ];

        if ($product) {
            $replacements['{product_name}'] = $productName;
            $replacements['{product}'] = $productName;
        } else {
            $replacements['{product_name}'] = '';
            $replacements['{product}'] = '';
        }

        $finalMessage = str_replace(
            array_keys($replacements),
            array_values($replacements),
            (string) $messageTemplate
        );

        return trim($finalMessage);
    }

    private function sendToFonnte(array $messages, string $fonnteToken, Campaign $campaign, array $scheduleData = [])
    {
        $payload = [
            'data' => json_encode($messages)
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => $fonnteToken
            ])->asForm()->post('https://api.fonnte.com/send', $payload);

            $res = $response->json();

            if ($response->successful() && isset($res['id']) && is_array($res['id'])) {
                $device = null;
                if (isset($res['quota']) && is_array($res['quota'])) {
                    $deviceNumbers = array_keys($res['quota']);
                    $device = $deviceNumbers[0] ?? null;
                }

                $statusMapping = [
                    'sent' => 'Terkirim',
                    'pending' => 'Tertunda', 
                    'waiting' => 'Menunggu',
                    'invalid' => 'Tidak Valid',
                    'expired' => 'Kedaluwarsa',
                    'processing' => 'Memproses',
                    'url unreachable' => 'URL Tidak Dapat Diakses'
                ];

                $processStatus = $res['process'] ?? 'pending';
                $processStatusIndo = $statusMapping[strtolower($processStatus)] ?? 'Tertunda';

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
                            'status' => $processStatusIndo,
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
                $errorMessage = isset($res['reason']) ? $res['reason'] : 'Unknown error from Fonnte API';
                throw new \Exception('Fonnte API Error: ' . $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send messages to Fonnte: ' . $e->getMessage());
            throw new \Exception('Gagal mengirim pesan ke Fonnte: ' . $e->getMessage());
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
                $customer->receipt = $purchaseData ? $purchaseData->pivot->receipt : null;
            } else {
                $customer->purchase_quantity = 1;
                $customer->receipt = null;
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
                    $messageLog->customer->receipt = $purchaseData ? $purchaseData->pivot->receipt : null;
                } else {
                    $messageLog->customer->purchase_quantity = 1;
                    $messageLog->customer->receipt = null;
                }
            }
        }

        return view('pages.Admin.Campaign.show', compact('campaign','customers', 'customerGroups','product','template','messageLogs'));
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', 'Kampanye berhasil dihapus.');
    }

    public function downloadCsvTemplate()
    {
        $filePath = public_path('assets/template_customers.csv');
        $filename = 'template_customer.csv';

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return Response::download($filePath, $filename, $headers);
    }

    public function reuse(Campaign $campaign)
    {
        return redirect()->route('campaigns.create', ['reuse' => $campaign->id]);
    }
}