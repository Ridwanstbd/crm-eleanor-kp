@props(['label', 'for'])

<div class="mb-2">
    <x-Atoms.Label :for="$for" class="font-bold">{{ $label }}</x-Atoms.Label>
    {{ $slot }}
    @error($for)
        <x-Atoms.Form.InputError :message="$message" />
    @enderror
</div>