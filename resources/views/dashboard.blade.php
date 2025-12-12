@extends('layouts.app')

@section('content')

@php
    // Provide safe defaults when controller doesn't pass these variables
    $hasDeadlineToday = $hasDeadlineToday ?? false;
    $tasksDueToday = $tasksDueToday ?? collect();
@endphp



<div class="container mx-auto p-6 max-w-7xl">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-orange-100 text-orange-800 rounded-lg border border-orange-200">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-lg border border-red-200">{{ session('error') }}</div>
    @endif

    {{-- Deadline Today Alert --}}

    @if($hasDeadlineToday)
        <div id="deadlineAlert" class="fixed inset-0 flex items-center justify-center z-50 transition-all duration-300" style="">
            <!-- Semi-transparent backdrop -->
            <div class="absolute inset-0 bg-black bg-opacity-20 backdrop-blur-sm" onclick="document.getElementById('deadlineAlert').remove()"></div>
            
            <!-- Alert Card -->
            <div id="alertCard" class="relative max-w-sm w-full mx-4 bg-gradient-to-br from-orange-50 to-orange-50 rounded-3xl p-6 border-2 border-orange-200 shadow-2xl overflow-hidden transition-all duration-300">
                <!-- Decorative background -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-orange-100 rounded-full opacity-18 -mr-12 -mt-12"></div>
                
                <div class="relative z-10">
                    <!-- Cute Icon & Title -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center shadow-md animate-bounce">
                                <span class="text-xl">⏰</span>
                            </div>
                            <h3 class="font-bold text-orange-700 text-xl">Due Today!</h3>
                        </div>
                        
                        <!-- Close Button -->
                        <button id="deadlineCloseBtn" type="button" onclick="moveToTop(true)" class="text-gray-400 hover:text-orange-600 transition-colors hover:scale-110">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                    
                    <!-- Task List -->
                    <div class="space-y-2">
                        @foreach($tasksDueToday as $task)
                            <a href="{{ route('tasks.show', $task->id) }}" class="block p-3 bg-white rounded-2xl hover:bg-orange-50 transition-all duration-200 border border-transparent hover:border-orange-200 group">
                                <p class="text-sm font-semibold text-gray-800 group-hover:text-orange-600 truncate">{{ $task->title }}</p>
                                <p class="text-xs text-gray-500 mt-1">🕐 {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('g:i A') : 'All Day' }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Sound & Notification -->
        <audio id="deadlineAlertSound" src="/sounds/placeholder.mp3" preload="auto"></audio>
        <script>
        function moveToTop(fromUser = false) {
            const alert = document.getElementById('deadlineAlert');
            if (!alert) return;
            const card = document.getElementById('alertCard');
            const backdrop = alert.querySelector('[onclick*="remove"]');

            // If called by user interaction, persist the moved state
            try {
                if (fromUser && window.localStorage) localStorage.setItem('deadlineAlertMoved', '1');
            } catch(e){}

            // Remove backdrop click to close (we keep the alert visible at top)
            if (backdrop) {
                backdrop.onclick = null;
                backdrop.style.opacity = '0';
                backdrop.style.pointerEvents = 'none';
            }

            // Change position to top
            alert.style.position = 'fixed';
            alert.style.inset = 'auto';
            alert.style.top = '20px';
            alert.style.left = '50%';
            alert.style.transform = 'translateX(-50%)';
            alert.style.width = 'auto';
            alert.style.maxWidth = '28rem';
            alert.style.display = 'flex';
            alert.style.alignItems = 'flex-start';
            alert.style.justifyContent = 'center';
            alert.style.zIndex = '50';

            // Remove mx-4 constraint and update card
            if (card) {
                card.style.margin = '0';
                card.style.width = '100%';
            }

            // Remove the close button so it can't be moved again
            try {
                const closeBtn = document.getElementById('deadlineCloseBtn');
                if (closeBtn) closeBtn.remove();
            } catch(e){}
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Play sound
            var sound = document.getElementById('deadlineAlertSound');
            if (sound) {
                sound.play().catch(function(){});
            }

            // If the user previously moved the alert up, keep it at the top
            try {
                if (localStorage && localStorage.getItem('deadlineAlertMoved') === '1') {
                    // If alert exists, move it to top without re-setting the flag
                    var existingAlert = document.getElementById('deadlineAlert');
                    if (existingAlert) moveToTop(false);
                }
            } catch(e){}

            // Browser notification
            if (window.Notification) {
                if (Notification.permission === 'granted') {
                    new Notification('⚠️ Task Deadline Today!', {
                        body: 'You have tasks due today.',
                        icon: '/images/warning.png'
                    });
                } else if (Notification.permission !== 'denied') {
                    Notification.requestPermission();
                }
            }

            // Auto-pin after 1 minute
            setTimeout(function() {
                @foreach($tasksDueToday as $task)
                fetch('/tasks/{{ $task->id }}/pin', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ is_pinned: true })
                });
                @endforeach
                // Optionally reload to reflect pinning
                location.reload();
            }, 60000);
        });
        </script>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4 bg-gradient-to-r from-white to-orange-50 rounded-3xl p-8 border-2 border-orange-100 shadow-xl">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i data-lucide="check-square" class="w-7 h-7 text-white"></i>
            </div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent">Tasks</h1>
            @if(isset($viewingUser))
                <div class="mt-2">
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-orange-50 text-orange-700 rounded-full text-sm font-semibold">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        Viewing tasks for {{ $viewingUser->name }}
                    </span>
                    <a href="{{ route('dashboard') }}" class="ml-3 text-sm text-gray-600 hover:text-orange-600">(back to my tasks)</a>
                </div>
            @endif
        </div>

        <div class="flex flex-wrap gap-3">
            <!-- Search -->
            <form method="GET" action="{{ route('dashboard') }}" class="flex bg-white border-2 border-orange-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-orange-500 focus-within:border-orange-500 shadow-sm hover:shadow-md transition-all duration-300">
                <input type="text" name="search" placeholder="Search tasks..." value="{{ request('search') }}"
                       class="px-4 py-2.5 outline-none w-40 sm:w-60 font-medium">
                <button type="submit" class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 px-5 text-white transition-all duration-300">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </button>
            </form>

            <!-- Combined Category + Sort Dropdown -->
            <div class="relative inline-block w-64">
                <button id="filterSortBtn" type="button"
                    class="w-full px-4 py-2.5 bg-white border-2 border-orange-200 rounded-xl text-left flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-orange-500 hover:border-orange-300 transition-all duration-300 shadow-sm hover:shadow-md font-semibold">
                    <span id="filterSortLabel" class="text-gray-700">{{ request('filter') ?? $sortLabel ?? 'All Tasks' }}</span>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-gray-500"></i>
                </button>

                <div id="filterSortOptions" class="absolute z-50 mt-2 w-full bg-white border-2 border-orange-200 rounded-xl shadow-xl hidden overflow-hidden">
                    <ul>
                        <li class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors font-medium text-gray-700 flex items-center gap-2" data-value="">
                            <i data-lucide="list" class="w-4 h-4 text-orange-500"></i>
                            All Tasks
                        </li>
                        <li class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors font-medium text-gray-700 flex items-center gap-2" data-value="Task from Admin">
                            <i data-lucide="user-check" class="w-4 h-4 text-orange-500"></i>
                            Task from Admin
                        </li>
                        <li class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors font-medium text-gray-700 flex items-center gap-2" data-value="My Task">
                            <i data-lucide="user" class="w-4 h-4 text-orange-500"></i>
                            My Task
                        </li>
                        <li class="border-t-2 border-orange-100"></li>
                        <li class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors font-medium text-gray-700 flex items-center gap-2" data-value="deadline_asc">
                            <i data-lucide="calendar" class="w-4 h-4 text-orange-600"></i>
                            Nearest Deadline
                        </li>
                        <li class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors font-medium text-gray-700 flex items-center gap-2" data-value="deadline_desc">
                            <i data-lucide="calendar" class="w-4 h-4 text-orange-600"></i>
                            Farthest Deadline
                        </li>
                        <li class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors font-medium text-gray-700 flex items-center gap-2" data-value="most_urgent">
                            <i data-lucide="zap" class="w-4 h-4 text-red-500"></i>
                            Most Urgent First
                        </li>
                        <li class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors font-medium text-gray-700 flex items-center gap-2" data-value="least_urgent">
                            <i data-lucide="clock" class="w-4 h-4 text-orange-500"></i>
                            Least Urgent First
                        </li>
                        <li class="border-t-2 border-orange-100"></li>
                        <li class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors font-medium text-gray-700 flex items-center gap-2" data-value="urgency=very_urgent" data-urgency="very_urgent">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-red-500"></i>
                            Very Urgent
                        </li>
                        <li class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors font-medium text-gray-700 flex items-center gap-2" data-value="urgency=urgent" data-urgency="urgent">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-orange-500"></i>
                            Urgent
                        </li>
                        <li class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors font-medium text-gray-700 flex items-center gap-2" data-value="urgency=normal" data-urgency="normal">
                            <i data-lucide="clock" class="w-4 h-4 text-yellow-500"></i>
                            Normal
                        </li>
                        <li class="px-4 py-3 hover:bg-orange-50 cursor-pointer transition-colors font-medium text-gray-700 flex items-center gap-2" data-value="urgency=least_urgent" data-urgency="least_urgent">
                            <i data-lucide="check-circle" class="w-4 h-4 text-orange-500"></i>
                            Least Urgent
                        </li>
                    </ul>
                </div>

                <form id="filterSortForm" method="GET" action="{{ route('dashboard') }}">
                    <input type="hidden" name="filter" id="filterInput" value="{{ request('filter') }}">
                    <input type="hidden" name="sort" id="sortInput" value="{{ request('sort') }}">
                        <input type="hidden" name="urgency" id="urgencyInput" value="{{ request('urgency') }}">
                    <input type="hidden" name="status" value="{{ request('status', 'active') }}">
                </form>
            </div>

            <a href="{{ route('tasks.create') }}" 
               class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 inline-flex items-center gap-2 hover:scale-105 active:scale-95">
               <i data-lucide="plus" class="w-5 h-5"></i>
               New Task
            </a>
                <a href="{{ route('speech.index') }}"
                    class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                    <i data-lucide="mic" class="w-5 h-5"></i>
                    Record Task
                </a>
        </div>
    </div>

   <!-- Tabs with Productivity Display START -->
