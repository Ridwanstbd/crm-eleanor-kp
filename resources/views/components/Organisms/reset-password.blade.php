<x-Layouts.AuthLayout>
    <x-Layouts.FormAuthContainer title="Reset Pasword">
        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <x-Molecules.Form.FormGroup :message="$errors->get('email')" label="__('Email')" for="email">
                <x-Atoms.Form.Input id="email" type="email" name="email" value="old('email', $request->email)" required autofocus autocomplete="username" />
            </x-Molecules.Form.FormGroup>
            <x-Molecules.Form.FormGroup :message="$errors->get('password')" label="__('Password')" for="password">
                <x-Atoms.Form.Input id="password" type="password" name="password" required autocomplete="new-password" />
            </x-Molecules.Form.FormGroup>
            <x-Molecules.Form.FormGroup :message="$errors->get('password_confirmation')" label="__('Confirm Password')" for="password_confirmation">
                <x-Atoms.Form.Input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            </x-Molecules.Form.FormGroup>
            <x-Atoms.Form.ButtonSubmit>
                {{ __('Reset Password') }}
            </x-Atoms.Form.ButtonSubmit> 
        </form>
    </x-Layouts.FormAuthContainer>
</x-Layouts.AuthLayout>
