@props([
    'name' => '',
    'id' => '',
    'value' => '',
    'placeholder' => 'Pilih waktu',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'min' => null,
    'max' => null,
    'step' => null,
    'class' => '',
])

@php
    $formattedValue = '';
    if ($value) {
        try {
            if ($value instanceof \Carbon\Carbon) {
                $formattedValue = $value->format('H:i');
            }
            elseif (is_string($value)) {
                $time = \Carbon\Carbon::parse($value);
                $formattedValue = $time->format('H:i');
            }
        } catch (\Exception $e) {
            if (preg_match('/^(\d{1,2}):(\d{2})/', $value, $matches)) {
                $formattedValue = sprintf('%02d:%02d', $matches[1], $matches[2]);
            } else {
                $formattedValue = $value;
            }
        }
    }

    $baseClasses = 'block w-full px-3 py-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
    
    $classes = $baseClasses . ' ' . $class;
    
    if ($disabled || $readonly) {
        $classes .= ' bg-gray-100 cursor-not-allowed opacity-60';
    }
@endphp

<input 
    type="time"
    name="{{ $name }}"
    id="{{ $id }}"
    value="{{ old($name, $formattedValue) }}"
    placeholder="{{ $placeholder }}"
    class="{{ $classes }}"
    {{ $required ? 'required' : '' }}
    {{ $disabled ? 'disabled' : '' }}
    {{ $readonly ? 'readonly' : '' }}
    {{ $min ? 'min=' . $min : '' }}
    {{ $max ? 'max=' . $max : '' }}
    {{ $step ? 'step=' . $step : '' }}
    {{ $attributes }}
/>