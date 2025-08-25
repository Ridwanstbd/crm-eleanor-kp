<x-Layouts.AdminLayout :title="'Grup: '.$group->name">
    <x-Layouts.PageHeader :title="'Grup: '.$group->name">
        <x-slot name="filters">
            <form method="GET" action="{{ route('customer-groups.show', $group) }}">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $dir }}">

                <x-Atoms.Form.SearchInput
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nomor telepon..."
                    onchange="this.form.submit()"
                />
            </form>
        </x-slot>
        <x-slot name="actions">
            <x-Atoms.Button
                as="a"
                :href="$backUrl"
                variant="primary"
            >Kembali ke Daftar</x-Atoms.Button>
        </x-slot>

    </x-Layouts.PageHeader>

    <x-Layouts.Table>
        <x-Molecules.Table.Header>
            <x-Atoms.Table.th class="w-16">No</x-Atoms.Table.th>

            <x-Atoms.Table.th
                sortable
                :direction="$sort === 'name' ? $dir : null"
                onclick="window.location='{{ route('customer-groups.show', array_merge(['group'=>$group->id], request()->query(), ['sort'=>'name','direction'=>($sort==='name' && $dir==='asc')?'desc':'asc'])) }}'">
                Nama Pelanggan
            </x-Atoms.Table.th>

            <x-Atoms.Table.th
                sortable
                :direction="$sort === 'phone' ? $dir : null"
                onclick="window.location='{{ route('customer-groups.show', array_merge(['group'=>$group->id], request()->query(), ['sort'=>'phone','direction'=>($sort==='phone' && $dir==='asc')?'desc':'asc'])) }}'">
                Nomor Telepon
            </x-Atoms.Table.th>

            <x-Atoms.Table.th class="w-40 text-center">Aksi</x-Atoms.Table.th>
        </x-Molecules.Table.Header>

        <x-Molecules.Table.Body>
            @forelse($customers as $i => $customer)
                <tr>
                    <x-Atoms.Table.td>{{ $customers->firstItem() + $i }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>{{ $customer->name }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>{{ $customer->phone }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>
                        <div class="flex items-center justify-center gap-2">
                            <x-Atoms.Button variant="secondary"
                                @click="$dispatch('open-modal', 'edit-customer-{{ $customer->id }}')">
                                Ubah
                            </x-Atoms.Button>
                            <x-Atoms.Button variant="danger"
                                @click="$dispatch('open-modal', 'delete-confirmation-{{ $customer->id }}')">
                                Hapus
                            </x-Atoms.Button>
                        </div>
                    </x-Atoms.Table.td>
                </tr>

                <x-Organisms.CustomerModal :customer="$customer" mode="edit" />

                <x-Layouts.Modal name="delete-confirmation-{{ $customer->id }}" title="" maxWidth="sm" :showIcon="false">
                    <form action="{{ route('customers.destroy', $customer) }}" method="POST">
                        @csrf @method('DELETE')
                        <x-Molecules.ConfirmationContent :id="$customer->id">
                            Yakin, Hapus {{ $customer->name }}?
                        </x-Molecules.ConfirmationContent>
                    </form>
                </x-Layouts.Modal>
            @empty
                <x-Atoms.Table.empty colspan="4">
                    <p class="mt-1 text-sm">Tidak ada pelanggan di grup ini.</p>
                </x-Atoms.Table.empty>
            @endforelse
        </x-Molecules.Table.Body>

        <x-slot name="pagination">
            {{ $customers->links() }}
        </x-slot>
    </x-Layouts.Table>
    <x-Organisms.CustomerModal />
</x-Layouts.AdminLayout>