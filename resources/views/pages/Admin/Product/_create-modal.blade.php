<x-layouts.modal
    name="create-product"
    title="Tambah Produk"
    mode="create"
>
    <form action="{{ route('products.store') }}" method="POST" class="space-y-4 p-4">
        @csrf
        <x-Fragments.Form.FormGroup label="Nama Produk" for="name">
            <x-elements.form.input name="name" placeholder="Nama Produk" />
        </x-Fragments.Form.FormGroup>
        <x-Fragments.Form.FormGroup label="Jangka Waktu" for="name">
            <x-elements.form.input type="number" name="default_estimation_days_per_unit" placeholder="Estimasi Habis (hari)" />
        </x-Fragments.Form.FormGroup>
        <div class="text-right">
            <x-elements.button type="submit" variant="primary">Simpan</x-elements.button>
        </div>
    </form>
</x-layouts.modal>
