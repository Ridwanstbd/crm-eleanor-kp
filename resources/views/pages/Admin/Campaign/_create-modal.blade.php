<x-Layouts.Modal name="create-campaign" title="Tambah Kampanye" mode="create">
    <form method="POST" action="{{ route('campaigns.store') }}">
        @csrf

        <div class="p-4 space-y-4">
            <x-Elements.Form.Input
                name="name"
                id="name"
                value="{{ old('name') }}"
                placeholder="Nama Kampanye"
            />

            <x-Elements.Form.Input
                type="date"
                name="schedule"
                id="schedule"
                value="{{ old('schedule') }}"
                placeholder="Tanggal Jadwal"
            />
        </div>

        <div class="flex justify-end gap-2 px-4 pb-4">
            <button
                type="button"
                @click="$dispatch('close-modal', 'create-campaign')"
                class="px-4 py-2 text-sm bg-gray-300 rounded hover:bg-gray-400"
            >
                Batal
            </button>
            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </form>
</x-Layouts.Modal>
