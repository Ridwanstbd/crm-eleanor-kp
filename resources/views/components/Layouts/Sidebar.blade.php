{{-- components/Fragments/Sidebar.blade.php --}}
@props(['menuItems'])

<aside :class="$store.sidebar.isOpen ? 'w-64' : 'w-16'" class="fixed top-0 left-0 h-full bg-white shadow-lg transition-all duration-300">
    <div class="flex items-center gap-2 px-4 py-6">
        <img src="{{ asset('assets/img/logo.png') }}" width="120" alt="Logo">
        <h1 x-show="$store.sidebar.isOpen" class="font-bold text-xl"> CRM</h1>
    </div>

    <nav class="p-2 mt-5">
        @foreach ($menuItems as $item)
            @php
                $isActive = request()->routeIs($item['route']) || 
                           request()->routeIs($item['route'] . '.*') ||
                           (isset($item['activeRoutes']) && collect($item['activeRoutes'])->contains(fn($route) => request()->routeIs($route)));
            @endphp
            
            <x-Elements.NavLink 
                href="{{ route($item['route']) }}"
                :active="$isActive"
            >
                <svg
                    class="w-6 h-6 text-gray-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="{{ $item['icon'] }}"
                    />
                </svg>
                <span x-show="$store.sidebar.isOpen" class="ml-3 text-gray-700">
                    {{ $item['text'] }}
                </span>
            </x-Elements.NavLink>
        @endforeach
    </nav>
</aside>