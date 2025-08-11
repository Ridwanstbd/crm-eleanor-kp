{{-- resources/views/components/elements/form/search-input.blade.php --}}
@props([
    'name' => 'search',
    'id' => 'search',
    'value' => '',
    'placeholder' => 'Cari nama produk...',
    'method' => 'GET'
])

<form method="{{ $method }}" class="relative w-full max-w-md">
    <div class="relative">
        <button 
            type="submit"
            class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600"
        >
            <span class="sr-only">Cari</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </button>
        <input
            type="text"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            class="block w-full pl-10 pr-12 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            {{ $attributes }}
        >
        
    </div>
</form>