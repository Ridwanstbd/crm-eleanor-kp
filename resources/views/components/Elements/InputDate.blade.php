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

<input 
    {{ $attributes->merge(['class' => 'block w-full px-3 py-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500']) }}
    type="date"
    name="{{ $name }}"
    id="{{ $id }}"
    value="{{ old($name, $value) }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($required) required @endif
    @if($disabled) disabled @endif
    @if($readonly) readonly @endif
    @if($min) min="{{ $min }}" @endif
    @if($max) max="{{ $max }}" @endif
/>