@props([
    'stats',
    'stateStats',
    'totalMessages',
    'getPercentage'
])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    {{-- Total Pesan --}}
    <x-Atoms.StatisticCard
        icon="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
        title="Total Pesan"
        :value="number_format($totalMessages)"
        iconColor="blue"
    />

    {{-- Terkirim --}}
    <x-Atoms.StatisticCard
        icon="M5 13l4 4L19 7"
        title="Terkirim"
        :value="number_format($stats['Terkirim'] ?? 0)"
        :percentage="$getPercentage($stats['Terkirim'] ?? 0)"
        iconColor="green"
    />

    {{-- Tertunda --}}
    <x-Atoms.StatisticCard
        icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
        title="Tertunda"
        :value="number_format($stats['Tertunda'] ?? 0)"
        :percentage="$getPercentage($stats['Tertunda'] ?? 0)"
        iconColor="yellow"
    />
</div>