<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - MySembako</title>
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
                        {{ session('store_name', Auth::user()->name ?? 'MySembako Admin') }}
                    </span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-full font-semibold text-sm hover:bg-gray-700 transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Kelola Produk</h1>
            <div class="flex gap-3">
                <a href="{{ route('owner.orders.index') }}" class="px-6 py-3 bg-blue-500 text-white rounded-full font-semibold hover:bg-blue-600 transition-colors">
                    Kelola Pesanan
                </a>
                <a href="{{ route('products.create') }}" class="px-6 py-3 bg-orange-500 text-white rounded-full font-semibold hover:bg-orange-600 transition-colors">
                    + Tambah Produk
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Products Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @if($products->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Foto</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Produk</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Stok</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($products as $product)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                        @else
                                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs">
                                                No Image
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">{{ $product->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-orange-500">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <span class="font-semibold {{ ($product->stock ?? 0) > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $product->stock ?? 0 }}
                                            </span>
                                            <button 
                                                onclick="openStokModal({{ $product->id }}, '{{ $product->name }}', {{ $product->stock ?? 0 }})"
                                                class="px-3 py-1 bg-green-500 text-white rounded-lg text-xs font-semibold hover:bg-green-600 transition-colors"
                                                title="Update Stok"
                                            >
                                                Update
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600 max-w-xs truncate">{{ $product->description ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex gap-2">
                                            <a href="{{ route('products.edit', $product->id) }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-semibold hover:bg-blue-600 transition-colors">
                                                Edit
                                            </a>
                                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-semibold hover:bg-red-600 transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <svg class="mx-auto h-20 w-20 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum ada produk</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan menambahkan produk pertama Anda</p>
                    <a href="{{ route('products.create') }}" class="inline-block px-6 py-3 bg-orange-500 text-white rounded-full font-semibold hover:bg-orange-600 transition-colors">
                        Tambah Produk
                    </a>
                </div>
            @endif
        </div>
    </main>

    <!-- Modal Update Stok -->
    <div id="stokModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl p-8 max-w-md w-full mx-4">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Update Stok Produk</h2>
            <p class="text-gray-600 mb-6" id="productName"></p>
            
            <form id="stokForm" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                
                <div>
                    <label for="stok" class="block text-sm font-semibold text-gray-700 mb-2">
                        Jumlah Stok <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="stok" 
                        name="stok" 
                        min="0" 
                        step="1" 
                        required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-full focus:outline-none focus:border-orange-500 transition-colors"
                        placeholder="Masukkan jumlah stok"
                    >
                </div>

                <div class="flex gap-4 pt-4">
                    <button 
                        type="button" 
                        onclick="closeStokModal()"
                        class="flex-1 px-6 py-3 bg-gray-600 text-white rounded-full font-semibold hover:bg-gray-700 transition-colors"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="flex-1 px-6 py-3 bg-orange-500 text-white rounded-full font-semibold hover:bg-orange-600 transition-colors"
                    >
                        Update Stok
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openStokModal(productId, productName, currentStok) {
            const modal = document.getElementById('stokModal');
            const form = document.getElementById('stokForm');
            const nameElement = document.getElementById('productName');
            const stokInput = document.getElementById('stok');
            
            nameElement.textContent = `Produk: ${productName}`;
            stokInput.value = currentStok;
            form.action = `/products/${productId}/update-stok`;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeStokModal() {
            const modal = document.getElementById('stokModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('stokModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStokModal();
            }
        });
    </script>
</body>
</html>