<div class="flex flex-wrap gap-2 mb-6 bg-white rounded-2xl p-2 shadow-lg border-2 border-orange-100 items-center">

    @php 
        $tabs = [
            'active' => ['label' => 'Active', 'icon' => 'circle-dot'],
            'completed' => ['label' => 'Completed', 'icon' => 'check-circle'],
            'archived' => ['label' => 'Archived', 'icon' => 'archive'],
            'recycle' => ['label' => 'Recycle Bin', 'icon' => 'trash-2']
        ]; 
    @endphp

    @foreach($tabs as $key => $tab)
        @php
            $isActive = request('status') === $key || (request()->routeIs('tasks.recycle') && $key==='recycle');
        @endphp
        <a href="{{ $key === 'recycle' ? route('tasks.recycle') : route('dashboard', ['status'=>$key]) }}"
           class="px-5 py-2.5 rounded-xl font-bold transition-all duration-300 inline-flex items-center gap-2
                @if($isActive)
                    bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-md
                @else
                    text-gray-600 hover:bg-orange-50 hover:text-orange-600
                @endif">
           <i data-lucide="{{ $tab['icon'] }}" class="w-4 h-4"></i>
           {{ $tab['label'] }}
        </a>
    @endforeach

    <!-- Productivity Display -->
    <div class="ml-auto flex items-center gap-3">
        <div class="productivity-today bg-gradient-to-r from-orange-400 to-orange-600 text-white px-4 py-2 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex flex-col items-center">
            <span class="text-xs font-semibold uppercase tracking-wide">Today</span>
            <span class="text-lg font-bold">{{ $todayProductivity ?? 0 }}%</span>
        </div>
        <div class="productivity-weekly bg-gradient-to-r from-orange-500 to-orange-700 text-white px-4 py-2 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex flex-col items-center">
            <span class="text-xs font-semibold uppercase tracking-wide">This Week</span>
            <span class="text-lg font-bold">{{ $weeklyProductivity ?? 0 }}%</span>
        </div>
        <div class="productivity-monthly bg-gradient-to-r from-orange-600 to-orange-800 text-white px-4 py-2 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex flex-col items-center">
            <span class="text-xs font-semibold uppercase tracking-wide">This Month</span>
            <span class="text-lg font-bold">{{ $monthlyProductivity ?? 0 }}%</span>
        </div>
    </div>

