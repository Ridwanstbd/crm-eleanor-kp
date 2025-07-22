<x-Layouts.AuthLayout>
    <x-Layouts.FormAuthContainer title="Masuk">
        <form method="POST" action="{{ route('login') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <x-Fragments.Form.EmailInput 
                name="email"
                id="email"
                placeholder="boypamitdangdutan@gmail.com"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                required
            />

            <x-Fragments.Form.PasswordInput 
                name="password"
                id="password"
                placeholder="Password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors pr-12"
                required
            />

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" id="remember_me" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <span class="ml-2 text-sm text-gray-600">Ingat Saya</span>
                </label>
                <x-Elements.Link href="{{ route('password.request')}}" class="text-sm text-red-600 hover:text-red-700">
                    Lupa Password
                </x-Elements.Link>
            </div>

            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Masuk
            </button>
        </form>
    </x-Layouts.FormAuthContainer>
</x-Layouts.AuthLayout>