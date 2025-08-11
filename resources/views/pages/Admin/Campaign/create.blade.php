<x-Layouts.AdminLayout>
    <x-Organisms.PageHeader title="Buat Kampanye" />
    
    <form method="POST" action="{{ route('campaigns.store') }}" enctype="multipart/form-data">
        @csrf
        <div x-data="{ 
            newAudiens: false, 
            customer: false,
            selectedProduct: '{{ old('product') }}',
            customerQuantities: {},
            allCustomersSelected: false,
            checkNewAudiens() {
                this.newAudiens = document.getElementById('new_audiens').checked;
                if (this.newAudiens) {
                    this.customer = false;
                    document.getElementById('customer').checked = false;
                }
            },
            checkCustomer() {
                this.customer = document.getElementById('customer').checked;
                if (this.customer) {
                    this.newAudiens = false;
                    document.getElementById('new_audiens').checked = false;
                }
            },
            updateSelectedProduct(event) {
                this.selectedProduct = event.target.value;
            },
            initCustomerQuantity(customerId) {
                if (!this.customerQuantities[customerId]) {
                    this.customerQuantities[customerId] = 1;
                }
            },
            updateCustomerQuantity(customerId, value) {
                this.customerQuantities[customerId] = parseInt(value) || 1;
            },
            toggleAllCustomers(checked) {
                this.allCustomersSelected = checked;
                const checkboxes = document.querySelectorAll('.customer-checkbox');
                checkboxes.forEach(checkbox => checkbox.checked = checked);
            },
            updateSelectAllState() {
                const selectAll = document.getElementById('select-all');
                const checkboxes = document.querySelectorAll('.customer-checkbox');
                const checkedBoxes = document.querySelectorAll('.customer-checkbox:checked');
                
                this.allCustomersSelected = checkboxes.length === checkedBoxes.length && checkboxes.length > 0;
                if (selectAll) {
                    selectAll.checked = this.allCustomersSelected;
                }
            }
        }">
        <div class="grid grid-cols-2 gap-2 w-full">
            <x-Fragments.Form.FormGroup label="Nama" for="name" >
                <x-Atoms.Input  
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        placeholder="Nama Kampanye"
                        class="w-full" 
                        required
                        />
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </x-Fragments.Form.FormGroup>
            
            <x-Fragments.Form.FormGroup label="Produk" for="product" >
                <select name="product" id="product" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" @change="updateSelectedProduct($event)">
                    <option value="">Pilih Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                @error('product')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </x-Fragments.Form.FormGroup>
            
            <x-Fragments.Form.FormGroup label="Template Pesan" for="template" >
                <select name="template" id="template" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Pilih Template</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" {{ old('template') == $template->id ? 'selected' : '' }}>
                            {{ $template->name }}
                        </option>
                    @endforeach
                </select>
                @error('template')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </x-Fragments.Form.FormGroup>
            
            <x-Fragments.Form.FormGroup label="Tanggal Terjual" for="tanggal_terjual">
                <x-Atoms.InputDate
                    name="tanggal_terjual"
                    id="tanggal_terjual"
                    value="{{ old('tanggal_terjual') }}"
                    min="{{ date('Y-m-d') }}"
                />
                @error('tanggal_terjual')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </x-Fragments.Form.FormGroup>
            <x-Fragments.Form.FormGroup label="Waktu Kirim Kampanye" for="time_send">
                <x-Atoms.InputTime
                    name="time_send"
                    id="time_send"
                    value="{{ old('time_send') }}"
                    min="{{ date('Y-m-d') }}"
                />
                @error('tanggal_terjual')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </x-Fragments.Form.FormGroup>
            
            <x-Fragments.Form.FormGroup label="Target Audiens" for="target">
                <div class="flex gap-2 mt-2">
                    <x-Atoms.Checkbox 
                        id="new_audiens" 
                        name="new_audiens" 
                        value="1"
                        label="Audiens Baru"
                        :checked="old('new_audiens')"
                        @change="checkNewAudiens()"
                    />
                    <x-Atoms.Checkbox 
                        id="customer" 
                        name="customer" 
                        value="1"
                        label="Pelanggan"
                        :checked="old('customer')"
                        @change="checkCustomer()"
                    />
                </div>
                @error('new_audiens')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                @error('customer')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </x-Fragments.Form.FormGroup>
        </div>

        {{-- Section untuk New Audiens --}}
        <div x-show="newAudiens" x-transition class="w-full mt-4">
            <div class="grid grid-cols-2 gap-2 w-full">
                <x-Fragments.Form.FormGroup label="Nama Grup Pembeli" for="name_group_customer" >
                    <x-Atoms.Input  
                            name="name_group_customer"
                            id="name_group_customer"
                            value="{{ old('name_group_customer') }}"
                            placeholder="Pembeli dari shopee"
                            class="w-full" 
                            />
                    @error('name_group_customer')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </x-Fragments.Form.FormGroup>
                
                <x-Fragments.Form.FormGroup label="Upload CSV" for="csv_file" >
                    <x-Atoms.Input  
                            type="file"
                            name="csv_file"
                            id="csv_file"
                            accept=".csv"
                            class="w-full" 
                            />
                    @error('csv_file')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </x-Fragments.Form.FormGroup>
            </div>
            
            {{-- Info format CSV --}}
            <div class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="text-sm font-medium text-blue-800 mb-2">Format CSV:</h4>
                <p class="text-sm text-blue-700 mb-2">File CSV harus memiliki kolom berikut:</p>
                <ul class="text-sm text-blue-600 list-disc list-inside space-y-1">
                    <li><strong>phone</strong> (wajib): Nomor telepon pelanggan</li>
                    <li><strong>name</strong> (opsional): Nama pelanggan</li>
                    <li><strong>purchase_quantity</strong> (opsional): Jumlah pembelian (1-999)</li>
                </ul>
                <p class="text-xs text-blue-500 mt-2">Contoh: phone,name,purchase_quantity</p>
                <p class="text-xs text-blue-500">081234567890,John Doe,5</p>
            </div>
        </div>

        {{-- Section untuk Customer --}}
        <div x-show="customer" x-transition class="grid grid-cols-1 gap-2 w-full mt-4">
            @error('selected_customers')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror
            
            <x-Layouts.Table>
                <x-Fragments.Table.Header>
                    <x-Atoms.Table.th>
                        <x-Atoms.Checkbox 
                            id="select-all" 
                            name="select-all" 
                            label="Pilih Semua"
                            x-model="allCustomersSelected"
                            @change="toggleAllCustomers($event.target.checked)"
                        />
                    </x-Atoms.Table.th>
                    <x-Atoms.Table.th>Nama</x-Atoms.Table.th>
                    <x-Atoms.Table.th>Nomor Telepon</x-Atoms.Table.th>
                    <x-Atoms.Table.th x-show="selectedProduct && customer">Jumlah Pembelian</x-Atoms.Table.th>
                </x-Fragments.Table.Header>
                <x-Fragments.Table.Body>
                    @forelse($customers as $customerItem)
                    <tr class="customer-row">
                        <x-Atoms.Table.td>
                            <x-Atoms.Checkbox 
                                id="customer_{{ $customerItem->id }}" 
                                name="selected_customers[]" 
                                value="{{ $customerItem->id }}"
                                class="customer-checkbox"
                                :checked="in_array($customerItem->id, old('selected_customers', []))"
                                @change="updateSelectAllState()"
                                x-init="initCustomerQuantity({{ $customerItem->id }})"
                            />
                        </x-Atoms.Table.td>
                        <x-Atoms.Table.td>{{ $customerItem->name }}</x-Atoms.Table.td>
                        <x-Atoms.Table.td>{{ $customerItem->phone }}</x-Atoms.Table.td>
                        <x-Atoms.Table.td x-show="selectedProduct && customer">
                            <x-Atoms.Input  
                                type="number"
                                name="customer_quantities[{{ $customerItem->id }}]"
                                value="{{ old('customer_quantities.' . $customerItem->id, 1) }}"
                                placeholder="Qty"
                                min="1"
                                max="999"
                                class="w-20" 
                                @input="updateCustomerQuantity({{ $customerItem->id }}, $event.target.value)"
                            />
                        </x-Atoms.Table.td>
                    </tr>
                    @empty
                    <x-Atoms.Table.Empty colspan="4">
                        <p class="text-sm text-gray-500 mt-2">Belum ada data pelanggan</p>
                    </x-Atoms.Table.Empty>
                    @endforelse
                    
                    <x-slot name="pagination">
                        <x-Fragments.Table.Pagination :paginator="$customers" />
                    </x-slot>
                </x-Fragments.Table.Body>
            </x-Layouts.Table>
            
            {{-- Info untuk pelanggan --}}
            <div x-show="selectedProduct && customer" class="mt-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-700">
                    <strong>Catatan:</strong> Setiap pelanggan dapat memiliki jumlah pembelian yang berbeda. 
                    Atur jumlah pembelian di kolom "Jumlah Pembelian" untuk setiap pelanggan yang dipilih.
                </p>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end mt-6">
            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </div>
    </form>
</x-Layouts.AdminLayout>