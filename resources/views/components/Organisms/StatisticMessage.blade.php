@props([
    'messageLogs',
    'totalMessages' => null
])

@php
    $stats = $messageLogs->groupBy('status')->map->count();
    $stateStats = $messageLogs->whereNotNull('state')->groupBy('state')->map->count();
    $calculatedTotal = $totalMessages ?? $messageLogs->count();
    
    $getPercentage = function($value) use ($calculatedTotal) {
        return $calculatedTotal > 0 ? round(($value / $calculatedTotal) * 100, 1) : 0;
    };
@endphp

<div class="space-y-6 mb-4">
    {{-- Statistik Utama --}}
    <x-Molecules.MainStatistics
        :stats="$stats"
        :stateStats="$stateStats"
        :totalMessages="$calculatedTotal"
        :getPercentage="$getPercentage"
    />

    {{-- Statistik Detail State --}}
    <x-Molecules.StateStatistics
        :stateStats="$stateStats"
        :getPercentage="$getPercentage"
    />
</div>