<x-layouts.modal
    name="create-product"
    title="Tambah Produk"
    mode="create"
    maxWidth="md"
    :showIcon="false"
>
    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <div class="space-y-4">
            {{-- Nama Produk --}}
            <div>
                <label for="create-name" class="block text-sm font-semibold text-gray-900 mb-1">
                    Nama Produk
                </label>
                <x-Atoms.form.input
                    id="create-name"
                    name="name"
                    placeholder="Susu Pengganti"
                    class="w-full"
                />
            </div>

            {{-- Jangka Waktu --}}
            <div>
                <label for="create-default_estimation_days_per_unit" class="block text-sm font-semibold text-gray-900 mb-1">
                    Jangka Waktu
                </label>
                <div class="flex items-center gap-2">
                    <x-Atoms.form.input
                        type="number"
                        id="create-default_estimation_days_per_unit"
                        name="default_estimation_days_per_unit"
                        placeholder="0 untuk produk selain konsumsi"
                        class="w-full"
                    />
                    <span class="text-sm text-gray-700">Hari</span>
                </div>
            </div>

            {{-- Tombol --}}
            <div>
                <x-Atoms.button
                    type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded"
                >
                    Tambah
                </x-Atoms.button>
            </div>
        </div>
    </form>
</x-layouts.modal>
