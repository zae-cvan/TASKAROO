<x-guest-layout>
    <div class="w-full max-w-sm mx-auto bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg relative">
            <!-- Close button to go back to landing page -->
            <a href="{{ route('landing') }}" aria-label="Close and return to landing" class="absolute right-3 -top-3 z-50 text-gray-700 hover:text-orange-600 transition-colors p-2 rounded-full bg-white shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </a>

            <div class="text-center mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-md mx-auto mb-3">
                    <i data-lucide="user-plus" class="w-6 h-6 text-white"></i>
                </div>
                <h1 class="text-2xl font-semibold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent mb-1">Join Taskaroo</h1>
                <p class="text-gray-600 text-sm">Create your account to get started</p>
            </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1 text-gray-700 font-semibold flex items-center gap-2">
                    <i data-lucide="user" class="w-5 h-5 text-orange-600"></i>
                    Full Name
                </label>
                <input type="text" name="name" value="{{ old('name') }}" class="border-2 border-orange-200 p-2 w-full rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300" placeholder="Enter your name..." required autofocus>
                @error('name') <p class="text-red-500 text-sm mt-2 font-semibold flex items-center gap-1"><i data-lucide="alert-circle" class="w-4 h-4"></i>{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 text-gray-700 font-semibold flex items-center gap-2">
                    <i data-lucide="mail" class="w-5 h-5 text-orange-600"></i>
                    Email Address
                </label>
                <input type="email" name="email" value="{{ old('email') }}" class="border-2 border-orange-200 p-2 w-full rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300" placeholder="Enter your email..." required>
                @error('email') <p class="text-red-500 text-sm mt-2 font-semibold flex items-center gap-1"><i data-lucide="alert-circle" class="w-4 h-4"></i>{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 text-gray-700 font-semibold flex items-center gap-2">
                    <i data-lucide="key" class="w-5 h-5 text-orange-600"></i>
                    Password
                </label>
                <input type="password" name="password" id="password" class="border-2 border-orange-200 p-2 w-full rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300" placeholder="Create a strong password..." required>
                <label class="flex items-center gap-2 mt-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" id="togglePassword" class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500 cursor-pointer">
                    <span>Show Password</span>
                </label>
                @error('password') <p class="text-red-500 text-sm mt-2 font-semibold flex items-center gap-1"><i data-lucide="alert-circle" class="w-4 h-4"></i>{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 text-gray-700 font-semibold flex items-center gap-2">
                    <i data-lucide="key" class="w-5 h-5 text-orange-600"></i>
                    Confirm Password
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="border-2 border-orange-200 p-2 w-full rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300" placeholder="Confirm your password..." required>
                <label class="flex items-center gap-2 mt-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" id="togglePasswordConfirmation" class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500 cursor-pointer">
                    <span>Show Password</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white p-2 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300 mt-4 inline-flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
                Create Account
            </button>

            <div class="text-center pt-4 border-t-2 border-orange-100">
                <p class="text-gray-600 text-sm mb-2">Already have an account?</p>
                <a href="{{ route('login') }}" class="text-orange-600 hover:text-orange-700 font-semibold transition-colors flex items-center justify-center gap-2 hover:underline text-sm">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    Login here
                </a>
            </div>
        </form>
    </div>
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
