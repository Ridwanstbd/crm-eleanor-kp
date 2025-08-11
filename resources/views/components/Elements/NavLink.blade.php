@props(['active' => false, 'href'])

@php
$classes = ($active ?? false)
            ? 'flex items-center p-3 mb-2 relative before:absolute before:right-0 before:top-0 before:bottom-0 before:w-1 before:bg-[#DD1F1F] before:rounded-l-full border-r-1 border-[#DD1F1F]'
            : 'flex items-center p-3 mb-2 hover:relative hover:before:absolute hover:before:right-0 hover:before:top-0 hover:before:bottom-0 hover:before:w-1 hover:before:bg-[#DD1F1F] hover:before:rounded-l-full hover:border-r-1 hover:border-[#DD1F1F] transition-all duration-200';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>