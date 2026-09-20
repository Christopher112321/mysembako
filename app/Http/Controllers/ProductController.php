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
     * Resolves realistic, high-quality images for products
     */
    public static function resolveImageUrl($product): string
    {
        if (!empty($product->image)) {
            if (str_starts_with($product->image, 'http://') || str_starts_with($product->image, 'https://')) {
                return $product->image;
            }
            return asset('storage/' . $product->image);
        }

        $name = strtolower($product->name ?? '');
        $cat = strtolower($product->category->name ?? '');

        if (str_contains($name, 'beras') || str_contains($cat, 'beras')) {
            return 'https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=600&q=80';
        }
        if (str_contains($name, 'minyak') || str_contains($cat, 'minyak')) {
            return 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&w=600&q=80';
        }
        if (str_contains($name, 'gula') || str_contains($cat, 'gula')) {
            return 'https://images.unsplash.com/photo-1581441363689-1f3c3c414635?auto=format&fit=crop&w=600&q=80';
        }
        if (str_contains($name, 'telur') || str_contains($cat, 'telur')) {
            return 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?auto=format&fit=crop&w=600&q=80';
        }
        if (str_contains($name, 'bumbu') || str_contains($cat, 'bumbu')) {
            return 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=600&q=80';
        }
        if (str_contains($name, 'mie') || str_contains($name, 'indomie') || str_contains($cat, 'mie')) {
            return 'https://images.unsplash.com/photo-1612927601601-6638404737ce?auto=format&fit=crop&w=600&q=80';
        }
        if (str_contains($name, 'kopi') || str_contains($name, 'teh') || str_contains($cat, 'kopi')) {
            return 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=600&q=80';
        }
        if (str_contains($name, 'susu') || str_contains($cat, 'susu')) {
            return 'https://images.unsplash.com/photo-1550583724-b2692b85b150?auto=format&fit=crop&w=600&q=80';
        }
        if (str_contains($name, 'air') || str_contains($name, 'galon') || str_contains($cat, 'air')) {
            return 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?auto=format&fit=crop&w=600&q=80';
        }
        if (str_contains($name, 'gas') || str_contains($cat, 'gas')) {
            return 'https://images.unsplash.com/photo-1585670149967-b4f4da88cc9f?auto=format&fit=crop&w=600&q=80';
        }
        if (str_contains($name, 'snack') || str_contains($cat, 'snack')) {
            return 'https://images.unsplash.com/photo-1621996346565-e3d5d6281290?auto=format&fit=crop&w=600&q=80';
        }

        return 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=600&q=80';
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
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (int) $product->price,
                    'img' => self::resolveImageUrl($product),
                    'store' => $product->owner->name ?? 'Toko Sembako',
                    'store_location' => $product->owner->location ?? 'Indonesia',
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
