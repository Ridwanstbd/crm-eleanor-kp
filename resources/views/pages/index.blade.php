<x-Layouts.AuthLayout>
    <x-Layouts.FormAuthContainer title="Login to your Account">
        <form method="POST" action="{{ route('login') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <x-Fragments.Form.EmailInput 
                name="email"
                id="email"
                label="Email"
                placeholder="example@example.com"
                required
            />
            <x-Fragments.Form.PasswordInput 
                name="password"
                id="password"
                label="Password"
                placeholder="input password here"
                required
            />

            <div class="flex items-center justify-between">
                <x-Elements.Checkbox id="remember_me"name="remember"label="Remember Me"/>
                <div class="text-sm">
                    <x-Elements.Link href="{{ route('password.request')}}" >
                        Forgot your password?
                    </x-Elements.Link>
                </div>
            </div>

            <x-Elements.Form.ButtonSubmit type="submit">
                <h1>Login</h1>
            </x-Elements.Form.ButtonSubmit>
        </form>
        <x-Fragments.RegisterLink />
    </x-Layouts.FormAuthContainer>
</x-Layouts.AuthLayout>