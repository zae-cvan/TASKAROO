@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-4xl">

    <!-- Main Task Card -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <!-- Header Section with Color Strip -->
        <div class="relative">
            <div class="h-2" style="background-color: {{ $task->color ?? '#DF5219' }}"></div>
            
            <div class="p-8">
                <!-- Title and Status -->
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-800 mb-3 flex items-center gap-3">{{ $task->title }}
                            @php $um = $task->urgency_meta ?? null; @endphp
                            <span style="background-color: {{ $um['color'] ?? '#E5E7EB' }}; color: white;" class="px-3 py-1 rounded-full text-sm font-semibold">{{ $um['label'] ?? 'Normal' }}</span>
                            @if($task->category === 'Task from Admin' || ($task->created_by && $task->created_by != $task->user_id))
                                <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded-full text-xs font-semibold">Assigned by Admin</span>
                            @endif
                        </h1>
                            <span class="inline-flex items-center gap-2 text-sm px-3 py-1.5 rounded-full font-medium
                            {{ $task->trashed()?'bg-red-100 text-red-700':'' }}
                            {{ !$task->trashed() && $task->status=='active'?'bg-orange-50 text-orange-700':'' }}
                            {{ !$task->trashed() && $task->status=='completed'?'bg-orange-100 text-orange-700':'' }}
                            {{ !$task->trashed() && $task->status=='archived'?'bg-orange-100 text-orange-700':'' }}">
                            @if($task->trashed())
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                In Recycle Bin
                            @elseif($task->status=='active')
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Active
                            @elseif($task->status=='completed')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Completed
                            @elseif($task->status=='archived')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                                Archived
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                {{ ucfirst($task->status) }}
                            @endif
                        </span>
                    </div>

                    <!-- Action Buttons -->
                    @if($task->trashed())
                        <!-- Recycle Bin Actions -->
                        <div class="flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('tasks.restore', $task->id) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition shadow-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                    </svg>
                                    Restore
                                </button>
                            </form>
                            <form method="POST" action="{{ route('tasks.forceDelete', $task->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this task? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete Permanently
                                </button>
                            </form>
                         
                        </div>
                    @else
                        <!-- Normal Task Actions -->
                        <div class="flex flex-wrap gap-2">
                            @if($task->status === 'active')
                            <form method="POST" action="{{ route('tasks.complete', $task->id) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition shadow-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Complete
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('tasks.edit', $task->id) }}?back=show" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition shadow-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>

                            @if($task->status === 'archived')
                            <form method="POST" action="{{ route('tasks.unarchive', $task->id) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition shadow-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                    </svg>
                                    Unarchive
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('tasks.archive', $task->id) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-lg transition shadow-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                    </svg>
                                    Archive
                                </button>
                            </form>
                        @endif

                        <button type="button" class="delete-btn inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-sm font-medium" data-task-id="{{ $task->id }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
            
                    </div>
                    @endif
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</h2>
                    <p class="text-gray-700 text-base leading-relaxed bg-gray-50 p-4 rounded-lg border border-gray-200">
                        {{ $task->description ?: 'No description provided.' }}
                    </p>
                </div>

                <!-- Attachment -->
                @if(!empty($task->attachment))
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Attachment</h2>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <a href="{{ asset('storage/' . $task->attachment) }}" target="_blank" class="text-orange-600 font-medium underline">View / Download attachment</a>
                    </div>
                </div>
                @endif

                <!-- Date Information -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex items-center gap-2 text-gray-600 mb-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="text-sm font-medium">Created</span>
                        </div>
                        <p class="text-gray-800 font-semibold ml-7">{{ $task->created_at->format('M d, Y h:i A') }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex items-center gap-2 text-gray-600 mb-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium">Due Date</span>
                        </div>
                        <p class="text-gray-800 font-semibold ml-7">
                            {{ $task->due_date ? $task->due_date->timezone('Asia/Manila')->format('M d, Y h:i A') : 'No deadline' }}
                        </p>
                    </div>

                    @if($task->trashed() && $task->deleted_at)
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <div class="flex items-center gap-2 text-red-600 mb-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span class="text-sm font-medium">Deleted At</span>
                        </div>
                        <p class="text-red-800 font-semibold ml-7">{{ $task->deleted_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Checklist Card -->
    <div class="mt-6 bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-orange-50 to-orange-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                Checklist
            </h2>
        </div>

        <div class="p-6">
            @if($task->checklistItems && count($task->checklistItems) > 0)
                <ul id="checklist" class="space-y-3">
                    @foreach($task->checklistItems as $item)
                        <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition group">
                            <input type="checkbox" 
                                class="check-item w-5 h-5 text-orange-600 rounded focus:ring-2 focus:ring-orange-500 cursor-pointer" 
                                {{ $item->completed ? 'checked' : '' }}>
                            <span class="flex-1 {{ $item->completed ? 'line-through text-gray-400' : 'text-gray-700' }} transition-all duration-200">
                                {{ $item->title }}
                            </span>
                            @if($item->completed)
                                <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-gray-500">No checklist items yet</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-[9999]">
    <div class="bg-white rounded-2xl p-6 w-96 shadow-xl relative">
        <h3 class="text-lg font-bold mb-4 text-black">Delete Task</h3>
        <p id="deleteMessage" class="text-gray-700 mb-6">Are you sure you want to delete this task? It will be moved to the recycle bin.</p>
        <form id="deleteForm" method="POST" action="" class="inline">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl border border-gray-300 hover:bg-gray-100">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-500 text-white hover:bg-red-600">Delete</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Task detail checklist check/uncheck
    const checklist = document.getElementById('checklist');
    if (checklist) {
        checklist.addEventListener('click', (e) => {
            if (e.target.classList.contains('check-item')) {
                const span = e.target.nextElementSibling;
                if (e.target.checked) {
                    span.classList.add('line-through', 'text-gray-400');
                } else {
                    span.classList.remove('line-through', 'text-gray-400');
                }
            }
        });
    }

    // Delete Modal Logic
    const modal = document.getElementById('deleteModal');
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const deleteForm = document.getElementById('deleteForm');
    const deleteMessage = document.getElementById('deleteMessage');

    if (modal && deleteForm) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', () => {
                if (deleteForm) {
                    deleteForm.action = `/tasks/${button.dataset.taskId}`;
                }
                if (deleteMessage) {
                    deleteMessage.textContent = 'Are you sure you want to delete this task? It will be moved to the recycle bin.';
                }
                if (modal) {
                    modal.classList.remove('hidden');
                }
            });
        });
    }

    function closeModal() {
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Close modal when clicking outside
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    }
</script>
@endsection