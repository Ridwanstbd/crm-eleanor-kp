<x-layouts.modal
    name="edit-customer-{{ $customer->id }}"
    title="Ubah Pelanggan"
    mode="edit"
    :showIcon="false"
>
    <form method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf
        @method('PUT')

        <div class="p-6 space-y-4">
            {{-- Nama Pelanggan --}}
            <div>
                <label for="name-{{ $customer->id }}" class="block text-sm font-semibold text-gray-900 mb-1">
                    Nama Pelanggan
                </label>
                <x-elements.form.input
                    id="name-{{ $customer->id }}"
                    name="name"
                    value="{{ $customer->name }}"
                    placeholder="Nama Pelanggan"
                    class="w-full"
                />
            </div>

            {{-- Nomor Telepon --}}
            <div>
                <label for="phone-{{ $customer->id }}" class="block text-sm font-semibold text-gray-900 mb-1">
                    Nomor Telepon
                </label>
                <x-elements.form.input
                    id="phone-{{ $customer->id }}"
                    name="phone"
                    value="{{ $customer->phone }}"
                    placeholder="081234567899"
                    class="w-full"
                />
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
