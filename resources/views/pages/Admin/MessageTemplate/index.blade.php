<x-Templates.IndexTemplate title="Templat Pesan" :paginator="$templates">
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
           Tambah
        </x-Atoms.Button>
    </x-slot>
    <x-slot name="tableHeader">
        <x-Atoms.Table.th>No</x-Atoms.Table.th>
        <x-Atoms.Table.th sortable :direction="$sortField === 'name' ? $sortDirection : null" onclick="window.location.href='{{ route('templates.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sortField === 'name' && $sortDirection === 'asc') ? 'desc' : 'asc'])) }}'">
            Nama
        </x-Atoms.Table.th>
        <x-Atoms.Table.th>Aksi</x-Atoms.Table.th>
    </x-slot>

    @forelse ($templates as $template)
        <tr>
            <x-Atoms.Table.td>{{ $loop->iteration }}</x-Atoms.Table.td>
            <x-Atoms.Table.td>{{ $template->name }}</x-Atoms.Table.td>
            <x-Atoms.Table.td>
                <x-Atoms.Button @click="$dispatch('open-modal', 'edit-message-template-{{ $template->id }}')" variant="secondary">Ubah</x-Atoms.Button>
                <x-Atoms.Button @click="$dispatch('open-modal', 'delete-confirmation-{{ $template->id }}')" variant="danger">Hapus</x-Atoms.Button>
            </x-Atoms.Table.td>
        </tr>

        <x-Organisms.CrudModal
            mode="edit"
            title="Ubah Template Pesan"
            maxWidth="lg"
            :name="'edit-message-template-' . $template->id"
            :action="route('templates.update', $template)"
        >
            <x-Molecules.Form.FormGroup for="name-{{ $template->id }}" label="Nama Template">
                <x-Atoms.Form.Input name="name" id="name-{{ $template->id }}" value="{{ old('name', $template->name) }}" class="mb-2" placeholder="Nama Template Pesan" />
            </x-Molecules.Form.FormGroup>
            <x-Molecules.Form.FormGroup for="messageTemplate-{{ $template->id }}" label="Isi Template">
                <x-Atoms.TextArea name="content" id="messageTemplate-{{ $template->id }}" rows="8" placeholder="Ketik template pesan Anda...">{{ old('content', $template->content) }}</x-Atoms.TextArea>
            </x-Molecules.Form.FormGroup>
            <div class="flex my-2 gap-2">
                <x-Atoms.Select name="item" id="placeholderSelect-{{ $template->id }}" placeholder="Pilih Item" class="w-1/2">
                    <x-Atoms.Option value="{customer_name}">Nama Pembeli</x-Atoms.Option>
                    <x-Atoms.Option value="{product_name}">Nama Produk</x-Atoms.Option>
                    <x-Atoms.Option value="{quantity_purchased}">Jumlah dibeli</x-Atoms.Option>
                    <x-Atoms.Option value="{estimated_finish_date}">Tanggal Estimasi</x-Atoms.Option>
                    <x-Atoms.Option value="{receipt}">Nomor Resi</x-Atoms.Option>
                </x-Atoms.Select>
                <x-Atoms.Button type="button" id="insertPlaceholderBtn-{{ $template->id }}">Masukkan</x-Atoms.Button>
            </div>
        </x-Organisms.CrudModal>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const templateId = '{{ $template->id }}';
                const messageTemplate = document.getElementById('messageTemplate-' + templateId);
                const placeholderSelect = document.getElementById('placeholderSelect-' + templateId);
                const insertPlaceholderBtn = document.getElementById('insertPlaceholderBtn-' + templateId);

                function toggleInsertButton() {
                    const selectedValue = placeholderSelect?.value;
                    if (insertPlaceholderBtn) {
                        insertPlaceholderBtn.disabled = !selectedValue;
                        insertPlaceholderBtn.classList.toggle('opacity-50', !selectedValue);
                        insertPlaceholderBtn.classList.toggle('cursor-not-allowed', !selectedValue);
                    }
                }

                function insertSelectedPlaceholder() {
                    const placeholder = placeholderSelect?.value;
                    if (!placeholder || !messageTemplate) return;
                    const textarea = messageTemplate;
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const text = textarea.value;
                    textarea.value = text.substring(0, start) + placeholder + text.substring(end);
                    textarea.focus();
                    textarea.setSelectionRange(start + placeholder.length, start + placeholder.length);
                }

                if (placeholderSelect) {
                    placeholderSelect.addEventListener('change', toggleInsertButton);
                }
                
                if (insertPlaceholderBtn) {
                    insertPlaceholderBtn.addEventListener('click', insertSelectedPlaceholder);
                }

                toggleInsertButton();

                document.addEventListener('modal-opened', function(e) {
                    if (e.detail === 'edit-message-template-' + templateId) {
                        if (placeholderSelect) placeholderSelect.value = '';
                        toggleInsertButton();
                        
                        const savedDraft = localStorage.getItem('messageTemplate_edit_draft_' + templateId);
                        if (savedDraft && messageTemplate) {
                            messageTemplate.value = savedDraft;
                        }
                    }
                });

                if (messageTemplate) {
                    messageTemplate.addEventListener('input', function() {
                        localStorage.setItem('messageTemplate_edit_draft_' + templateId, this.value);
                    });
                }

                const form = messageTemplate?.closest('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        localStorage.removeItem('messageTemplate_edit_draft_' + templateId);
                    });
                }

                if (messageTemplate && placeholderSelect) {
                    messageTemplate.addEventListener('keydown', function(e) {
                        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter' && placeholderSelect.value) {
                            e.preventDefault();
                            insertSelectedPlaceholder();
                        }
                    });
                }
            });
        </script>
        @endpush

        <x-Layouts.Modal
            name="delete-confirmation-{{ $template->id }}"
            title="Konfirmasi Hapus"
            maxWidth="sm"
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
</x-Templates.IndexTemplate>

