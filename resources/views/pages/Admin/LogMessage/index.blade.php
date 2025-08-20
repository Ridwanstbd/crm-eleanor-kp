<x-Layouts.AdminLayout title="Log Pesan">
    <div x-data="logModal()">
        <x-Layouts.PageHeader title="Log Pesan"></x-Layouts.PageHeader>
        <x-Layouts.Table>
            <x-Molecules.Table.Header>
                <x-Atoms.Table.th>No</x-Atoms.Table.th>
                <x-Atoms.Table.th>Nama</x-Atoms.Table.th>
                <x-Atoms.Table.th>Nomor</x-Atoms.Table.th>
                <x-Atoms.Table.th>Status</x-Atoms.Table.th>
                <x-Atoms.Table.th>Jadwal Kirim</x-Atoms.Table.th>
                <x-Atoms.Table.th>Aksi</x-Atoms.Table.th>
            </x-Molecules.Table.Header>

            <x-Molecules.Table.Body>
                @forelse ($logs as $log)
                    <tr>
                        <x-Atoms.Table.td>{{ $loop->iteration }}</x-Atoms.Table.td>
                        <x-Atoms.Table.td>{{ $log->customer ? $log->customer->name : '-' }}</x-Atoms.Table.td>
                        <x-Atoms.Table.td>{{ $log->target }}</x-Atoms.Table.td>
                        <x-Atoms.Table.td>{{ ucfirst($log->status) }}</x-Atoms.Table.td>
                        <x-Atoms.Table.td>{{ \Carbon\Carbon::parse($log->scheduled_at ?? $log->created_at)->format('d M Y, H:i') }}</x-Atoms.Table.td>
                        <x-Atoms.Table.td>
                        <x-Atoms.Button 
                        @click="showDetail({{ json_encode([
                                    'id' => $log->id,
                                    'name' => $log->customer ? $log->customer->name : '-',
                                    'target' => $log->target,
                                    'status' => ucfirst($log->status),
                                    'state' => ucfirst($log->state),
                                    'scheduled_at' => \Carbon\Carbon::parse($log->scheduled_at ?? $log->created_at)->format('d M Y, H:i'),
                                    'message' => $log->message
                                ]) }})"
                        variant="secondary">Detail
                        </x-Atoms.Button>
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
                <div class="grid grid-cols-2 gap-4">
                    <x-Molecules.Form.FormGroup label="Nama" for="log_name">
                        <p class="text-gray-900" x-text="selectedLog?.name || ''"></p>
                    </x-Molecules.Form.FormGroup>
    
                    <x-Molecules.Form.FormGroup label="Nomor Tujuan" for="log_target">
                        <p class="text-gray-900" x-text="selectedLog?.target || ''"></p>
                    </x-Molecules.Form.FormGroup>
                </div>                
                <div class="grid grid-cols-2 gap-4">
                    <x-Molecules.Form.FormGroup label="Status" for="log_status">
                        <p class="text-gray-900" x-text="selectedLog?.status || ''"></p>
                    </x-Molecules.Form.FormGroup>
                    
                    <x-Molecules.Form.FormGroup label="Keadaan" for="log_state">
                        <p class="text-gray-900" x-text="selectedLog?.state || 'Tidak Diketahui'"></p>
                    </x-Molecules.Form.FormGroup>
                </div>
                
                <x-Molecules.Form.FormGroup label="Waktu Kirim" for="log_scheduled_at">
                    <p class="text-gray-900" x-text="selectedLog?.scheduled_at || ''"></p>
                </x-Molecules.Form.FormGroup>
                
                <x-Molecules.Form.FormGroup label="Isi Pesan" for="log_message">
                    <x-Atoms.TextArea 
                        rows="8" 
                        height="200px"
                        resize="vertical"
                        disabled
                        x-model="selectedLog?.message || ''"
                    />
                </x-Molecules.Form.FormGroup>
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