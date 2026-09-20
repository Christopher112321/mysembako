<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - MySembako</title>
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
            <h1 class="text-3xl font-bold text-gray-900">Kelola Pesanan</h1>
            <p class="text-gray-600 mt-2">Kelola semua pesanan untuk produk Anda</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Status Filter Tabs -->
        <div class="mb-6 bg-white rounded-xl shadow-sm p-4">
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('owner.orders.index', ['status' => 'all']) }}" 
                   class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors {{ $status === 'all' ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Semua
                </a>
                <a href="{{ route('owner.orders.index', ['status' => 'pending']) }}" 
                   class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors {{ $status === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Pending
                </a>
                <a href="{{ route('owner.orders.index', ['status' => 'paid']) }}" 
                   class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors {{ $status === 'paid' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Paid
                </a>
                <a href="{{ route('owner.orders.index', ['status' => 'done']) }}" 
                   class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors {{ $status === 'done' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Done
                </a>
                <a href="{{ route('owner.orders.index', ['status' => 'cancel']) }}" 
                   class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors {{ $status === 'cancel' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Cancel
                </a>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">ID Pesanan</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Pembeli</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Subtotal Produk Saya</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Metode Pembayaran</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $order->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">{{ $order->buyer->name ?? '-' }}</div>
                                        <div class="text-sm text-gray-500">{{ $order->buyer->email ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-orange-500">Rp {{ number_format($order->subtotal ?? 0, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status == 'paid') bg-blue-100 text-blue-800
                                            @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                                            @elseif($order->status == 'done') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $order->payment_method ? ucfirst($order->payment_method) : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $order->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('owner.orders.show', $order) }}" class="px-3 py-1 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition-colors">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Menampilkan 
                            <span class="font-semibold">{{ $orders->firstItem() }}</span>
                            sampai
                            <span class="font-semibold">{{ $orders->lastItem() }}</span>
                            dari
                            <span class="font-semibold">{{ $orders->total() }}</span>
                            pesanan
                        </div>
                        <div class="flex gap-2">
                            {{ $orders->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="mx-auto h-20 w-20 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum ada pesanan</h3>
                    <p class="text-gray-500">
                        @if($status !== 'all')
                            Tidak ada pesanan dengan status {{ ucfirst($status) }}
                        @else
                            Pesanan untuk produk Anda akan muncul di sini
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </main>
</body>
</html>

