@props([
    'name' => 'password',
    'id' => 'password',
    'label' => 'Kata Sandi',
    'placeholder' => 'password'
])

<div>
    <x-Atoms.Label for="{{$id}}">
        {{$label}}
    </x-Atoms.Label>
    
    <div class="relative">
        <x-Atoms.Form.Input 
            type="password"
            :id="$id"
            :name="$name"
            :placeholder="$placeholder"
            class="pr-10"
            {{ $attributes }}
        />
        
        <button 
            type="button"
            class="absolute inset-y-0 right-0 flex items-center pr-3"
            onclick="togglePasswordVisibility('{{$id}}')"
        >
            <svg id="eye-{{$id}}" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <svg id="eye-slash-{{$id}}" class="w-5 h-5 text-gray-400 hover:text-gray-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
            </svg>
        </button>
    </div>
    
    <x-Atoms.Form.InputError :message="$errors->first($name)" />
</div>

<script>
function togglePasswordVisibility(inputId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = document.getElementById('eye-' + inputId);
    const eyeSlashIcon = document.getElementById('eye-slash-' + inputId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeSlashIcon.classList.remove('hidden');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeSlashIcon.classList.add('hidden');
    }
}
</script>