@props(['for'])
<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-700']) }} for="{{ $for }}">
    {{ $slot }}
</label>