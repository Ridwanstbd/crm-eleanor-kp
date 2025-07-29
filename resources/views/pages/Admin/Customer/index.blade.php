{{-- pages/Admin/Customer/index.blade.php --}}
<x-Layouts.AdminLayout title="Pelanggan">
    <div class="">
        <header class="flex items-center justify-between py-3">
            <h2 class="text-xl font-semibold">Pelanggan</h2>
            <div class="flex gap-2">
                <input type="hidden" name="sort" value="{{ $sortField }}">
                <input type="hidden" name="direction" value="{{ $sortDirection }}">

                <x-Elements.Form.SearchInput
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama pelanggan..."
                    onchange="this.form.submit()"
                />
                <x-Elements.Button @click="$dispatch('open-modal', 'create-customer')" >
                    <x-Elements.Link href="#" >
                        Tambah
                    </x-Elements.Link>
                </x-Elements.Button>
            </div>
        </header>

        <x-Layouts.Table>
            <x-Fragments.Table.Header>
                <x-Elements.Table.th>No</x-Elements.Table.th>
                <x-Elements.Table.th sortable :direction="$sortField === 'name' ? $sortDirection : null" onclick="window.location.href='{{ route('customers.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sortField === 'name' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'">
                    Nama Pelanggan
                </x-Elements.Table.th>
                <x-Elements.Table.th sortable :direction="$sortField === 'phone' ? $sortDirection : null" onclick="window.location.href='{{ route('customers.index', array_merge(request()->query(), ['sort' => 'phone', 'direction' => ($sortField === 'phone' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'">
                    Nomor Telepon
                </x-Elements.Table.th>
                <x-Elements.Table.th>Aksi</x-Elements.Table.th>
            </x-Fragments.Table.Header>

            <x-Fragments.Table.Body>
                @forelse ($customers as $customer)
                    <tr>
                        <x-Elements.Table.td>{{ $loop->iteration }}</x-Elements.Table.td>
                        <x-Elements.Table.td>{{ $customer->name }}</x-Elements.Table.td>
                        <x-Elements.Table.td>{{ $customer->phone }}</x-Elements.Table.td>
                        <x-Elements.Table.td>
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
                        </x-Elements.Table.td>
                    </tr>

                    {{-- Include modals --}}
                    @include('pages.Admin.Customer._edit-modal', ['customer' => $customer])
                    @include('pages.Admin.Customer._delete-modal', ['customer' => $customer])
                @empty
                    <x-Elements.Table.empty colspan="4">
                        <p class="mt-1">Belum ada pelanggan.</p>
                    </x-Elements.Table.empty>
                @endforelse
            </x-Fragments.Table.Body>

            <x-slot name="pagination">
                <x-Fragments.Table.Pagination :paginator="$customers" />
            </x-slot>
        </x-Layouts.Table>
    </div>

    {{-- Modal Tambah Pelanggan --}}
    @include('pages.Admin.Customer._create-modal')
</x-Layouts.AdminLayout>
