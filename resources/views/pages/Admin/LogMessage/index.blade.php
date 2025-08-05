{{-- pages/Admin/LogMessage/index.blade.php --}}
<x-Layouts.AdminLayout title="Log Pesan">
    <div class="">
        <header class="flex items-center justify-between py-3">
            <h2 class="text-xl font-semibold">Log Pesan</h2>
        </header>

        {{-- Tabel Log Pesan --}}
        <x-Layouts.Table>
            <x-Fragments.Table.Header>
                <x-Elements.Table.th>No</x-Elements.Table.th>
                <x-Elements.Table.th>Nomor</x-Elements.Table.th>
                <x-Elements.Table.th>Status</x-Elements.Table.th>
                <x-Elements.Table.th>Waktu Kirim</x-Elements.Table.th>
                <x-Elements.Table.th>Aksi</x-Elements.Table.th>
            </x-Fragments.Table.Header>

            <x-Fragments.Table.Body>
                @forelse ($logs as $log)
                    <tr>
                        <x-Elements.Table.td>{{ $loop->iteration }}</x-Elements.Table.td>
                        <x-Elements.Table.td>{{ $log->target }}</x-Elements.Table.td>
                        <x-Elements.Table.td>{{ ucfirst($log->status) }}</x-Elements.Table.td>
                        <x-Elements.Table.td>{{ \Carbon\Carbon::parse($log->waktu_kirim)->format('d M Y, H:i') }}</x-Elements.Table.td>
                        <x-Elements.Table.td>
                            {{-- <x-Elements.Button>
                                <x-Elements.Link href="{{ route('logs.show', $log->id) }}" class="text-white">
                                    Detail
                                </x-Elements.Link>
                            </x-Elements.Button> --}}
                        </x-Elements.Table.td>
                    </tr>
                @empty
                    <x-Elements.Table.empty colspan="5">
                        <p class="mt-1">Belum ada log pesan.</p>
                    </x-Elements.Table.empty>
                @endforelse
            </x-Fragments.Table.Body>

            {{-- Pagination --}}
            <x-slot name="pagination">
                <x-Fragments.Table.Pagination :paginator="$logs" />
            </x-slot>
        </x-Layouts.Table>
    </div>
</x-Layouts.AdminLayout>
