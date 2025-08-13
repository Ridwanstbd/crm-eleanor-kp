@props(['type' => 'button', 'variant' => 'primary', 'size' => 'md', 'fullWidth' => false])

@php
    if ($variant === 'submit') {
        $type = 'submit';
    }

    $variants = [
        'primary' => 'bg-indigo-600 hover:bg-indigo-700 text-white',
        'secondary' => 'text-sm text-white bg-yellow-500 rounded hover:bg-yellow-600',
        'danger' => 'text-sm text-white bg-red-600 rounded hover:bg-red-700',
        'submit' => 'bg-red-600 hover:bg-red-700 text-white font-semibold rounded',
        'muted' => 'bg-gray-400 text-white rounded hover:bg-gray-500',
        'info' => 'text-white bg-blue-500 rounded hover:bg-blue-600'
    ];

    $sizes = [
        'sm' => 'px-2 py-1 text-sm',
        'md' => 'px-4 py-2 text-base',
        'lg' => 'px-6 py-3 text-lg'
    ];

    $widthClass = $fullWidth ? 'w-full' : 'inline-block';
@endphp

<button
    {{ $attributes->merge([
        'type' => $type,
        'class' => "transition-colors duration-200 cursor-pointer rounded-lg $widthClass " . $variants[$variant] . ' ' . $sizes[$size]
    ]) }}>
    {{ $slot }}
</button>