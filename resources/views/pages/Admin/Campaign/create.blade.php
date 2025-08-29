<x-Layouts.AdminLayout>
    <x-Layouts.PageHeader title="Buat Kampanye" />
    
    <form method="POST" action="{{ route('campaigns.store') }}" enctype="multipart/form-data">
        @csrf
        <div x-data="campaignForm()">
        <div class="grid grid-cols-2 gap-2 w-full">
            <x-Molecules.Form.FormGroup label="Nama" for="name" >
                <x-Atoms.Input  
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            placeholder="Nama Kampanye"
                            class="w-full" 
                            required
                            />
            </x-Molecules.Form.FormGroup>
            
            <x-Molecules.Form.FormGroup label="Produk (Opsional)" for="product" >
                <x-Atoms.Select name="product" id="product" class="w-full" @change="updateSelectedProduct($event)">
                    <x-Atoms.Option value="">Tanpa Produk</x-Atoms.Option>
                    @foreach($products as $product)
                        <x-Atoms.Option value="{{ $product->id }}" :selected="old('product') == $product->id">
                            {{ $product->name }}
                        </x-Atoms.Option>
                    @endforeach
                </x-Atoms.Select>
                <p class="text-sm text-gray-500 mt-1">Pilih produk jika kampanye terkait dengan produk tertentu</p>
            </x-Molecules.Form.FormGroup>
            
            <x-Molecules.Form.FormGroup label="Template Pesan" for="template" >
                <x-Atoms.Select name="template" id="template" class="w-full" required>
                    <x-Atoms.Option value="">Pilih Template</x-Atoms.Option>
                    @foreach($templates as $template)
                        <x-Atoms.Option value="{{ $template->id }}" :selected="old('template') == $template->id">
                            {{ $template->name }}
                        </x-Atoms.Option>
                    @endforeach
                </x-Atoms.Select>
            </x-Molecules.Form.FormGroup>
            
            <x-Molecules.Form.FormGroup label="Tanggal Kampanye" for="schedule">
                <x-Atoms.InputDate
                    name="schedule"
                    id="schedule"
                    value="{{ old('schedule') }}"
                    min="{{ date('Y-m-d') }}"
                />
            </x-Molecules.Form.FormGroup>
            <x-Molecules.Form.FormGroup label="Waktu Kirim Kampanye" for="time_send">
                <x-Atoms.InputTime
                    name="time_send"
                    id="time_send"
                    value="{{ old('time_send') }}"
                    min="{{ date('Y-m-d') }}"
                />
            </x-Molecules.Form.FormGroup>
            
            <x-Molecules.Form.FormGroup label="Target Audiens" for="target">
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
            </x-Molecules.Form.FormGroup>
        </div>

        <div x-show="newAudiens" x-transition class="w-full mt-4">
            <div class="grid grid-cols-2 gap-2 w-full">
                <x-Molecules.Form.FormGroup label="Nama Grup Pembeli" for="name_group_customer">
                    <x-Atoms.Input  
                            name="name_group_customer"
                            id="name_group_customer"
                            value="{{ old('name_group_customer') }}"
                            placeholder="Pembeli dari shopee"
                            class="w-full" 
                            />
                </x-Molecules.Form.FormGroup>
                
                <x-Molecules.Form.FormGroup label="Upload CSV" for="csv_file">
                    <x-Atoms.Input  
                            type="file"
                            name="csv_file"
                            id="csv_file"
                            accept=".csv"
                            class="w-full" 
                            />
                </x-Molecules.Form.FormGroup>
            </div>
            <x-Organisms.InformationCsvUpload />
            
            <div x-show="!selectedProduct" class="mt-3 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <p class="text-sm text-amber-700">
                    <strong>Catatan:</strong> Karena tidak ada produk yang dipilih, kolom "jumlah_beli" dan "resi" dalam CSV akan diabaikan.
                </p>
            </div>
        </div>

        <div x-show="customer" x-transition class="grid grid-cols-1 gap-4 w-full mt-4">
    
        <div x-show="!showCustomerSelection" x-transition>
            <x-Molecules.Form.FormGroup label="Pilih Grup Pelanggan" for="customer_groups">
                <div class="grid grid-cols-2 gap-2 mt-2">
                    @forelse($customerGroups as $group)
                        <x-Atoms.Checkbox 
                            id="group_{{ $group->id }}" 
                            name="selected_customer_groups[]" 
                            value="{{ $group->id }}"
                            label="{{ $group->name }} ({{ $group->customers_count ?? 0 }} pelanggan)"
                            :checked="in_array($group->id, old('selected_customer_groups', []))"
                            @change="toggleCustomerGroup({{ $group->id }}, $event.target.checked)"
                        />
                    @empty
                        <p class="text-sm text-gray-500 col-span-2">Belum ada grup pelanggan</p>
                    @endforelse
                </div>
                @error('selected_customer_groups')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </x-Molecules.Form.FormGroup>

            <div class="flex justify-between items-center mt-4">
                <div>
                    <span class="text-sm text-gray-600" x-show="selectedGroups.length > 0">
                        <strong x-text="selectedGroups.length"></strong> grup dipilih
                    </span>
                </div>
                <x-Atoms.Button 
                    type="button" 
                    variant="primary"
                    x-show="selectedGroups.length > 0"
                    @click="goToCustomerSelection()"
                >
                    Lanjut Pilih Pelanggan
                </x-Atoms.Button>
            </div>
        </div>

        <div x-show="showCustomerSelection" x-transition>
            <div class="flex justify-between items-center mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Pilih Pelanggan</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Dari grup: <strong x-text="getSelectedGroupNames().join(', ')"></strong>
                    </p>
                    <p class="text-sm text-blue-600">
                        Total tersedia: <span x-text="getFilteredCustomersByGroups().length"></span> pelanggan
                    </p>
                </div>
                <x-Atoms.Button 
                    type="button" 
                    variant="secondary"
                    @click="goBackToGroupSelection()"
                >
                    ← Kembali ke Grup
                </x-Atoms.Button>
            </div>

            @error('selected_customers')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror
            
            <x-Organisms.SearchInputCustomers 
                :totalCustomers="0"
                placeholder="Cari nama atau nomor telepon..."
            />
            
            <x-Layouts.Table minHeight="400px">
                <x-Molecules.Table.Header>
                    <x-Atoms.Table.th>
                        <x-Atoms.Checkbox 
                            id="select-all-filtered" 
                            name="select-all-filtered" 
                            label="Pilih Semua"
                            x-model="allFilteredCustomersSelected"
                            @change="toggleAllFilteredCustomers($event.target.checked)"
                        />
                    </x-Atoms.Table.th>
                    <x-Atoms.Table.th>Nama</x-Atoms.Table.th>
                    <x-Atoms.Table.th>Nomor Telepon</x-Atoms.Table.th>
                    <x-Atoms.Table.th>Grup</x-Atoms.Table.th>
                    <x-Atoms.Table.th x-show="selectedProduct">Jumlah Pembelian</x-Atoms.Table.th>
                    <x-Atoms.Table.th x-show="selectedProduct">Nomor Resi</x-Atoms.Table.th> <!-- New Header -->
                </x-Molecules.Table.Header>
                <x-Molecules.Table.Body>
                    <template x-for="customerItem in getDisplayedCustomers()" :key="customerItem.id">
                        <tr class="customer-row">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <input 
                                        x-bind:id="'customer_filtered_' + customerItem.id"
                                        name="selected_customers[]" 
                                        type="checkbox"
                                        x-bind:value="customerItem.id"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded customer-checkbox-filtered"
                                        x-bind:checked="isCustomerSelected(customerItem.id)"
                                        @change="toggleCustomerSelection(customerItem.id, $event.target.checked)"
                                    />
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="customerItem.name"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="customerItem.phone"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="group in customerItem.groups" :key="group.id">
                                        <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full" 
                                            x-text="group.name"
                                            :class="selectedGroups.includes(group.id) ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'">
                                        </span>
                                    </template>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" x-show="selectedProduct">
                                <input 
                                    type="number"
                                    x-bind:name="'customer_quantities[' + customerItem.id + ']'"
                                    x-bind:value="getCustomerQuantity(customerItem.id)"
                                    placeholder="Qty"
                                    min="1"
                                    max="999"
                                    class="w-20 border border-gray-300 rounded px-2 py-1" 
                                    @input="updateCustomerQuantity(customerItem.id, $event.target.value)"
                                />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" x-show="selectedProduct">
                                <input 
                                    type="text"
                                    x-bind:name="'customer_receipts[' + customerItem.id + ']'"
                                    x-bind:value="getCustomerReceipt(customerItem.id)"
                                    placeholder="Nomor Resi"
                                    class="w-40 border border-gray-300 rounded px-2 py-1" 
                                    @input="updateCustomerReceipt(customerItem.id, $event.target.value)"
                                />
                            </td>
                        </tr>
                    </template>
                    <tr x-show="getFilteredCustomersByGroups().length === 0">
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada pelanggan dalam grup yang dipilih
                        </td>
                    </tr>
                    
                    <tr x-show="searchQuery.length > 0 && getDisplayedCustomers().length === 0 && getFilteredCustomersByGroups().length > 0">
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada pelanggan yang ditemukan dengan kata kunci "<span x-text="searchQuery"></span>"
                        </td>
                    </tr>
                </x-Molecules.Table.Body>
            </x-Layouts.Table>
            
            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg" x-show="selectedCustomers.length > 0">
                <p class="text-sm text-green-700">
                    <strong x-text="selectedCustomers.length"></strong> pelanggan dipilih untuk kampanye
                </p>
            </div>
            
            <div x-show="selectedProduct" class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-700">
                    <strong>Catatan:</strong> Setiap pelanggan dapat memiliki jumlah pembelian dan nomor resi yang berbeda.
                </p>
            </div>
            
            <div x-show="!selectedProduct" class="mt-3 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <p class="text-sm text-amber-700">
                    <strong>Catatan:</strong> Tidak ada produk yang dipilih, jadi jumlah pembelian dan nomor resi tidak diperlukan.
                </p>
            </div>
        </div>
    </div>

        <div class="flex justify-end mt-6" 
             x-show="!(customer && !showCustomerSelection)">
            <x-Atoms.Button type="submit" variant="danger">Kirim Pesan Kampanye</x-Atoms.Button>
        </div>
    </div>
    </form>

    <script>
        function campaignForm() {
            return {
                newAudiens: false, 
                customer: false,
                showCustomerSelection: false,
                selectedProduct: '{{ old('product') }}',
                customerQuantities: {},
                customerReceipts: {}, 
                allFilteredCustomersSelected: false,
                searchQuery: '',
                selectedGroups: @json(old('selected_customer_groups', [])),
                selectedCustomers: @json(old('selected_customers', [])),
                customerGroups: @json($customerGroups),
                allCustomers: @json($customers->load('groups')),
                
                init() {
                    if (this.selectedGroups.length > 0) {
                        this.showCustomerSelection = true;
                    }
                },
                
                checkNewAudiens() {
                    this.newAudiens = document.getElementById('new_audiens').checked;
                    if (this.newAudiens) {
                        this.customer = false;
                        document.getElementById('customer').checked = false;
                        this.resetCustomerFlow();
                    }
                },
                
                checkCustomer() {
                    this.customer = document.getElementById('customer').checked;
                    if (this.customer) {
                        this.newAudiens = false;
                        document.getElementById('new_audiens').checked = false;
                    } else {
                        this.resetCustomerFlow();
                    }
                },
                
                updateSelectedProduct(event) {
                    this.selectedProduct = event.target.value;
                },
                
                toggleCustomerGroup(groupId, checked) {
                    if (checked) {
                        if (!this.selectedGroups.includes(groupId)) {
                            this.selectedGroups.push(groupId);
                        }
                    } else {
                        this.selectedGroups = this.selectedGroups.filter(id => id !== groupId);
                    }
                },
                
                goToCustomerSelection() {
                    if (this.selectedGroups.length === 0) {
                        alert('Pilih minimal satu grup pelanggan terlebih dahulu');
                        return;
                    }
                    
                    this.showCustomerSelection = true;
                    this.searchQuery = '';
                    this.updateSelectAllFilteredState();
                },
                
                goBackToGroupSelection() {
                    this.selectedCustomers = [];
                    this.customerQuantities = {};
                    this.customerReceipts = {}; 
                    this.allFilteredCustomersSelected = false;
                    this.searchQuery = '';
                    this.showCustomerSelection = false;
                    
                    this.$nextTick(() => {
                        const customerCheckboxes = document.querySelectorAll('.customer-checkbox-filtered');
                        customerCheckboxes.forEach(checkbox => checkbox.checked = false);
                        
                        const selectAllCheckbox = document.getElementById('select-all-filtered');
                        if (selectAllCheckbox) {
                            selectAllCheckbox.checked = false;
                        }
                    });
                },
                
                getSelectedGroupNames() {
                    return this.customerGroups
                        .filter(group => this.selectedGroups.includes(group.id))
                        .map(group => group.name);
                },
                
                getFilteredCustomersByGroups() {
                    if (this.selectedGroups.length === 0) {
                        return [];
                    }
                    
                    return this.allCustomers.filter(customer => 
                        customer.groups.some(group => 
                            this.selectedGroups.includes(group.id)
                        )
                    );
                },
                
                getDisplayedCustomers() {
                    let customers = this.getFilteredCustomersByGroups();
                    
                    if (this.searchQuery.trim() === '') {
                        return customers;
                    }
                    
                    const query = this.searchQuery.trim();
                    return customers.filter(customer => {
                        const name = customer.name ? customer.name.toLowerCase() : '';
                        const phone = customer.phone ? customer.phone.toString() : '';
                        
                        const nameMatch = name.includes(query.toLowerCase());
                        const cleanQuery = query.replace(/\D/g, '');
                        const cleanPhone = phone.replace(/\D/g, '');
                        const phoneMatch = cleanPhone.includes(cleanQuery);
                        const phoneExactMatch = phone.includes(query);
                        
                        return nameMatch || phoneMatch || phoneExactMatch;
                    });
                },
                
                isCustomerSelected(customerId) {
                    return this.selectedCustomers.includes(customerId);
                },
                
                toggleCustomerSelection(customerId, checked) {
                    if (checked) {
                        if (!this.selectedCustomers.includes(customerId)) {
                            this.selectedCustomers.push(customerId);
                        }
                        if (this.selectedProduct) {
                            this.initCustomerQuantity(customerId);
                            this.initCustomerReceipt(customerId);
                        }
                    } else {
                        this.selectedCustomers = this.selectedCustomers.filter(id => id !== customerId);
                        delete this.customerQuantities[customerId];
                        delete this.customerReceipts[customerId];
                    }
                    this.updateSelectAllFilteredState();
                },
                
                toggleAllFilteredCustomers(checked) {
                    const displayedCustomers = this.getDisplayedCustomers();
                    
                    if (checked) {
                        displayedCustomers.forEach(customer => {
                            if (!this.selectedCustomers.includes(customer.id)) {
                                this.selectedCustomers.push(customer.id);
                                if (this.selectedProduct) {
                                    this.initCustomerQuantity(customer.id);
                                    this.initCustomerReceipt(customer.id);
                                }
                            }
                        });
                    } else {
                        displayedCustomers.forEach(customer => {
                            this.selectedCustomers = this.selectedCustomers.filter(id => id !== customer.id);
                            delete this.customerQuantities[customer.id];
                            delete this.customerReceipts[customer.id]; // Delete receipt
                        });
                    }
                    
                    this.allFilteredCustomersSelected = checked;
                },
                
                updateSelectAllFilteredState() {
                    const displayedCustomers = this.getFilteredCustomersByGroups();
                    if (displayedCustomers.length === 0) {
                        this.allFilteredCustomersSelected = false;
                        return;
                    }
                    
                    const selectedDisplayedCount = displayedCustomers.filter(customer => 
                        this.selectedCustomers.includes(customer.id)
                    ).length;
                    
                    this.allFilteredCustomersSelected = selectedDisplayedCount === displayedCustomers.length;
                },
                
                initCustomerQuantity(customerId) {
                    if (!this.customerQuantities[customerId]) {
                        this.customerQuantities[customerId] = 1;
                    }
                },

                initCustomerReceipt(customerId) {
                    if (!this.customerReceipts[customerId]) {
                        this.customerReceipts[customerId] = '';
                    }
                },
                
                getCustomerQuantity(customerId) {
                    return this.customerQuantities[customerId] || 1;
                },

                getCustomerReceipt(customerId) {
                    return this.customerReceipts[customerId] || '';
                },
                
                updateCustomerQuantity(customerId, value) {
                    this.customerQuantities[customerId] = parseInt(value) || 1;
                },

                updateCustomerReceipt(customerId, value) {
                    this.customerReceipts[customerId] = value;
                },
                
                resetCustomerFlow() {
                    this.selectedGroups = [];
                    this.selectedCustomers = [];
                    this.customerQuantities = {};
                    this.customerReceipts = {};
                    this.allFilteredCustomersSelected = false;
                    this.searchQuery = '';
                    this.showCustomerSelection = false;
                    
                    this.$nextTick(() => {
                        const groupCheckboxes = document.querySelectorAll('input[name="selected_customer_groups[]"]');
                        groupCheckboxes.forEach(checkbox => checkbox.checked = false);
                        
                        const customerCheckboxes = document.querySelectorAll('.customer-checkbox-filtered');
                        customerCheckboxes.forEach(checkbox => checkbox.checked = false);
                        
                        const selectAllCheckbox = document.getElementById('select-all-filtered');
                        if (selectAllCheckbox) {
                            selectAllCheckbox.checked = false;
                        }
                    });
                },
                
                searchCustomers() {
                    this.updateSelectAllFilteredState();
                },
                
                clearSearch() {
                    this.searchQuery = '';
                    this.updateSelectAllFilteredState();
                }
            }
        }
    </script>
</x-Layouts.AdminLayout>
