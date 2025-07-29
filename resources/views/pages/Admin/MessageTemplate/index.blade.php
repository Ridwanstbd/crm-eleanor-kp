<x-Layouts.AdminLayout title="Template Pesan">
    <div>
        <header class="flex items-center justify-between py-3">
            <h2 class="text-xl font-semibold">Template Pesan</h2>

            <div class="flex gap-2">
                <input type="hidden" name="sort" value="{{ $sortField }}">
                <input type="hidden" name="direction" value="{{ $sortDirection }}">

                <x-Elements.Form.SearchInput
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama template..."
                    onchange="this.form.submit()"
                />

                <x-Elements.Button @click="$dispatch('open-modal', 'create-message-template')">
                    <x-Elements.Link href="#">Tambah</x-Elements.Link>
                </x-Elements.Button>
            </div>
        </header>

        <x-Layouts.Table>
            <x-Fragments.Table.Header>
                <x-Elements.Table.th>No</x-Elements.Table.th>
                <x-Elements.Table.th sortable
                    :direction="$sortField === 'title' ? $sortDirection : null"
                    onclick="window.location.href='{{ route('templates.index', array_merge(request()->query(), ['sort' => 'title', 'direction' => ($sortField === 'title' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'"
                >
                    Nama
                </x-Elements.Table.th>
                <x-Elements.Table.th>Aksi</x-Elements.Table.th>
            </x-Fragments.Table.Header>

            <x-Fragments.Table.Body>
                @forelse ($templates as $template)
                    <tr>
                        <x-Elements.Table.td>{{ $loop->iteration }}</x-Elements.Table.td>
                        <x-Elements.Table.td>{{ $template->title }}</x-Elements.Table.td>
                        <x-Elements.Table.td>
                            <div class="flex gap-2">
                                <button
                                    @click="$dispatch('open-modal', 'edit-message-template-{{ $template->id }}')"
                                    class="px-3 py-1 text-sm text-white bg-yellow-500 rounded hover:bg-yellow-600">
                                    Ubah
                                </button>

                                <button
                                    @click="$dispatch('open-modal', 'delete-message-template-{{ $template->id }}')"
                                    class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                                    Hapus
                                </button>
                            </div>
                        </x-Elements.Table.td>
                    </tr>

                    {{-- Modals --}}
                    @include('pages.Admin.MessageTemplate._edit-modal', ['template' => $template])
                    @include('pages.Admin.MessageTemplate._delete-modal', ['template' => $template])
                @empty
                    <x-Elements.Table.empty colspan="3">
                        <p class="mt-1 text-sm">Belum ada template pesan.</p>
                    </x-Elements.Table.empty>
                @endforelse
            </x-Fragments.Table.Body>

            <x-slot name="pagination">
                <x-Fragments.Table.Pagination :paginator="$templates" />
            </x-slot>
        </x-Layouts.Table>
    </div>

    {{-- Modal Tambah --}}
    @include('pages.Admin.MessageTemplate._create-modal')
</x-Layouts.AdminLayout>
