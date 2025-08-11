<x-layouts.modal
    name="create-customer"
    title="Tambah Pelanggan"
    mode="create"
    maxWidth="md"
    :showIcon="false"
>
    <form method="POST" action="{{ route('customers.store') }}">
        @csrf

        <div class="space-y-4">
            {{-- Nama Pelanggan --}}
            <div>
                <label for="create-customer-name" class="block text-sm font-semibold text-gray-900 mb-1">
                    Nama Pelanggan
                </label>
                <x-Atoms.form.input
                    id="create-customer-name"
                    name="name"
                    placeholder="Nama Pelanggan"
                    class="w-full"
                />
            </div>

            {{-- Nomor Telepon --}}
            <div>
                <label for="create-customer-phone" class="block text-sm font-semibold text-gray-900 mb-1">
                    Nomor Telepon
                </label>
                <x-Atoms.form.input
                    id="create-customer-phone"
                    name="phone"
                    placeholder="081234567899"
                    class="w-full"
                />
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
