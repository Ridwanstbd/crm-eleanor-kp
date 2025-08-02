<x-Layouts.Modal name="create-message-template" title="Tambah Template Pesan" mode="create">
    <form method="POST" action="{{ route('templates.store') }}">
        @csrf
        <x-Elements.Form.Input name="name" id="name" class="mb-2" placeholder="Nama Template Pesan" />
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

        <div class="flex my-2">
            <x-Elements.Select
                name="item" 
                id="placeholderSelect" 
                valueField="id"
                placeholder="Pilih Item"
                :selected="old('template')"
                class="w-1/2"
            >
            <x-Elements.Option value="{name}">Nama Pembeli</x-Elements.Option>
            <x-Elements.Option value="{product_name}">Nama Produk</x-Elements.Option>
            <x-Elements.Option value="{quantity_purchased}">Jumlah dibeli</x-Elements.Option>
            <x-Elements.Option value="{estimated_finish_date}">Tanggal Estimasi</x-Elements.Option>
            </x-Elements.Select>
            <x-Elements.Button id="insertPlaceholderBtn" type="button">
                Masukkan
            </x-Elements.Button>
        </div>
        <div class="flex justify-end gap-2 px-4 pb-4">
            <button type="button" @click="$dispatch('close-modal', 'create-message-template')" class="px-4 py-2 text-sm bg-gray-300 rounded hover:bg-gray-400">
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


            // Add character counter if element exists
            if (messageTemplate) {
                messageTemplate.addEventListener('input', updateCharCount);
                updateCharCount(); // Initialize
            }
        });
    </script>
    @endpush
</x-Layouts.Modal>