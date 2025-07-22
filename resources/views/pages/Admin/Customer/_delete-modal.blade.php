<x-Layouts.Modal name="delete-customer-{{ $customer->id }}" title="Hapus Pelanggan" mode="destroy">
    <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="p-4">
        @csrf
        @method('DELETE')

        <p class="mb-4 text-sm text-gray-600">
            Apakah Anda yakin ingin menghapus pelanggan <strong>{{ $customer->name }}</strong>?
        </p>

        <div class="flex justify-end gap-2">
            <button type="button" @click="$dispatch('close-modal', 'delete-customer-{{ $customer->id }}')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">
                Hapus
            </button>
        </div>
    </form>
</x-Layouts.Modal>
