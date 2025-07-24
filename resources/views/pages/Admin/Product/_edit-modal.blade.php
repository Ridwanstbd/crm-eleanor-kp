<x-layouts.modal
    name="edit-product-{{ $product->id }}"
    title="Edit Produk"
    mode="default"
>
    <div class="p-6"> {{-- tambahkan wrapper padding manual di sini --}}
        <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Nama Produk --}}
            <x-Fragments.Form.FormGroup label="Nama Produk" for="name">
                <x-elements.form.input name="name" :value="$product->name" placeholder="Nama Produk" />
            </x-Fragments.Form.FormGroup>

            {{-- Jangka Waktu --}}
            <x-Fragments.Form.FormGroup label="Jangka Waktu" for="default_estimation_days_per_unit">
                <x-elements.form.input
                    type="number"
                    name="default_estimation_days_per_unit"
                    :value="$product->default_estimation_days_per_unit"
                    placeholder="Estimasi Habis (hari)"
                />
            </x-Fragments.Form.FormGroup>

            {{-- Tombol --}}
            <div class="text-right">
                <x-elements.button type="submit" variant="primary">Simpan</x-elements.button>
            </div>
        </form>
    </div>
</x-layouts.modal>
