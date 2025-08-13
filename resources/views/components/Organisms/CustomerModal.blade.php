@props(['customer' => null, 'mode'=>'create'])
<x-Layouts.Modal
    name="edit-customer-{{ $customer->id }}"
    title="Ubah Pelanggan"
    mode="edit"
    maxWidth="md"
    :showIcon="false"
>
    <form method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf
        @method('PUT')

        <x-Molecules.Form.FormGroup for="edit-customer-name-{{ $customer->id }}" label="Nama Pelanggan">
            <x-Atoms.form.input
                id="edit-customer-name-{{ $customer->id }}"
                name="name"
                value="{{ $customer->name }}"
                placeholder="Nama Pelanggan"
                class="w-full"
            />
        </x-Molecules.Form.FormGroup>
        <x-Molecules.Form.FormGroup for="edit-customer-phone-{{ $customer->id }}" label="Nomor Telepon" >
            <x-Atoms.form.input
                id="edit-customer-phone-{{ $customer->id }}"
                name="phone"
                value="{{ $customer->phone }}"
                placeholder="6281234567899"
                class="w-full"
            />
        </x-Molecules.Form.FormGroup>
        <x-Atoms.button variant="submit" fullWidth>
            Simpan
        </x-Atoms.button>
    </form>
</x-Layouts.Modal>
@if ($mode === 'edit' && $customer)
@elseif($mode === 'create')
<x-Layouts.Modal
    name="create-customer"
    title="Tambah Pelanggan"
    mode="create"
    maxWidth="md"
    :showIcon="false"
>
    <form method="POST" action="{{ route('customers.store') }}">
        @csrf

        <x-Molecules.Form.FormGroup for="create-customer-name" label="Nama Pelanggan">
            <x-Atoms.form.input
                id="create-customer-name"
                name="name"
                placeholder="Nama Pelanggan"
                class="w-full"
            />
        </x-Molecules.Form.FormGroup>
        <x-Molecules.Form.FormGroup for="create-customer-phone" label="Nomor Telepon">
            <x-Atoms.form.input
                id="create-customer-phone"
                name="phone"
                placeholder="6281234567899"
                class="w-full"
            />
        </x-Molecules.Form.FormGroup>
        <x-Atoms.button variant="submit" fullWidth>
            Tambah
        </x-Atoms.button>
    </form>
</x-Layouts.Modal>
@endif