@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    <!-- Task Creation Card -->
    <div class="bg-gradient-to-br from-white to-orange-50 rounded-3xl p-8 shadow-2xl border-2 border-orange-100 space-y-6">

        <!-- Header -->
        <div class="border-b-2 border-orange-200 pb-6">
            <h2 class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent mb-2">Create New Task</h2>
            <p class="text-gray-600 font-medium">Add a new task to your list</p>
        </div>

        @if ($errors->has('due_date'))
            <div class="mb-4 p-4 bg-red-100 border-2 border-red-400 text-red-700 rounded-xl font-semibold">
                <i data-lucide="alert-circle" class="w-5 h-5 inline mr-2"></i>
                <strong>Error:</strong> {{ $errors->first('due_date') }}
            </div>
        @endif

        <form method="POST" action="{{ route('tasks.store') }}" class="space-y-6" enctype="multipart/form-data">
            @csrf

            <!-- Title -->
            <div>
                <label class="block font-bold text-gray-700 mb-2 flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-5 h-5 text-orange-600"></i>
                    Task Title
                </label>
                <input type="text" name="title" id="titleInput" class="w-full p-4 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300" placeholder="Enter task title..." required>
            </div>

            <!-- Description -->
            <div>
                <label class="block font-bold text-gray-700 mb-2 flex items-center gap-2">
                    <i data-lucide="file-text" class="w-5 h-5 text-orange-600"></i>
                    Description
                </label>
                <textarea name="description" id="descriptionInput" rows="4" class="w-full p-4 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300 resize-none" placeholder="Describe your task..."></textarea>
            </div>

            <!-- Due Date & Category -->
            <div class="grid sm:grid-cols-2 gap-6">
               <div>
                    <label class="block font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-5 h-5 text-orange-600"></i>
                        Due Date
                    </label>
                    <input 
                        type="datetime-local" 
                        name="due_date" 
                        value="{{ old('due_date') }}" 
                        min="{{ now()->format('Y-m-d\TH:i') }}"
                        class="w-full p-4 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300"
                        required
                    >
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <i data-lucide="tag" class="w-5 h-5 text-orange-600"></i>
                        Category
                    </label>
                    <select name="category" class="w-full p-4 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300">
                        <option value="My Task" selected>My Task</option>
                        <option value="Task from Admin">Task from Admin</option>
                    </select>
                </div>
            </div>

            <!-- Urgency -->
            <div>
                <label class="block font-bold text-gray-700 mb-2 flex items-center gap-2">
                    <i data-lucide="zap" class="w-5 h-5 text-orange-600"></i>
                    Urgency Level
                </label>
                <select name="urgency" class="w-full p-4 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300" required>
                    <option value="very_urgent" {{ old('urgency')=='very_urgent' ? 'selected' : '' }}>🔴 Very Urgent (Red)</option>
                    <option value="urgent" {{ old('urgency')=='urgent' ? 'selected' : '' }}>🟠 Urgent (Orange)</option>
                    <option value="normal" {{ old('urgency','normal')=='normal' ? 'selected' : '' }}>🟡 Normal (Yellow)</option>
                    <option value="least_urgent" {{ old('urgency')=='least_urgent' ? 'selected' : '' }}>🟢 Least Urgent (Green)</option>
                </select>
            </div>

            <!-- Color & Pin -->
            <div class="flex items-center gap-6 bg-gradient-to-r from-orange-50 to-orange-100 p-5 rounded-2xl border-2 border-orange-200">
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_pinned" id="is_pinned" class="w-5 h-5 cursor-pointer accent-orange-600 rounded">
                    <label for="is_pinned" class="font-bold text-gray-700 cursor-pointer flex items-center gap-2">
                        <i data-lucide="pin" class="w-5 h-5 text-orange-600"></i>
                        Pin this task
                    </label>
                </div>
            </div>

            <!-- Checklist -->
            <div class="p-6 bg-gradient-to-r from-orange-50 to-orange-100 rounded-2xl border-2 border-orange-200 space-y-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="check-square" class="w-5 h-5 text-orange-600"></i>
                    Task Checklist
                </h3>

                <ul id="checklist" class="space-y-2"></ul>

                <div class="flex gap-2 mt-4">
                    <input type="text" id="newChecklistItem" placeholder="Add checklist item..."
                        class="flex-1 p-3 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300">
                    <button type="button" id="addChecklistItem"
                        class="px-5 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 font-bold shadow-md hover:shadow-lg hover:scale-105 active:scale-95 inline-flex items-center gap-2">
                        <i data-lucide="plus" class="w-5 h-5"></i>
                        Add
                    </button>
                </div>
            </div>

            <!-- Attachment -->
            <div>
                <label class="block font-bold text-gray-700 mb-2 flex items-center gap-2">
                    <i data-lucide="paperclip" class="w-5 h-5 text-orange-600"></i>
                    Attach File (optional)
                </label>
                <input type="file" name="attachment" class="w-full p-4 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 hover:border-orange-300">
            </div>

            <!-- Actions: Cancel + Save -->
            <div class="flex gap-4 pt-4">
                <a href="{{ route('dashboard') }}" 
                   class="flex-1 text-center px-6 py-3 border-2 border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-100 transition-all duration-300 inline-flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                    <i data-lucide="x" class="w-5 h-5"></i>
                    Cancel
                </a>
                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-xl font-bold hover:from-orange-600 hover:to-orange-700 transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Save Task
                </button>
            </div>

        </form>

    </div>
</div>

@endsection

@section('scripts')
<script>
    // Load speech text from sessionStorage if available
    const speechText = sessionStorage.getItem('speechText');
    if (speechText) {
        document.getElementById('descriptionInput').value = speechText;
        // Clear sessionStorage after loading
        sessionStorage.removeItem('speechText');
        sessionStorage.removeItem('speechLanguage');
    }

    const checklist = document.getElementById('checklist');
    const addBtn = document.getElementById('addChecklistItem');
    const newItemInput = document.getElementById('newChecklistItem');

    addBtn.addEventListener('click', () => {
        const title = newItemInput.value.trim();
        if (!title) return;

        const li = document.createElement('li');
        li.className = "flex items-center gap-2";
        li.innerHTML = `
            <input type="checkbox" class="check-item">
            <input type="text" name="checklist[]" value="${title}" class="border-2 border-orange-200 rounded-lg flex-1 px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium">
            <button type="button" class="remove-item px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-300 font-semibold">✖️</button>
        `;
        checklist.appendChild(li);
        newItemInput.value = '';
    });

    checklist.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-item')) e.target.closest('li').remove();
        if (e.target.classList.contains('check-item')) {
            const input = e.target.nextElementSibling;
            input.classList.toggle('line-through');
            input.classList.toggle('text-gray-400');
        }
    });
</script>
@endsection