</div>

</div>

    <!-- BULK ACTION FORM START -->
    <form method="POST" action="{{ route('tasks.bulkAction') }}" id="bulkActionForm">
        @csrf
        <input type="hidden" name="redirect_to" id="redirectTo" value="">

        <!-- Bulk Actions Bar -->
        <div id="bulkActionsBar" class="mb-6 p-5 bg-gradient-to-r from-orange-50 to-orange-50 border-2 border-orange-200 rounded-2xl hidden transition-all duration-300 shadow-lg">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-md">
                        <i data-lucide="check-square" class="w-5 h-5 text-white"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-800">
                        <span id="selectedCount">0</span> task(s) selected
                    </span>
                </div>
                <div class="flex gap-2 flex-wrap">
                    @if($status === 'recycle')
                        <button type="submit" name="action" value="restore" data-redirect="{{ route('dashboard', ['status'=>'active']) }}" 
                            class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 text-sm font-bold shadow-md hover:shadow-lg inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            Restore
                        </button>

                        <button type="submit" name="action" value="delete" data-redirect="{{ route('tasks.recycle') }}" 
                            class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-300 text-sm font-bold shadow-md hover:shadow-lg inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            Delete Permanently
                        </button>

                    @elseif($status === 'completed')
                        <button type="submit" name="action" value="uncomplete" data-redirect="{{ route('dashboard', ['status'=>'active']) }}" 
                            class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 text-sm font-bold shadow-md hover:shadow-lg inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            Mark Uncomplete
                        </button>
                        <button type="submit" name="action" value="recycle" data-redirect="{{ route('tasks.recycle') }}" 
                            class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-300 text-sm font-bold shadow-md hover:shadow-lg inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            Move to Recycle Bin
                        </button>
                        @elseif($status === 'archived')
                    <button type="submit" name="action" value="recycle" data-redirect="{{ route('tasks.recycle') }}" 
                        class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-300 text-sm font-bold shadow-md hover:shadow-lg inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        Move to Recycle Bin
                    </button>

                        @elseif($status === 'completed')
                            <button type="submit" name="action" value="recycle" data-redirect="{{ route('tasks.recycle') }}" 
                                class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-300 text-sm font-bold shadow-md hover:shadow-lg inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                Move to Recycle Bin
                            </button>
                            <button type="submit" name="action" value="uncomplete" data-redirect="{{ route('dashboard', ['status'=>'active']) }}" 
                                class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 text-sm font-bold shadow-md hover:shadow-lg inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                Mark Active
                            </button>

                    @else
                        <button type="submit" name="action" value="complete" data-redirect="{{ route('dashboard', ['status'=>'completed']) }}" 
                            class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 text-sm font-bold shadow-md hover:shadow-lg inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Mark Complete
                        </button>

                        <button type="submit" name="action" value="archive" data-redirect="{{ route('dashboard', ['status'=>'archived']) }}" 
                            class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 text-sm font-bold shadow-md hover:shadow-lg inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                            <i data-lucide="archive" class="w-4 h-4"></i>
                            Archive
                        </button>

                        <button type="submit" name="action" value="recycle" data-redirect="{{ route('tasks.recycle') }}" 
                            class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-300 text-sm font-bold shadow-md hover:shadow-lg inline-flex items-center gap-2 hover:scale-105 active:scale-95">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            Move to Recycle Bin
                        </button>
                    @endif
                </div>
            </div>
        </div>

    <!-- Pinned Tasks -->
