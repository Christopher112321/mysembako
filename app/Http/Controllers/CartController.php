<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Get all cart items for authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $cartItems = Cart::where('user_id', $user->id)
            ->with('product.images', 'product.owner')
            ->get()
            ->map(function ($cartItem) {
                $product = $cartItem->product;
                
                // Format gambar - prioritaskan ProductImage, jika tidak ada gunakan image dari product
                $imageUrl = null;
                if ($product->images && $product->images->count() > 0) {
                    $imageUrl = asset('storage/' . $product->images->first()->image_path);
                } elseif ($product->image) {
                    $imageUrl = asset('storage/' . $product->image);
                } else {
                    $imageUrl = 'https://via.placeholder.com/150?text=No+Image';
                }

                return [
                    'id' => $cartItem->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => (int) $product->price,
                    'desc' => $product->description ?? '',
                    'description' => $product->description ?? '', // Also include description for consistency
                    'img' => $imageUrl,
                    'quantity' => $cartItem->quantity,
                    'stock' => $product->stock,
                ];
            });

        return response()->json($cartItems);
    }

    /**
     * Add product to cart
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check stock availability
        if ($product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock
            ], 400);
        }

        // Check if product already in cart
        $existingCart = Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingCart) {
            // Update quantity if adding same product
            $newQuantity = $existingCart->quantity + $request->quantity;
            
            if ($product->stock < $newQuantity) {
                return response()->json([
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock
                ], 400);
            }

            $existingCart->update(['quantity' => $newQuantity]);
            return response()->json([
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'cart' => $existingCart
            ], 200);
        }

        // Create new cart item
        $cart = Cart::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart' => $cart
        ], 201);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, Cart $cart)
    {
        $user = Auth::user();
        
        if (!$user || $cart->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Load product relationship if not already loaded
        if (!$cart->relationLoaded('product')) {
            $cart->load('product');
        }
        $product = $cart->product;

        // Check stock availability
        if ($product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock
            ], 400);
        }

        $cart->update(['quantity' => $request->quantity]);

        return response()->json([
            'message' => 'Jumlah produk berhasil diperbarui',
            'cart' => $cart
        ], 200);
    }

    /**
     * Remove product from cart
     */
    public function destroy(Cart $cart)
    {
        $user = Auth::user();
        
        if (!$user || $cart->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $cart->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus dari keranjang'
        ], 200);
    }

    /**
     * Clear all cart items for authenticated user
     */
    public function clear()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'Keranjang berhasil dikosongkan'
        ], 200);
    }
}

