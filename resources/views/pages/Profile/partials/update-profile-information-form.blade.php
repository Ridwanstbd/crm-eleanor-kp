<section class="py-4">
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <x-Molecules.Form.FormGroup :message="$errors->get('name')" :label="__('Name')" for="name">
            <x-Atoms.Form.Input name="name" id="name" :value="old('name', $user->name)" required autofocus autocomplete="name"/>
        </x-Molecules.Form.FormGroup>

        <x-Molecules.Form.FormGroup :message="$errors->get('email')" :label="__('Email')" for="email">
            <x-Atoms.Form.Input id="email" type="email" name="email" :value="old('email', $user->email)" required autofocus autocomplete="username" />
            
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </x-Molecules.Form.FormGroup>

        <x-Molecules.Form.FormGroup :message="$errors->get('fonnte_token')" :label="__('Fonnte Token')" for="fonnte_token">
            <x-Atoms.Form.Input name="fonnte_token" id="fonnte_token" :value="old('fonnte_token', $user->fonnte_token)" required autofocus autocomplete="fonnte_token"/>
        </x-Molecules.Form.FormGroup>
        <x-Molecules.Form.FormGroup :message="$errors->get('delay_message')" :label="__('Tunda Pesan /detik')" for="delay_message">
            <x-Atoms.Form.Input name="delay_message" type="number" id="delay_message" :value="old('delay_message', $user->delay_message)" required autofocus autocomplete="delay_message"/>
        </x-Molecules.Form.FormGroup>
        <x-Molecules.Form.FormGroup :message="$errors->get('delay_message')" :label="__('URL Webhook untuk pembaruan status pesan')" for="delay_message">
             <p class="text-gray-900" >https://crm.eleanordigital.com/webhook/update-status</p>
        </x-Molecules.Form.FormGroup>

        <div class="flex items-center gap-4">
            <x-Atoms.Button variant="primary" type="submit">
                {{ __('Simpan') }}
            </x-Atoms.Button>

            @if (session('status') === 'profile-updated')
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