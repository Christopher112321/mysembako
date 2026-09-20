@extends('admin.layout')

@section('title', 'Kelola Produk')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Kelola Produk</h1>
    <p class="text-gray-600 mt-2">Kelola semua produk dari semua toko</p>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Foto</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Nama Produk</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Owner</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Harga</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Stok</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-lg">
                            @else
                                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs">No Image</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">{{ $product->name }}</div>
                            <div class="text-sm text-gray-500 truncate max-w-xs">{{ $product->description ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $product->owner->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-orange-500">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-semibold {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($product->status == 'aktif') bg-green-100 text-green-800
                                @elseif($product->status == 'habis') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $product->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.products.show', $product) }}" class="px-3 py-1 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600">Detail</a>
                                <form action="{{ route('admin.products.delete', $product) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600">Hapus</button>
                                </form>
                            </div>
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
        {{ $products->links() }}
    </div>
</div>
@endsection