@if($tasks->where('is_pinned', true)->count() > 0 || $tasks->where('auto_pinned', true)->count() > 0)
<div class="mb-8">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center shadow-md">
            <i data-lucide="pin" class="w-5 h-5 text-white"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Pinned Tasks</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($tasks->where('is_pinned', true)->concat($tasks->where('auto_pinned', true)) as $task)
        @php
            $diffMinutes = $task->due_date ? $task->due_date->diffInMinutes(now()) : 99999;
            if ($diffMinutes <= 30) {
                $autoPinClass = 'text-red-500';
                $label = 'Due in 30 mins';
            } elseif ($diffMinutes <= 1440) {
                $autoPinClass = 'text-orange-500';
                $label = 'Due in 1 day';
            } elseif ($diffMinutes <= 4320) {
                $autoPinClass = 'text-yellow-500';
                $label = 'Due in 3 days';
            } else {
                $autoPinClass = 'text-gray-400';
                $label = 'Auto-pinned';
            }
        @endphp
        <div class="task-card flex bg-gradient-to-br from-orange-50 to-orange-100 shadow-lg rounded-2xl border-2 border-l-8 p-6 relative transition-all duration-300 ease-out hover:shadow-2xl hover:-translate-y-2 group overflow-hidden" style="border-left-color: {{ $task->color ?? '#DF5219' }};">
            <div class="flex-1 pr-6 relative" onclick="openTask({{ $task->id }})">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-xl font-bold text-gray-800 hover:text-orange-600 transition-colors flex items-center gap-2">
                        <span>{{ $task->title }}</span>
                        @php $um = $task->urgency_meta ?? null; @endphp
                        <span style="background-color: {{ $um['color'] ?? '#E5E7EB' }}; color: white;" class="px-2 py-0.5 rounded-full text-xs font-semibold" aria-label="{{ $um['label'] ?? 'Normal' }}">
                            {{ $um['label'] ?? 'Normal' }}
                        </span>
                    </h3>
                    <i data-lucide="pin" class="w-5 h-5 text-yellow-500 absolute top-0 right-0"></i>
                    @if($task->auto_pinned)
                        <span class="text-xs font-bold {{ $autoPinClass }} ml-2 px-2 py-1 bg-white/70 rounded-lg">{{ $label }}</span>
                    @endif
                </div>
                <p class="text-gray-600 text-sm mb-4 line-clamp-2 leading-relaxed">{{ $task->description }}</p>
                <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 bg-white/60 backdrop-blur px-3 py-2 rounded-lg">
                    <i data-lucide="calendar" class="w-4 h-4 text-orange-500"></i>
                    {{ $task->due_date ? $task->due_date->timezone('Asia/Manila')->format('M d, Y h:i A') : 'No due date' }}
                </div>
            </div>
            <div class="flex items-start pt-1 pl-2">
                <input type="checkbox" name="task_ids[]" value="{{ $task->id }}" class="task-checkbox w-5 h-5 text-orange-600 rounded-lg focus:ring-2 focus:ring-orange-500 cursor-pointer border-2 border-orange-300 mt-1">
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif


        <!-- Regular Tasks -->
