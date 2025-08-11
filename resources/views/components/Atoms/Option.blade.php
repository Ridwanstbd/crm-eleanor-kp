@props([
    'value' => '',
    'selected' => false,
    'disabled' => false
])

<option 
    {{ $attributes->merge(['class' => '']) }} 
    value="{{ $value }}"
    @if($selected || old($attributes->get('name')) == $value) selected @endif
    @if($disabled) disabled @endif
>
    {{ $slot }}
</option>