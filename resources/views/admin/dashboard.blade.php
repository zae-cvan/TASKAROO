@extends('layouts.app')

@section('content')

<div class="container mx-auto p-6 max-w-6xl">
    <!-- Header -->
    <div class="bg-gradient-to-r from-white to-orange-50 rounded-3xl p-8 mb-8 border-2 border-orange-100 shadow-xl">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent">Admin Dashboard</h1>
                <p class="text-gray-600 mt-1 font-medium">Manage users and tasks you've assigned</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-orange-100 text-orange-800 rounded-xl border-2 border-orange-400 font-semibold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-xl border-2 border-red-400 font-semibold flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('admin.users.index') }}" class="group block p-6 bg-white rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-2 border-orange-100 hover:border-orange-200">
            <div class="flex items-center justify-between mb-3">
                <div class="text-sm font-bold text-gray-600 uppercase tracking-wide">Total Users</div>
                <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center shadow-md">
                    <i data-lucide="users" class="w-5 h-5 text-white"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 group-hover:text-orange-600 transition-colors">{{ $totalUsers }}</div>
        </a>

        <div class="p-6 bg-white rounded-2xl shadow-lg border-2 border-orange-100">
            <div class="flex items-center justify-between mb-3">
                <div class="text-sm font-bold text-gray-600 uppercase tracking-wide">Tasks Assigned</div>
                <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center shadow-md">
                    <i data-lucide="clipboard-check" class="w-5 h-5 text-white"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $totalAssigned }}</div>
            <div class="text-xs text-gray-500 mt-2 font-medium">Total overview</div>
        </div>

        <a href="{{ route('admin.tasks.index', ['status' => 'active']) }}" class="group block p-6 bg-white rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-2 border-orange-100 hover:border-orange-200">
            <div class="flex items-center justify-between mb-3">
                <div class="text-sm font-bold text-gray-600 uppercase tracking-wide">Pending</div>
                <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center shadow-md">
                    <i data-lucide="clock" class="w-5 h-5 text-white"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 group-hover:text-yellow-600 transition-colors">{{ $pendingAssigned }}</div>
        </a>

        <a href="{{ route('admin.tasks.index', ['status' => 'completed']) }}" class="group block p-6 bg-white rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-2 border-orange-100 hover:border-orange-200">
            <div class="flex items-center justify-between mb-3">
                <div class="text-sm font-bold text-gray-600 uppercase tracking-wide">Completed</div>
                <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center shadow-md">
                    <i data-lucide="check-circle" class="w-5 h-5 text-white"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 group-hover:text-orange-600 transition-colors">{{ $completedAssigned }}</div>
        </a>
    </div>

    <!-- Quick Actions -->
    <div class="flex gap-3 mb-8 flex-wrap">
        <a href="{{ route('admin.tasks.assign') }}" class="px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 font-bold inline-flex items-center gap-2 hover:scale-105 active:scale-95">
            <i data-lucide="plus-circle" class="w-5 h-5"></i>
            Assign New Task
        </a>
        <a href="{{ route('admin.tasks.recycle') }}" class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 font-bold inline-flex items-center gap-2 hover:scale-105 active:scale-95">
            <i data-lucide="trash-2" class="w-5 h-5"></i>
            Recycle Bin
        </a>
    </div>

    <!-- Recent Tasks Section -->
    <div class="bg-white rounded-3xl shadow-xl border-2 border-orange-100 overflow-hidden">
        <div class="p-6 border-b-2 border-orange-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center shadow-md">
                <i data-lucide="history" class="w-5 h-5 text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Recent Tasks You Assigned</h2>
        </div>
        
        @if(isset($recentTasks) && $recentTasks->count())
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gradient-to-r from-orange-50 to-orange-50 border-b-2 border-orange-100">
                        <tr>
                            <th class="px-6 py-4 font-bold text-gray-700">Task Title</th>
                            <th class="px-6 py-4 font-bold text-gray-700">Assigned To</th>
                            <th class="px-6 py-4 font-bold text-gray-700">Due Date</th>
                            <th class="px-6 py-4 font-bold text-gray-700">Status</th>
                            <th class="px-6 py-4 font-bold text-gray-700 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-orange-100">
                        @foreach($recentTasks as $task)
                            <tr class="hover:bg-orange-50 transition-colors duration-200">
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ $task->title }}</td>
                                <td class="px-6 py-4 text-gray-700">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-orange-100 text-orange-800 rounded-lg text-sm font-medium">
                                        {{ optional($task->user)->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-700 font-medium">{{ optional($task->due_date) ? \Illuminate\Support\Carbon::parse($task->due_date)->toFormattedDateString() : '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-sm font-bold
                                        {{ $task->status === 'active' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $task->status === 'completed' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $task->status === 'archived' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <a href="{{ route('admin.tasks.edit', $task->id) }}" class="px-3 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg font-semibold text-sm transition-colors duration-200 inline-flex items-center gap-1">
                                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" style="display:inline;" class="admin-delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" data-task-id="{{ $task->id }}" data-task-title="{{ $task->title }}" class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold text-sm transition-colors duration-200 open-delete-modal inline-flex items-center gap-1">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-4 text-gray-500">No recent tasks assigned.</div>
            @endif
        </div>
    </div>

</div>

@endsection

@section('scripts')
    <!-- Delete confirmation modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-xl shadow-lg w-11/12 max-w-md p-6">
            <h3 id="modalTitle" class="text-xl font-bold mb-2">Confirm deletion</h3>
            <p id="modalBody" class="text-sm text-gray-600 mb-4">Are you sure you want to delete this task? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button id="cancelDelete" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                <button id="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
            </div>
        </div>
    </div>

    <script>
        (function(){
            let targetForm = null;
            const modal = document.getElementById('deleteModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            const confirmBtn = document.getElementById('confirmDelete');
            const cancelBtn = document.getElementById('cancelDelete');

            document.querySelectorAll('.open-delete-modal').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    const id = btn.getAttribute('data-task-id');
                    const title = btn.getAttribute('data-task-title') || '';
                    // find the closest form
                    targetForm = btn.closest('form');
                    modalTitle.textContent = 'Delete task #' + id;
                    modalBody.textContent = `Are you sure you want to permanently move "${title}" to the Recycle Bin?`;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
            });

            confirmBtn.addEventListener('click', function(){
                if (targetForm) {
                    targetForm.submit();
                }
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });

            cancelBtn.addEventListener('click', function(){
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });

            // close when clicking outside modal box
            modal.addEventListener('click', function(e){
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });
        })();
    </script>
@endsection
