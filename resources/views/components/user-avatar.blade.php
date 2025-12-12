@props(['user', 'size' => 'md', 'class' => ''])

@php
    $sizeClasses = match($size) {
        'xs' => 'w-6 h-6 text-xs',
        'sm' => 'w-8 h-8 text-sm',
        'md' => 'w-12 h-12 text-base',
        'lg' => 'w-16 h-16 text-lg',
        'xl' => 'w-24 h-24 text-2xl',
        '2xl' => 'w-32 h-32 text-4xl',
        default => 'w-12 h-12 text-base',
    };
    
    $userInitial = strtoupper(substr($user->name ?? 'U', 0, 1));
@endphp

@if($user?->profile_photo)
    <div class="{{ $sizeClasses }} rounded-full overflow-hidden shadow-lg ring-2 ring-offset-1 ring-orange-200 flex-shrink-0 {{ $class }}">
        <img src="{{ asset('storage/' . $user->profile_photo) }}" 
             alt="{{ $user->name }}" 
             class="w-full h-full object-cover">
    </div>
@else
    <div class="{{ $sizeClasses }} bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg ring-2 ring-offset-1 ring-orange-200 flex-shrink-0 {{ $class }}">
        {{ $userInitial }}
    </div>
@endif
