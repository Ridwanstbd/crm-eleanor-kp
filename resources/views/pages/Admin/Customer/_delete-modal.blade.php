<x-layouts.modal
    name="delete-customer-{{ $customer->id }}"
    title=""
    mode="default"
    :showIcon="false"
>
    <form action="{{ route('customers.destroy', $customer) }}" method="POST">
        @csrf
        @method('DELETE')

        <div class="p-6 text-center space-y-6">
            {{-- Ikon besar --}}
            <div class="flex justify-center">
                <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4
                            c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            {{-- Pesan utama --}}
            <h2 class="text-lg font-bold text-gray-800">Yakin, Hapus Pelanggan?</h2>

            {{-- Tombol aksi --}}
            <div class="flex justify-center gap-4">
                <button type="button"
                    @click="$dispatch('close-modal', 'delete-customer-{{ $customer->id }}')"
                    class="px-6 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                    Batal
                </button>
                <button type="submit"
                    class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Hapus
                </button>
            </div>
        </div>
    </form>
</x-layouts.modal>
