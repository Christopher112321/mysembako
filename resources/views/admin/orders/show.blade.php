@extends('admin.layout')

@section('title', 'Detail Pesanan')

@section('content')
<div class="mb-8">
    <a href="{{ route('admin.orders') }}" class="text-orange-500 hover:text-orange-600 mb-4 inline-block">← Kembali</a>
    <h1 class="text-3xl font-bold text-gray-900">Detail Pesanan #{{ $order->id }}</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Item Pesanan</h2>
            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $item->product->name ?? 'Produk dihapus' }}</p>
                            <p class="text-sm text-gray-600">Qty: {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-orange-500">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div>
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Pesanan</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Buyer</label>
                    <p class="text-gray-900">{{ $order->buyer->name ?? '-' }}</p>
                    <p class="text-sm text-gray-600">{{ $order->buyer->email ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Total Harga</label>
                    <p class="text-orange-500 text-2xl font-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status == 'paid') bg-blue-100 text-blue-800
                        @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                        @elseif($order->status == 'done') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ $order->status }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                    <p class="text-gray-900">{{ $order->payment_method ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <p class="text-gray-900">{{ $order->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Update Status</h2>
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                <select name="status" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 mb-4">
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="done" {{ $order->status == 'done' ? 'selected' : '' }}>Done</option>
                    <option value="cancel" {{ $order->status == 'cancel' ? 'selected' : '' }}>Cancel</option>
                </select>
                <button type="submit" class="w-full px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-semibold">
                    Update Status
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

