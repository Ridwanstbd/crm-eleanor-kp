<x-Layouts.AdminLayout>
    <div x-data="messageLogModal()">
        <x-Layouts.PageHeader title="Detail Kampanye {{$campaign->name}}" />
        <div class="grid grid-cols-2 gap-2">
            <x-Molecules.Form.FormGroup label="Nama Kampanye" for="name">
                <x-Atoms.Input name="name" value="{{$campaign->name}}" disabled readonly/>
            </x-Molecules.Form.FormGroup>
            <x-Molecules.Form.FormGroup label="Produk" for="product">
                    @if($product)
                        <x-Molecules.Select 
                            name="product" 
                            id="product"
                            :options="collect([$product])"
                            valueField="id"
                            textField="name"
                            :selected="$product->id"
                            :allowEmpty="false"
                            disabled
                            readonly>
                        </x-Molecules.Select>
                    @else
                        <x-Atoms.Input name="product" value="Tanpa Produk" disabled readonly/>
                        <p class="text-sm text-gray-500 mt-1">Kampanye ini tidak terkait dengan produk tertentu</p>
                    @endif
                    @error('product')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </x-Molecules.Form.FormGroup>

            <x-Molecules.Form.FormGroup label="Tanggal Terjual" for="tanggal_terjual">
                <x-Atoms.InputDate
                    name="tanggal_terjual"
                    id="tanggal_terjual"
                    value="{{ $campaign->schedule }}"
                    readonly
                    disabled
                />
                @error('tanggal_terjual')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </x-Molecules.Form.FormGroup>
            
            <x-Molecules.Form.FormGroup label="Waktu Kirim Kampanye" for="time_send">
                <x-Atoms.InputTime
                    name="time_send"
                    id="time_send"
                    value="{{ $campaign->time_send }}"
                    readonly
                    disabled
                />
                @error('time_send')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </x-Molecules.Form.FormGroup>
        </div>
        <x-Molecules.Form.FormGroup label="Template" for="template">
            <x-Atoms.TextArea 
                name="content" 
                id="messageTemplate-{{ $template->id }}"
                rows="8"
                height="200px"
                resize="vertical"
                placeholder="Ketik template pesan Anda..."
                class="mb-2"
                :class="$errors->has('content') ? 'border-red-500 focus:ring-red-500' : ''"
                disabled readonly>{{ $template->content }}</x-Atoms.TextArea>
        </x-Molecules.Form.FormGroup>
        
        <x-Layouts.Table min-height="500px">
            <x-Molecules.Table.Header>
                <x-Atoms.Table.th>No</x-Atoms.Table.th>
                <x-Atoms.Table.th>Nama Pelanggan</x-Atoms.Table.th>
                <x-Atoms.Table.th>Nomor Telepon</x-Atoms.Table.th>
                @if($product)
                <x-Atoms.Table.th>Jumlah Beli</x-Atoms.Table.th>
                @endif
                <x-Atoms.Table.th>Status Pesan</x-Atoms.Table.th>
                <x-Atoms.Table.th>Jadwal Kirim</x-Atoms.Table.th>
                <x-Atoms.Table.th>Aksi</x-Atoms.Table.th>
            </x-Molecules.Table.Header>

            <x-Molecules.Table.Body>
                @forelse ($messageLogs as $messageLog)
                    <tr>
                        <x-Atoms.Table.td>{{ $loop->iteration + ($messageLogs->currentPage() - 1) * $messageLogs->perPage() }}</x-Atoms.Table.td>
                        <x-Atoms.Table.td>
                            {{ $messageLog->customer ? $messageLog->customer->name : '-' }}
                        </x-Atoms.Table.td>
                        <x-Atoms.Table.td>{{ $messageLog->target }}</x-Atoms.Table.td>
                        @if($product)
                        <x-Atoms.Table.td>
                            {{ $messageLog->customer ? $messageLog->customer->purchase_quantity : '-' }}
                        </x-Atoms.Table.td>
                        @endif
                        <x-Atoms.Table.td>
                            @if($messageLog->status == 'success')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Berhasil
                                </span>
                            @elseif($messageLog->status == 'failed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Gagal
                                </span>
                            @elseif($messageLog->status == 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Pending
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    {{ ucfirst($messageLog->status ?? 'Tidak Diketahui') }}
                                </span>
                            @endif
                        </x-Atoms.Table.td>
                        <x-Atoms.Table.td>
                            @if($messageLog->scheduled_at)
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($messageLog->scheduled_at)->format('d/m/Y H:i') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($messageLog->scheduled_at)->diffForHumans() }}
                                </div>
                            @else
                                <span class="text-sm text-gray-500">Belum dikirim</span>
                            @endif
                        </x-Atoms.Table.td>
                        <x-Atoms.Table.td>
                            <div class="flex space-x-2">
                                <button
                                    @click="showDetail({{ json_encode([
                                        'id' => $messageLog->id,
                                        'customer_name' => $messageLog->customer ? $messageLog->customer->name : 'Pelanggan Tidak Ditemukan',
                                        'target' => $messageLog->target,
                                        'purchase_quantity' => $messageLog->customer ? $messageLog->customer->purchase_quantity : '-',
                                        'status' => ucfirst($messageLog->status ?? 'Tidak Diketahui'),
                                        'state' => ucfirst($messageLog->state ?? 'Tidak Diketahui'),
                                        'scheduled_at' => $messageLog->scheduled_at ? \Carbon\Carbon::parse($messageLog->scheduled_at)->format('d/m/Y H:i') : 'Belum dikirim',
                                        'message' => $messageLog->message
                                    ]) }})"
                                    class="px-3 py-1 text-sm text-white bg-yellow-500 rounded hover:bg-yellow-600">
                                    detail
                                </button>
                                
                                @if($messageLog->status == 'failed')
                                    <button 
                                        type="button" 
                                        class="inline-flex items-center px-2.5 py-1.5 border border-yellow-300 shadow-sm text-xs font-medium rounded text-yellow-700 bg-yellow-50 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Retry
                                    </button>
                                @endif
                            </div>
                        </x-Atoms.Table.td>
                    </tr>
                @empty
                    <x-Atoms.Table.empty colspan="7">
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m8-8v2m0 6V9.5"></path>
                            </svg>
                            <p class="mt-2 text-sm font-medium text-gray-900">Belum ada log pesan</p>
                            <p class="mt-1 text-sm text-gray-500">Pesan untuk kampanye ini belum ada yang dikirim.</p>
                        </div>
                    </x-Atoms.Table.empty>
                @endforelse
            </x-Molecules.Table.Body>

            <x-slot name="pagination">
                <x-Molecules.Table.Pagination :paginator="$messageLogs" />
            </x-slot>
        </x-Layouts.Table>

        @include('pages.Admin.Campaign.partials.show-detail')
    </div>

    <script>
        function messageLogModal() {
            return {
                selectedMessageLog: null,
                
                showDetail(messageLogData) {
                    this.selectedMessageLog = { ...messageLogData };
                    this.$dispatch('open-modal', 'message-log-detail-modal');
                },
                
                closeDetail() {
                    this.selectedMessageLog = null;
                    this.$dispatch('close-modal', 'message-log-detail-modal');
                }
            }
        }
    </script>
</x-Layouts.AdminLayout>