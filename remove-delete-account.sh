#!/bin/bash
set -e
mkdir -p resources/views/profile

echo 'Writing resources/views/profile/edit.blade.php'
cat > resources/views/profile/edit.blade.php << 'PROFEOF2'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile — EM Power Beautiful Skin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-rose-50 text-gray-800 text-sm">

    <div class="bg-rose-100 border-b border-rose-200 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-xs">EM</div>
            <div class="leading-tight">
                <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                <div class="text-[10px] tracking-widest text-gray-400">CORPORATION</div>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <input type="text" placeholder="search asset ID/Employee..." class="text-xs border-gray-300 rounded-full px-4 py-1.5 w-56">
            <x-notification-bell />
            <x-account-menu />
        </div>
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#8962;</span> Dashboard</a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128421;</span> Assets</a>
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128101;</span> Employees</a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#9881;</span> Maintenance</a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100"><span>&#128196;</span> Reports</a>
        </div>

        <div class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-lg font-bold">MY PROFILE</h1>
                <p class="text-xs text-gray-400">Account settings</p>
            </div>

            <div class="max-w-2xl space-y-6">

                <div class="bg-white border border-rose-100 rounded-xl p-6 shadow-sm">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Profile Information</h2>
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="bg-white border border-rose-100 rounded-xl p-6 shadow-sm">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Update Password</h2>
                    @include('profile.partials.update-password-form')
                </div>

            </div>
        </div>
    </div>
</body>
</html>

PROFEOF2
echo 'Delete Account section removed!'