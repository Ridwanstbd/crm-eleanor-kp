<x-Layouts.AdminLayout title="Produk">
    <x-Organisms.PageHeader title="Produk">
        <x-slot name="filters">
            <input type="hidden" name="sort" value="{{ $sortField }}">
            <input type="hidden" name="direction" value="{{ $sortDirection }}">

            <x-Elements.Form.SearchInput
                name="search"
                id="search"
                value="{{ request('search') }}"
                placeholder="Cari nama produk..."
                onchange="this.form.submit()"
            />
        </x-slot>
        <x-slot name="actions">
            <x-Elements.Button @click="$dispatch('open-modal', 'create-product')" >
                <x-Elements.Link href="#" >
                    Tambah
                </x-Elements.Link>
            </x-Elements.Button>
        </x-slot>
    </x-Organisms.PageHeader>

    <x-Layouts.Table>
        <x-Fragments.Table.Header>
            <x-Elements.Table.th>No</x-Elements.Table.th>
            <x-Elements.Table.th sortable :direction="$sortField === 'name' ? $sortDirection : null" onclick="window.location.href='{{ route('products.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sortField === 'name' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'"
            >Nama Produk</x-Elements.Table.th>
            <x-Elements.Table.th sortable :direction="$sortField === 'default_estimation_days_per_unit' ? $sortDirection : null" onclick="window.location.href='{{ route('products.index', array_merge(request()->query(), ['sort' => 'default_estimation_days_per_unit', 'direction' => ($sortField === 'default_estimation_days_per_unit' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'"
            >Estimasi Habis</x-Elements.Table.th>
            <x-Elements.Table.th>Aksi</x-Elements.Table.th>
        </x-Fragments.Table.Header>

        <x-Fragments.Table.Body>
            @forelse ($products as $product)
                <tr>
                    <x-Elements.Table.td>{{ $loop->iteration }}</x-Elements.Table.td>
                    <x-Elements.Table.td>{{ $product->name }}</x-Elements.Table.td>
                    <x-Elements.Table.td>{{ $product->default_estimation_days_per_unit }} hari</x-Elements.Table.td>
                    <x-Elements.Table.td>
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
                    </x-Elements.Table.td>

                </tr>

                {{-- Include modals --}}
                @include('pages.Admin.Product._edit-modal', ['product' => $product])
                @include('pages.Admin.Product._delete-modal', ['product' => $product])
            @empty
                <x-Elements.Table.empty colspan="4">
                    <p class="mt-1">Belum ada produk.</p>
                </x-Elements.Table.empty>
            @endforelse
        </x-Fragments.Table.Body>
        <x-slot name="pagination">
           <x-Fragments.Table.Pagination :paginator="$products" />
       </x-slot>
    </x-Layouts.Table>
    @include('pages.Admin.Product._create-modal')
</x-Layouts.AdminLayout>
