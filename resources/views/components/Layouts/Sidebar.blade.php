@props(['menuItems'])

<!-- Container Sidebar dengan animasi -->
<aside :class="$store.sidebar.isOpen ? 'w-64' : 'w-16'" 
       class="fixed top-0 left-0 h-full bg-gray-500 shadow-lg transition-all duration-300">
    
    <!-- Header Sidebar dengan Logo -->
    <div class="flex items-center gap-2 px-4 py-6">
        <img src="{{ asset('assets/img/logo.png') }}" width="120" alt="Logo">
        <h1 x-show="$store.sidebar.isOpen" class="font-bold text-xl text-white"> CRM</h1>
    </div>

    <!-- Menu Navigasi -->
    <nav class="pl-2 mt-3">
        @foreach ($menuItems as $item)
            @php
                // Deteksi menu aktif berdasarkan route
                $isActive = request()->routeIs($item['route']) || 
                           request()->routeIs($item['route'] . '.*') ||
                           (isset($item['activeRoutes']) && 
                            collect($item['activeRoutes'])->contains(
                                fn($route) => request()->routeIs($route)
                            ));
            @endphp
            
            <!-- Link Menu dengan komponen Atom NavLink -->
            <x-Atoms.NavLink 
                href="{{ route($item['route']) }}"
                :active="$isActive" 
            >
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                </svg>
                <span x-show="$store.sidebar.isOpen" class="ml-3 text-white">
                    {{ $item['text'] }}
                </span>
            </x-Atoms.NavLink>
        @endforeach
    </nav>
    
    <!-- Form Logout (Selalu di bagian bawah) -->
    <form action="{{ route('logout') }}" method="POST" class="block absolute bottom-1 w-full px-1">
        @csrf
        <x-Atoms.Button variant="submit" fullWidth class="flex">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="lucide lucide-log-out">
                <path d="m16 17 5-5-5-5"/>
                <path d="M21 12H9"/>
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            </svg>
            <span x-show="$store.sidebar.isOpen" class="ml-2">Keluar</span>
        </x-Atoms.Button>
    </form>
</aside>