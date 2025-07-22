<x-Layouts.Modal name="create-customer" title="Tambah Pelanggan" mode="create">
    <form method="POST" action="{{ route('customers.store') }}" class="space-y-4 p-4">
        @csrf

        <x-Elements.Form.Input name="name" id="name" placeholder="Nama Pelanggan" />
        <x-Elements.Form.Input name="phone" id="phone" placeholder="Nomor Telepon" />

        <div class="flex justify-end gap-2 pt-4">
            <button type="button" @click="$dispatch('close-modal', 'create-customer')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700">
                Simpan
            </button>
        </div>
    </form>
</x-Layouts.Modal>
