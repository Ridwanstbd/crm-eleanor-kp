<x-Layouts.AdminLayout>
    <x-Organisms.PageHeader title="Buat Kampanye" />
    
    <form method="POST" action="{{ route('campaigns.store') }}" enctype="multipart/form-data">
        @csrf
        <div x-data="{ 
            newAudiens: false, 
            customer: false,
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
            }
        }">
        <div class="grid grid-cols-2 gap-2 w-full">
            <x-Fragments.Form.FormGroup label="Nama" for="name" >
                <x-Elements.Input  
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        placeholder="Nama Kampanye"
                        class="w-full" 
                        />
            </x-Fragments.Form.FormGroup>
            <x-Fragments.Form.FormGroup label="Produk" for="product" >
                <x-Fragments.Select
                        name="product" 
                        id="product" 
                        :options="$products"
                        valueField="id"
                        placeholder="Pilih Produk"
                        :selected="old('product')"
                        class="w-full"
                    />
            </x-Fragments.Form.FormGroup>
            <x-Fragments.Form.FormGroup label="Template Pesan" for="template" >
                <x-Fragments.Select
                        name="template" 
                        id="template" 
                        :options="$templates"
                        valueField="id"
                        placeholder="Pilih Template"
                        :selected="old('template')"
                        class="w-full"
                    />
            </x-Fragments.Form.FormGroup>
            <x-Fragments.Form.FormGroup label="Tanggal Terjual" for="tanggal_terjual">
                <x-Elements.InputDate
                    name="tanggal_terjual"
                    id="tanggal_terjual"
                    value="{{ old('tanggal_terjual') }}"
                    min="{{ date('Y-m-d') }}"
                />
            </x-Fragments.Form.FormGroup>
            <x-Fragments.Form.FormGroup label="Target Audiens" for="target">
                <div class="flex gap-2 mt-2">
                    <x-Elements.Checkbox 
                        id="new_audiens" 
                        name="new_audiens" 
                        label="Audiens Baru"
                        @change="checkNewAudiens()"
                    />
                    <x-Elements.Checkbox 
                        id="customer" 
                        name="customer" 
                        label="Pelanggan"
                        @change="checkCustomer()"
                    />
                </div>
            </x-Fragments.Form.FormGroup>
        </div>

        {{-- Section untuk New Audiens --}}
        <div x-show="newAudiens" x-transition class="grid grid-cols-2 gap-2 w-full mt-4">
            <x-Fragments.Form.FormGroup label="Nama Grup Pembeli" for="name_group_customer" >
                <x-Elements.Input  
                        name="name_group_customer"
                        id="name_group_customer"
                        value="{{ old('name_group_customer') }}"
                        placeholder="Pembeli di shopee"
                        class="w-full" 
                        />
            </x-Fragments.Form.FormGroup>
            <x-Fragments.Form.FormGroup label="Upload CSV" for="csv_file" >
                <x-Elements.Input  
                        type="file"
                        name="csv_file"
                        id="csv_file"
                        accept=".csv"
                        class="w-full" 
                        />
            </x-Fragments.Form.FormGroup>
        </div>

        {{-- Section untuk Customer --}}
        <div x-show="customer" x-transition class="grid grid-cols-2 gap-2 w-full mt-4">
            <x-Fragments.Form.FormGroup label="Pilih Grup Customer" for="customer_group" >
                <x-Fragments.Select
                        name="customer_group" 
                        id="customer_group" 
                        :options="$customerGroups ?? []"
                        valueField="id"
                        placeholder="Pilih Grup Customer"
                        :selected="old('customer_group')"
                        class="w-full"
                    />
            </x-Fragments.Form.FormGroup>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end mt-6">
            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </div>
</x-Layouts.AdminLayout>