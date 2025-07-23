<x-Layouts.Modal name="edit-campaign-{{ $campaign->id }}" title="Ubah Kampanye" mode="edit">
    <form method="POST" action="{{ route('campaigns.update', $campaign) }}">
        @csrf
        @method('PUT')

        <div class="p-4 space-y-4">
            <x-Elements.Form.Input
                name="name"
                id="name-{{ $campaign->id }}"
                value="{{ $campaign->name }}"
                placeholder="Nama Kampanye"
            />

            <x-Elements.Form.Input
                type="date"
                name="schedule"
                id="schedule-{{ $campaign->id }}"
                value="{{ $campaign->schedule }}"
                placeholder="Tanggal Jadwal"
            />
        </div>

        <div class="flex justify-end gap-2 px-4 pb-4">
            <button
                type="button"
                @click="$dispatch('close-modal', 'edit-campaign-{{ $campaign->id }}')"
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
