<x-Layouts.Modal name="message-log-detail-modal" title="Detail Log Pesan">
            <div class="space-y-4" x-show="selectedMessageLog">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pelanggan:</label>
                        <p class="text-gray-900" x-text="selectedMessageLog?.customer_name || ''"></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon:</label>
                        <p class="text-gray-900" x-text="selectedMessageLog?.target || ''"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Beli:</label>
                        <p class="text-gray-900" x-text="selectedMessageLog?.purchase_quantity || ''"></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status:</label>
                        <p class="text-gray-900" x-text="selectedMessageLog?.status || ''"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Kirim:</label>
                    <p class="text-gray-900" x-text="selectedMessageLog?.sent_at || ''"></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Isi Pesan:</label>
                    <textarea 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed opacity-60 resize-y"
                        rows="8"
                        style="height: 200px;"
                        disabled
                        x-model="selectedMessageLog?.message || ''"
                    ></textarea>
                </div>
            </div>
        </x-Layouts.Modal>