<x-Organisms.CrudModal
    mode="create"
    title="Tambah Template Pesan"
    name="create-message-template"
    maxWidth="lg"
    :action="route('templates.store')"
>
    <x-Molecules.Form.FormGroup for="name" label="Nama Template">
        <x-Atoms.Form.Input name="name" id="name" class="mb-2" placeholder="Nama Template Pesan" />
    </x-Molecules.Form.FormGroup>
    <x-Molecules.Form.FormGroup for="content" label="Konten">
        <x-Atoms.TextArea name="content" id="messageTemplate" rows="8" placeholder="Ketik template pesan Anda..." />
    </x-Molecules.Form.FormGroup>
    <div class="flex my-2 gap-2">
        <x-Atoms.Select name="item" id="placeholderSelect" placeholder="Pilih Item" class="w-1/2">
            <x-Atoms.Option value="{customer_name}">Nama Pembeli</x-Atoms.Option>
            <x-Atoms.Option value="{product_name}">Nama Produk</x-Atoms.Option>
            <x-Atoms.Option value="{quantity_purchased}">Jumlah dibeli</x-Atoms.Option>
            <x-Atoms.Option value="{estimated_finish_date}">Tanggal Estimasi</x-Atoms.Option>
            <x-Atoms.Option value="{receipt}">Nomor Resi</x-Atoms.Option>
        </x-Atoms.Select>
        <x-Atoms.Button type="button" id="insertPlaceholderBtn">Masukkan</x-Atoms.Button>
    </div>
</x-Organisms.CrudModal>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messageTemplate = document.getElementById('messageTemplate');
        const placeholderSelect = document.getElementById('placeholderSelect');
        const insertPlaceholderBtn = document.getElementById('insertPlaceholderBtn');

        function toggleInsertButton() {
            const selectedValue = placeholderSelect.value;
            if (insertPlaceholderBtn) {
                insertPlaceholderBtn.disabled = !selectedValue;
                insertPlaceholderBtn.classList.toggle('opacity-50', !selectedValue);
                insertPlaceholderBtn.classList.toggle('cursor-not-allowed', !selectedValue);
            }
        }

        function insertSelectedPlaceholder() {
            const placeholder = placeholderSelect.value;
            if (!placeholder || !messageTemplate) return;
            const textarea = messageTemplate;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            textarea.value = text.substring(0, start) + placeholder + text.substring(end);
            textarea.focus();
            textarea.setSelectionRange(start + placeholder.length, start + placeholder.length);
        }

        if (placeholderSelect) {
            placeholderSelect.addEventListener('change', toggleInsertButton);
        }
        
        if (insertPlaceholderBtn) {
            insertPlaceholderBtn.addEventListener('click', insertSelectedPlaceholder);
        }

        toggleInsertButton();

        document.addEventListener('modal-opened', function(e) {
            if (e.detail === 'create-message-template') {
                if (messageTemplate) messageTemplate.value = '';
                if (placeholderSelect) placeholderSelect.value = '';
                toggleInsertButton();
            }
        });

        if (messageTemplate) {
            messageTemplate.addEventListener('input', function() {
                localStorage.setItem('messageTemplate_draft', this.value);
            });
            const savedDraft = localStorage.getItem('messageTemplate_draft');
            if (savedDraft && !messageTemplate.value) {
                messageTemplate.value = savedDraft;
            }
        }

        const form = messageTemplate?.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                localStorage.removeItem('messageTemplate_draft');
            });
        }

        if (messageTemplate && placeholderSelect) {
            messageTemplate.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter' && placeholderSelect.value) {
                    e.preventDefault();
                    insertSelectedPlaceholder();
                }
            });
        }
    });
</script>
@endpush