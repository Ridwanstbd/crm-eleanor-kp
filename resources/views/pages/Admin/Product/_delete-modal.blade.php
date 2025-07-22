<x-layouts.modal
    name="delete-product-{{ $product->id }}"
    title="Hapus Produk"
    mode="destroy"
    :message="'Apakah Anda yakin ingin menghapus produk ' . $product->name . '?'"
>
    <form action="{{ route('products.destroy', $product) }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="flex justify-end gap-2">
            <x-elements.button type="submit" variant="danger">Hapus</x-elements.button>
        </div>
    </form>
</x-layouts.modal>
