<footer class=" py-8 mt-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div class="space-y-6">
                <x-Atoms.Link href="/">
                <div class="flex items-center">
                        {{-- <img src="{{ asset('assets/img/logo.png') }}" alt="{{ $system->name }}" class="h-12">
                        <span class="text-xl font-semibold ml-2">{{ $system->name }}</span> --}}
                    </div>
                </x-Atoms.Link>
                <p class="text-gray-600">
                    {{-- {{ $system->slogan }} --}}
                </p>
                
                <div class="space-y-2">
                    <p class="text-gray-600 font-medium">Email di</p>
                    {{-- <a href="mailto:{{ $system->email }}" class="text-gray-700 hover:text-emerald-600">
                        {{ $system->email }}
                    </a> --}}
                </div>
                
                <div class="space-y-2">
                    <p class="text-gray-600 font-medium">Social</p>
                    <a href="#" class="inline-block text-gray-600 hover:text-emerald-600">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="space-y-6">
                <div class="space-y-2">
                    <p class="text-gray-600 font-medium">Alamat</p>
                    <p class="text-gray-600">
                        {{-- {{ $system->address }} --}}
                    </p>
                </div>
                
                <div class="space-y-2">
                    <p class="text-gray-600 font-medium">Kontak</p>
                    {{-- <a href="https://wa.me/{{ $system->phone }}" class="flex items-center text-gray-600 hover:text-emerald-600">
                        <img src="{{ asset('assets/img/whatsapp.png') }}" alt="{{ $system->name }}" class="w-10 h-10">
                        {{ $system->phone }}
                    </a> --}}
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-200 mt-8 pt-6 text-center">
            <p class="text-gray-600">
                2025 © Prompt. All rights reserved. Created By 
            </p>
        </div>
    </div>
</footer>