@props([
    'striped' => false,
    'height' => null,
    'maxHeight' => null,
    'minHeight' => null
])

@php
    $style = '';
    if ($height) {
        $style .= "height: {$height};";
    }
    if ($maxHeight) {
        $style .= "max-height: {$maxHeight};";
    }
    if ($minHeight) {
        $style .= "min-height: {$minHeight};";
    }
    
    $containerClasses = 'overflow-x-auto bg-white rounded-lg shadow';
    if ($height || $maxHeight) {
        $containerClasses .= ' overflow-y-auto';
    }
@endphp

<div class="{{ $containerClasses }}" @if($style) style="{{ $style }}" @endif>
    <table class="min-w-full divide-y divide-gray-200">
        {{ $slot }}
    </table>
    
    @isset($pagination)
        {{ $pagination }}
    @endisset
</div>