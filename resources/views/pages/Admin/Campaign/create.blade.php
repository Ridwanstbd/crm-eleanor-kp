<x-Layouts.AdminLayout>
    <x-Layouts.PageHeader title="Buat Kampanye" />
    
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
            
            <x-Molecules.Form.FormGroup label="Produk" for="product" >
                <x-Atoms.Select name="product" id="product" class="w-full" @change="updateSelectedProduct($event)">
                    <x-Atoms.Option value="">Pilih Produk</x-Atoms.Option>
                    @foreach($products as $product)
                        <x-Atoms.Option value="{{ $product->id }}" :selected="old('product') == $product->id">
                            {{ $product->name }}
                        </x-Atoms.Option>
                    @endforeach
                </x-Atoms.Select>
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
            
            <x-Molecules.Form.FormGroup label="Tanggal Terjual" for="tanggal_terjual">
                <x-Atoms.InputDate
                    name="tanggal_terjual"
                    id="tanggal_terjual"
                    value="{{ old('tanggal_terjual') }}"
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
        </div>

        <div x-show="customer" x-transition class="grid grid-cols-1 gap-2 w-full mt-4">
            @error('selected_customers')
                <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
            @enderror
            
            <x-Layouts.Table>
                <x-Molecules.Table.Header>
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
                </x-Molecules.Table.Header>
                <x-Molecules.Table.Body>
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
                        <x-Molecules.Table.Pagination :paginator="$customers" />
                    </x-slot>
                </x-Molecules.Table.Body>
            </x-Layouts.Table>
            
            <div x-show="selectedProduct && customer" class="mt-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-700">
                    <strong>Catatan:</strong> Setiap pelanggan dapat memiliki jumlah pembelian yang berbeda. 
                    Atur jumlah pembelian di kolom "Jumlah Pembelian" untuk setiap pelanggan yang dipilih.
                </p>
            </div>
        </div>

        <div class="flex justify-end mt-6">
            <x-Atoms.Button type="submit" variant="primary">Simpan</x-Atoms.Button>
        </div>
    </div>
    </form>
</x-Layouts.AdminLayout>