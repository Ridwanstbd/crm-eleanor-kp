@props([
    'level' => 2,
    'size' => null
])

@php
$tag = "h{$level}";
$defaultSize = match($level) {
    1 => 'text-3xl',
    2 => 'text-xl',
    3 => 'text-lg',
    4 => 'text-base',
    default => 'text-xl'
};
$sizeClass = $size ?? $defaultSize;
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => "font-semibold $sizeClass"]) }}>
    {{ $slot }}
</{{ $tag }}>