<x-guest-layout>
    <h2 class="text-2xl font-bold mb-4 text-center">Verify OTP</h2>

    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">

        <label class="block mb-2 font-semibold">Enter OTP</label>
        <input type="text" name="otp" class="w-full border rounded px-3 py-2 mb-4" placeholder="6-digit OTP" required>

        @error('otp')
            <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
        @enderror

        <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600 transition">
            Verify OTP
        </button>
    </form>
</x-guest-layout>
