<x-layouts.modal
    name="edit-product-{{ $product->id }}"
    title="Ubah Produk"
    mode="edit"
    maxWidth="md"
    :showIcon="false"
>
    <form method="POST" action="{{ route('products.update', $product) }}">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            {{-- Nama Produk --}}
            <div>
                <label for="edit-name-{{ $product->id }}" class="block text-sm font-semibold text-gray-900 mb-1">
                    Nama Produk
                </label>
                <x-elements.form.input
                    id="edit-name-{{ $product->id }}"
                    name="name"
                    value="{{ $product->name }}"
                    placeholder="Susu Pengganti"
                    class="w-full"
                />
            </div>

            {{-- Jangka Waktu --}}
            <div>
                <label for="edit-default_estimation_days_per_unit-{{ $product->id }}" class="block text-sm font-semibold text-gray-900 mb-1">
                    Jangka Waktu
                </label>
                <div class="flex items-center gap-2">
                    <x-elements.form.input
                        type="number"
                        id="edit-default_estimation_days_per_unit-{{ $product->id }}"
                        name="default_estimation_days_per_unit"
                        value="{{ $product->default_estimation_days_per_unit }}"
                        placeholder="0 untuk produk selain konsumsi"
                        class="w-full"
                    />
                    <span class="text-sm text-gray-700">Hari</span>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div>
                <x-elements.button
                    type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded"
                >
                    Simpan
                </x-elements.button>
            </div>
        </div>
    </form>
</x-layouts.modal>
