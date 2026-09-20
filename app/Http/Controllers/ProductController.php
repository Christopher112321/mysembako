<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Kumpulan kategori dasar yang umum untuk sembako.
     */
    private function defaultCategoryNames(): array
    {
        return [
            'Beras',
            'Minyak Goreng',
            'Gula Pasir',
            'Telur',
            'Bumbu Dapur',
            'Mie & Pasta',
            'Kopi & Teh',
            'Susu & Olahan',
            'Air Minum / Galon',
            'Gas LPG',
            'Snack & Cemilan',
        ];
    }

    /**
     * Pastikan kategori dasar tersedia di database dan kembalikan daftar kategori.
     */
    private function ensureDefaultCategories()
    {
        foreach ($this->defaultCategoryNames() as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        return Category::orderBy('name')->get();
    }

    /**
     * Dapatkan atau buat kategori berdasarkan nama, mengembalikan category_id.
     */
    private function resolveCategoryId(?string $categoryName): ?int
    {
        $name = trim($categoryName ?? '');
        if ($name === '') {
            return null;
        }

        $category = Category::firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name]
        );

        return $category->id;
    }

    public function index()
    {
        // Admin hanya melihat produknya sendiri
        $products = Product::where('owner_id', Auth::id())->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = $this->ensureDefaultCategories();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori'    => 'required|string|max:255',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'deskripsi'   => 'nullable|string',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Get or create category ID logic if necessary; for now, we'll just use 'category_id' as NULL if not implemented
        // You may retrieve category_id based on the given category name, or expect category_id to be provided directly.

        $categoryId = $this->resolveCategoryId($request->kategori);

        $data = [
            'owner_id'    => Auth::id(),
            'name'        => $request->nama_produk,
            'price'       => $request->harga,
            'stock'       => $request->stok,
            'description' => $request->deskripsi,
            'status'      => 'aktif',
            'category_id' => $categoryId,
        ];

        // Simple example: if you pass category_id directly
        // $data['category_id'] = $request->category_id;

        // Handle file upload for 'image' field (matches model Product)
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoPath = $foto->store('products', 'public');
            $data['image'] = $fotoPath;
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        // Pastikan admin hanya edit produknya sendiri
        if ($product->owner_id !== Auth::id()) {
            abort(403);
        }

        $categories = $this->ensureDefaultCategories();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->owner_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori'    => 'required|string|max:255',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'deskripsi'   => 'nullable|string',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $categoryId = $this->resolveCategoryId($request->kategori);

        $data = [
            'name'        => $request->nama_produk,
            'price'       => $request->harga,
            'stock'       => $request->stok,
            'description' => $request->deskripsi,
            // 'category_id' => $request->category_id, // Set if you have category logic
            'category_id' => $categoryId,
            'status'      => $product->status ?? 'aktif',
        ];

        // Handle file upload
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $foto = $request->file('foto');
            $fotoPath = $foto->store('products', 'public');
            $data['image'] = $fotoPath;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        if ($product->owner_id !== Auth::id()) {
            abort(403);
        }

        // Delete photo if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }

    public function updateStok(Request $request, Product $product)
    {
        if ($product->owner_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'stok' => 'required|integer|min:0',
        ]);

        $product->update([
            'stock' => $request->stok,
        ]);

        return redirect()->route('products.index')->with('success', 'Stok produk berhasil diperbarui!');
    }

    /**
     * API endpoint untuk mendapatkan semua produk aktif (untuk homepage)
     */
    public function apiIndex()
    {
        // Ambil produk dengan status aktif dan stok > 0
        $products = Product::where('status', 'aktif')
            ->where('stock', '>', 0)
            ->with(['owner', 'category'])
            ->get()
            ->map(function ($product) {
                // Format gambar - jika ada image, gunakan full URL, jika tidak gunakan placeholder
                $imageUrl = $product->image 
                    ? asset('storage/' . $product->image) 
                    : 'https://via.placeholder.com/150?text=No+Image';
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (int) $product->price, // Pastikan price adalah number
                    'img' => $imageUrl,
                    'store' => $product->owner->name ?? 'Unknown Store',
                    'description' => $product->description ?? '',
                    'stock' => $product->stock,
                    'category' => $product->category->name ?? null,
                    'category_id' => $product->category_id,
                ];
            });

        return response()->json($products);
    }

    /**
     * API endpoint untuk daftar kategori (termasuk default bawaan).
     */
    public function apiCategories()
    {
        $categories = $this->ensureDefaultCategories()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
            ];
        });

        return response()->json($categories);
    }
}
