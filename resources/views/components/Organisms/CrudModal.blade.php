@props([
    'mode' => 'create',
    'title',
    'action',
    'name',
    'submitLabel' => null,
    'maxWidth' => 'md'
])

<x-Layouts.Modal
    :name="$name"
    :title="$title"
    :maxWidth="$maxWidth"
    :showIcon="false"
>
    <form method="POST" action="{{ $action }}">
        @csrf
        @if ($mode === 'edit')
            @method('PUT')
        @endif

        {{ $slot }}

        <x-Atoms.Button type="submit" fullWidth >
            {{ $submitLabel ?? ($mode === 'create' ? 'Tambah' : 'Simpan') }}
        </x-Atoms.Button>
    </form>
</x-Layouts.Modal>