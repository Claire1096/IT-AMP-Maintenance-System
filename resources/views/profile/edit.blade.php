@extends('layouts.shell')

@section('title', 'Profile')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">MY PROFILE</h1>
            <p class="text-xs text-gray-400">Account settings</p>
        </div>
        @if (auth()->user()->is_admin)
            <a href="{{ route('users.create') }}" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">+ ADD USER</a>
        @endif
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
@endsection