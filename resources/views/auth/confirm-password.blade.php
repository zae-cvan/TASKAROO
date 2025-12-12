<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            
            <label class="flex items-center gap-2 mt-2 text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" id="togglePassword" class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500 cursor-pointer">
                <span>Show Password</span>
            </label>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<script>
    // Password toggle functionality
    const togglePasswordCheckbox = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (togglePasswordCheckbox && passwordInput) {
        togglePasswordCheckbox.addEventListener('change', (e) => {
            passwordInput.type = e.target.checked ? 'text' : 'password';
        });
    }
</script>
