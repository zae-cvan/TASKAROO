<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Taskaroo') }} - Accomplish More, Effortlessly</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            width: 100vw;
            height: 100vh;
            position: relative;
        }
        
        /* Background with energetic gradient */
        .landing-container {
            width: 100%;
            height: 100vh;
            position: relative;
            background: linear-gradient(135deg, #FFE5D9 0%, #FFD4C4 25%, #FFB8A3 50%, #FF9F7F 75%, #FF8C6B 100%);
            overflow: hidden;
        }
        
        /* Large asymmetrical diagonal gradient shape - soft "/" style */
        .landing-container::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 65%;
            height: 100%;
            background: linear-gradient(
                135deg,
                rgba(255, 235, 215, 0.35) 0%,
                rgba(255, 220, 200, 0.3) 15%,
                rgba(255, 205, 185, 0.25) 30%,
                rgba(255, 190, 170, 0.2) 45%,
                rgba(255, 175, 155, 0.15) 60%,
                rgba(255, 160, 140, 0.1) 75%,
                transparent 100%
            );
            clip-path: polygon(0 0, 100% 0, 100% 100%, 20% 100%);
            z-index: 0;
        }
        
        /* Enhanced grid pattern overlay */
        .grid-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 50px 50px;
            opacity: 0.6;
            z-index: 0;
        }
        
        /* Subtle radial accent overlay */
        .landing-container::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: radial-gradient(
                ellipse at 80% 30%,
                rgba(255, 200, 180, 0.12) 0%,
                rgba(255, 184, 163, 0.08) 30%,
                transparent 60%
            );
            z-index: 0;
        }
        
        /* Soft diagonal gradient accent - organic flow */
        .diagonal-gradient-shape {
            position: absolute;
            top: 0;
            right: 0;
            width: 60%;
            height: 100%;
            background: linear-gradient(
                140deg,
                rgba(255, 245, 235, 0.2) 0%,
                rgba(255, 230, 215, 0.18) 20%,
                rgba(255, 215, 195, 0.15) 40%,
                rgba(255, 200, 175, 0.12) 60%,
                rgba(255, 185, 160, 0.08) 80%,
                transparent 100%
            );
            clip-path: polygon(0 0, 100% 0, 100% 100%, 25% 100%);
            z-index: 0;
        }
        
        /* Main content wrapper */
        .content-wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
            align-items: center;
            justify-content: space-between;
            padding: 0 8%;
            position: relative;
            z-index: 1;
        }
        
        /* Left side content */
        .left-content {
            flex: 0 0 45%;
            max-width: 600px;
            z-index: 2;
        }
        
        .headline {
            font-size: 5.75rem;
            font-weight: 800;
            line-height: 1.05;
            margin-bottom: 2rem;
            color: #1a1a1a;
            letter-spacing: -0.02em;
        }
        
        .headline-line-1,
        .headline-line-2 {
            display: block;
            color: #1a1a1a;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        .headline-line-1 {
            animation-delay: 0.2s;
        }
        
        .headline-line-2 {
            animation-delay: 0.4s;
        }
        
        .headline-line-3 {
            display: block;
            background: linear-gradient(135deg, #DF5219 0%, #FFA358 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
            animation-delay: 0.6s;
        }
        
        .subheadline {
            font-size: 1.125rem;
            line-height: 1.75;
            color: #3a3a3a;
            margin-bottom: 2.75rem;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
            animation-delay: 0.8s;
            font-weight: 400;
        }
        
        .buttons-container {
            display: flex;
            gap: 1rem;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
            animation-delay: 1s;
        }
        
        .btn-login {
            background: #DF5219;
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-login:hover {
            background: #FFA358;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(223, 82, 25, 0.3);
        }
        
        .btn-signup {
            background: #F5E6D9;
            color: #3a3a3a;
            padding: 0.875rem 2rem;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            border: 1px solid rgba(0, 0, 0, 0.08);
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-signup:hover {
            border-color: #DF5219;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-signup::after {
            content: '→';
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }
        
        .btn-signup:hover::after {
            transform: translateX(4px);
        }
        
        /* Right side - Logo section */
        .right-content {
            flex: 0 0 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            height: 100%;
        }
        
        .logo-container {
            position: relative;
            width: 500px;
            height: 500px;
            opacity: 0;
            animation: fadeInScale 1s ease-out forwards;
            animation-delay: 0.5s;
        }
        
        /* Outer border wrapper */
        .logo-container::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 580px;
            height: 580px;
            border-radius: 50%;
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 0 1px rgba(223, 82, 25, 0.2);
            z-index: 1;
            pointer-events: none;
        }
        
        /* Circular frame with glow */
        .circular-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: linear-gradient(135deg, #F5E6D9 0%, #EEE9E6 50%, #F5E6D9 100%);
            border: 1px solid rgba(223, 82, 25, 0.15);
            box-shadow: 
                0 0 80px rgba(223, 82, 25, 0.2),
                0 0 150px rgba(223, 82, 25, 0.12),
                0 20px 60px rgba(0, 0, 0, 0.1),
                inset 0 0 50px rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }
        
        /* Logo image container */
        .logo-svg-container {
            position: relative;
            z-index: 3;
            width: 70%;
            height: 70%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
        }
        
        /* Revolving laser dot */
        .orbiting-dot {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin-left: -10px;
            margin-top: -10px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 163, 88, 0.5) 0%, rgba(223, 82, 25, 0.4) 70%);
            box-shadow: 
                0 0 10px rgba(255, 163, 88, 0.3),
                0 0 20px rgba(255, 163, 88, 0.2),
                0 0 30px rgba(255, 163, 88, 0.15),
                0 0 50px rgba(255, 163, 88, 0.08);
            transform-origin: center center;
            z-index: 4;
            animation: orbit 11s linear infinite;
        }
        
        @keyframes orbit {
            from {
                transform: rotate(0deg) translateX(250px) rotate(0deg);
            }
            to {
                transform: rotate(360deg) translateX(250px) rotate(-360deg);
            }
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <div class="diagonal-gradient-shape"></div>
        <div class="grid-pattern"></div>
        
        <div class="content-wrapper">
            <!-- Left Content -->
            <div class="left-content">
                <h1 class="headline">
                    <span class="headline-line-1">Accomplish</span>
                    <span class="headline-line-2">More,</span>
                    <span class="headline-line-3">Effortlessly.</span>
                </h1>
                
                <p class="subheadline">
                    Taskaroo helps individuals stay organized and productive with intelligent automation and machine-learning insights.
                </p>
                
                <div class="buttons-container">
                    <a href="{{ route('login') }}" class="btn-login">Log In</a>
                    <a href="{{ route('register') }}" class="btn-signup">Sign Up Free</a>
                </div>
            </div>
            
            <!-- Right Content - Logo -->
            <div class="right-content">
                <div class="logo-container">
                    <!-- Orbiting dot -->
                    <div class="orbiting-dot"></div>
                    
                    <!-- Circular frame -->
                    <div class="circular-frame">
                        <div class="logo-svg-container">
                            <img src="{{ asset('images/taskaroo-logo.png') }}" alt="Taskaroo Logo" class="logo-image" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

