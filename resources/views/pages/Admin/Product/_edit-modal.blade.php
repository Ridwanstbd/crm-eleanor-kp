<x-layouts.modal
    name="edit-product-{{ $product->id }}"
    title="Edit Produk"
    mode="edit"
>
    <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-4 p-6">
        @csrf
        @method('PUT')

        <x-elements.form.input name="name" :value="$product->name" class="p-2" />
        <x-elements.form.input type="number" name="default_estimation_days_per_unit" :value="$product->default_estimation_days_per_unit" class="p-2" />

        <div class="text-right">
            <x-elements.button type="submit" variant="primary" class="px-4 py-2">Simpan Perubahan</x-elements.button>
        </div>
    </form>
</x-layouts.modal>
