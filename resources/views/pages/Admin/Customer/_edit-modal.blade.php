<x-layouts.modal
    name="edit-customer-{{ $customer->id }}"
    title="Ubah Pelanggan"
    mode="default"
    :showIcon="false"
>
    <div class="p-6"> {{-- Tambahkan padding agar isi modal tidak rapat ke batas --}}
        <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <x-Fragments.Form.FormGroup label="Nama Pelanggan" for="name">
                <x-elements.form.input
                    name="name"
                    id="name-{{ $customer->id }}"
                    value="{{ $customer->name }}"
                    placeholder="Nama Pelanggan"
                />
            </x-Fragments.Form.FormGroup>

            {{-- Nomor --}}
            <x-Fragments.Form.FormGroup label="Nomor" for="phone">
                <x-elements.form.input
                    name="phone"
                    id="phone-{{ $customer->id }}"
                    value="{{ $customer->phone }}"
                    placeholder="081234567899"
                />
            </x-Fragments.Form.FormGroup>

            {{-- Tombol --}}
            <div>
                <x-elements.button
                    type="submit"
                    variant="danger"
                    class="w-full justify-center text-base font-semibold rounded-md"
                >
                    Simpan
                </x-elements.button>
            </div>
        </form>
    </div>
</x-layouts.modal>
