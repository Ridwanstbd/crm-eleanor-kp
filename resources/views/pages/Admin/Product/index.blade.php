{{-- pages/Admin/Product/index.blade.php --}}
<x-Layouts.AdminLayout title="Produk">
    <div class="">
        <header class="flex items-center justify-between py-4">
            <h2 class="text-xl font-semibold">Produk</h2>
            <a href="#" @click="$dispatch('open-modal', 'create-product')" class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                Tambah Produk
            </a>
        </header>

         {{-- Search --}}
        <div class="mb-4">
            <form method="GET" class="flex gap-2">
                <x-Elements.Form.Input
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama produk..."
                />
                <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                    Cari
                </button>
            </form>
        </div>

        <x-Layouts.Table>
            <x-Fragments.Table.Header>
                <x-Elements.Table.th>No</x-Elements.Table.th>
                <x-Elements.Table.th sortable>Nama Produk</x-Elements.Table.th>
                <x-Elements.Table.th sortable>Estimasi Habis</x-Elements.Table.th>
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
        </x-Layouts.Table>
    </div>

    {{-- Modal Tambah Produk --}}
    @include('pages.Admin.Product._create-modal')
</x-Layouts.AdminLayout>
