<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Product;
use App\Models\MessageTemplate;
use App\Models\CustomerGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::all();
        return view('pages.Admin.Campaign.index', compact('campaigns'));
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
        ];

        $validationMessages = [
            'name.required' => 'Nama kampanye wajib diisi.',
            'template.required' => 'Template pesan wajib dipilih.',
            'template.exists' => 'Template pesan yang dipilih tidak valid.',
            'product.exists' => 'Produk yang dipilih tidak valid.',
            'tanggal_terjual.date' => 'Format tanggal tidak valid.',
        ];

        // Cek apakah ada target audiens yang dipilih
        if (!$request->has('new_audiens') && !$request->has('customer')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Pilih minimal satu target audiens (Audiens Baru atau Pelanggan).');
        }

        // Validasi untuk new audiens
        if ($request->has('new_audiens') && $request->new_audiens == '1') {
            $validationRules['name_group_customer'] = 'required|string|max:255';
            $validationRules['csv_file'] = 'required|file|mimes:csv,txt|max:2048';
            
            $validationMessages['name_group_customer.required'] = 'Nama grup pelanggan wajib diisi untuk audiens baru.';
            $validationMessages['csv_file.required'] = 'File CSV wajib diupload untuk audiens baru.';
            $validationMessages['csv_file.mimes'] = 'File harus berformat CSV.';
            $validationMessages['csv_file.max'] = 'Ukuran file maksimal 2MB.';
        }

        // Validasi untuk existing customers
        if ($request->has('customer') && $request->customer == '1') {
            $validationRules['selected_customers'] = 'required|array|min:1';
            $validationRules['selected_customers.*'] = 'exists:customers,id';
            
            $validationMessages['selected_customers.required'] = 'Pilih minimal satu pelanggan.';
            $validationMessages['selected_customers.min'] = 'Pilih minimal satu pelanggan.';
            $validationMessages['selected_customers.*.exists'] = 'Pelanggan yang dipilih tidak valid.';
            
            // Jika ada produk, validasi customer quantities
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
            ]);
            if ($request->has('new_audiens') && $request->new_audiens == '1') {
                $this->handleNewAudience($request, $campaign);
            }

            if ($request->has('customer') && $request->customer == '1') {
                $this->handleExistingCustomers($request, $campaign);
            }

            DB::commit();
            
            return redirect()->route('campaigns.index')
                ->with('success', 'Kampanye berhasil dibuat.');
                
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
                    $campaign->product_id => ['last_purchase_quantity' => $purchaseQuantity]
                ]);
                
            }
        }

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
                $headers = ['phone', 'name', 'purchase_quantity'];
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
                                $campaign->product_id => ['last_purchase_quantity' => $purchaseQuantity]
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

        } catch (\Exception $e) {
            Storage::disk('public')->delete($csvPath);
            throw new \Exception('Error processing CSV file: ' . $e->getMessage());
        }
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
        
        // If no name field found, try second column or generate default
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
        
        // If no quantity field found, try third column or default to 1
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
            $phone = '+62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) === '62') {
            $phone = '+' . $phone;
        } elseif (substr($phone, 0, 3) !== '+62') {
            $phone = '+62' . $phone;
        }
        
        return $phone;
    }

    public function edit(Campaign $campaign)
    {
        return view('pages.Admin.Campaign.show', compact('campaign'));
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
}