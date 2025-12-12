@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-white to-orange-50 rounded-3xl shadow-xl p-8 mb-8 border-2 border-orange-100">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i data-lucide="bell" class="w-8 h-8 text-white"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent">Notifications</h1>
                <p class="text-gray-600 mt-1 font-medium">Stay updated with your task notifications</p>
            </div>
        </div>
    </div>

    @if($notifications->count())
        <div class="space-y-4">
            @foreach($notifications as $notif)
                <div class="p-6 bg-gradient-to-r from-white to-orange-50 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border-2 border-orange-100 flex justify-between items-start hover:border-orange-200 group">
                    <div class="flex items-start gap-4 flex-1">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0 group-hover:shadow-lg transition-all duration-300">
                            <i data-lucide="mail" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-lg font-bold text-gray-800">{{ $notif->data['title'] ?? 'No title' }}</p>
                            @php
                                $now = \Carbon\Carbon::now();
                                $diffSeconds = (int) $notif->created_at->diffInSeconds($now);
                                $diffMinutes = (int) $notif->created_at->diffInMinutes($now);
                                $diffHours = (int) $notif->created_at->diffInHours($now);
                                $diffDays = (int) $notif->created_at->diffInDays($now);
                                
                                if ($diffSeconds < 60) {
                                    $when = $diffSeconds . ' second' . ($diffSeconds !== 1 ? 's' : '') . ' ago';
                                } elseif ($diffMinutes < 60) {
                                    $when = $diffMinutes . ' minute' . ($diffMinutes !== 1 ? 's' : '') . ' ago';
                                } elseif ($diffHours < 24) {
                                    $when = $diffHours . ' hour' . ($diffHours !== 1 ? 's' : '') . ' ago';
                                } elseif ($diffDays < 7) {
                                    $when = $diffDays . ' day' . ($diffDays !== 1 ? 's' : '') . ' ago';
                                } else {
                                    $when = $notif->created_at->format('M d, Y');
                                }
                            @endphp
                            <p class="text-sm text-gray-600 mt-1 font-medium">{{ $when }}</p>
                        </div>
                    </div>
                    @if(isset($notif->data['task_id']))
                        <a href="{{ url('/tasks/'.$notif->data['task_id']) }}" class="ml-4 px-6 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all duration-300 inline-flex items-center gap-2 hover:scale-105 active:scale-95 whitespace-nowrap flex-shrink-0">
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            Open
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-3xl shadow-xl p-16 border-2 border-orange-100 text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-orange-100 to-orange-200 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                <i data-lucide="inbox" class="w-10 h-10 text-orange-600"></i>
            </div>
            <p class="text-gray-700 text-xl font-bold">No notifications yet</p>
            <p class="text-gray-500 text-sm mt-2">Check back later for updates!</p>
        </div>
    @endif
</div>
@endsection
