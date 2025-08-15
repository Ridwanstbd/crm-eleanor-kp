@props([
    'totalCustomers' => 0,
    'placeholder' => 'Cari nomor telepon (contoh: 6281234567890 atau 0812)...'
])

<div class="mb-4">
    <div class="relative w-full max-w-md">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input
                type="text"
                x-model="searchQuery"
                @input.debounce.300ms="searchCustomers()"
                placeholder="{{ $placeholder }}"
                class="block w-full pl-10 pr-12 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                {{ $attributes }}
            >
            <button 
                x-show="searchQuery.length > 0"
                @click="clearSearch()"
                type="button"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors duration-200"
                title="Hapus pencarian"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Search Results Counter -->
    <div x-show="searchQuery.length > 0" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform translate-y-1"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-1"
         class="mt-2 text-sm text-gray-600">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>
                Menampilkan <span x-text="filteredCustomers.length" class="font-semibold text-blue-600"></span> 
                dari <span class="font-semibold">{{ $totalCustomers }}</span> pelanggan
            </span>
        </div>
    </div>
    
    <div x-show="searchQuery.length > 0 && filteredCustomers.length === 0" 
         class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-start gap-2">
            <svg class="w-4 h-4 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-yellow-800">
                <p class="font-medium">Tidak ada hasil ditemukan</p>
                <p class="mt-1">Tips pencarian:</p>
                <ul class="mt-1 list-disc list-inside text-xs space-y-1">
                    <li>Coba kata kunci yang lebih pendek</li>
                    <li>Periksa ejaan nomor telepon</li>
                    <li>Gunakan sebagian nomor telepon (contoh: 62812)</li>
                </ul>
            </div>
        </div>
    </div>
</div>