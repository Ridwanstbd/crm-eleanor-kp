@props([
    'rows' => 3,
    'cols' => null,
    'width' => null,
    'height' => null,
    'resize' => 'vertical'
])

@php
    $baseClasses = 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
    
    $resizeClass = match($resize) {
        'none' => 'resize-none',
        'both' => 'resize',
        'horizontal' => 'resize-x',
        'vertical' => 'resize-y',
        default => 'resize-y'
    };
    
    $classes = $baseClasses . ' ' . $resizeClass;
    
    $style = '';
    if ($width) {
        $style .= "width: {$width};";
    }
    if ($height) {
        $style .= "height: {$height};";
    }
@endphp

<textarea 
    rows="{{ $rows }}"
    @if($cols) cols="{{ $cols }}" @endif
    @if($style) style="{{ $style }}" @endif
    {{ $attributes->merge(['class' => $classes]) }}
>{{ $slot }}</textarea>