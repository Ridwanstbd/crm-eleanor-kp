<x-Layouts.AdminLayout title="Pelanggan">
    <x-Organisms.PageHeader title="Pelanggan">
        <x-slot name="filters">
            <x-Atoms.Form.SearchInput
                name="search"
                id="search"
                value="{{ request('search') }}"
                placeholder="Cari nama pelanggan..."
                onchange="this.form.submit()"
            />
        </x-slot>
        <x-slot name="actions">
            <x-Atoms.Button @click="$dispatch('open-modal', 'create-customer')" >
                <x-Atoms.Link href="#" >
                    Tambah
                </x-Atoms.Link>
            </x-Atoms.Button>
        </x-slot>
    </x-Organisms.PageHeader>
    <x-Layouts.Table>
        <x-Fragments.Table.Header>
            <x-Atoms.Table.th>No</x-Atoms.Table.th>
            <x-Atoms.Table.th sortable :direction="$sortField === 'name' ? $sortDirection : null" onclick="window.location.href='{{ route('customers.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sortField === 'name' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'">
                Nama Pelanggan
            </x-Atoms.Table.th>
            <x-Atoms.Table.th sortable :direction="$sortField === 'phone' ? $sortDirection : null" onclick="window.location.href='{{ route('customers.index', array_merge(request()->query(), ['sort' => 'phone', 'direction' => ($sortField === 'phone' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'">
                Nomor Telepon
            </x-Atoms.Table.th>
            <x-Atoms.Table.th>Aksi</x-Atoms.Table.th>
        </x-Fragments.Table.Header>

        <x-Fragments.Table.Body>
            @forelse ($customers as $customer)
                <tr>
                    <x-Atoms.Table.td>{{ $loop->iteration }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>{{ $customer->name }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>{{ $customer->phone }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>
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
                    </x-Atoms.Table.td>
                </tr>

                {{-- Include modals --}}
                @include('pages.Admin.Customer._edit-modal', ['customer' => $customer])
                @include('pages.Admin.Customer._delete-modal', ['customer' => $customer])
            @empty
                <x-Atoms.Table.empty colspan="4">
                    <p class="mt-1">Belum ada pelanggan.</p>
                </x-Atoms.Table.empty>
            @endforelse
        </x-Fragments.Table.Body>

        <x-slot name="pagination">
            <x-Fragments.Table.Pagination :paginator="$customers" />
        </x-slot>
    </x-Layouts.Table>

    {{-- Modal Tambah Pelanggan --}}
    @include('pages.Admin.Customer._create-modal')
</x-Layouts.AdminLayout>
