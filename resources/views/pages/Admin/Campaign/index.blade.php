<x-Layouts.AdminLayout title="Kampanye">
    <x-Organisms.PageHeader title="Kampanye">
        <x-slot name="filters">
            <input type="hidden" name="sort" value="{{ $sortField }}">
            <input type="hidden" name="direction" value="{{ $sortDirection }}">

            <x-Elements.Form.SearchInput
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Cari Kampanye..."
                    onchange="this.form.submit()"
                />
        </x-slot>
        <x-slot name="actions">
            <x-Elements.Button>
                <x-Elements.Link :href="route('campaigns.create')">
                    Tambah 
                </x-Elements.Link>
            </x-Elements.Button>
        </x-slot>
    </x-Organisms.PageHeader>
    <x-Layouts.Table>
        <x-Fragments.Table.Header>
            <x-Elements.Table.th>No</x-Elements.Table.th>
            <x-Elements.Table.th sortable :direction="$sortField === 'name' ? $sortDirection : null" onclick="window.location.href='{{ route('campaigns.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sortField === 'name' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'">Nama Kampanye</x-Elements.Table.th>
            <x-Elements.Table.th sortable :direction="$sortField === 'schedule' ? $sortDirection : null" onclick="window.location.href='{{ route('campaigns.index', array_merge(request()->query(), ['sort' => 'schedule', 'direction' => ($sortField === 'schedule' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'">Jadwal</x-Elements.Table.th>
            <x-Elements.Table.th>Aksi</x-Elements.Table.th>
        </x-Fragments.Table.Header>

        <x-Fragments.Table.Body>
           @forelse ($campaigns as $campaign)
            <tr>
                <x-Elements.Table.td>{{ $loop->iteration }}</x-Elements.Table.td>
                <x-Elements.Table.td>{{ $campaign->name }}</x-Elements.Table.td>
                <x-Elements.Table.td>{{ \Carbon\Carbon::parse($campaign->schedule)->translatedFormat('d F Y') }}</x-Elements.Table.td>
                <x-Elements.Table.td>
                    <div class="flex gap-2">
                        {{-- Tombol Ubah --}}
                        <button class="px-3 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                            <x-Elements.Link :href="route('campaigns.edit',$campaign->id)">Detail</x-Elements.Link>
                        </button>

                        {{-- Tombol Hapus --}}
                        <button
                            @click="$dispatch('open-modal', 'delete-campaign-{{ $campaign->id }}')"
                            class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                            Hapus
                        </button>
                    </div>
                </x-Elements.Table.td>
            </tr>
            {{-- Modal Hapus --}}
            @include('pages.Admin.Campaign._delete-modal', ['campaign' => $campaign])
            @empty
                <x-Elements.Table.empty colspan="4">
                    <p class="mt-1 text-sm">Belum ada kampanye.</p>
                </x-Elements.Table.empty>
            @endforelse
        </x-Fragments.Table.Body>

        <x-slot name="pagination">
            <x-Fragments.Table.Pagination :paginator="$campaigns" />
        </x-slot>
    </x-Layouts.Table>

</x-Layouts.AdminLayout>
