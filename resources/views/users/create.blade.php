@extends('layouts.shell')

@section('title', 'Add User')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">ADD USER</h1>
            <p class="text-xs text-gray-400">Account settings / Add new user</p>
        </div>
        <a href="{{ route('profile.edit') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-xs max-w-xl">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('users.store') }}" class="max-w-xl">
        @csrf
        <div class="bg-white border border-rose-100 rounded-xl p-6 shadow-sm space-y-4">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">NAME *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full text-xs border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">EMAIL *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full text-xs border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">PASSWORD *</label>
                <input type="password" name="password" required class="w-full text-xs border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">ROLE / DASHBOARD ACCESS *</label>
                <select name="role" required class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">Select Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(old('role') === $role)>{{ ucwords(str_replace('_', ' ', $role)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">EMPLOYMENT TYPE *</label>
                <select name="employment_type" required class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">Select</option>
                    @foreach ($employmentTypes as $type)
                        <option value="{{ $type }}" @selected(old('employment_type') === $type)>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="grant_admin" id="grant_admin" value="1" @checked(old('grant_admin'))>
                <label for="grant_admin" class="text-xs text-gray-600">Grant admin privileges (can create/manage other users)</label>
            </div>

            <div class="text-right pt-2">
                <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                    CREATE USER
                </button>
            </div>
        </div>
    </form>
@endsection