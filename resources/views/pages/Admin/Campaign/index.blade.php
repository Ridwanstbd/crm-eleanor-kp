<x-Templates.IndexTemplate
    title="Kampanye"
    :paginator="$campaigns"
>
    <x-slot name="filters">
        <input type="hidden" name="sort" value="{{ $sortField }}">
        <input type="hidden" name="direction" value="{{ $sortDirection }}">

        <x-Atoms.Form.SearchInput
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari Kampanye..."
            onchange="this.form.submit()"
        />
    </x-slot>

    <x-slot name="actions">
        <x-Atoms.Button href="{{ route('campaigns.create') }}">
            Tambah
        </x-Atoms.Button>
    </x-slot>

    <x-slot name="tableHeader">
        <x-Atoms.Table.th>No</x-Atoms.Table.th>
        <x-Atoms.Table.th sortable :direction="$sortField === 'name' ? $sortDirection : null" onclick="window.location.href='{{ route('campaigns.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sortField === 'name' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'">Nama Kampanye</x-Atoms.Table.th>
        <x-Atoms.Table.th sortable :direction="$sortField === 'created_at' ? $sortDirection : null" onclick="window.location.href='{{ route('campaigns.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => ($sortField === 'created_at' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'">Waktu dibuat</x-Atoms.Table.th>
        <x-Atoms.Table.th>Aksi</x-Atoms.Table.th>
    </x-slot>

    @forelse ($campaigns as $campaign)
        <tr>
            <x-Atoms.Table.td>{{ $loop->iteration }}</x-Atoms.Table.td>
            <x-Atoms.Table.td>{{ $campaign->name }}</x-Atoms.Table.td>
            <x-Atoms.Table.td>{{ \Carbon\Carbon::parse($campaign->created_at)->translatedFormat('d F Y') }}</x-Atoms.Table.td>
            <x-Atoms.Table.td>
                <x-Atoms.Button variant="info" href="{{ route('campaigns.edit', $campaign->id) }}">
                    Detail
                </x-Atoms.Button>
                <x-Atoms.Button @click="$dispatch('open-modal', 'delete-confirmation-{{ $campaign->id }}')" variant="danger">Hapus</x-Atoms.Button>
            </x-Atoms.Table.td>
        </tr>
        <x-Layouts.Modal
            name="delete-confirmation-{{ $campaign->id }}"
            title="Konfirmasi Hapus"
            maxWidth="sm"
        >
            <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST">
                @csrf
                @method('DELETE')
                <x-Molecules.ConfirmationContent :id="$campaign->id">
                    Yakin, Hapus {{ $campaign->name }}?
                </x-Molecules.ConfirmationContent>
            </form>
        </x-Layouts.Modal>
    @empty
        <x-Atoms.Table.empty colspan="4">
            <p class="mt-1 text-sm">Belum ada kampanye.</p>
        </x-Atoms.Table.empty>
    @endforelse
</x-Templates.IndexTemplate>