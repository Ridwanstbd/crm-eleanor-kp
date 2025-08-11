<header 
    :class="$store.sidebar.isOpen ? 'ml-64' : 'ml-16'"
    class="fixed top-0 right-0 left-0 z-10 transition-all duration-300"
>
    <div class="bg-white py-2 mx-4 mt-2 rounded-lg flex items-center justify-between">
        {{-- Left side - Sidebar Toggle --}}
        <div class="flex items-center pl-4">
            <button @click="$store.sidebar.toggle()" class="p-2  rounded-lg transition-colors duration-200">
                <svg x-show="!$store.sidebar.isOpen" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sidebar">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                </svg>
                <svg x-show="$store.sidebar.isOpen" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sidebar">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                </svg>
            </button>
        </div>

        {{-- Right side - Profile Dropdown --}}
        <div class="flex items-center gap-4">
            <div x-data="{ isOpen: false }" class="relative pr-4">
                <button 
                    @click="isOpen = !isOpen"
                    class="flex items-center gap-2 p-2 rounded-lg transition-colors duration-200"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-round-icon lucide-circle-user-round">
                        <path d="M18 20a6 6 0 0 0-12 0"/>
                        <circle cx="12" cy="10" r="4"/>
                        <circle cx="12" cy="12" r="10"/>
                    </svg>
                </button>
                
                <div 
                    x-show="isOpen" 
                    @click.away="isOpen = false"
                    x-transition
                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2"
                >
                    <x-Atoms.Link href="{{ route('profile.edit') }}" class="block px-4 py-2">
                        Profile
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