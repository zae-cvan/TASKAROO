@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    <!-- Admin Task Edit Card (matches user create layout) -->
    <div class="bg-gradient-to-br from-white to-orange-50 rounded-3xl p-8 shadow-2xl border-2 border-orange-100 space-y-6">

        <div class="border-b-2 border-orange-200 pb-6">
            <h2 class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent mb-2">Edit Task (Admin)</h2>
            <p class="text-gray-600 font-medium">Modify task details and assignment</p>
        </div>

        @if ($errors->has('due_date'))
            <div class="mb-4 p-4 bg-red-100 border-2 border-red-400 text-red-700 rounded-xl font-semibold">
                <i data-lucide="alert-circle" class="w-5 h-5 inline mr-2"></i>
                <strong>Error:</strong> {{ $errors->first('due_date') }}
            </div>
        @endif

        <form action="{{ route('admin.tasks.update', $task->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-gray-700 mb-2 flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-5 h-5 text-orange-600"></i>
                    Task Title
                </label>
                <input type="text" name="title" value="{{ old('title', $task->title) }}" class="w-full p-4 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300" required>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-2 flex items-center gap-2">
                    <i data-lucide="file-text" class="w-5 h-5 text-orange-600"></i>
                    Description
                </label>
                <textarea name="description" rows="4" class="w-full p-4 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-medium hover:border-orange-300">{{ old('description', $task->description) }}</textarea>
            </div>

            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-5 h-5 text-orange-600"></i>
                        Due Date
                    </label>
                    <input type="datetime-local" name="due_date" value="{{ old('due_date', $task->due_date ? $task->due_date->timezone('Asia/Manila')->format('Y-m-d\\TH:i') : '' ) }}" class="w-full p-4 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <i data-lucide="user" class="w-5 h-5 text-orange-600"></i>
                        Assign To
                    </label>
                    <select name="user_id" class="w-full p-4 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300" required>
                        <option value="">-- Unassigned --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (old('user_id', $task->user_id) == $user->id) ? 'selected' : '' }}>{{ $user->name }} &lt;{{ $user->email }}&gt;</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-2 flex items-center gap-2">
                    <i data-lucide="zap" class="w-5 h-5 text-orange-600"></i>
                    Urgency
                </label>
                <select name="urgency" class="w-full p-4 border-2 border-orange-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300">
                    <option value="very_urgent" {{ old('urgency', $task->urgency) == 'very_urgent' ? 'selected' : '' }}>Very Urgent</option>
                    <option value="urgent" {{ old('urgency', $task->urgency) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="normal" {{ old('urgency', $task->urgency) == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="least_urgent" {{ old('urgency', $task->urgency) == 'least_urgent' ? 'selected' : '' }}>Least Urgent</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-2 flex items-center gap-2">
                    <i data-lucide="paperclip" class="w-5 h-5 text-orange-600"></i>
                    Attachment (optional)
                </label>
                @if($task->attachment)
                    <div class="mb-2">
                        <a href="{{ asset('storage/' . $task->attachment) }}" target="_blank" class="text-orange-600 underline">View current attachment</a>
                    </div>
                @endif
                <input type="file" name="attachment" class="w-full p-4 border-2 border-orange-200 rounded-xl">
            </div>

            <div class="flex gap-4 pt-2">
                <a href="{{ route('admin.dashboard') }}" class="flex-1 text-center px-6 py-3 border-2 border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-100 transition-all duration-300">Cancel</a>
                <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-xl font-bold hover:from-orange-600 hover:to-orange-700 transition-all duration-300">Update Task</button>
            </div>

        </form>

    </div>

</div>

@endsection
