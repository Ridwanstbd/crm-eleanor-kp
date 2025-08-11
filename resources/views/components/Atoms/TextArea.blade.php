@props([
    'rows' => 3,
    'cols' => null,
    'width' => null,
    'height' => null,
    'resize' => 'vertical',
    'disabled' => false
])

@php
    $isDisabled = $disabled || $attributes->has('disabled');
    
    $baseClasses = 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
    
    $disabledClasses = $isDisabled ? 'bg-gray-100 text-gray-500 cursor-not-allowed opacity-60' : '';
    
    $resizeClass = match($resize) {
        'none' => 'resize-none',
        'both' => 'resize',
        'horizontal' => 'resize-x',
        'vertical' => 'resize-y',
        default => 'resize-y'
    };
    
    $classes = trim($baseClasses . ' ' . $resizeClass . ' ' . $disabledClasses);
    
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
    @if($isDisabled) disabled @endif
    {{ $attributes->merge(['class' => $classes]) }}
>{{ $slot }}</textarea>