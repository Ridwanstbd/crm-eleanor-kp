<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-Atoms.Head :title="$title ?? 'Admin Dashboard'" />
<body class="bg-gray-200">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebar', {
                isOpen: window.innerWidth >= 768,
                toggle() {
                    this.isOpen = !this.isOpen;
                }
            });

            Alpine.store('navigation', {
                currentRoute: '{{ Route::currentRouteName() }}',
                currentUrl: '{{ url()->current() }}',
                isActive(routeName, activeRoutes = []) {
                    if (this.currentRoute === routeName) return true;
                    
                    if (this.currentRoute.startsWith(routeName + '.')) return true;
                    
                    if (activeRoutes.length > 0) {
                        return activeRoutes.some(route => {
                            return this.currentRoute === route || this.currentRoute.startsWith(route + '.');
                        });
                    }
                    
                    return false;
                }
            });

            window.addEventListener('resize', () => {
                Alpine.store('sidebar').isOpen = window.innerWidth >= 768;
            });

            Alpine.store('system', @json($system ?? []));
        });
    </script>

    @php
        $jsonPath = public_path('json/menu-items.json');
        $menuItems = json_decode(file_get_contents($jsonPath), true)['menuItems'];
        $system = [];
    @endphp

    <div x-data="{ isOpen: true }">
        <x-Layouts.Sidebar :menuItems="$menuItems" />
        <x-Layouts.Header />
        <main 
            :class="$store.sidebar.isOpen ? 'ml-64' : 'ml-16'" 
            class="transition-all duration-300"
        >
            <div class="pt-20 px-4">
                <div class="bg-white p-4 px-10 mx-auto rounded-lg shadow">
                    {{$slot}}
                </div>
            </div>
        </main>
    </div>
    
    @if(session('success'))
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 3000)"
         class="fixed top-4 right-4 z-50 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg"
         role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }"
            x-show="show"
            class="fixed top-4 right-2 z-50 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg"
            role="alert">
            {{-- <p>Terjadi kesalahan sistem.<br/> Silakan coba lagi atau hubungi administrator. </p> --}}
            <p>Terjadi kesalahan sistem. <br /> {{ session('error' )}} </p>
            <button @click="show = false" class="mt-2 text-sm text-red-500 underline">
                Close
            </button>
        </div>
    @endif
    @stack('scripts')
</body>
</html>
