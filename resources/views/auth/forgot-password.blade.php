<x-Layouts.AuthLayout>
    <x-Layouts.FormAuthContainer title="Lupa Password">
        <div class="text-md text-gray-600">
            {{ __('Lupa kata sandi Anda? Tidak masalah. Cukup beri tahu kami alamat email Anda dan kami akan mengirimkan email berisi tautan pengaturan ulang kata sandi sehingga Anda dapat memilih yang baru.') }}
        </div>
        <!-- Session Status -->
        <x-Elements.AuthSessionStatus class="mb-4" :status="session('status')" />
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <x-Molecules.Form.FormGroup :message="$errors->get('email')" label="Email" for="email">
                <x-Elements.Form.Input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            </x-Molecules.Form.FormGroup>
               
        </form>
    </x-Layouts.FormAuthContainer>
</x-Layouts.AuthLayout>
