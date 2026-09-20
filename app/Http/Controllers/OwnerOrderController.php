<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerOrderController extends Controller
{
    /**
     * Get orders for products owned by the authenticated owner
     */
    public function index(Request $request)
    {
        $owner = Auth::user();
        
        if (!$owner || !$owner->isOwner()) {
            abort(403, 'Unauthorized access');
        }

        // Get filter status from request (default: 'all')
        $status = $request->get('status', 'all');

        // Get product IDs owned by this owner
        $productIds = Product::where('owner_id', $owner->id)->pluck('id');

        // Base query for orders that contain products owned by this owner
        $query = Order::with(['buyer', 'items.product'])
            ->whereHas('items', function ($query) use ($productIds) {
                $query->whereIn('product_id', $productIds);
            });

        // Filter by status if not 'all'
        if ($status !== 'all' && in_array($status, ['pending', 'paid', 'cancel'])) {
            $query->where('status', $status);
        }

        // Get orders with pagination (15 per page)
        $orders = $query->latest()
            ->paginate(15)
            ->through(function ($order) use ($productIds) {
                // Filter items to only show products owned by this owner
                $order->items = $order->items->filter(function ($item) use ($productIds) {
                    return $productIds->contains($item->product_id);
                });
                
                // Recalculate total for this owner's products only
                $order->subtotal = $order->items->sum(function ($item) {
                    return $item->quantity * $item->price;
                });
                
                return $order;
            });

        return view('owner.orders.index', compact('orders', 'status'));
    }

    /**
     * Show order details
     */
    public function show(Order $order)
    {
        $owner = Auth::user();
        
        if (!$owner || !$owner->isOwner()) {
            abort(403, 'Unauthorized access');
        }

        $productIds = Product::where('owner_id', $owner->id)->pluck('id');

        // Verify order contains owner's products
        $hasOwnerProducts = $order->items()
            ->whereIn('product_id', $productIds)
            ->exists();

        if (!$hasOwnerProducts) {
            abort(403, 'Order tidak terkait dengan produk Anda');
        }

        $order->load(['buyer', 'items.product']);
        
        // Filter items to only show owner's products
        $order->items = $order->items->filter(function ($item) use ($productIds) {
            return $productIds->contains($item->product_id);
        });

        return view('owner.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $owner = Auth::user();
        
        if (!$owner || !$owner->isOwner()) {
            abort(403, 'Unauthorized access');
        }

        $productIds = Product::where('owner_id', $owner->id)->pluck('id');

        // Verify order contains owner's products
        $hasOwnerProducts = $order->items()
            ->whereIn('product_id', $productIds)
            ->exists();

        if (!$hasOwnerProducts) {
            abort(403, 'Order tidak terkait dengan produk Anda');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,paid,shipped,done,cancel',
        ]);

        $order->update($validated);

        return redirect()->route('owner.orders.show', $order)
            ->with('success', 'Status pesanan berhasil diperbarui!');
    }
}

