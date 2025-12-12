<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@section('scripts')
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
    
    // Setup toggles for update password form
    setupPasswordToggle('toggleCurrentPassword', 'update_password_current_password');
    setupPasswordToggle('toggleNewPassword', 'update_password_password');
    setupPasswordToggle('toggleConfirmPassword', 'update_password_password_confirmation');
    
    // Setup toggle for delete user password
    setupPasswordToggle('toggleDeleteUserPassword', 'password');
</script>
@endsection
