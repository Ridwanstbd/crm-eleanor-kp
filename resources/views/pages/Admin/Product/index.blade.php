<x-Templates.IndexTemplate title="Produk" :paginator="$products">
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
    <x-slot name="tableHeader">
        <x-Atoms.Table.th>No</x-Atoms.Table.th>
            <x-Atoms.Table.th sortable :direction="$sortField === 'name' ? $sortDirection : null" onclick="window.location.href='{{ route('products.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sortField === 'name' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'"
            >Nama Produk</x-Atoms.Table.th>
            <x-Atoms.Table.th sortable :direction="$sortField === 'default_estimation_days_per_unit' ? $sortDirection : null" onclick="window.location.href='{{ route('products.index', array_merge(request()->query(), ['sort' => 'default_estimation_days_per_unit', 'direction' => ($sortField === 'default_estimation_days_per_unit' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'"
            >Estimasi Habis</x-Atoms.Table.th>
            <x-Atoms.Table.th>Aksi</x-Atoms.Table.th>
    </x-slot>
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
                <x-Organisms.CrudModal 
                    mode="edit"
                    title="Ubah Produk"
                    :name="'edit-product-' . $product->id"
                    :action="route('products.update', $product)"
                >
                    <x-Molecules.Form.FormGroup for="edit-name-{{ $product->id }}" label="Nama Produk">
                    <x-Atoms.Form.Input
                        id="edit-name-{{ $product->id }}"
                        name="name"
                        value="{{ $product->name }}"
                        placeholder="Susu Pengganti"
                        class="w-full"
                    />
                    </x-Molecules.Form.FormGroup>
                    <x-Molecules.Form.FormGroup for="edit-default_estimation_days_per_unit-{{ $product->id }}" label="Jangka Waktu">
                        <div class="flex items-center gap-2">
                            <x-Atoms.Form.Input
                                type="number"
                                id="edit-default_estimation_days_per_unit-{{ $product->id }}"
                                name="default_estimation_days_per_unit"
                                value="{{ $product->default_estimation_days_per_unit }}"
                                placeholder="0 untuk produk selain konsumsi"
                                class="w-full"
                            />
                            <span class="text-sm text-gray-700">Hari</span>
                        </div>
                    </x-Molecules.Form.FormGroup>
                </x-Organisms.CrudModal>
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
</x-Templates.IndexTemplate>
<x-Organisms.CrudModal
    mode="create"
    title="Tambah Produk"
    name="create-product"
    :action="route('products.store')"
>
    <x-Molecules.Form.FormGroup for="create-name" label="Nama Produk">
        <x-Atoms.Form.Input
            id="create-name"
            name="name"
            :value="old('name')"
            placeholder="Susu Pengganti"
            class="w-full"
        />
    </x-Molecules.Form.FormGroup>
    <x-Molecules.Form.FormGroup for="create-default_estimation_days_per_unit" label="Jangka Waktu">
        <div class="flex items-center gap-2">
            <x-Atoms.Form.Input
                type="number"
                id="create-default_estimation_days_per_unit"
                name="default_estimation_days_per_unit"
                :value="old('default_estimation_days_per_unit')"
                placeholder="0 Utk Non Konsumsi"
                class="w-full"
            />
            <span class="text-sm text-gray-700">Hari</span>
        </div>
    </x-Molecules.Form.FormGroup>
</x-Organisms.CrudModal>
