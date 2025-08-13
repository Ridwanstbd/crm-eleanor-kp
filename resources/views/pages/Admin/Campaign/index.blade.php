<x-Layouts.AdminLayout title="Kampanye">
    <x-Layouts.PageHeader title="Kampanye">
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
    </x-Layouts.PageHeader>
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
                    <x-Atoms.Button variant="info">
                        <x-Atoms.Link :href="route('campaigns.edit',$campaign->id)">Detail</x-Atoms.Link>
                    </x-Atoms.Button>
                    <x-Atoms.Button @click="$dispatch('open-modal', 'delete-confirmation-{{ $campaign->id }}')" variant="danger">Hapus</x-Atoms.Button>
                </x-Atoms.Table.td>
            </tr>
            <x-Layouts.Modal
                    name="delete-confirmation-{{ $campaign->id }}"
                    title=""
                    maxWidth="sm"
                    :showIcon="false"
                >
                    <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <x-Molecules.ConfirmationContent :id="$campaign->id">
                            Yakin, Hapus {{$campaign->name}}?
                        </x-Molecules.ConfirmationContent>
                    </form>
                </x-Layouts.Modal>
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
