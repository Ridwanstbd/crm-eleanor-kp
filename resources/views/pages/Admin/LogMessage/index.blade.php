<x-Layouts.AdminLayout title="Log Pesan">
    <div x-data="logModal()">
        <x-Organisms.PageHeader title="Log Pesan"></x-Organisms.PageHeader>
        <x-Layouts.Table>
            <x-Molecules.Table.Header>
                <x-Atoms.Table.th>No</x-Atoms.Table.th>
                <x-Atoms.Table.th>Nama</x-Atoms.Table.th>
                <x-Atoms.Table.th>Nomor</x-Atoms.Table.th>
                <x-Atoms.Table.th>Status</x-Atoms.Table.th>
                <x-Atoms.Table.th>Waktu Kirim</x-Atoms.Table.th>
                <x-Atoms.Table.th>Aksi</x-Atoms.Table.th>
            </x-Molecules.Table.Header>

            <x-Molecules.Table.Body>
                @forelse ($logs as $log)
                    <tr>
                        <x-Atoms.Table.td>{{ $loop->iteration }}</x-Atoms.Table.td>
                        <x-Atoms.Table.td>{{ $log->customer ? $log->customer->name : '-' }}</x-Atoms.Table.td>
                        <x-Atoms.Table.td>{{ $log->target }}</x-Atoms.Table.td>
                        <x-Atoms.Table.td>{{ ucfirst($log->status) }}</x-Atoms.Table.td>
                        <x-Atoms.Table.td>{{ \Carbon\Carbon::parse($log->updated_at)->format('d M Y, H:i') }}</x-Atoms.Table.td>
                        <x-Atoms.Table.td>
                            <button
                                @click="showDetail({{ json_encode([
                                    'id' => $log->id,
                                    'name' => $log->customer ? $log->customer->name : '-',
                                    'target' => $log->target,
                                    'status' => ucfirst($log->status),
                                    'updated_at' => \Carbon\Carbon::parse($log->updated_at)->format('d M Y, H:i'),
                                    'message' => $log->message
                                ]) }})"
                                class="px-3 py-1 text-sm text-white bg-yellow-500 rounded hover:bg-yellow-600">
                                detail
                            </button>
                        </x-Atoms.Table.td>
                    </tr>
                @empty
                    <x-Atoms.Table.empty colspan="5">
                        <p class="mt-1">Belum ada log pesan.</p>
                    </x-Atoms.Table.empty>
                @endforelse
            </x-Molecules.Table.Body>

            <x-slot name="pagination">
                <x-Molecules.Table.Pagination :paginator="$logs" />
            </x-slot>
        </x-Layouts.Table>
        <x-Layouts.Modal name="log-detail-modal" title="Detail Log Pesan">
            <div class="space-y-4" x-show="selectedLog">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama:</label>
                    <p class="text-gray-900" x-text="selectedLog?.name || ''"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Tujuan:</label>
                    <p class="text-gray-900" x-text="selectedLog?.target || ''"></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status:</label>
                    <p class="text-gray-900" x-text="selectedLog?.status || ''"></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Kirim:</label>
                    <p class="text-gray-900" x-text="selectedLog?.updated_at || ''"></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Isi Pesan:</label>
                    <textarea 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed opacity-60 resize-y"
                        rows="8"
                        style="height: 200px;"
                        disabled
                        x-model="selectedLog?.message || ''"
                    ></textarea>
                </div>
            </div>
        </x-Layouts.Modal>
    </div>

    <script>
        function logModal() {
            return {
                selectedLog: null,
                
                showDetail(logData) {
                    this.selectedLog = { ...logData }; 
                    this.$dispatch('open-modal', 'log-detail-modal');
                },
                
                closeDetail() {
                    this.selectedLog = null;
                    this.$dispatch('close-modal', 'log-detail-modal');
                }
            }
        }
    </script>
</x-Layouts.AdminLayout>