@extends('admin.layout')

@section('title', 'Kelola Users')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Kelola Users</h1>
    <p class="text-gray-600 mt-2">Kelola semua pengguna sistem</p>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Nama</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Role</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Phone</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($user->role_id == 1) bg-blue-100 text-blue-800
                                @elseif($user->role_id == 2) bg-green-100 text-green-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                {{ $user->role->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->phone ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="px-3 py-1 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600">Detail</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="px-3 py-1 bg-orange-500 text-white rounded-lg text-sm hover:bg-orange-600">Edit</a>
                                <form action="{{ route('admin.users.delete', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->links() }}
    </div>
</div>
@endsection