<div>
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-md">
                <i data-lucide="list-checks" class="w-5 h-5 text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">All Tasks</h2>
        </div>
        @if($tasks->count() > 0)
            <label class="flex items-center gap-2 cursor-pointer hover:bg-orange-50 px-4 py-2.5 rounded-xl transition-all duration-300 border-2 border-orange-100 hover:border-orange-300 shadow-sm hover:shadow-md group">
                <input type="checkbox" id="select-all" class="w-5 h-5 text-orange-600 rounded-lg focus:ring-2 focus:ring-orange-500 cursor-pointer border-2 border-orange-300">
                <span class="text-sm font-bold text-gray-700 group-hover:text-orange-600 transition-colors">Select All</span>
            </label>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($tasks->where('is_pinned', false) as $task)
            <div class="task-card flex bg-white shadow-lg rounded-2xl border-2 border-l-8 p-6 relative transition-all duration-300 ease-out hover:shadow-2xl hover:-translate-y-2 overflow-hidden group hover:bg-gradient-to-br hover:from-white hover:to-orange-50" style="border-left-color: {{ $task->color ?? '#DF5219' }};">
                <div class="flex-1 pr-6 relative" onclick="openTask({{ $task->id }})">
                    <h3 class="text-xl font-bold text-gray-800 hover:text-orange-600 mb-3 transition-colors flex items-center gap-2">
                        <span>{{ $task->title }}</span>
                        @php $um = $task->urgency_meta ?? null; @endphp
                        <span style="background-color: {{ $um['color'] ?? '#E5E7EB' }}; color: white;" class="px-2 py-0.5 rounded-full text-xs font-semibold" aria-label="{{ $um['label'] ?? 'Normal' }}">
                            {{ $um['label'] ?? 'Normal' }}
                        </span>
                    </h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2 leading-relaxed">{{ $task->description }}</p>

                    <div class="flex items-center gap-2 text-xs font-semibold text-gray-600 bg-orange-100 px-3 py-2 rounded-lg border border-orange-200">
                        <i data-lucide="calendar" class="w-4 h-4 text-orange-500"></i>
                        {{ $task->due_date ? $task->due_date->timezone('Asia/Manila')->format('M d, Y h:i A') : 'No due date' }}
                    </div>

                </div>
                <div class="flex items-start pt-1 pl-2">
                    <input type="checkbox" name="task_ids[]" value="{{ $task->id }}" class="task-checkbox w-5 h-5 text-orange-600 rounded-lg focus:ring-2 focus:ring-orange-500 cursor-pointer border-2 border-orange-300 mt-1">
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16">
                <div class="inline-block p-6 bg-gradient-to-br from-orange-100 to-orange-200 rounded-3xl mb-4">
                    <i data-lucide="inbox" class="w-16 h-16 text-orange-400"></i>
                </div>
                <p class="text-gray-500 text-xl font-semibold">No tasks found</p>
                <p class="text-gray-400 text-sm mt-2">Create your first task to get started!</p>
            </div>
        @endforelse
    </div>
