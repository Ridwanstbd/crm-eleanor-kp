{{-- components/Fragments/Table/Pagination.blade.php --}}
@props(['paginator'])

@if ($paginator->hasPages())
    <div class="px-4 py-3 bg-gray-50 border-t sm:px-6">
        <div class="flex items-center justify-between">
            <div class="hidden sm:block">
                <p class="text-sm text-gray-700">
                    Menampilkan
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    hasil
                </p>
            </div>
            
            <div class="flex justify-between flex-1 sm:justify-end space-x-2">
                @if ($paginator->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 border border-gray-300 cursor-default rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left-icon lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700  border border-gray-300 rounded-md hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left-icon lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>
                    </a>
                @endif

                @php
                    $current = $paginator->currentPage();
                    $last = $paginator->lastPage();
                    
                    if ($last <= 3) {
                        $start = 1;
                        $end = $last;
                    } else {
                        if ($current <= 2) {
                            $start = 1;
                            $end = 3;
                        } elseif ($current >= $last - 1) {
                            $start = $last - 2;
                            $end = $last;
                        } else {
                            $start = $current - 1;
                            $end = $current + 1;
                        }
                    }
                @endphp

                {{-- Show first page and ellipsis if needed --}}
                @if ($start > 1)
                    <a href="{{ $paginator->url(1) }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        1
                    </a>
                    @if ($start > 2)
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                            ...
                        </span>
                    @endif
                @endif

                {{-- Show page numbers in range --}}
                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $current)
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 cursor-default rounded-md">
                            {{ $i }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($i) }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            {{ $i }}
                        </a>
                    @endif
                @endfor

                {{-- Show ellipsis and last page if needed --}}
                @if ($end < $last)
                    @if ($end < $last - 1)
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                            ...
                        </span>
                    @endif
                    <a href="{{ $paginator->url($last) }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        {{ $last }}
                    </a>
                @endif

                {{-- Next Button --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right-icon lucide-chevron-right"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                @else
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 border border-gray-300 cursor-default rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right-icon lucide-chevron-right"><path d="m9 18 6-6-6-6"/></svg>
                    </span>
                @endif
            </div>
        </div>
    </div>
@endif