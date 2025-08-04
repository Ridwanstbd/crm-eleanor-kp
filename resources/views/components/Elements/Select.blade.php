@props([
    'disabled' => false,
    'readonly' => false
])

<select {{ $attributes->merge([
    'class' => 'block w-full px-3 py-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500' . 
               ($disabled || $readonly ? ' bg-gray-100 cursor-not-allowed' : '')
]) }}
@if($disabled || $readonly) disabled @endif
>
    {{ $slot }}
</select>