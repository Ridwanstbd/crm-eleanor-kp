<x-Layouts.AdminLayout title="Produk">
    <x-Organisms.PageHeader title="Produk">
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
    </x-Organisms.PageHeader>

    <x-Layouts.Table>
        <x-Fragments.Table.Header>
            <x-Atoms.Table.th>No</x-Atoms.Table.th>
            <x-Atoms.Table.th sortable :direction="$sortField === 'name' ? $sortDirection : null" onclick="window.location.href='{{ route('products.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sortField === 'name' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'"
            >Nama Produk</x-Atoms.Table.th>
            <x-Atoms.Table.th sortable :direction="$sortField === 'default_estimation_days_per_unit' ? $sortDirection : null" onclick="window.location.href='{{ route('products.index', array_merge(request()->query(), ['sort' => 'default_estimation_days_per_unit', 'direction' => ($sortField === 'default_estimation_days_per_unit' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'"
            >Estimasi Habis</x-Atoms.Table.th>
            <x-Atoms.Table.th>Aksi</x-Atoms.Table.th>
        </x-Fragments.Table.Header>

        <x-Fragments.Table.Body>
            @forelse ($products as $product)
                <tr>
                    <x-Atoms.Table.td>{{ $loop->iteration }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>{{ $product->name }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>{{ $product->default_estimation_days_per_unit }} hari</x-Atoms.Table.td>
                    <x-Atoms.Table.td>
                        <div class="flex gap-2">
                            {{-- Tombol Ubah --}}
                            <button
                                @click="$dispatch('open-modal', 'edit-product-{{ $product->id }}')"
                                class="px-3 py-1 text-sm text-white bg-yellow-500 rounded hover:bg-yellow-600">
                                Ubah
                            </button>

                            {{-- Tombol Hapus --}}
                            <button
                                @click="$dispatch('open-modal', 'delete-product-{{ $product->id }}')"
                                class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                                Hapus
                            </button>
                        </div>
                    </x-Atoms.Table.td>

                </tr>

                {{-- Include modals --}}
                @include('pages.Admin.Product._edit-modal', ['product' => $product])
                @include('pages.Admin.Product._delete-modal', ['product' => $product])
            @empty
                <x-Atoms.Table.empty colspan="4">
                    <p class="mt-1">Belum ada produk.</p>
                </x-Atoms.Table.empty>
            @endforelse
        </x-Fragments.Table.Body>
        <x-slot name="pagination">
           <x-Fragments.Table.Pagination :paginator="$products" />
       </x-slot>
    </x-Layouts.Table>
    @include('pages.Admin.Product._create-modal')
</x-Layouts.AdminLayout>
