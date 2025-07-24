{{-- _edit-modal.blade.php --}}
<x-Layouts.Modal name="edit-customer-{{ $customer->id }}" title="Ubah Pelanggan" mode="edit">
    <form method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf
        @method('PUT')

        <div class="p-4 space-y-4">
            <x-Elements.Form.Input name="name" id="name-{{ $customer->id }}" value="{{ $customer->name }}" placeholder="Nama Pelanggan" />
            <x-Elements.Form.Input name="phone" id="phone-{{ $customer->id }}" value="{{ $customer->phone }}" placeholder="Nomor Telepon" />
        </div>

        <div class="flex justify-end gap-2 px-4 pb-4">
            <button type="button" @click="$dispatch('close-modal', 'edit-customer-{{ $customer->id }}')" class="px-4 py-2 text-sm bg-gray-300 rounded hover:bg-gray-400">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded hover:bg-indigo-700">
                Simpan
            </button>
        </div>
    </form>
</x-Layouts.Modal>
