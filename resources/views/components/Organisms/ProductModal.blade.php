@props(['product' => null, 'mode' => 'create'])

@if($mode === 'edit' && $product)
    <x-Layouts.Modal
        name="edit-product-{{ $product->id }}"
        title="Ubah Produk"
        mode="edit"
        maxWidth="md"
        :showIcon="false"
    >
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf
            @method('PUT')
            
            <x-Molecules.Form.FormGroup for="edit-name-{{ $product->id }}" label="Nama Produk">
                <x-Atoms.form.input
                    id="edit-name-{{ $product->id }}"
                    name="name"
                    value="{{ $product->name }}"
                    placeholder="Susu Pengganti"
                    class="w-full"
                />
            </x-Molecules.Form.FormGroup>
            <x-Molecules.Form.FormGroup for="edit-default_estimation_days_per_unit-{{ $product->id }}" label="Jangka Waktu">
                <div class="flex items-center gap-2">
                    <x-Atoms.form.input
                        type="number"
                        id="edit-default_estimation_days_per_unit-{{ $product->id }}"
                        name="default_estimation_days_per_unit"
                        value="{{ $product->default_estimation_days_per_unit }}"
                        placeholder="0 untuk produk selain konsumsi"
                        class="w-full"
                    />
                    <span class="text-sm text-gray-700">Hari</span>
                </div>
            </x-Molecules.Form.FormGroup>

            <x-Atoms.Button type="submit" fullWidth>
                Simpan
            </x-Atoms.Button>
        </form>
    </x-Layouts.Modal>
@elseif($mode === 'create')
    <x-Layouts.Modal
        name="create-product"
        title="Tambah Produk"
        mode="create"
        maxWidth="md"
        :showIcon="false"
    >
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <x-Molecules.Form.FormGroup label="Nama Produk" for="create-name">
                <x-Atoms.form.input
                    id="create-name"
                    name="name"
                    placeholder="Susu Pengganti"
                    class="w-full"
                />
            </x-Molecules.Form.FormGroup>
            <x-Molecules.Form.FormGroup label="Jangka Waktu" for="create-default_estimation_days_per_unit">
                <div class="flex items-center gap-2">
                    <x-Atoms.form.input
                        type="number"
                        id="create-default_estimation_days_per_unit"
                        name="default_estimation_days_per_unit"
                        placeholder="0 Utk Non Konsumsi"
                        class="w-full"
                    />
                    <span class="text-sm text-gray-700">Hari</span>
                </div>
            </x-Molecules.Form.FormGroup>
            <x-Atoms.button variant="submit" fullWidth>
                Tambah
            </x-Atoms.button>
        </form>
    </x-Layouts.Modal>
@endif