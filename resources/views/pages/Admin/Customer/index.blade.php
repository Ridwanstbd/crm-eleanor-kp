<x-Layouts.AdminLayout title="Pelanggan">
    <x-Layouts.PageHeader title="Pelanggan">
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
    </x-Layouts.PageHeader>
    <x-Layouts.Table>
        <x-Molecules.Table.Header>
            <x-Atoms.Table.th>No</x-Atoms.Table.th>
            <x-Atoms.Table.th sortable :direction="$sortField === 'name' ? $sortDirection : null" onclick="window.location.href='{{ route('customers.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sortField === 'name' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'">
                Nama Pelanggan
            </x-Atoms.Table.th>
            <x-Atoms.Table.th sortable :direction="$sortField === 'phone' ? $sortDirection : null" onclick="window.location.href='{{ route('customers.index', array_merge(request()->query(), ['sort' => 'phone', 'direction' => ($sortField === 'phone' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'">
                Nomor Telepon
            </x-Atoms.Table.th>
            <x-Atoms.Table.th>Aksi</x-Atoms.Table.th>
        </x-Molecules.Table.Header>

        <x-Molecules.Table.Body>
            @forelse ($customers as $customer)
                <tr>
                    <x-Atoms.Table.td>{{ $loop->iteration }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>{{ $customer->name }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>{{ $customer->phone }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>
                        <x-Atoms.Button @click="$dispatch('open-modal', 'edit-customer-{{ $customer->id }}')" variant="secondary">Ubah</x-Atoms.Button>
                        <x-Atoms.Button @click="$dispatch('open-modal', 'delete-confirmation-{{ $customer->id }}')" variant="danger">Hapus</x-Atoms.Button>
                    </x-Atoms.Table.td>
                </tr>
                <x-Organisms.CustomerModal :customer="$customer" mode="edit" />
                <x-Layouts.Modal
                    name="delete-confirmation-{{ $customer->id }}"
                    title=""
                    maxWidth="sm"
                    :showIcon="false"
                >
                    <form action="{{ route('customers.destroy', $customer) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <x-Molecules.ConfirmationContent :id="$customer->id">
                            Yakin, Hapus {{$customer->name}}?
                        </x-Molecules.ConfirmationContent>
                    </form>
                </x-Layouts.Modal>
            @empty
                <x-Atoms.Table.empty colspan="4">
                    <p class="mt-1">Belum ada pelanggan.</p>
                </x-Atoms.Table.empty>
            @endforelse
        </x-Molecules.Table.Body>

        <x-slot name="pagination">
            <x-Molecules.Table.Pagination :paginator="$customers" />
        </x-slot>
    </x-Layouts.Table>
    <x-Organisms.CustomerModal />
</x-Layouts.AdminLayout>
