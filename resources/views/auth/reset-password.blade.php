<x-guest-layout>
    <h2 class="text-2xl font-bold mb-4 text-center">Reset Password</h2>

    <form method="POST" action="{{ route('password.reset') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="otp" value="{{ $otp }}">

        <label class="block mb-2 font-semibold">New Password</label>
        <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2 mb-2" required>
        <label class="flex items-center gap-2 mb-4 text-sm text-gray-600 cursor-pointer">
            <input type="checkbox" id="togglePassword" class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500 cursor-pointer">
            <span>Show Password</span>
        </label>

        <label class="block mb-2 font-semibold">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border rounded px-3 py-2 mb-2" required>
        <label class="flex items-center gap-2 mb-4 text-sm text-gray-600 cursor-pointer">
            <input type="checkbox" id="togglePasswordConfirmation" class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500 cursor-pointer">
            <span>Show Password</span>
        </label>

        @error('password')
            <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
        @enderror

        <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600 transition">
            Reset Password
        </button>
    </form>
</x-guest-layout>

<script>
    // Password toggle functionality
    function setupPasswordToggle(toggleCheckboxId, passwordInputId) {
        const toggleCheckbox = document.getElementById(toggleCheckboxId);
        const passwordInput = document.getElementById(passwordInputId);
        
        if (toggleCheckbox && passwordInput) {
            toggleCheckbox.addEventListener('change', (e) => {
                passwordInput.type = e.target.checked ? 'text' : 'password';
            });
        }
    }
    
    // Setup toggles for both password fields
    setupPasswordToggle('togglePassword', 'password');
    setupPasswordToggle('togglePasswordConfirmation', 'password_confirmation');
</script>
