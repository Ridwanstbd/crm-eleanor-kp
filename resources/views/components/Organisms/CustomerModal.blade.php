@props(['customer' => null, 'mode'=>'create'])
@if ($mode === 'edit' && $customer)
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
            <x-Atoms.Form.Input
                id="edit-customer-name-{{ $customer->id }}"
                name="name"
                value="{{ $customer->name }}"
                placeholder="Nama Pelanggan"
                class="w-full"
            />
        </x-Molecules.Form.FormGroup>
        <x-Molecules.Form.FormGroup for="edit-customer-phone-{{ $customer->id }}" label="Nomor Telepon" >
            <x-Atoms.Form.Input
                id="edit-customer-phone-{{ $customer->id }}"
                name="phone"
                value="{{ $customer->phone }}"
                placeholder="6281234567899"
                class="w-full"
            />
        </x-Molecules.Form.FormGroup>
        <x-Atoms.Button variant="submit" fullWidth>
            Simpan
        </x-Atoms.Button>
    </form>
</x-Layouts.Modal>
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
            <x-Atoms.Form.Input
                id="create-customer-name"
                name="name"
                placeholder="Nama Pelanggan"
                class="w-full"
            />
        </x-Molecules.Form.FormGroup>
        <x-Molecules.Form.FormGroup for="create-customer-phone" label="Nomor Telepon">
            <x-Atoms.Form.Input
                id="create-customer-phone"
                name="phone"
                placeholder="6281234567899"
                class="w-full"
            />
        </x-Molecules.Form.FormGroup>
        <x-Atoms.Button variant="submit" fullWidth>
            Tambah
        </x-Atoms.Button>
    </form>
</x-Layouts.Modal>
@endif