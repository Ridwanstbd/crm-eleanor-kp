<x-Layouts.AdminLayout title="Produk">
    <x-Layouts.PageHeader title="Produk">
        <x-slot name="filters">
            <input type="hidden" name="sort" value="{{ $sortField }}">
            <input type="hidden" name="direction" value="{{ $sortDirection }}">

            <x-Atoms.Form.SearchInput
                name="search"
                id="search"
                value="{{ request('search') }}"
                placeholder="Cari nama produk..."
                onchange="this.form.submit()"
            />
        </x-slot>
        <x-slot name="actions">
            <x-Atoms.Button @click="$dispatch('open-modal', 'create-product')" >
                <x-Atoms.Link href="#" >
                    Tambah
                </x-Atoms.Link>
            </x-Atoms.Button>
        </x-slot>
    </x-Layouts.PageHeader>

    <x-Layouts.Table>
        <x-Molecules.Table.Header>
            <x-Atoms.Table.th>No</x-Atoms.Table.th>
            <x-Atoms.Table.th sortable :direction="$sortField === 'name' ? $sortDirection : null" onclick="window.location.href='{{ route('products.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sortField === 'name' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'"
            >Nama Produk</x-Atoms.Table.th>
            <x-Atoms.Table.th sortable :direction="$sortField === 'default_estimation_days_per_unit' ? $sortDirection : null" onclick="window.location.href='{{ route('products.index', array_merge(request()->query(), ['sort' => 'default_estimation_days_per_unit', 'direction' => ($sortField === 'default_estimation_days_per_unit' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'"
            >Estimasi Habis</x-Atoms.Table.th>
            <x-Atoms.Table.th>Aksi</x-Atoms.Table.th>
        </x-Molecules.Table.Header>

        <x-Molecules.Table.Body>
            @forelse ($products as $product)
                <tr>
                    <x-Atoms.Table.td>{{ $loop->iteration }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>{{ $product->name }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>{{ $product->default_estimation_days_per_unit }} hari</x-Atoms.Table.td>
                    <x-Atoms.Table.td>
                        <x-Atoms.Button @click="$dispatch('open-modal', 'edit-product-{{ $product->id }}')" variant="secondary">Ubah</x-Atoms.Button>
                        <x-Atoms.Button @click="$dispatch('open-modal', 'delete-confirmation-{{ $product->id }}')" variant="danger">Hapus</x-Atoms.Button>
                    </x-Atoms.Table.td>
                </tr>
                <x-Organisms.ProductModal :product="$product" mode="edit" />
                <x-Layouts.Modal
                    name="delete-confirmation-{{ $product->id }}"
                    title=""
                    maxWidth="sm"
                    :showIcon="false"
                >
                    <form action="{{ route('products.destroy', $product) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <x-Molecules.ConfirmationContent :id="$product->id">
                            Yakin, Hapus {{$product->name}}?
                        </x-Molecules.ConfirmationContent>
                    </form>
                </x-Layouts.Modal>
            @empty
                <x-Atoms.Table.empty colspan="4">
                    <p class="mt-1">Belum ada produk.</p>
                </x-Atoms.Table.empty>
            @endforelse
        </x-Molecules.Table.Body>
        <x-slot name="pagination">
           <x-Molecules.Table.Pagination :paginator="$products" />
       </x-slot>
    </x-Layouts.Table>
    <x-Organisms.ProductModal/>
</x-Layouts.AdminLayout>
