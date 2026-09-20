@extends('admin.layout')

@section('title', 'Detail User')

@section('content')
<div class="mb-8">
    <a href="{{ route('admin.users') }}" class="text-orange-500 hover:text-orange-600 mb-4 inline-block">← Kembali</a>
    <h1 class="text-3xl font-bold text-gray-900">Detail User</h1>
</div>

<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama</label>
            <p class="text-gray-900">{{ $user->name }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
            <p class="text-gray-900">{{ $user->email }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                @if($user->role_id == 1) bg-blue-100 text-blue-800
                @elseif($user->role_id == 2) bg-green-100 text-green-800
                @else bg-purple-100 text-purple-800
                @endif">
                {{ $user->role->name ?? 'N/A' }}
            </span>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
            <p class="text-gray-900">{{ $user->phone ?? '-' }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Dibuat</label>
            <p class="text-gray-900">{{ $user->created_at->format('d M Y H:i') }}</p>
        </div>
    </div>
    
    <div class="mt-6 flex gap-4">
        <a href="{{ route('admin.users.edit', $user) }}" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">Edit User</a>
    </div>
</div>
@endsection

