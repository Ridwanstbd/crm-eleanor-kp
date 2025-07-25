<x-layouts.modal name="edit-product-{{ $product->id }}" title="Ubah Produk" mode="default">
    <form method="POST" action="{{ route('products.update', $product) }}">
        @csrf
        @method('PUT')

        <div class="p-6 space-y-4">

            {{-- Nama Produk --}}
            <div>
                <label for="name-{{ $product->id }}" class="block mb-1 font-medium">Nama Produk</label>
                <input
                    type="text"
                    id="name-{{ $product->id }}"
                    name="name"
                    value="{{ $product->name }}"
                    placeholder="Nama Produk"
                    class="w-full px-4 py-2 border rounded"
                />
            </div>

            {{-- Jangka Waktu --}}
            <div>
                <label for="default_estimation_days_per_unit-{{ $product->id }}" class="block mb-1 font-medium">Jangka Waktu</label>
                <div class="flex items-center">
                    <input
                        type="number"
                        id="default_estimation_days_per_unit-{{ $product->id }}"
                        name="default_estimation_days_per_unit"
                        value="{{ $product->default_estimation_days_per_unit }}"
                        placeholder="Estimasi Habis"
                        class="w-full px-4 py-2 border rounded"
                    />
                    <span class="ml-2">Hari</span>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <button
                type="submit"
                class="w-full py-2 text-white bg-red-600 rounded hover:bg-red-700 text-center font-semibold"
            >
                Simpan
            </button>
        </div>
    </form>
</x-layouts.modal>
