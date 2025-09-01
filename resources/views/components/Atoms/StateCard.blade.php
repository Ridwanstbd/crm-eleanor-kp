@props([
    'title',
    'value',
    'percentage',
    'icon',
    'color' => 'gray'
])

<div class="bg-{{ $color }}-50 border border-{{ $color }}-200 rounded-lg p-4">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-{{ $color }}-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
                </svg>
                <p class="text-sm font-medium text-{{ $color }}-800">{{ $title }}</p>
            </div>
            <div class="mt-2 flex items-baseline">
                <p class="text-2xl font-semibold text-{{ $color }}-900">{{ $value }}</p>
                <p class="ml-2 text-sm font-medium text-{{ $color }}-700">{{ $percentage }}%</p>
            </div>
        </div>
    </div>
</div>