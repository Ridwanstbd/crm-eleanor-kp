<x-Layouts.AdminLayout title="Kampanye">
    <x-Organisms.PageHeader title="Kampanye">
        <x-slot name="filters">
            <input type="hidden" name="sort" value="{{ $sortField }}">
            <input type="hidden" name="direction" value="{{ $sortDirection }}">

            <x-Atoms.Form.SearchInput
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Cari Kampanye..."
                    onchange="this.form.submit()"
                />
        </x-slot>
        <x-slot name="actions">
            <x-Atoms.Button>
                <x-Atoms.Link :href="route('campaigns.create')">
                    Tambah 
                </x-Atoms.Link>
            </x-Atoms.Button>
        </x-slot>
    </x-Organisms.PageHeader>
    <x-Layouts.Table>
        <x-Molecules.Table.Header>
            <x-Atoms.Table.th>No</x-Atoms.Table.th>
            <x-Atoms.Table.th sortable :direction="$sortField === 'name' ? $sortDirection : null" onclick="window.location.href='{{ route('campaigns.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sortField === 'name' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'">Nama Kampanye</x-Atoms.Table.th>
            <x-Atoms.Table.th sortable :direction="$sortField === 'schedule' ? $sortDirection : null" onclick="window.location.href='{{ route('campaigns.index', array_merge(request()->query(), ['sort' => 'schedule', 'direction' => ($sortField === 'schedule' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'">Jadwal</x-Atoms.Table.th>
            <x-Atoms.Table.th>Aksi</x-Atoms.Table.th>
        </x-Molecules.Table.Header>

        <x-Molecules.Table.Body>
           @forelse ($campaigns as $campaign)
            <tr>
                <x-Atoms.Table.td>{{ $loop->iteration }}</x-Atoms.Table.td>
                <x-Atoms.Table.td>{{ $campaign->name }}</x-Atoms.Table.td>
                <x-Atoms.Table.td>{{ \Carbon\Carbon::parse($campaign->schedule)->translatedFormat('d F Y') }}</x-Atoms.Table.td>
                <x-Atoms.Table.td>
                    <div class="flex gap-2">
                        {{-- Tombol Ubah --}}
                        <button class="px-3 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                            <x-Atoms.Link :href="route('campaigns.edit',$campaign->id)">Detail</x-Atoms.Link>
                        </button>

                        {{-- Tombol Hapus --}}
                        <button
                            @click="$dispatch('open-modal', 'delete-campaign-{{ $campaign->id }}')"
                            class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                            Hapus
                        </button>
                    </div>
                </x-Atoms.Table.td>
            </tr>
            {{-- Modal Hapus --}}
            @include('pages.Admin.Campaign._delete-modal', ['campaign' => $campaign])
            @empty
                <x-Atoms.Table.empty colspan="4">
                    <p class="mt-1 text-sm">Belum ada kampanye.</p>
                </x-Atoms.Table.empty>
            @endforelse
        </x-Molecules.Table.Body>

        <x-slot name="pagination">
            <x-Molecules.Table.Pagination :paginator="$campaigns" />
        </x-slot>
    </x-Layouts.Table>

</x-Layouts.AdminLayout>
