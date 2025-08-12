<section class="py-4">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Perbarui Kata Sandi') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')
        <x-Molecules.Form.FormGroup :message="$errors->updatePassword->get('current_password')" :label="__('Kata Sandi Saat ini')" for="update_password_current_password">
            <x-Atoms.Form.Input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"/>
        </x-Molecules.Form.FormGroup>
        <x-Molecules.Form.FormGroup :message="$errors->updatePassword->get('password')" :label="__('Kata Sandi Baru')" for="update_password_password">
            <x-Atoms.Form.Input id="update_password_password" name="password" type="password" autocomplete="new-password"/>
        </x-Molecules.Form.FormGroup>
        <x-Molecules.Form.FormGroup :message="$errors->updatePassword->get('password_confirmation')" :label="__('Konfirmasi Kata Sandi')" for="update_password_password_confirmation">
            <x-Atoms.Form.Input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"/>
        </x-Molecules.Form.FormGroup>

        <div class="flex items-center gap-4">
            <x-Atoms.Button variant="primary" type="submit">
                {{ __('Simpan') }}
            </x-Atoms.Button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>