@extends('admin.layout')

@section('title', 'Activity Logs')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Activity Logs</h1>
    <p class="text-gray-600 mt-2">Riwayat aktivitas sistem</p>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Description</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Waktu</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-gray-900">{{ $log->user->name ?? 'System' }}</div>
                            <div class="text-sm text-gray-500">{{ $log->user->email ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">{{ $log->action }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">{{ $log->description ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $log->created_at->format('d M Y H:i') }}
                            <div class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $logs->links() }}
    </div>
</div>
@endsection

