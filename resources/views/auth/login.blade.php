<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — EM Power Beautiful Skin</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-rose-50 relative overflow-hidden flex items-center justify-center">

    {{-- Decorative gradient blob, bottom-right --}}
    <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full opacity-80"
         style="background: radial-gradient(circle at 30% 30%, #f472b6, #a855f7 70%);">
    </div>

    <div class="relative z-10 bg-white rounded-2xl shadow-xl p-10 w-full max-w-md mx-4">

        {{-- Logo + name --}}
        <div class="flex flex-col items-center text-center mb-2">
            <img src="{{ asset('logo.svg') }}" alt="EM Power Beautiful Skin" class="w-20 h-20 rounded-full object-cover">
            <h1 class="text-xl font-bold text-gray-800">E<span class="text-pink-600">M</span> Power Beautiful Skin</h1>
            <div class="text-[10px] tracking-widest text-gray-800">CORPORATION</div>
        </div>

        <p class="text-center text-xs italic text-gray-400 mb-8">"Serving quality beyond the basics"</p>

        {{-- Session Status --}}
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Username / Email --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 mb-1">USERNAME</label>
                <div class="flex items-center border border-gray-300 rounded-full px-4 py-2.5">
                    <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           autocomplete="username" placeholder="Type here..."
                           class="border-0 ring-0 focus:ring-0 focus:outline-none bg-transparent text-sm w-full p-0">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div class="mb-2">
                <label class="block text-xs font-semibold text-gray-500 mb-1">PASSWORD</label>
                <div class="flex items-center border border-gray-300 rounded-full px-4 py-2.5">
                    <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <input id="password" type="password" name="password" required
                           autocomplete="current-password" placeholder="Type here..."
                           class="border-0 ring-0 focus:ring-0 focus:outline-none bg-transparent text-sm w-full p-0">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between mb-6 mt-3">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-pink-600 shadow-sm focus:ring-pink-500" name="remember">
                    <span class="ms-2 text-xs text-gray-500">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-xs text-gray-500 hover:text-pink-600 underline" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <button type="submit" class="w-full py-3 bg-black text-white text-sm font-semibold rounded-full hover:bg-gray-800 transition">
                Sign In
            </button>
        </form>
    </div>
</body>
</html>