<x-Layouts.AdminLayout title="Pelanggan">
    <div class="">
        {{-- Header --}}
        <header class="flex items-center justify-between py-4">
            <h2 class="text-xl font-semibold">Pelanggan</h2>

            {{-- Tombol Tambah (trigger modal) --}}
            <button
                @click="$dispatch('open-modal', 'create-customer')"
                class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
            >
                Tambah
            </button>
        </header>

        {{-- Search --}}
        <div class="mb-4">
            <form method="GET" class="flex gap-2">
                <x-Elements.Form.Input
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama pelanggan..."
                />
                <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                    Cari
                </button>
            </form>
        </div>

        {{-- Tabel --}}
        <x-Layouts.Table>
            <x-Fragments.Table.Header>
                <x-Elements.Table.th>No</x-Elements.Table.th>
                <x-Elements.Table.th>Nama</x-Elements.Table.th>
                <x-Elements.Table.th>Nomor</x-Elements.Table.th>
                <x-Elements.Table.th>Pesan Terakhir</x-Elements.Table.th>
                <x-Elements.Table.th>Aksi</x-Elements.Table.th>
            </x-Fragments.Table.Header>

            <x-Fragments.Table.Body>
               @forelse ($customers as $customer)
                <tr>
                    <x-Elements.Table.td>{{ $loop->iteration }}</x-Elements.Table.td>
                    <x-Elements.Table.td>{{ $customer->name }}</x-Elements.Table.td>
                    <x-Elements.Table.td>{{ $customer->phone }}</x-Elements.Table.td>
                    <x-Elements.Table.td>
                        {{ $customer->last_time_message
                            ? \Carbon\Carbon::parse($customer->last_time_message)->translatedFormat('d F Y')
                            : '-' }}
                    </x-Elements.Table.td>
                    <x-Elements.Table.td>
                        <div class="flex gap-2">
                            {{-- Tombol Ubah --}}
                            <button
                                @click="$dispatch('open-modal', 'edit-customer-{{ $customer->id }}')"
                                class="px-3 py-1 text-sm text-white bg-yellow-500 rounded hover:bg-yellow-600">
                                Ubah
                            </button>

                            {{-- Tombol Hapus --}}
                            <button
                                @click="$dispatch('open-modal', 'delete-customer-{{ $customer->id }}')"
                                class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                                Hapus
                            </button>
                        </div>
                    </x-Elements.Table.td>
                </tr>

                {{-- Modal Edit --}}
                @include('pages.Admin.Customer._edit-modal', ['customer' => $customer])

                {{-- Modal Hapus --}}
                @include('pages.Admin.Customer._delete-modal', ['customer' => $customer])
                @empty
                    <x-Elements.Table.empty colspan="5">
                        <p class="mt-1 text-sm">Belum ada pelanggan.</p>
                    </x-Elements.Table.empty>
                @endforelse
            </x-Fragments.Table.Body>

            <x-slot name="pagination">
                <x-Fragments.Table.Pagination :paginator="$customers" />
            </x-slot>
        </x-Layouts.Table>
    </div>

    {{-- Modal Tambah --}}
    @include('pages.Admin.Customer._create-modal')
</x-Layouts.AdminLayout>
