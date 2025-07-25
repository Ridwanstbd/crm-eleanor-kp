<x-layouts.modal
    name="create-product"
    title="Tambah Produk"
    mode="create"
>
    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <div class="p-6 space-y-4">
            {{-- Nama Produk --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-900 mb-1">
                    Nama Produk
                </label>
                <x-elements.form.input
                    name="name"
                    placeholder="Susu Pengganti"
                    class="w-full"
                />
            </div>

            {{-- Jangka Waktu --}}
            <div>
                <label for="default_estimation_days_per_unit" class="block text-sm font-semibold text-gray-900 mb-1">
                    Jangka Waktu
                </label>
                <div class="flex items-center gap-2">
                    <x-elements.form.input
                        type="number"
                        name="default_estimation_days_per_unit"
                        placeholder="12"
                        class="w-full"
                    />
                    <span class="text-sm text-gray-700">Hari</span>
                </div>
            </div>

            {{-- Tombol --}}
            <div>
                <x-elements.button
                    type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded"
                >
                    Tambah
                </x-elements.button>
            </div>
        </div>
    </form>
</x-layouts.modal>
