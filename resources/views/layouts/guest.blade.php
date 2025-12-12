<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Taskaroo') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">

    @include('partials.dark-mode')
    
    <style>
        body {
            background: linear-gradient(135deg, #FFE5D9 0%, #FFD4C4 25%, #FFB8A3 50%, #FF9F7F 75%, #FF8C6B 100%);
            min-height: 100vh;
        }
        
        .guest-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(223, 82, 25, 0.2);
        }
        
        /* Dark mode for guest layout */
        html.dark-mode body {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 25%, #151515 50%, #1a1a1a 75%, #0f0f0f 100%) !important;
        }
        
        html.dark-mode .guest-container {
            background: rgba(31, 31, 31, 0.95) !important;
            border: 2px solid rgba(234, 88, 12, 0.4) !important;
            color: #ffffff !important;
        }
        
        html.dark-mode .guest-container * {
            color: #ffffff !important;
        }
        
        html.dark-mode .guest-container input,
        html.dark-mode .guest-container select,
        html.dark-mode .guest-container textarea {
            background-color: #2a2a2a !important;
            color: #ffffff !important;
            border-color: #4a4a4a !important;
        }
        
        html.dark-mode .guest-container input:focus,
        html.dark-mode .guest-container select:focus,
        html.dark-mode .guest-container textarea:focus {
            background-color: #2f2f2f !important;
            border-color: #ea580c !important;
        }
        
        html.dark-mode .guest-container label {
            color: #e5e7eb !important;
        }
        
        html.dark-mode .guest-container a {
            color: #fb923c !important;
        }
        
        html.dark-mode .guest-container a:hover {
            color: #f97316 !important;
        }
        
        html.dark-mode .guest-container button {
            color: #ffffff !important;
        }
        
        html.dark-mode .guest-container .text-gray-600,
        html.dark-mode .guest-container .text-gray-500 {
            color: #d1d5db !important;
        }
    </style>
</head>
<body class="font-sans antialiased flex items-center justify-center min-h-screen p-4">

   <div class="guest-container w-full max-w-md rounded-3xl shadow-2xl p-8">
    <div class="text-center mb-8">

        <!-- Removed the orange gradient box, icon now stands alone -->
        <i data-lucide="flower" class="w-12 h-12 text-orange-600 mx-auto mb-4"></i>

    </div>


        {{ $slot }}
    </div>

</body>
</html>
