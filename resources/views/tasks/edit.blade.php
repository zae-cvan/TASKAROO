@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

	<!-- Task Edit Card -->
	<div class="bg-gradient-to-br from-white to-orange-50 rounded-3xl p-8 shadow-2xl border-2 border-orange-100 space-y-6 mt-8">

		<!-- Header -->
		<div class="border-b-2 border-orange-200 pb-4">
			<h2 class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent mb-2">Edit Task</h2>
			<p class="text-gray-600 font-medium">Update details for your task</p>
		</div>

		@if(session('success'))
			<div class="mb-4 p-3 bg-orange-100 text-orange-800 rounded-lg border border-orange-300">{{ session('success') }}</div>
		@endif
		@if($errors->any())
			<div class="mb-4 p-4 bg-red-100 border-2 border-red-400 text-red-700 rounded-xl font-semibold">
				<i data-lucide="alert-circle" class="w-5 h-5 inline mr-2"></i>
				<strong>Error:</strong> {{ $errors->first() }}
			</div>
		@endif

		<form method="POST" action="{{ route('tasks.update', $task->id) }}" class="space-y-6" enctype="multipart/form-data">
			@csrf
			@method('PUT')
			@if(request()->get('back') === 'show')
				<input type="hidden" name="back" value="show">
			@endif

			<!-- Title -->
			<div>
				<label class="block font-bold text-gray-700 mb-2 flex items-center gap-2">
					<i data-lucide="edit-3" class="w-5 h-5 text-orange-600"></i>
					Task Title
				</label>
				<input type="text" name="title" value="{{ old('title', $task->title) }}" class="w-full p-4 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300" placeholder="Enter task title..." required>
			</div>

			<!-- Description -->
			<div>
				<label class="block font-bold text-gray-700 mb-2 flex items-center gap-2">
					<i data-lucide="file-text" class="w-5 h-5 text-orange-600"></i>
					Description
				</label>
				<textarea name="description" id="descriptionInput" rows="4" class="w-full p-4 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300 resize-none" placeholder="Describe your task...">{{ old('description', $task->description) }}</textarea>
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
						value="{{ old('due_date', $task->due_date ? $task->due_date->timezone('Asia/Manila')->format('Y-m-d\TH:i') : '') }}" 
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
						<option value="My Task" {{ old('category', $task->category)=='My Task' ? 'selected' : '' }}>My Task</option>
						<option value="Task from Admin" {{ old('category', $task->category)=='Task from Admin' ? 'selected' : '' }}>Task from Admin</option>
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
					<option value="very_urgent" {{ old('urgency', $task->urgency)=='very_urgent' ? 'selected' : '' }}>🔴 Very Urgent (Red)</option>
					<option value="urgent" {{ old('urgency', $task->urgency)=='urgent' ? 'selected' : '' }}>🟠 Urgent (Orange)</option>
					<option value="normal" {{ old('urgency', $task->urgency)=='normal' ? 'selected' : '' }}>🟡 Normal (Yellow)</option>
					<option value="least_urgent" {{ old('urgency', $task->urgency)=='least_urgent' ? 'selected' : '' }}>🟢 Least Urgent (Green)</option>
				</select>
			</div>

			<!-- Pin -->
			<div class="flex items-center gap-6 bg-gradient-to-r from-orange-50 to-orange-100 p-5 rounded-2xl border-2 border-orange-200">
				<div class="flex items-center gap-3">
					<input type="checkbox" name="is_pinned" id="is_pinned" class="w-5 h-5 cursor-pointer accent-orange-600 rounded" {{ old('is_pinned', $task->is_pinned) ? 'checked' : '' }}>
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

				<ul id="checklist" class="space-y-2">
					@if(isset($task->checklist) && is_array($task->checklist) && count($task->checklist) > 0)
						@foreach($task->checklist as $index => $item)
							@php
								$itemTitle = is_array($item) ? ($item['title'] ?? '') : ($item->title ?? '');
								$itemCompleted = is_array($item) ? ($item['completed'] ?? false) : ($item->completed ?? false);
							@endphp
							<li class="flex items-center gap-2">
								<input type="hidden" name="checklist_completed[{{ $index }}]" value="{{ $itemCompleted ? '1' : '0' }}" class="checklist-completed">
								<input type="checkbox" class="check-item" {{ $itemCompleted ? 'checked' : '' }}>
								<input type="text" name="checklist[]" value="{{ $itemTitle }}" class="border-2 border-orange-200 rounded-lg flex-1 px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium {{ $itemCompleted ? 'line-through text-gray-400' : '' }}">
								<button type="button" class="remove-item px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-300 font-semibold">✖️</button>
							</li>
						@endforeach
					@elseif(isset($task->checklistItems) && $task->checklistItems && count($task->checklistItems) > 0)
						@foreach($task->checklistItems as $index => $item)
							<li class="flex items-center gap-2">
								<input type="hidden" name="checklist_completed[{{ $index }}]" value="{{ ($item->completed ?? false) ? '1' : '0' }}" class="checklist-completed">
								<input type="checkbox" class="check-item" {{ $item->completed ?? false ? 'checked' : '' }}>
								<input type="text" name="checklist[]" value="{{ $item->title ?? '' }}" class="border-2 border-orange-200 rounded-lg flex-1 px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium {{ ($item->completed ?? false) ? 'line-through text-gray-400' : '' }}">
								<button type="button" class="remove-item px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-300 font-semibold">✖️</button>
							</li>
						@endforeach
					@endif
				</ul>

				<div class="flex gap-2 mt-4">
					<input type="text" id="newChecklistItem" placeholder="Add checklist item..." class="flex-1 p-3 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium">
					<button type="button" id="addChecklistItem" class="px-5 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 font-bold shadow-md hover:shadow-lg hover:scale-105 active:scale-95 inline-flex items-center gap-2">
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
				@if($task->attachment)
					<div class="mt-2 text-sm">
						Current: <a href="{{ asset('storage/'.$task->attachment) }}" target="_blank" class="text-orange-600 hover:underline">View attachment</a>
					</div>
				@endif
			</div>

			<!-- Actions: Cancel + Save -->
			<div class="flex gap-4 pt-4">
				<a href="{{ $task->trashed() ? route('tasks.recycle') : (request()->get('back') === 'show' ? route('tasks.show', $task->id) : route('dashboard')) }}" 
				   class="flex-1 text-center px-6 py-3 border-2 border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-100 transition-all duration-300 inline-flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
					<i data-lucide="x" class="w-5 h-5"></i>
					Cancel
				</a>
				<button type="submit"
					class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-xl font-bold hover:from-orange-600 hover:to-orange-700 transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center justify-center gap-2 hover:scale-105 active:scale-95">
					<i data-lucide="save" class="w-5 h-5"></i>
					Save Changes
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
		const desc = document.getElementById('descriptionInput');
		if (desc) desc.value = speechText;
		sessionStorage.removeItem('speechText');
		sessionStorage.removeItem('speechLanguage');
	}

	// Checklist behavior (add/remove items)
	const checklist = document.getElementById('checklist');
	const addBtn = document.getElementById('addChecklistItem');
	const newItemInput = document.getElementById('newChecklistItem');

	function addChecklistItem() {
		if (!newItemInput || !checklist) return;
		const title = newItemInput.value.trim();
		if (!title) return;

		const li = document.createElement('li');
		li.className = "flex items-center gap-2";
		const index = checklist.querySelectorAll('li').length;
		li.innerHTML = `
			<input type="hidden" name="checklist_completed[${index}]" value="0" class="checklist-completed">
			<input type="checkbox" class="check-item">
			<input type="text" name="checklist[]" value="${title.replace(/"/g, '&quot;')}" class="border-2 border-orange-200 rounded-lg flex-1 px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium">
			<button type="button" class="remove-item px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-300 font-semibold">✖️</button>
		`;
		checklist.appendChild(li);
		newItemInput.value = '';
		newItemInput.focus();
	}

	if (addBtn) {
		addBtn.addEventListener('click', addChecklistItem);
	}

	// Add Enter key support
	if (newItemInput) {
		newItemInput.addEventListener('keypress', (e) => {
			if (e.key === 'Enter') {
				e.preventDefault();
				addChecklistItem();
			}
		});
	}

	if (checklist) {
		checklist.addEventListener('click', (e) => {
			if (e.target.classList.contains('remove-item')) {
				e.target.closest('li').remove();
			}
			if (e.target.classList.contains('check-item')) {
				const li = e.target.closest('li');
				const input = e.target.nextElementSibling;
				const hiddenInput = li.querySelector('.checklist-completed');
				
				if (input && input.tagName === 'INPUT') {
					const isChecked = e.target.checked;
					if (isChecked) {
						input.classList.add('line-through', 'text-gray-400');
						if (hiddenInput) hiddenInput.value = '1';
					} else {
						input.classList.remove('line-through', 'text-gray-400');
						if (hiddenInput) hiddenInput.value = '0';
					}
				}
			}
		});
	}
</script>
@endsection
