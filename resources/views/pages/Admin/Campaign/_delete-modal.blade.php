<x-Layouts.Modal name="delete-campaign-{{ $campaign->id }}" title="Hapus Kampanye" mode="destroy" message="Apakah Anda yakin ingin menghapus kampanye ini?">
    <form method="POST" action="{{ route('campaigns.destroy', $campaign) }}">
        @csrf
        @method('DELETE')

        <div class="flex justify-end gap-2 px-4 pb-4">
            <button
                type="button"
                @click="$dispatch('close-modal', 'delete-campaign-{{ $campaign->id }}')"
                class="px-4 py-2 text-sm bg-gray-300 rounded hover:bg-gray-400"
            >
                Batal
            </button>
            <button type="submit" class="px-4 py-2 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                Hapus
            </button>
        </div>
    </form>
</x-Layouts.Modal>
