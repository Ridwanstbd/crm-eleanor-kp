@props([
    'name' => '',
    'id' => '',
    'value' => '',
    'placeholder' => 'Pilih waktu',
    'required' => false,
    'disabled' => false,
    'min' => null,
    'max' => null,
    'step' => null,
    'class' => '',
])

@php
    $baseClasses = 'block w-full px-3 py-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
    
    $classes = $baseClasses . ' ' . $class;
    
    if ($disabled) {
        $classes .= ' bg-gray-100 cursor-not-allowed opacity-60';
    }
@endphp

<input 
    type="time"
    name="{{ $name }}"
    id="{{ $id }}"
    value="{{ $value }}"
    placeholder="{{ $placeholder }}"
    class="{{ $classes }}"
    {{ $required ? 'required' : '' }}
    {{ $disabled ? 'disabled' : '' }}
    {{ $min ? 'min=' . $min : '' }}
    {{ $max ? 'max=' . $max : '' }}
    {{ $step ? 'step=' . $step : '' }}
    {{ $attributes }}
/>