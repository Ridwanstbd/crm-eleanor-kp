<x-Layouts.Modal name="message-log-detail-modal" title="Detail Log Pesan">
    <div class="space-y-4" x-show="selectedMessageLog">
        <div class="grid grid-cols-2 gap-4">
            <x-Molecules.Form.FormGroup label="Nama Pelanggan" for="message_log_customer_name">
                <p class="text-gray-900" x-text="selectedMessageLog?.customer_name || ''"></p>
            </x-Molecules.Form.FormGroup>
            
            <x-Molecules.Form.FormGroup label="Nomor Telepon" for="message_log_target">
                <p class="text-gray-900" x-text="selectedMessageLog?.target || ''"></p>
            </x-Molecules.Form.FormGroup>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <x-Molecules.Form.FormGroup label="Jumlah Beli" for="message_log_purchase_quantity">
                <p class="text-gray-900" x-text="selectedMessageLog?.purchase_quantity || ''"></p>
            </x-Molecules.Form.FormGroup>
            
            <x-Molecules.Form.FormGroup label="Status" for="message_log_status">
                <p class="text-gray-900" x-text="selectedMessageLog?.status || ''"></p>
            </x-Molecules.Form.FormGroup>
        </div>

        <x-Molecules.Form.FormGroup label="Waktu Kirim" for="message_log_sent_at">
            <p class="text-gray-900" x-text="selectedMessageLog?.sent_at || ''"></p>
        </x-Molecules.Form.FormGroup>
        
        <x-Molecules.Form.FormGroup label="Isi Pesan" for="message_log_message">
            <x-Atoms.TextArea 
                rows="8" 
                height="200px"
                resize="vertical"
                disabled
                x-model="selectedMessageLog?.message || ''"
            />
        </x-Molecules.Form.FormGroup>
    </div>
</x-Layouts.Modal>