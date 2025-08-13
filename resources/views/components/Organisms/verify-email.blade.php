<x-Layouts.AuthLayout >
    <x-Layouts.FormAuthContainer title="Verifikasi Email">
        <div class="mb-4 text-sm text-gray-600">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </div>
        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif
            <div class="mt-4 flex flex-row items-center justify-center">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-Atoms.ButtonSubmit>
                        {{ __('Resend Verification Email') }}
                    </x-Atoms.ButtonSubmit>        
                    
                </form>
        
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-Atoms.ButtonSubmit>
                        {{ __('Log Out') }}
                    </x-Atoms.ButtonSubmit>
                </form>
            </div>
    </x-Layouts.FormAuthContainer>
</x-Layouts.AuthLayout>
