@props(['type' => 'button', 'variant' => 'primary'])

@php
    $variants = [
        'primary' => 'bg-indigo-600 hover:bg-indigo-700 text-white',
        'secondary' => 'bg-gray-500 hover:bg-gray-600 text-white',
        'danger' => 'bg-red-500 hover:bg-red-600 text-white'
    ];
@endphp

<button
    {{ $attributes->merge([
        'type' => $type,
        'class' => 'px-4 py-2 rounded-lg transition-colors duration-200 cursor-pointer ' . $variants[$variant]
    ]) }}>
    {{ $slot }}
</button>
