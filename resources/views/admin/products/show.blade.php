@extends('admin.layout')

@section('title', 'Detail Produk')

@section('content')
<div class="mb-8">
    <a href="{{ route('admin.products') }}" class="text-orange-500 hover:text-orange-600 mb-4 inline-block">← Kembali</a>
    <h1 class="text-3xl font-bold text-gray-900">Detail Produk</h1>
</div>

<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full rounded-lg">
            @else
                <div class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">No Image</div>
            @endif
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Produk</label>
                <p class="text-gray-900 text-lg font-semibold">{{ $product->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Owner</label>
                <p class="text-gray-900">{{ $product->owner->name ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Harga</label>
                <p class="text-orange-500 text-xl font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Stok</label>
                <p class="text-gray-900 font-semibold {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $product->stock }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                    @if($product->status == 'aktif') bg-green-100 text-green-800
                    @elseif($product->status == 'habis') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ $product->status }}
                </span>
            </div>
        </div>
    </div>
    
    <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
        <p class="text-gray-900">{{ $product->description ?? '-' }}</p>
    </div>
    
    <div class="flex gap-4">
        <form action="{{ route('admin.products.delete', $product) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Hapus Produk</button>
        </form>
    </div>
</div>
@endsection

