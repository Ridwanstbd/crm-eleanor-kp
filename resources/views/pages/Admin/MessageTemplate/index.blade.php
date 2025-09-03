<x-Layouts.AdminLayout title="Template Pesan">
    <x-Layouts.PageHeader title="Template Pesan">
        <x-slot name="filters">
            <input type="hidden" name="sort" value="{{ $sortField }}">
            <input type="hidden" name="direction" value="{{ $sortDirection }}">
            <x-Atoms.Form.SearchInput
                name="search"
                id="search"
                value="{{ request('search') }}"
                placeholder="Cari nama template..."
                onchange="this.form.submit()"
            />
        </x-slot>
        <x-slot name="actions">
            <x-Atoms.Button @click="$dispatch('open-modal', 'create-message-template')">
                <x-Atoms.Link href="#">Tambah</x-Atoms.Link>
            </x-Atoms.Button>
        </x-slot>
    </x-Layouts.PageHeader>

    <x-Layouts.Table>
        <x-Molecules.Table.Header>
            <x-Atoms.Table.th>No</x-Atoms.Table.th>
            <x-Atoms.Table.th sortable :direction="$sortField === 'name' ? $sortDirection : null" onclick="window.location.href='{{ route('templates.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sortField === 'name' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'" >
                Nama
            </x-Atoms.Table.th>
            <x-Atoms.Table.th>Aksi</x-Atoms.Table.th>
        </x-Molecules.Table.Header>

        <x-Molecules.Table.Body>
            @forelse ($templates as $template)
                <tr>
                    <x-Atoms.Table.td>{{ $loop->iteration }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>{{ $template->name }}</x-Atoms.Table.td>
                    <x-Atoms.Table.td>
                        <x-Atoms.Button @click="$dispatch('open-modal', 'edit-message-template-{{ $template->id }}')" variant="secondary">Ubah</x-Atoms.Button>
                        <x-Atoms.Button @click="$dispatch('open-modal', 'delete-confirmation-{{ $template->id }}')" variant="danger">Hapus</x-Atoms.Button>
                    </x-Atoms.Table.td>
                </tr>
                <x-Organisms.MessageTemplateModal :template="$template" mode="edit"/>
                <x-Layouts.Modal
                    name="delete-confirmation-{{ $template->id }}"
                    title=""
                    maxWidth="sm"
                    :showIcon="false"
                >
                    <form action="{{ route('templates.destroy', $template) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <x-Molecules.ConfirmationContent :id="$template->id">
                            Yakin, Hapus {{$template->name}}?
                        </x-Molecules.ConfirmationContent>
                    </form>
                </x-Layouts.Modal>
            @empty
                <x-Atoms.Table.empty colspan="3">
                    <p class="mt-1 text-sm">Belum ada template pesan.</p>
                </x-Atoms.Table.empty>
            @endforelse
        </x-Molecules.Table.Body>

        <x-slot name="pagination">
            <x-Molecules.Table.Pagination :paginator="$templates" />
        </x-slot>
    </x-Layouts.Table>
    <x-Organisms.MessageTemplateModal/>
</x-Layouts.AdminLayout>
