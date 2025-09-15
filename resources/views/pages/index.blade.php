<x-Layouts.AuthLayout>
    <x-Layouts.FormAuthContainer title="Masuk">
        <form method="POST" action="{{ route('login') }}" enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
            @csrf
            <x-Molecules.Form.EmailInput
                name="email"
                id="email"
                placeholder="eleanordigital@gmail.com"
                required
            />
            <x-Molecules.Form.PasswordInput
                name="password"
                id="password"
                placeholder="Password"
                required
            />
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" id="remember_me" class="w-3 h-3 sm:w-4 sm:h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <span class="ml-2 text-gray-600">Ingat Saya</span>
                </label>
                <x-Atoms.Link href="{{ route('password.request')}}" class="text-red-600 hover:text-red-700">
                    Lupa Password
                </x-Atoms.Link>
            </div>
            <x-Atoms.Button variant="submit" fullWidth>Masuk</x-Atoms.Button>
        </form>
    </x-Layouts.FormAuthContainer>
</x-Layouts.AuthLayout>