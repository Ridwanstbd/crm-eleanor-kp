@props([
    'name' => '',
    'id' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'min' => '',
    'max' => ''
])

@php
    $formattedValue = '';
    if ($value) {
        try {
            if ($value instanceof \Carbon\Carbon) {
                $formattedValue = $value->format('Y-m-d');
            }
            elseif (is_string($value)) {
                $formattedValue = \Carbon\Carbon::parse($value)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            $formattedValue = $value;
        }
    }
    
    $baseClasses = 'block w-full px-3 py-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
    
    if ($disabled || $readonly) {
        $baseClasses .= ' bg-gray-100 cursor-not-allowed opacity-60';
    }
@endphp

<input 
    {{ $attributes->merge(['class' => $baseClasses]) }}
    type="date"
    name="{{ $name }}"
    id="{{ $id }}"
    value="{{ old($name, $formattedValue) }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($required) required @endif
    @if($disabled) disabled @endif
    @if($readonly) readonly @endif
    @if($min) min="{{ $min }}" @endif
    @if($max) max="{{ $max }}" @endif
/>