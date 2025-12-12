<!DOCTYPE html>
<html lang="en">
<head> 
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="user-id" content="{{ auth()->id() }}">

<title>Taskaroo</title>

@vite(['resources/css/app.css', 'resources/js/app.js'])

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    body, html { margin: 0; height: 100%; font-family: sans-serif; }
    .float-heart { animation: float 3s ease-in-out infinite; }
    @keyframes float { 0% { transform: translateY(0); opacity: 0.8; } 50% { transform: translateY(-6px); opacity:1; } 100% { transform: translateY(0); opacity:0.8; } }

    nav { height: 80px; line-height: 80px; }

    .layout { display: flex; height: calc(100vh - 80px); margin-top: 80px; }
    aside { width: 280px; background: #fff; border-right: 2px solid #FFE5D9; display: flex; flex-direction: column; overflow: hidden; }
    .sidebar-content { flex: 1; overflow-y: auto; padding: 1.5rem; }
    .logout-section { padding: 1.5rem; border-top: 2px solid #FFE5D9; }
    main { flex: 1; overflow-y: auto; background: #FFF5F0; padding: 2rem; }

    .shine-effect { position: relative; overflow: hidden; }
    .shine-effect::after { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); transition: left 0.5s; }
    .shine-effect:hover::after { animation: shine 0.8s; }
    @keyframes shine { 0% { left: -100%; } 100% { left: 100%; } }

    .active-link { background-color: #FFE5D9; color: #DF5219; font-weight: bold; }
    .active-link .indicator { opacity: 1; }
    .indicator { width: 4px; height: 100%; background-color: #DF5219; border-radius: 0 4px 4px 0; opacity: 0; transition: opacity 0.3s; }
</style>

@include('partials.dark-mode')

</head>
<body>

<!-- Notification Button -->
<button id="notifBtn" class="relative">
  🔔 <span id="unreadCount" class="badge">0</span>
</button>

<div id="notifDropdown" class="hidden absolute bg-white border shadow-lg w-80">
  <ul id="notifList"></ul>
</div>

<!-- Fixed Header -->
<nav class="bg-white shadow-lg fixed top-0 left-0 w-full z-50 border-b border-orange-200 flex justify-between items-center px-6">
    <div class="flex items-center gap-3">
        <img src="{{ asset('images/taskaroo-logo.png') }}" alt="Taskaroo Logo" class="logo-image w-12 h-12 object-contain float-heart" />
        <span class="font-bold text-2xl tracking-tight text-orange-700">Taskaroo</span>
    </div>

    <button id="theme-toggle" 
        class="bg-white/80 text-orange-600 px-4 py-2 rounded-xl font-semibold 
        hover:bg-white transition-all duration-300 shadow-md hover:shadow-lg 
        flex items-center gap-2 backdrop-blur hover:scale-105 active:scale-95">
        <i data-lucide="moon" class="w-5 h-5"></i>
        <span class="hidden sm:inline">Mode</span>
    </button>
</nav>

<!-- Layout -->
<div class="layout">
    <aside>
        <div class="sidebar-content">
            @if(auth()->check())
            <a href="{{ route('profile') }}" class="block mb-8 p-5 bg-orange-50 rounded-2xl shadow-lg relative overflow-hidden shine-effect hover:shadow-xl transition-all duration-300">
                <div class="flex items-center gap-3 mb-3">
                    @if(auth()->user() && auth()->user()->profile_photo)
                        <div class="w-14 h-14 rounded-full overflow-hidden shadow-lg ring-4 ring-orange-200">
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg ring-4 ring-orange-200">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-gray-800 truncate text-lg">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <div class="pt-3 border-t-2 border-orange-200">
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-orange-700 bg-orange-200 px-3 py-1.5 rounded-full shadow-sm">
                        Active
                    </span>
                </div>
            </a>
            @endif


            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 transition-all duration-300 font-semibold shadow-sm shine-effect group
                   {{ request()->routeIs('dashboard') ? 'active-link' : '' }}">
                    <div class="indicator"></div>
                    <i data-lucide="layout-dashboard" class="w-5 h-5 text-orange-600"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('activity.log') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 transition-all duration-300 font-semibold shadow-sm shine-effect group
                   {{ request()->routeIs('activity.log') ? 'active-link' : '' }}">
                    <div class="indicator"></div>
                    <i data-lucide="history" class="w-5 h-5 text-orange-600"></i>
                    <span>Activity Log</span>
                </a>

                @php
                    $unreadCount = Auth::user()->unreadNotifications()->count();
                @endphp
                <a href="{{ route('notifications') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 transition-all duration-300 font-semibold shadow-sm shine-effect group
                   {{ request()->routeIs('notifications') ? 'active-link' : '' }}">
                    <div class="indicator"></div>
                    <i data-lucide="bell" class="w-5 h-5 text-orange-600"></i>
                    <span>Notifications</span>
                    <span id="sidebarUnreadCount" 
                          class="ml-auto bg-orange-200 text-orange-700 text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ $unreadCount > 0 ? $unreadCount : '' }}
                    </span>
                </a>
            </nav>
        </div>

        <div class="logout-section">
            <button id="logout-btn" type="button"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 
                hover:bg-red-50 transition-all duration-300 font-semibold shadow-sm hover:shadow-md">
                <i data-lucide="power" class="w-5 h-5"></i>
                <span>Logout</span>
            </button>
        </div>
    </aside>

    <main>
        @yield('content')
    </main>
</div>

<!-- Logout Modal -->
<div id="logoutModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-md">
    <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md mx-4 scale-95 hover:scale-100 transition-all duration-300 border-4 border-orange-200">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-gradient-to-br from-orange-100 to-orange-100 rounded-2xl flex items-center justify-center shadow-lg border-2 border-orange-200">
                <span class="text-3xl">❗</span>
            </div>
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-orange-600 to-orange-600 bg-clip-text text-transparent">Confirm Logout</h2>
                <p class="text-sm text-gray-500 mt-1">Are you sure you want to leave?</p>
            </div>
        </div>
        <p class="text-gray-600 mb-6 text-center">You will be redirected to the login page.</p>
        <form id="logoutForm" method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="flex gap-3">
                <button type="button" onclick="closeLogoutModal()"
                    class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105 active:scale-95">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl font-semibold shadow-md hover:shadow-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 hover:scale-105 active:scale-95">
                    Logout
                </button>
            </div>
        </form>
    </div>
</div>

@yield('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Logout modal
    const logoutBtn = document.getElementById('logout-btn');
    const logoutModal = document.getElementById('logoutModal');
    if (logoutBtn && logoutModal) {
        logoutBtn.addEventListener('click', () => { logoutModal.classList.remove('hidden'); });
        window.closeLogoutModal = () => { logoutModal.classList.add('hidden'); };
    }

    // Notifications
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifList = document.getElementById('notifList');
    const unreadCount = document.getElementById('unreadCount');
    const sidebarUnread = document.getElementById('sidebarUnreadCount');

    if (notifBtn && notifDropdown && notifList && unreadCount) {
        notifBtn.addEventListener('click', () => { notifDropdown.classList.toggle('hidden'); });

        document.addEventListener('click', function(e) {
            if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.add('hidden');
            }
        });

        async function fetchNotifications() {
            try {
                const res = await fetch('/notifications/unread', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    credentials: 'same-origin'
                });
                const data = await res.json();
                const count = data.length;
                unreadCount.textContent = count || '';
                if (sidebarUnread) sidebarUnread.textContent = count || '';
                notifList.innerHTML = data.map(n => `
                    <li class="px-4 py-2 hover:bg-orange-50 cursor-pointer">
                        <a href="${n.url}" class="block text-gray-700">
                            <strong>${n.title}</strong><br>
                            <span class="text-xs text-gray-500">${n.when}</span>
                        </a>
                    </li>
                `).join('');
            } catch(err) {
                console.error('Error fetching notifications:', err);
            }
        }

        fetchNotifications();
        setInterval(fetchNotifications, 30000);
    }
});
</script>

</body>
</html>