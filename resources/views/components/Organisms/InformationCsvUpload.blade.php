<div class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
    <div class="grid grid-cols-2">
        <x-Atoms.Typography.Heading  level="5" class="text-sm font-medium text-blue-800 mb-2">Format CSV:</x-Atoms.Typography.Heading>
        <x-Atoms.Button 
            variant="secondary" 
            href="{{route('download.csv.template')}}"
        >
            Download Template CSV
        </x-Atoms.Button>
    </div>
    <p class="text-sm text-blue-700 mb-2">File CSV harus memiliki kolom berikut:</p>
    <ul class="text-sm text-blue-600 list-disc list-inside space-y-1">
        <li><strong>nomor</strong> (wajib): Nomor telepon pelanggan</li>
        <li><strong>nama</strong> (opsional): Nama pelanggan</li>
        <li><strong>jumlah_beli</strong> (opsional): Jumlah pembelian (1-999)</li>
    </ul>
    <p class="text-xs text-blue-500 mt-2">Contoh: kolom A nama , kolom B nomor, kolom C Jumlah Beli</p>
    <p class="text-xs text-blue-500">boy, 6281234567890, 5</p>
</div>