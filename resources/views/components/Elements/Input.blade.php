@props(['type' => 'text'])
<input 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => 'block w-full px-3 py-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500']) }}
>