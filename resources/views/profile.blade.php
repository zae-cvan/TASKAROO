@extends('layouts.app')

@section('content')
@php
    $authUser = auth()->user();
@endphp
<div class="max-w-6xl mx-auto">

    <!-- Page Header with Icon -->
    <div class="mb-8 flex items-center gap-4 bg-gradient-to-r from-white to-orange-50 rounded-3xl p-8 border-2 border-orange-100 shadow-xl">
        <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
            <i data-lucide="user-circle" class="w-8 h-8 text-white"></i>
        </div>
        <div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent">Account Settings</h1>
            <p class="text-gray-600 mt-1 font-medium">Manage your profile information and security settings</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main Profile Card -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Profile Information Card -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border-2 border-orange-100">
                <!-- Profile Header -->
                <div class="bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
                    <div class="relative flex items-center gap-6">
                        @if($authUser->profile_photo)
                            <div class="w-24 h-24 overflow-hidden rounded-2xl shadow-2xl ring-4 ring-white/50 bg-white">
                                <img src="{{ asset('storage/' . $authUser->profile_photo) }}" alt="Profile" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-24 h-24 bg-white rounded-2xl flex items-center justify-center text-4xl font-bold text-orange-600 shadow-2xl ring-4 ring-white/50">
                                {{ strtoupper(substr($authUser->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="text-white">
                            <h2 class="text-3xl font-bold mb-1">{{ $authUser->name }}</h2>
                            <p class="text-white flex items-center gap-2">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                                {{ $authUser->email }}
                            </p>
                            <span class="inline-flex items-center gap-1 mt-3 bg-white/20 backdrop-blur px-4 py-1.5 rounded-full text-sm font-semibold">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Active Account
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Profile Info -->
                <div id="profile-view" class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i data-lucide="user" class="w-5 h-5 text-orange-500"></i>
                            Profile Information
                        </h3>
                        <button id="editProfileBtn" 
                                class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 inline-flex items-center gap-2 shadow-md hover:shadow-lg hover:scale-105 active:scale-95 font-semibold">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                            Edit Profile
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-orange-50 to-orange-50 rounded-2xl border-2 border-orange-100 hover:border-orange-200 transition-all duration-300 shadow-sm hover:shadow-md">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                <i data-lucide="user" class="w-6 h-6 text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 mb-1 font-medium">Full Name</p>
                                <p class="text-gray-900 font-bold text-lg">{{ $authUser->name }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-orange-50 to-orange-50 rounded-2xl border-2 border-orange-100 hover:border-orange-200 transition-all duration-300 shadow-sm hover:shadow-md">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                <i data-lucide="mail" class="w-6 h-6 text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 mb-1 font-medium">Email Address</p>
                                <p class="text-gray-900 font-bold text-lg">{{ $authUser->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-orange-50 to-orange-50 rounded-2xl border-2 border-orange-100 hover:border-orange-200 transition-all duration-300 shadow-sm hover:shadow-md">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 mb-1 font-medium">Bio</p>
                                <p class="text-gray-900 font-bold text-lg">{{ $authUser->bio ?? 'No bio added yet' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div id="profile-edit" class="hidden p-8">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <label for="name" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                    <i data-lucide="user" class="w-4 h-4 text-orange-500"></i>
                                    Full Name
                                </label>
                                <input type="text" name="name" id="name" value="{{ $authUser->name }}"
                                       class="w-full border-2 border-orange-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                    <i data-lucide="mail" class="w-4 h-4 text-orange-500"></i>
                                    Email Address
                                </label>
                                <input type="email" name="email" id="email" value="{{ $authUser->email }}"
                                       class="w-full border-2 border-orange-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium">
                            </div>

                            <div>
                                <label for="bio" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                    <i data-lucide="file-text" class="w-4 h-4 text-orange-500"></i>
                                    Bio
                                </label>
                                <textarea name="bio" id="bio" rows="4" placeholder="Tell us about yourself..."
                                       class="w-full border-2 border-orange-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium resize-none">{{ $authUser->bio ?? '' }}</textarea>
                                <p class="text-xs text-gray-500 mt-2">Maximum 1000 characters</p>
                            </div>
                            
                            <div>
                                <label for="profile_photo" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                    <i data-lucide="image" class="w-4 h-4 text-orange-500"></i>
                                    Profile Photo
                                </label>
                                <div class="flex gap-4 items-start">
                                    <div class="flex-1">
                                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="w-full border-2 border-orange-200 rounded-xl px-4 py-3">
                                        <p class="text-xs text-gray-500 mt-2">Recommended: Square image, at least 200x200px</p>
                                    </div>
                                    <div id="photoPreview" class="flex-shrink-0 flex flex-col gap-2">
                                        @if($authUser->profile_photo)
                                            <div class="w-20 h-20 rounded-xl overflow-hidden shadow-lg ring-2 ring-orange-200">
                                                <img src="{{ asset('storage/' . $authUser->profile_photo) }}" alt="Current profile" class="w-full h-full object-cover">
                                            </div>
                                            <button type="button" id="removePhotoBtn" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition-colors duration-200 flex items-center justify-center gap-1 whitespace-nowrap">
                                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                                                Remove
                                            </button>
                                        @else
                                            <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center text-white font-bold text-2xl shadow-lg ring-2 ring-orange-200">
                                                {{ strtoupper(substr($authUser->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <input type="hidden" name="remove_profile_photo" id="removeProfilePhotoInput" value="0">
                            </div>
                        </div>

                        <div class="flex gap-3 mt-8">
                            <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 font-bold shadow-md hover:shadow-lg hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                                <i data-lucide="check" class="w-5 h-5"></i>
                                Save Changes
                            </button>
                            <button type="button" id="cancelEdit" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-300 font-bold hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                                <i data-lucide="x" class="w-5 h-5"></i>
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

         <!-- Change Password Modal Trigger -->
<div class="bg-white rounded-2xl shadow-lg p-8 border-2 border-orange-100 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-md">
            <i data-lucide="lock" class="w-6 h-6 text-white"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800">Change Password</h3>
    </div>
    <button id="openChangePasswordModal" class="px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl shadow hover:from-orange-600 hover:to-orange-700 transition">
        Change
    </button>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl w-11/12 max-w-md p-6 space-y-6 shadow-lg">
        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="lock" class="w-6 h-6 text-orange-500"></i>
            Update Password
        </h3>

        <p id="passwordMessage" class="text-sm text-gray-600">We'll send an OTP to your email to verify it's you.</p>

        <form id="changePasswordForm" method="POST" action="{{ route('password.changeWithOtpDb') }}" class="space-y-4">
            @csrf

            <!-- OTP Input -->
            <div id="otpDiv" class="hidden">
                <label class="block text-sm font-semibold text-gray-700">OTP</label>
                <input type="text" name="otp" id="otpInput" placeholder="Enter 6-digit OTP"
                       class="w-full border-2 border-orange-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition" required>
            </div>

            <!-- New Password -->
            <div id="passwordDiv" class="hidden space-y-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">New Password</label>
                    <input type="password" name="password" id="changePassword" placeholder="Enter new password"
                           class="w-full border-2 border-orange-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition" required>
                    <label class="flex items-center gap-2 mt-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" id="toggleChangePassword" class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500 cursor-pointer">
                        <span>Show Password</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="changePasswordConfirmation" placeholder="Confirm new password"
                           class="w-full border-2 border-orange-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition" required>
                    <label class="flex items-center gap-2 mt-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" id="toggleChangePasswordConfirmation" class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500 cursor-pointer">
                        <span>Show Password</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-between items-center mt-4">
                <button type="button" id="sendOtpBtn" class="px-4 py-2 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition">Send OTP</button>
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition hidden">Update Password</button>
            </div>

            <p id="otpStatus" class="text-orange-600 text-sm hidden mt-2">OTP sent! Check your email.</p>
        </form>

        <button id="closeChangePasswordModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
    </div>
</div>

<!-- Success Modal -->
<div id="passwordSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl w-11/12 max-w-sm p-6 space-y-4 shadow-lg text-center">
        <h3 class="text-xl font-bold text-orange-600">Password Updated!</h3>
        <p class="text-gray-700 text-sm">Your password has been successfully changed.</p>
        <button id="closeSuccessModal" class="mt-4 px-6 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition font-bold">OK</button>
    </div>
</div>


  <!-- Sidebar with Stats & Danger Zone -->
        <div class="space-y-6">

            <!-- Account Stats Card -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl shadow-lg p-6 border-2 border-orange-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i data-lucide="activity" class="w-5 h-5 text-orange-500"></i>
                    Account Stats
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="calendar" class="w-5 h-5 text-orange-600"></i>
                            </div>
                            <span class="text-sm text-gray-600 font-medium">Member Since</span>
                        </div>
                        <span class="text-sm font-bold text-gray-800">{{ $authUser->created_at->format('M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-white rounded-xl shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="shield" class="w-5 h-5 text-orange-600"></i>
                            </div>
                            <span class="text-sm text-gray-600 font-medium">Account Status</span>
                        </div>
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">Active</span>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-red-200">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-red-600">Danger Zone</h3>
                </div>
                <p class="text-gray-600 text-sm mb-6 leading-relaxed">
                    Once you delete your account, there is no going back. All your data will be permanently removed.
                </p>
                <button type="button" id="openDeleteModal" 
                        class="w-full px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-300 font-bold shadow-md hover:shadow-lg hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                    Delete Account Forever
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl w-11/12 max-w-md p-6 space-y-4">
        <h3 class="text-xl font-bold text-red-600 flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            Confirm Account Deletion
        </h3>
        <p class="text-gray-700 text-sm">
            Are you absolutely sure you want to delete your account? This action cannot be undone.
        </p>

        <form id="deleteAccountFormModal" method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Confirm Password</label>
                <input type="password" name="password" id="deletePassword" required
                       class="w-full border-2 border-red-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300">
                <label class="flex items-center gap-2 mt-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" id="toggleDeletePassword" class="w-4 h-4 text-red-600 rounded focus:ring-red-500 cursor-pointer">
                    <span>Show Password</span>
                </label>
            </div>

            <!-- Optional Reason -->
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Reason for Leaving (Optional)</label>
                <textarea name="reason" rows="3" placeholder="Optional"
                          class="w-full border-2 border-red-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-300 resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" id="cancelDeleteModal" class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 font-bold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 font-bold">Delete</button>
            </div>
        </form>
    </div>
</div>

    <!-- Second Confirmation Modal -->
<div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl w-11/12 max-w-md p-6 space-y-4">
        <h3 class="text-xl font-bold text-red-600 flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            Confirm Deletion
        </h3>
        <p class="text-gray-700 text-sm">
            ⚠️ Are you absolutely sure you want to delete your account? This action CANNOT be undone.
        </p>
        <div class="flex justify-end gap-3">
            <button id="cancelConfirmDelete" class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 font-bold">Cancel</button>
            <button id="confirmDeleteBtn" class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 font-bold">Yes, Delete</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (window.lucide) lucide.createIcons();

    /* =========================
       Profile Edit Toggle
    ========================= */
    const viewDiv = document.getElementById('profile-view');
    const editDiv = document.getElementById('profile-edit');
    const editBtn = document.getElementById('editProfileBtn');
    const cancelBtn = document.getElementById('cancelEdit');

    if (editBtn && viewDiv && editDiv && cancelBtn) {
        editBtn.addEventListener('click', () => {
            viewDiv.classList.add('hidden');
            editDiv.classList.remove('hidden');
            if (window.lucide) lucide.createIcons();
        });
        cancelBtn.addEventListener('click', () => {
            editDiv.classList.add('hidden');
            viewDiv.classList.remove('hidden');
            if (window.lucide) lucide.createIcons();
        });
    }

    /* =========================
       Delete Account Modal
    ========================= */
    const openDeleteBtn = document.getElementById('openDeleteModal');
    const deleteModal = document.getElementById('deleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDeleteModal');
    const deleteFormModal = document.getElementById('deleteAccountFormModal');
    const confirmModal = document.getElementById('confirmDeleteModal');
    const cancelConfirmBtn = document.getElementById('cancelConfirmDelete');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    if (openDeleteBtn && deleteModal && cancelDeleteBtn) {
        openDeleteBtn.addEventListener('click', () => deleteModal.classList.remove('hidden'));
        cancelDeleteBtn.addEventListener('click', () => deleteModal.classList.add('hidden'));
        deleteModal.addEventListener('click', e => { if(e.target === deleteModal) deleteModal.classList.add('hidden'); });
    }

    if (deleteFormModal && confirmModal) {
        deleteFormModal.addEventListener('submit', function(e) {
            e.preventDefault();
            const password = deleteFormModal.querySelector('input[name="password"]').value;
            if (!password) {
                alert('⚠️ Please enter your password to confirm.');
                return;
            }
            confirmModal.classList.remove('hidden');
        });
    }

    if (cancelConfirmBtn && confirmModal) {
        cancelConfirmBtn.addEventListener('click', () => confirmModal.classList.add('hidden'));
    }

    if (confirmDeleteBtn && confirmModal && deleteModal && deleteFormModal) {
        confirmDeleteBtn.addEventListener('click', () => {
            confirmModal.classList.add('hidden');
            deleteModal.classList.add('hidden');
            deleteFormModal.submit();
        });
    }

    /* =========================
       Change Password Modal
    ========================= */
    const openPasswordModalBtn = document.getElementById('openChangePasswordModal');
    const changePasswordModal = document.getElementById('changePasswordModal');
    const closePasswordModalBtn = document.getElementById('closeChangePasswordModal');
    const changePasswordForm = document.getElementById('changePasswordForm');
    const passwordSuccessModal = document.getElementById('passwordSuccessModal');
    const closeSuccessBtn = document.getElementById('closeSuccessModal');

    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const otpDiv = document.getElementById('otpDiv');
    const passwordDiv = document.getElementById('passwordDiv');
    const submitBtn = document.getElementById('submitBtn');
    const otpStatus = document.getElementById('otpStatus');
    const passwordMessage = document.getElementById('passwordMessage');

    // Open & close modal
    if (openPasswordModalBtn && changePasswordModal) {
        openPasswordModalBtn.addEventListener('click', () => changePasswordModal.classList.remove('hidden'));
    }
    if (closePasswordModalBtn && changePasswordModal) {
        closePasswordModalBtn.addEventListener('click', () => changePasswordModal.classList.add('hidden'));
        changePasswordModal.addEventListener('click', e => { if(e.target === changePasswordModal) changePasswordModal.classList.add('hidden'); });
    }

    /* =========================
       OTP Send & Show Password Inputs
    ========================= */
    if (sendOtpBtn && otpDiv && passwordDiv && submitBtn && otpStatus && passwordMessage) {
        sendOtpBtn.addEventListener('click', async () => {
            sendOtpBtn.disabled = true;
            passwordMessage.textContent = 'Sending OTP...';
            try {
                const res = await fetch("{{ route('password.sendOtp') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({})
                });

                if(res.ok){
                    otpDiv.classList.remove('hidden');
                    passwordDiv.classList.remove('hidden');
                    submitBtn.classList.remove('hidden');
                    otpStatus.classList.remove('hidden');
                    passwordMessage.textContent = 'OTP sent! Enter it below along with your new password.';
                } else {
                    passwordMessage.textContent = 'Failed to send OTP. Try again.';
                    sendOtpBtn.disabled = false;
                }
            } catch(err) {
                passwordMessage.textContent = 'Error occurred. Check your connection.';
                sendOtpBtn.disabled = false;
                console.error(err);
            }
        });
    }

    /* =========================
       Submit Change Password
    ========================= */
    if (changePasswordForm && passwordSuccessModal) {
        changePasswordForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(changePasswordForm);

            try {
                const res = await fetch(changePasswordForm.action, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: formData
                });

                if(res.ok){
                    passwordSuccessModal.classList.remove('hidden');
                } else {
                    alert('Failed to update password. Please try again.');
                }
            } catch(err){
                alert('An error occurred. Please check your connection.');
                console.error(err);
            }
        });
    }

    /* =========================
       Close Success Modal
    ========================= */
    if (closeSuccessBtn && passwordSuccessModal && changePasswordModal && changePasswordForm) {
        closeSuccessBtn.addEventListener('click', () => {
            passwordSuccessModal.classList.add('hidden');
            changePasswordModal.classList.add('hidden');
            changePasswordForm.reset();
            otpDiv.classList.add('hidden');
            passwordDiv.classList.add('hidden');
            submitBtn.classList.add('hidden');
            otpStatus.classList.add('hidden');
            sendOtpBtn.disabled = false;
            passwordMessage.textContent = "We'll send an OTP to your email to verify it's you.";
        });
    }

    /* =========================
       Profile Photo Preview
    ========================= */
    const photoInput = document.getElementById('profile_photo');
    const photoPreview = document.getElementById('photoPreview');
    
    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    this.value = '';
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size must be less than 5MB.');
                    this.value = '';
                    return;
                }
                
                // Create preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    photoPreview.innerHTML = `
                        <div class="w-20 h-20 rounded-xl overflow-hidden shadow-lg ring-2 ring-orange-200">
                            <img src="${event.target.result}" alt="Preview" class="w-full h-full object-cover">
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    /* =========================
       Remove Profile Photo Button
    ========================= */
    const removePhotoBtn = document.getElementById('removePhotoBtn');
    const removeProfilePhotoInput = document.getElementById('removeProfilePhotoInput');
    
    if (removePhotoBtn && photoPreview && removeProfilePhotoInput) {
        removePhotoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Set the hidden input to indicate removal
            removeProfilePhotoInput.value = '1';
            
            // Update preview to show initial
            const userInitial = '{{ strtoupper(substr($authUser->name, 0, 1)) }}';
            photoPreview.innerHTML = `
                <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center text-white font-bold text-2xl shadow-lg ring-2 ring-orange-200">
                    ${userInitial}
                </div>
            `;
            
            // Hide the remove button
            removePhotoBtn.style.display = 'none';
            
            // Show a success message
            const successMsg = document.createElement('p');
            successMsg.className = 'text-xs text-orange-600 mt-2';
            successMsg.textContent = 'Photo will be removed when you save changes.';
            photoPreview.appendChild(successMsg);
        });
    }

    /* =========================
       Password Toggle for Change Password Modal
    ========================= */
    function setupPasswordToggle(toggleCheckboxId, passwordInputId) {
        const toggleCheckbox = document.getElementById(toggleCheckboxId);
        const passwordInput = document.getElementById(passwordInputId);
        
        if (toggleCheckbox && passwordInput) {
            toggleCheckbox.addEventListener('change', (e) => {
                passwordInput.type = e.target.checked ? 'text' : 'password';
            });
        }
    }
    
    // Setup toggles for change password modal
    setupPasswordToggle('toggleChangePassword', 'changePassword');
    setupPasswordToggle('toggleChangePasswordConfirmation', 'changePasswordConfirmation');
    
    // Setup toggle for delete account password
    setupPasswordToggle('toggleDeletePassword', 'deletePassword');
});

</script>
@endsection
