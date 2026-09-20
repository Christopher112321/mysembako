@extends('admin.layout')

@section('title', 'Edit User')

@section('content')
<div class="mb-8">
    <a href="{{ route('admin.users') }}" class="text-orange-500 hover:text-orange-600 mb-4 inline-block">← Kembali</a>
    <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
</div>

<div class="bg-white rounded-xl shadow-sm p-6">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
        </div>
        
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
        </div>
        
        <div>
            <label for="role_id" class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
            <select id="role_id" name="role_id" required
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                        {{ $role->name }} - {{ $role->description }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
        </div>
        
        <div class="flex gap-4">
            <button type="submit" class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-semibold">
                Update User
            </button>
            <a href="{{ route('admin.users') }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

