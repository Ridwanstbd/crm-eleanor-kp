@props([
    'stateStats',
    'getPercentage'
])

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    {{-- Tersampaikan --}}
    <x-Atoms.StateCard
        title="Tersampaikan"
        :value="number_format($stateStats['Tersampaikan'] ?? 0)"
        :percentage="$getPercentage($stateStats['Tersampaikan'] ?? 0)"
        icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
        color="gray"
    />

    {{-- Dibaca --}}
    <x-Atoms.StateCard
        title="Dibaca"
        :value="number_format($stateStats['Dibaca'] ?? 0)"
        :percentage="$getPercentage($stateStats['Dibaca'] ?? 0)"
        icon="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
        color="blue"
    />

    {{-- Terkirim --}}
    <x-Atoms.StateCard
        title="Terkirim"
        :value="number_format($stateStats['Terkirim'] ?? 0)"
        :percentage="$getPercentage($stateStats['Terkirim'] ?? 0)"
        icon="M5 13l4 4L19 7"
        color="gray"
    />
</div>