@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6">Recycle Bin</h1>

    @if($tasks->isEmpty())
        <p>No deleted tasks found.</p>
    @else

        {{-- BULK FORM --}}
        <form id="bulkForm" action="{{ route('tasks.bulkAction') }}" method="POST">
            @csrf
        </form>

        {{-- Select All & Selected Count --}}
        <div class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div>
                    <input type="checkbox" id="selectAll" class="mr-2">
                    <label for="selectAll" class="font-semibold">Select All</label>
                </div>
                <span id="selectedCount" class="text-sm text-gray-600">(0 selected)</span>
            </div>
            
            {{-- Bulk Action Buttons (Only visible when items selected) --}}
            <div id="bulkActions" class="hidden gap-2">
                <button type="submit" form="bulkForm" name="action" value="restore" class="px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded hover:from-orange-600 hover:to-orange-700 transition">
                    Restore
                </button>
                <button type="submit" form="bulkForm" name="action" value="delete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                    Delete Permanently
                </button>
            </div>
        </div>

        @foreach($tasks as $task)
            <div class="p-4 border rounded mb-2 bg-white hover:bg-gray-50 transition flex justify-between items-center">

                <div class="flex items-center gap-3">
                    <input 
                        type="checkbox" 
                        name="task_ids[]" 
                        value="{{ $task->id }}" 
                        class="taskCheckbox w-4 h-4"
                        form="bulkForm"
                    >
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $task->title }}</h3>
                        <p class="text-sm text-gray-500">Deleted at: {{ $task->deleted_at }}</p>
                    </div>
                </div>

                {{-- No individual action buttons --}}
            </div>
        @endforeach

    @endif
</div>

<script>
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.taskCheckbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    // Update bulk actions visibility and count
    function updateBulkActions() {
        const checkedCount = document.querySelectorAll('.taskCheckbox:checked').length;
        selectedCount.textContent = `(${checkedCount} selected)`;
        
        if (checkedCount > 0) {
            bulkActions.classList.remove('hidden');
            bulkActions.classList.add('flex');
        } else {
            bulkActions.classList.add('hidden');
            bulkActions.classList.remove('flex');
        }
    }

    // Select all functionality
    selectAll.addEventListener('change', () => {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateBulkActions();
    });

    // Update when individual checkboxes change
    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            selectAll.checked = document.querySelectorAll('.taskCheckbox:checked').length === checkboxes.length;
            updateBulkActions();
        });
    });
</script>
@endsection