@props(['label', 'for', 'error' => null])
<div class="my-2">
    <x-Atoms.Label :for="$for">{{ $label }}</x-Atoms.Label>
    {{ $slot }}
    <x-Atoms.Form.InputError :message="$error" />
</div>