</div>

    </form> <!-- <<< IMPORTANT: close bulkActionForm HERE to avoid nested forms / broken logout -->

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden  z-[9999]">
        <div class="bg-white rounded-2xl p-6 w-96 shadow-xl relative">
           <h3 class="text-lg font-bold mb-4 text-black">Confirmation</h3>

            <p id="confirmMessage" class="text-gray-700 mb-6">Are you sure?</p>
            <div class="flex justify-end gap-3">
                <button id="cancelBtn" class="px-4 py-2 rounded-xl border border-gray-300 hover:bg-gray-100">Cancel</button>
                <button id="confirmBtn" class="px-4 py-2 rounded-xl bg-red-500 text-white hover:bg-red-600">Yes</button>
            </div>
        </div>
    </div>

</div>
@endsection

<style>
    /* Hide checkboxes by default, show on card hover or when checked */
    .task-card .task-checkbox { opacity: 0; transition: opacity 0.2s; }
    .task-card.group:hover .task-checkbox, .task-card:focus-within .task-checkbox, .task-card .task-checkbox:checked { opacity: 1; }

.task-card span {
    font-size: 0.7rem;
    font-weight: bold;
}
</style>


@section('scripts')
@parent
<script>
// Run dashboard JS when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide) lucide.createIcons();

const taskCheckboxes = () => Array.from(document.querySelectorAll('.task-checkbox'));
const selectAllCheckbox = document.getElementById('select-all');
const bulkActionsBar = document.getElementById('bulkActionsBar');
const selectedCount = document.getElementById('selectedCount');
const bulkForm = document.getElementById('bulkActionForm');
const redirectInput = document.getElementById('redirectTo');

// Remove JS that toggles opacity for checkboxes; CSS group-hover handles visibility

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
        const checked = this.checked;
        taskCheckboxes().forEach(box => {
            box.checked = checked;
            if (checked) box.classList.remove('opacity-0');
            else box.classList.add('opacity-0');
        });
        updateBulkActions();
    });
}

function updateBulkActions() {
    if (!selectedCount || !bulkActionsBar) return;
    const checkedCount = taskCheckboxes().filter(box => box.checked).length;
    selectedCount.textContent = checkedCount;
    bulkActionsBar.classList.toggle('hidden', checkedCount === 0);
}


Array.from(taskCheckboxes()).forEach(box => {
    box.addEventListener('change', function() {
        if (this.checked) this.classList.remove('opacity-0');
        else this.classList.add('opacity-0');

        // Sync select-all checkbox state
        if (selectAllCheckbox) {
            const allChecked = taskCheckboxes().length > 0 && taskCheckboxes().every(b => b.checked);
            selectAllCheckbox.checked = allChecked;
        }
        updateBulkActions();
    });
});

const modal = document.getElementById('confirmModal');
const confirmMessage = document.getElementById('confirmMessage');
let currentButton = null;

if (bulkForm) {
    Array.from(bulkForm.querySelectorAll('button[type="submit"]')).forEach(btn => {
        btn.addEventListener('click', function(e) {
            // If no tasks selected -> prevent action
            if (!taskCheckboxes().some(box => box.checked)) {
                alert('Please select at least one task.');
                e.preventDefault();
                return false;
            }

            // Only show modal for destructive actions
           const destructiveActions = ['recycle', 'delete', 'forceDelete', 'archive', 'complete', 'uncomplete'];

            if (destructiveActions.includes(this.value)) {
                e.preventDefault(); // Stop default submit
                currentButton = this; // Save clicked button
                confirmMessage.textContent = `Are you sure you want to ${this.textContent.trim()}?`;
                modal.classList.remove('hidden'); // Show modal
            } else {
                // Non-destructive, submit normally (set redirect)
                if (redirectInput) redirectInput.value = this.dataset.redirect || '';
            }
        });
    });
}

