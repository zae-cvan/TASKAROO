@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-6xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-orange-600 to-orange-500">User Management</h1>
            <p class="text-sm text-gray-600">View, search and manage application users.</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="history.back()" class="px-4 py-2 bg-white border text-gray-800 rounded shadow-sm">← Back</button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-orange-100 text-orange-800 rounded">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between">
            <div class="flex items-center gap-3">
                <input id="userSearch" type="search" placeholder="Search users..." class="border px-3 py-2 rounded w-72" />
                <span class="text-sm text-gray-500">Showing {{ $users->total() }} users</span>
            </div>
            <div>
                <!-- future: bulk actions -->
            </div>
        </div>

        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 w-20">Photo</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Active</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                @foreach($users as $user)
                    <tr class="border-t hover:bg-orange-50">
                        <td class="px-4 py-3 flex items-center justify-center">
                            @if($user->profile_photo)
                                <div class="w-12 h-12 rounded-full overflow-hidden shadow-lg ring-2 ring-offset-1 ring-orange-200">
                                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg ring-2 ring-offset-1 ring-orange-200">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->role }}</td>
                        <td class="px-4 py-3">{{ $user->is_active ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" style="display:inline">
                                @csrf
                                <button class="px-3 py-1 text-sm rounded {{ $user->is_active ? 'bg-gray-300 text-gray-800' : 'bg-orange-500 text-white' }}">{{ $user->is_active ? 'Disable' : 'Enable' }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</div>

@endsection

@section('scripts')
<script>
document.getElementById('userSearch')?.addEventListener('input', function(e){
    const q = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#usersTableBody tr');
    rows.forEach(r => {
        r.style.display = (r.textContent.toLowerCase().includes(q)) ? '' : 'none';
    });
});
</script>
@endsection
