<x-layouts.modal
    name="create-customer"
    title="Tambah Pelanggan"
    mode="create"
    :showIcon="false"
>
    <form method="POST" action="{{ route('customers.store') }}">
        @csrf

        <div class="p-6 space-y-4">
            {{-- Nama Pelanggan --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-900 mb-1">
                    Nama Pelanggan
                </label>
                <x-elements.form.input
                    name="name"
                    placeholder="Nama Pelanggan"
                    class="w-full"
                />
            </div>

            {{-- Nomor Telepon --}}
            <div>
                <label for="phone" class="block text-sm font-semibold text-gray-900 mb-1">
                    Nomor Telepon
                </label>
                <x-elements.form.input
                    name="phone"
                    placeholder="081234567899"
                    class="w-full"
                />
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
