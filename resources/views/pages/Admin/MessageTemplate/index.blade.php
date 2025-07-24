<x-Layouts.AdminLayout title="Template Pesan">
        {{-- Header --}}
        <header class="flex items-center justify-between py-4">
            <h2 class="text-xl font-semibold">Template Pesan</h2>
            <a href="#" @click="$dispatch('open-modal', 'create-message-template')" class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                Tambah Template
            </a>
        </header>

        {{-- Search --}}
        <div class="mb-4">
            <form method="GET" class="flex gap-2">
                <x-Elements.Form.Input
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama template pesan..."
                />
                <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                    Cari
                </button>
            </form>


        {{-- Tabel --}}
        <x-Layouts.Table>
            <x-Fragments.Table.Header>
                <x-Elements.Table.th>No</x-Elements.Table.th>
                <x-Elements.Table.th>Nama Template</x-Elements.Table.th>
                <x-Elements.Table.th>Aksi</x-Elements.Table.th>
            </x-Fragments.Table.Header>

            <x-Fragments.Table.Body>
                @forelse ($templates as $template)
                    <tr>
                        <x-Elements.Table.td>{{ $loop->iteration }}</x-Elements.Table.td>
                        <x-Elements.Table.td>{{ $template->name }}</x-Elements.Table.td>
                        <x-Elements.Table.td>
                            <div class="flex gap-2">
                                {{-- Tombol Ubah --}}
                                <button
                                    @click="$dispatch('open-modal', 'edit-message-template-{{ $template->id }}')"
                                    class="px-3 py-1 text-sm text-white bg-yellow-500 rounded hover:bg-yellow-600"
                                >
                                    Ubah
                                </button>

                                {{-- Tombol Hapus --}}
                                <button
                                    @click="$dispatch('open-modal', 'delete-message-template-{{ $template->id }}')"
                                    class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700"
                                >
                                    Hapus
                                </button>
                            </div>
                        </x-Elements.Table.td>
                    </tr>

                    {{-- Modal Edit --}}
                    @include('pages.Admin.MessageTemplate._edit-modal', ['template' => $template])

                    {{-- Modal Hapus --}}
                    @include('pages.Admin.MessageTemplate._delete-modal', ['template' => $template])
                @empty
                    <x-Elements.Table.empty colspan="3">
                        <p class="mt-1">Belum ada template pesan.</p>
                    </x-Elements.Table.empty>
                @endforelse
            </x-Fragments.Table.Body>

            <x-slot name="pagination">
                {{-- Pagination jika diperlukan --}}
            </x-slot>
        </x-Layouts.Table>
    </div>

    {{-- Modal Tambah Template --}}
    @include('pages.Admin.MessageTemplate._create-modal')
</x-Layouts.AdminLayout>
