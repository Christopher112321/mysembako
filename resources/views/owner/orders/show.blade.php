<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - MySembako</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Poppins', Arial, Helvetica, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center gap-3">
                    <img src="/icon/Logo_Mysembako.svg" alt="Logo" class="h-9" onerror="this.style.display='none'">
                    <span class="text-xl font-bold text-orange-500 tracking-wide">
                        {{ Auth::user()->name ?? 'MySembako' }}
                    </span>
                </div>
                <div class="flex gap-4 items-center">
                    <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-full font-semibold text-sm hover:bg-gray-300 transition-colors">
                        Kelola Produk
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-full font-semibold text-sm hover:bg-gray-700 transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Page Header -->
        <div class="mb-8">
            <a href="{{ route('owner.orders.index') }}" class="text-orange-500 hover:text-orange-600 mb-4 inline-block">← Kembali ke Daftar Pesanan</a>
            <h1 class="text-3xl font-bold text-gray-900">Detail Pesanan #{{ $order->id }}</h1>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Order Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Item Pesanan (Produk Anda)</h2>
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
                        
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-gray-900">Subtotal Produk Anda:</span>
                                <span class="font-bold text-orange-500 text-lg">
                                    Rp {{ number_format($order->items->sum(function($item) { return $item->quantity * $item->price; }), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Order Info & Actions -->
            <div>
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Pesanan</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pembeli</label>
                            <p class="text-gray-900">{{ $order->buyer->name ?? '-' }}</p>
                            <p class="text-sm text-gray-600">{{ $order->buyer->email ?? '-' }}</p>
                            @if($order->buyer->phone)
                                <p class="text-sm text-gray-600">{{ $order->buyer->phone }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Total Harga Pesanan</label>
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
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran</label>
                            <p class="text-gray-900">{{ $order->payment_method ? ucfirst($order->payment_method) : '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                            <p class="text-gray-900">{{ $order->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Update Status Form -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Update Status</h2>
                    <form action="{{ route('owner.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 mb-4">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="done" {{ $order->status == 'done' ? 'selected' : '' }}>Done</option>
                            <option value="cancel" {{ $order->status == 'cancel' ? 'selected' : '' }}>Cancel</option>
                        </select>
                        <button type="submit" class="w-full px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-semibold transition-colors">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

