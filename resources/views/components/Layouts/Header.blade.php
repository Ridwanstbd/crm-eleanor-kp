<header 
    :class="$store.sidebar.isOpen ? 'ml-64' : 'ml-16'"
    class="fixed top-0 right-0 left-0 z-10 transition-all duration-300"
>
    <div class="bg-white py-2 mx-4 mt-2 rounded-lg flex items-center justify-between">
        {{-- Left side - Sidebar Toggle --}}
        <div class="flex items-center pl-4">
            <button @click="$store.sidebar.toggle()" class="p-2  rounded-lg transition-colors duration-200">
                <svg x-show="!$store.sidebar.isOpen" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-panel-left-open-icon lucide-panel-left-open"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/><path d="m14 9 3 3-3 3"/></svg>
                <svg x-show="$store.sidebar.isOpen" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-panel-left-close-icon lucide-panel-left-close"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/><path d="m16 15-3-3 3-3"/></svg>
            </button>
        </div>

        {{-- Right side - Profile Dropdown --}}
        <div class="flex items-center gap-4">
            <div x-data="{ isOpen: false }" class="relative pr-4">
                <button 
                    @click="isOpen = !isOpen"
                    class="flex items-center gap-2 p-2 rounded-lg transition-colors duration-200"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings-icon lucide-settings"><path d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
                
                <div 
                    x-show="isOpen" 
                    @click.away="isOpen = false"
                    x-transition
                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2"
                >
                    <x-Atoms.Link href="{{ route('profile.edit') }}" class="block px-4 py-2">
                        Pengaturan
                    </x-Atoms.Link>
                    <x-Atoms.Link href="{{ route('admin.settings') }}" class="block px-4 py-2">
                        Informasi Sistem
                    </x-Atoms.Link>
                    <hr class="my-2">
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>