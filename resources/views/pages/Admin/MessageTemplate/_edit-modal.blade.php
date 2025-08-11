<x-Layouts.Modal name="edit-message-template-{{ $template->id }}" title="Ubah Template Pesan" mode="edit">
    <form method="POST" action="{{ route('templates.update', $template) }}">
        @csrf
        @method('PUT')
        
        <x-Atoms.Form.Input name="name" id="name-{{ $template->id }}" value="{{ $template->name }}" class="mb-2" placeholder="Nama Template Pesan" />
        
        <x-Atoms.TextArea 
            name="content" 
            id="messageTemplate-{{ $template->id }}"
            rows="8"
            height="200px"
            resize="vertical"
            placeholder="Ketik template pesan Anda..."
            class="mb-2"
            :class="$errors->has('content') ? 'border-red-500 focus:ring-red-500' : ''"
            >{{ $template->content }}</x-Atoms.TextArea>

        <div class="flex my-2">
            <x-Atoms.Select
                name="item" 
                id="placeholderSelect-{{ $template->id }}" 
                valueField="id"
                placeholder="Pilih Item"
                :selected="old('template')"
                class="w-1/2"
            >
                <x-Atoms.Option value="{name}">Nama</x-Atoms.Option>
                <x-Atoms.Option value="{product_name}">Nama Produk</x-Atoms.Option>
                <x-Atoms.Option value="{quantity_purchased}">Jumlah dibeli</x-Atoms.Option>
                <x-Atoms.Option value="{estimated_finish_date}">Tanggal Estimasi</x-Atoms.Option>
            </x-Atoms.Select>
            <x-Atoms.Button id="insertPlaceholderBtn-{{ $template->id }}" type="button">
                Masukkan
            </x-Atoms.Button>
        </div>

        <div class="flex justify-end gap-2 px-4 pb-4">
            <button type="button" @click="$dispatch('close-modal', 'edit-message-template-{{ $template->id }}')" class="px-4 py-2 text-sm bg-gray-300 rounded hover:bg-gray-400">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                Simpan
            </button>
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
                
                placeholderSelect.value = '';
                toggleInsertButton();
            }

            if (placeholderSelect) {
                placeholderSelect.addEventListener('change', toggleInsertButton);
            }
            
            if (insertPlaceholderBtn) {
                insertPlaceholderBtn.addEventListener('click', insertSelectedPlaceholder);
            }

            toggleInsertButton();

            // Handle modal opened event
            document.addEventListener('modal-opened', function(e) {
                if (e.detail === 'edit-message-template-' + templateId) {
                    if (placeholderSelect) placeholderSelect.value = '';
                    toggleInsertButton();
                    
                    // Load saved draft if exists
                    const savedDraft = localStorage.getItem('messageTemplate_edit_draft_' + templateId);
                    if (savedDraft && messageTemplate) {
                        messageTemplate.value = savedDraft;
                    }
                }
            });

            // Auto-save draft functionality
            if (messageTemplate) {
                messageTemplate.addEventListener('input', function() {
                    localStorage.setItem('messageTemplate_edit_draft_' + templateId, this.value);
                });
            }

            // Clear draft on form submit
            const form = messageTemplate?.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    localStorage.removeItem('messageTemplate_edit_draft_' + templateId);
                });
            }

            // Keyboard shortcuts for better UX
            if (messageTemplate && placeholderSelect) {
                messageTemplate.addEventListener('keydown', function(e) {
                    // Ctrl/Cmd + Enter to insert selected placeholder
                    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter' && placeholderSelect.value) {
                        e.preventDefault();
                        insertSelectedPlaceholder();
                    }
                });
            }

            // Handle modal closed event - optionally clear draft
            document.addEventListener('modal-closed', function(e) {
                if (e.detail === 'edit-message-template-' + templateId) {
                    // Optionally clear draft when modal is closed without saving
                    // localStorage.removeItem('messageTemplate_edit_draft_' + templateId);
                }
            });
        });
    </script>
    @endpush
</x-Layouts.Modal>