// Modal buttons
const cancelBtn = document.getElementById('cancelBtn');
const confirmBtn = document.getElementById('confirmBtn');

if (cancelBtn) {
    cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));
}

if (confirmBtn) {
    confirmBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
       if (currentButton && bulkForm) {
        if (redirectInput) redirectInput.value = currentButton.dataset.redirect || '';

        // Add hidden input for action (if not present)
        let hiddenAction = bulkForm.querySelector('input[name="action"]');
        if (!hiddenAction) {
            hiddenAction = document.createElement('input');
            hiddenAction.type = 'hidden';
            hiddenAction.name = 'action';
            bulkForm.appendChild(hiddenAction);
        }
        hiddenAction.value = currentButton.value;

        bulkForm.submit(); // Submit the form
    }

    });
}


window.openTask = function(id) {
    const e = window.event;
    if (e && e.target && e.target.type === "checkbox") return;
    window.location.href = "/tasks/" + id;
};

const btn = document.getElementById('filterSortBtn');
const options = document.getElementById('filterSortOptions');
const label = document.getElementById('filterSortLabel');
const filterInput = document.getElementById('filterInput');
const sortInput = document.getElementById('sortInput');
const form = document.getElementById('filterSortForm');
if (btn && options && label && filterInput && sortInput && form) {
    btn.addEventListener('click', () => {
        options.classList.toggle('hidden');
        if(window.lucide) lucide.createIcons();
    });
    Array.from(options.querySelectorAll('li')).forEach(option => {
        option.addEventListener('click', () => {
            const value = option.dataset.value || '';
            // urgency items use data-urgency / urgency= value
            if (value.startsWith('urgency=')) {
                const u = value.split('=')[1];
                urgencyInput.value = u;
                // clear other filters/sorts
                filterInput.value = '';
                sortInput.value = '';
            } else if (['Task from Admin','My Task',''].includes(value)) {
                filterInput.value = value;
                urgencyInput.value = '';
                sortInput.value = '';
            } else {
                // sort selection
                sortInput.value = value;
                filterInput.value = '';
                urgencyInput.value = '';
            }
            label.textContent = option.textContent;
            options.classList.add('hidden');
            form.submit();
        });
    });
    document.addEventListener('click', e => {
        if (!btn.contains(e.target) && !options.contains(e.target)) options.classList.add('hidden');
    });
}
// Function to update productivity display
function updateProductivity() {
    fetch('/api/productivity', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update Today productivity
        const todayEl = document.querySelector('.productivity-today');
        if (todayEl) {
            const spans = todayEl.querySelectorAll('span');
            if (spans.length >= 2) {
                spans[spans.length - 1].textContent = data.todayProductivity + '%';
            }
        }
        
        // Update Weekly productivity
        const weeklyEl = document.querySelector('.productivity-weekly');
        if (weeklyEl) {
            const spans = weeklyEl.querySelectorAll('span');
            if (spans.length >= 2) {
                spans[spans.length - 1].textContent = data.weeklyProductivity + '%';
            }
        }
        
        // Update Monthly productivity
        const monthlyEl = document.querySelector('.productivity-monthly');
        if (monthlyEl) {
            const spans = monthlyEl.querySelectorAll('span');
            if (spans.length >= 2) {
                spans[spans.length - 1].textContent = data.monthlyProductivity + '%';
            }
        }
    })
    .catch(error => {
        console.error('Error updating productivity:', error);
    });
}

// Update productivity after bulk actions complete
if (bulkForm) {
    const originalSubmit = bulkForm.onsubmit;
    bulkForm.addEventListener('submit', function(e) {
        // Let the form submit normally, then update productivity after a delay
        setTimeout(() => {
            updateProductivity();
        }, 1000);
    });
}

// Listen to task events and update productivity
const userIdMeta = document.head.querySelector('meta[name="user-id"]');
const userId = userIdMeta ? userIdMeta.content : null;
if (window.Echo && userId) {
    Echo.private(`user.${userId}`)
        .listen('.task.created', e => {
            console.log('New task created:', e.task.title);
            alert(`New Task: ${e.task.title}`);
            // Update productivity when task is created
            setTimeout(() => updateProductivity(), 500);
        });
}

// Periodically update productivity (every 30 seconds)
setInterval(() => {
    updateProductivity();
}, 30000);
});
</script>
@endsection
