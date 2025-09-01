@props([
    'icon',
    'title',
    'value',
    'percentage' => null,
    'iconColor' => 'blue',
    'percentageColor' => null
])

<div class="bg-white overflow-hidden shadow-sm rounded-lg border">
    <div class="p-5">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-{{ $iconColor }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
                </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">{{ $title }}</dt>
                    <dd class="flex items-baseline">
                        <div class="text-2xl font-semibold text-gray-900">{{ $value }}</div>
                        @if($percentage !== null)
                            <div class="ml-2 flex items-baseline text-sm font-semibold text-{{ $percentageColor ?? $iconColor }}-600">
                                {{ $percentage }}%
                            </div>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>