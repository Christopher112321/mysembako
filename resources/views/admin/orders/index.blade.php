@extends('admin.layout')

@section('title', 'Kelola Pesanan')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Kelola Pesanan</h1>
    <p class="text-gray-600 mt-2">Kelola semua pesanan dari pembeli</p>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Buyer</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Total Harga</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Payment Method</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $order->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-gray-900">{{ $order->buyer->name ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $order->buyer->email ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-orange-500">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'paid') bg-blue-100 text-blue-800
                                @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                                @elseif($order->status == 'done') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $order->payment_method ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.orders.show', $order) }}" class="px-3 py-1 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $orders->links() }}
    </div>
</div>
@endsection

