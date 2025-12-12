@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-white to-orange-50 rounded-3xl shadow-xl p-8 mb-8 border-2 border-orange-100">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i data-lucide="history" class="w-8 h-8 text-white"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent">Activity Log</h1>
                <p class="text-gray-600 mt-1 font-medium">Track all your recent actions and changes</p>
            </div>
        </div>
    </div>

    <!-- Activity List -->
    <div class="bg-white rounded-3xl shadow-xl p-8 border-2 border-orange-100">
        @if($logs->isNotEmpty())
            <div class="space-y-4">
                @foreach($logs as $log)
                    <div class="flex gap-4 p-5 bg-gradient-to-r from-orange-50 to-white border-2 border-orange-100 rounded-2xl hover:shadow-lg hover:border-orange-200 transition-all duration-300 group">
                        
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-md group-hover:shadow-lg transition-all duration-300">
                                <i data-lucide="clipboard-check" class="w-6 h-6 text-white"></i>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-gray-800 mb-2 text-lg">{{ $log->action }}</div>
                            <div class="flex items-center gap-2 text-sm text-gray-500 font-medium">
                                <i data-lucide="clock" class="w-4 h-4 text-orange-600"></i>
                                <small>{{ $log->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                        </div>

                        <!-- Timeline Connector -->
                        <div class="flex-shrink-0 w-1 bg-gradient-to-b from-orange-400 to-orange-200 rounded-full"></div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gradient-to-br from-orange-100 to-orange-200 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <i data-lucide="inbox" class="w-10 h-10 text-orange-600"></i>
                </div>
                <p class="text-gray-600 text-xl font-bold">No activity recorded yet.</p>
                <p class="text-gray-500 text-sm mt-2">Your actions will appear here</p>
            </div>
        @endif
    </div>

</div>
@endsection
