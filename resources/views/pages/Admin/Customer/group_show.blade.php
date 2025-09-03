<x-Templates.IndexTemplate
    :title="'Grup: '.$group->name"
    :paginator="$customers"
>
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

    <x-slot name="tableHeader">
        <x-Atoms.Table.th >No</x-Atoms.Table.th>
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
        <x-Atoms.Table.th >Aksi</x-Atoms.Table.th>
    </x-slot>

    @forelse($customers as $i => $customer)
        <tr>
            <x-Atoms.Table.td>{{ $customers->firstItem() + $i }}</x-Atoms.Table.td>
            <x-Atoms.Table.td>{{ $customer->name }}</x-Atoms.Table.td>
            <x-Atoms.Table.td>{{ $customer->phone }}</x-Atoms.Table.td>
            <x-Atoms.Table.td>
                <x-Atoms.Button variant="secondary" @click="$dispatch('open-modal', 'edit-customer-{{ $customer->id }}')">
                    Ubah
                </x-Atoms.Button>
                <x-Atoms.Button variant="danger" @click="$dispatch('open-modal', 'delete-confirmation-{{ $customer->id }}')">
                    Hapus
                </x-Atoms.Button>
            </x-Atoms.Table.td>
        </tr>
        <x-Organisms.CrudModal
            title="Ubah Pelanggan"
            mode="edit"
            :name="'edit-customer-' . $customer->id"
            :action="route('customers.update', $customer)"
        >        
            <x-Molecules.Form.FormGroup for="edit-customer-name-{{ $customer->id }}" label="Nama Pelanggan">
                <x-Atoms.Form.Input
                    id="edit-customer-name-{{ $customer->id }}"
                    name="name"
                    value="{{ $customer->name }}"
                    placeholder="Nama Pelanggan"
                    class="w-full"
                />
            </x-Molecules.Form.FormGroup>
            <x-Molecules.Form.FormGroup for="edit-customer-phone-{{ $customer->id }}" label="Nomor Telepon" >
                <x-Atoms.Form.Input
                    id="edit-customer-phone-{{ $customer->id }}"
                    name="phone"
                    value="{{ $customer->phone }}"
                    placeholder="6281234567899"
                    class="w-full"
                />
            </x-Molecules.Form.FormGroup>
        </x-Organisms.CrudModal>
        <x-Layouts.Modal name="delete-confirmation-{{ $customer->id }}">
            <form action="{{ route('customers.destroy', ['customer' => $customer, 'group' => $group]) }}" method="POST">
                @csrf @method('DELETE')
                <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
                <x-Molecules.ConfirmationContent :id="$customer->id">
                    Yakin, Hapus {{ $customer->name }} dari grup "{{ $group->name }}"?
                    @if($customer->groups()->count() > 1)
                        <div class="mt-2 text-xs text-gray-600">
                            <em>Customer ini akan dihapus dari grup "{{ $group->name }}" saja karena masih tergabung di {{ $customer->groups()->count() - 1 }} grup lainnya.</em>
                        </div>
                    @else
                        <div class="mt-2 text-xs text-red-600">
                            <em><strong>Peringatan:</strong> Customer ini hanya ada di grup ini, sehingga akan dihapus sepenuhnya dari sistem.</em>
                        </div>
                    @endif
                </x-Molecules.ConfirmationContent>
            </form>
        </x-Layouts.Modal>
    @empty
        <x-Atoms.Table.empty colspan="4">
            <p class="mt-1 text-sm">Tidak ada pelanggan di grup ini.</p>
        </x-Atoms.Table.empty>
    @endforelse

</x-Templates.IndexTemplate>
