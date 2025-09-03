<x-Layouts.AuthLayout>
    <x-Layouts.FormAuthContainer title="Lupa Kata Sandi">
        <div class="text-md max-w-[13.7rem] w-full text-gray-600 mb-4">
            <p>Lupa kata sandi Anda? Tidak masalah.</p>
            {{ __('Cukup beri tahu kami alamat email Anda dan kami akan mengirimkan email berisi tautan pengaturan ulang kata sandi.') }}
        </div>
        <!-- Session Status -->
        <x-Atoms.AuthSessionStatus class="mb-4" :status="session('status')" />
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <x-Molecules.Form.FormGroup :message="$errors->get('email')" label="Email" for="email">
                <x-Atoms.Form.Input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="test@gmail.com + (klik enter)" required autofocus />
            </x-Molecules.Form.FormGroup>
               
        </form>
    </x-Layouts.FormAuthContainer>
</x-Layouts.AuthLayout>
