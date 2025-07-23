<x-Layouts.Modal name="create-message-template" title="Tambah Template Pesan" mode="create">
    <form method="POST" action="{{ route('templates.store') }}">
        @csrf

        <div class="p-4 space-y-4">
            <x-Elements.Form.Input name="name" id="name" placeholder="Nama Template Pesan" />
        </div>

        <div class="flex justify-end gap-2 px-4 pb-4">
            <button type="button" @click="$dispatch('close-modal', 'create-message-template')" class="px-4 py-2 text-sm bg-gray-300 rounded hover:bg-gray-400">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </form>
</x-Layouts.Modal>
