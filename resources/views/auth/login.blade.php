<x-guest-layout>
      <!-- Close button to go back to landing page -->
<a href="{{ route('landing') }}" aria-label="Close and return to landing"
   class="absolute right-3 -top-3 z-50 text-gray-700 hover:text-orange-600 transition-colors p-2 rounded-full bg-white shadow-sm">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
    </svg>
</a>

<!-- Card wrapper -->
<div class="w-full max-w-sm mx-auto bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg relative">

    <!-- Welcome / Login Title -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Welcome Back</h2>
        <p class="text-gray-600 text-sm mt-1">Log in to continue</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

            <div>
                <label class="block mb-1 text-gray-700 font-semibold flex items-center gap-2">
                    <i data-lucide="mail" class="w-5 h-5 text-orange-600"></i>
                    Email Address
                </label>
                <input type="email" name="email" value="{{ old('email') }}" class="border-2 border-orange-200 p-2 w-full rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300" placeholder="Enter your email..." required autofocus>
                @error('email') <p class="text-red-500 text-sm mt-2 font-semibold flex items-center gap-1"><i data-lucide="alert-circle" class="w-4 h-4"></i>{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 text-gray-700 font-semibold flex items-center gap-2">
                    <i data-lucide="key" class="w-5 h-5 text-orange-600"></i>
                    Password
                </label>
                <input type="password" name="password" id="password" class="border-2 border-orange-200 p-2 w-full rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300" placeholder="Enter your password..." required>
                <label class="flex items-center gap-2 mt-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" id="togglePassword" class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500 cursor-pointer">
                    <span>Show Password</span>
                </label>
                @error('password') <p class="text-red-500 text-sm mt-2 font-semibold flex items-center gap-1"><i data-lucide="alert-circle" class="w-4 h-4"></i>{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center text-sm text-gray-600 font-medium cursor-pointer">
                    <input type="checkbox" name="remember" class="mr-2 w-4 h-4 accent-orange-600 rounded cursor-pointer">
                    Remember me
                </label>
                @if (Route::has('forgot.password'))
                    <a href="{{ route('forgot.password') }}" class="text-orange-600 hover:text-orange-700 font-semibold transition-colors">Forgot Password?</a>
                @endif
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white p-2 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300 mt-4 inline-flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                <i data-lucide="log-in" class="w-5 h-5"></i>
                Login
            </button>

            <div class="text-center pt-4 border-t-2 border-orange-100">
                <p class="text-gray-600 text-sm mb-2">New here?</p>
                <a href="{{ route('register') }}" class="text-orange-600 hover:text-orange-700 font-semibold transition-colors flex items-center justify-center gap-2 hover:underline text-sm">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Create an account
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>

<script>
    const togglePasswordCheckbox = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (togglePasswordCheckbox && passwordInput) {
        togglePasswordCheckbox.addEventListener('change', (e) => {
            passwordInput.type = e.target.checked ? 'text' : 'password';
        });
    }
</script>
