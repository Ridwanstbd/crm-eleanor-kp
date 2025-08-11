{{-- components/Elements/Table/th.blade.php --}}
@props(['sortable' => false, 'direction' => null])

<th {{ $attributes->merge(['class' => 'px-4 py-3 bg-gray-50 text-left']) }}>
    @if($sortable)
        <div class="flex items-center gap-2 cursor-pointer group">
            {{ $slot }}
            <span class="text-gray-400 group-hover:text-gray-600">
                @if($direction === 'asc')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                @elseif($direction === 'desc')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                @else
                    <svg class="w-4 h-4 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                @endif
            </span>
        </div>
    @else
        {{ $slot }}
    @endif
</th>