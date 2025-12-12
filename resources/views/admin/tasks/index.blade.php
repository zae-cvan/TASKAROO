@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-5xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-orange-600 to-orange-500">Tasks You Created</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.tasks.index', ['status' => 'active']) }}" class="px-3 py-2 rounded {{ $status === 'active' ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white' : 'bg-white border' }}">Active</a>
            <a href="{{ route('admin.tasks.index', ['status' => 'completed']) }}" class="px-3 py-2 rounded {{ $status === 'completed' ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white' : 'bg-white border' }}">Completed</a>
            <a href="{{ route('admin.tasks.index', ['status' => 'archived']) }}" class="px-3 py-2 rounded {{ $status === 'archived' ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white' : 'bg-white border' }}">Archived</a>
            <a href="{{ route('admin.tasks.recycle') }}" class="px-3 py-2 rounded {{ $status === 'recycle' ? 'bg-red-500 text-white' : 'bg-white border' }}">Recycle Bin</a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        @if($tasks->count())
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Assigned To</th>
                        <th class="px-4 py-3">Due</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                        <tr class="border-t hover:bg-orange-50">
                            <td class="px-4 py-3">{{ $task->title }}</td>
                            <td class="px-4 py-3">{{ optional($task->user)->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->toFormattedDateString() : '—' }}</td>
                            <td class="px-4 py-3">{{ ucfirst($task->status) }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.tasks.edit', $task->id) }}" class="px-2 py-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded mr-2">Edit</a>
                                <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" style="display:inline;" class="admin-delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" data-task-id="{{ $task->id }}" data-task-title="{{ $task->title }}" class="px-2 py-1 bg-red-600 text-white rounded open-delete-modal">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-4 text-gray-500">No tasks found for this filter.</div>
        @endif
    </div>
</div>
@endsection
