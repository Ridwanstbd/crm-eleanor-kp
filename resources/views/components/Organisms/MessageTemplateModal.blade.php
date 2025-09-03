{{-- views/components/Organisms/MessageTemplateModal.blade.php --}}
@props(['template' => null, 'mode' => 'create' ])

@if ($mode === 'edit' && $template)
<x-Layouts.Modal name="edit-message-template-{{ $template->id }}" title="Ubah Template Pesan" mode="edit">
    <form method="POST" action="{{ route('templates.update', $template) }}">
        @csrf
        @method('PUT')
        <x-Molecules.Form.FormGroup for="name-{{ $template->id }}" value="{{ $template->name }}" label="Nama Template">
            <x-Atoms.Form.Input name="name" id="name-{{ $template->id }}" value="{{ $template->name }}" class="mb-2" placeholder="Nama Template Pesan" />
        </x-Molecules.Form.FormGroup>
        <x-Molecules.Form.FormGroup for="messageTemplate-{{ $template->id }}" label="Isi Template">
            <x-Atoms.TextArea 
                name="content" 
                id="messageTemplate-{{ $template->id }}"
                rows="8"
                height="200px"
                resize="vertical"
                placeholder="Ketik template pesan Anda..."
                class="mb-2"
                :class="$errors->has('content') ? 'border-red-500 focus:ring-red-500' : ''"
                >
            {{ $template->content }}
            </x-Atoms.TextArea>
        </x-Molecules.Form.FormGroup>
                
        <div class="flex my-2 gap-2">
            <x-Atoms.Select
                name="item" 
                id="placeholderSelect-{{ $template->id }}" 
                valueField="id"
                placeholder="Pilih Item"
                :selected="old('template')"
                class="w-1/2"
            >
                <x-Atoms.Option value="{customer_name}">Nama Pembeli</x-Atoms.Option>
                <x-Atoms.Option value="{product_name}">Nama Produk</x-Atoms.Option>
                <x-Atoms.Option value="{quantity_purchased}">Jumlah dibeli</x-Atoms.Option>
                <x-Atoms.Option value="{estimated_finish_date}">Tanggal Estimasi</x-Atoms.Option>
                <x-Atoms.Option value="{receipt}">Nomor Resi</x-Atoms.Option>
            </x-Atoms.Select>
            <x-Atoms.Button id="insertPlaceholderBtn-{{ $template->id }}">
                Masukkan
            </x-Atoms.Button>
        </div>

        <div class="flex justify-end gap-2">
            <x-Atoms.Button @click="$dispatch('close-modal', 'edit-message-template-{{ $template->id }}')" variant="muted">
                Batal
            </x-Atoms.Button>
            <x-Atoms.Button variant="submit">Simpan</x-Atoms.Button>
        </div>
    </form>

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
</x-Layouts.Modal>
@elseif($mode === 'create')
<x-Layouts.Modal name="create-message-template" title="Tambah Template Pesan" mode="create">
    <form method="POST" action="{{ route('templates.store') }}">
        @csrf
        <x-Molecules.Form.FormGroup for="name" label="Nama Template">
            <x-Atoms.Form.Input name="name" id="name" class="mb-2" placeholder="Nama Template Pesan" />
        </x-Molecules.Form.FormGroup>
        <x-Molecules.Form.FormGroup for="content" label="Konten">
            <x-Atoms.TextArea 
                name="content" 
                id="messageTemplate"
                rows="8"
                height="200px"
                resize="vertical"
                placeholder="Ketik template pesan Anda..."
                class="mb-2"
                :class="$errors->has('message_template') ? 'border-red-500 focus:ring-red-500' : ''"
                />
        </x-Molecules.Form.FormGroup>

        <div class="flex my-2 gap-2">
            <x-Atoms.Select
                name="item" 
                id="placeholderSelect" 
                valueField="id"
                placeholder="Pilih Item"
                :selected="old('template')"
                class="w-1/2"
            >
            <x-Atoms.Option value="{customer_name}">Nama Pembeli</x-Atoms.Option>
            <x-Atoms.Option value="{product_name}">Nama Produk</x-Atoms.Option>
            <x-Atoms.Option value="{quantity_purchased}">Jumlah dibeli</x-Atoms.Option>
            <x-Atoms.Option value="{estimated_finish_date}">Tanggal Estimasi</x-Atoms.Option>
            <x-Atoms.Option value="{receipt}">Nomor Resi</x-Atoms.Option>
            </x-Atoms.Select>
            <x-Atoms.Button id="insertPlaceholderBtn" type="button">
                Masukkan
            </x-Atoms.Button>
        </div>
        <div class="flex justify-end gap-2">
            <x-Atoms.Button variant="muted" @click="$dispatch('close-modal', 'create-message-template')" >Batal</x-Atoms.Button>
            <x-Atoms.Button variant="submit">Simpan</x-Atoms.Button>
        </div>
    </form>

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

            if (messageTemplate) {
                messageTemplate.addEventListener('input', updateCharCount);
                updateCharCount(); 
            }
        });
    </script>
    @endpush
</x-Layouts.Modal>
@endif
