<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('buyer_id', Auth::id())->get();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'total_price' => 'required|numeric',
            'status' => 'nullable|string',
            'order_date' => 'nullable|date',
        ]);

        Order::create([
            'buyer_id' => Auth::id(),
            'total_price' => $request->total_price,
            'status' => $request->status ?? 'pending',
            'order_date' => $request->order_date ?? now(),
        ]);

        return redirect()->route('orders.index');
    }

    public function show(Order $order)
    {
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'total_price' => 'required|numeric',
            'status' => 'nullable|string',
            'order_date' => 'nullable|date',
        ]);

        $order->update($request->all());

        return redirect()->route('orders.index');
    }

    public function destroy(Order $order)
    {
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        $order->delete();

        return redirect()->route('orders.index');
    }

    /**
     * Cancel order (only for pending orders)
     */
    public function cancel(Order $order)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verify order belongs to user
        if ($order->buyer_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Only allow cancel for pending orders
        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Hanya pesanan dengan status pending yang dapat dibatalkan'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Restore stock for all items in the order
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }

            // Update order status to cancel
            $order->update(['status' => 'cancel']);

            DB::commit();

            return response()->json([
                'message' => 'Pesanan berhasil dibatalkan',
                'order' => $order->fresh(['items.product']),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat membatalkan pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark order as completed (only allowed for shipped orders by the buyer)
     */
    public function complete(Order $order)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verify order belongs to user
        if ($order->buyer_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Only allow complete for shipped orders
        if ($order->status !== 'shipped') {
            return response()->json([
                'message' => 'Hanya pesanan dengan status dikirim yang dapat diselesaikan'
            ], 400);
        }

        try {
            $order->update(['status' => 'done']);

            return response()->json([
                'message' => 'Pesanan ditandai selesai',
                'order' => $order->fresh(['items.product']),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyelesaikan pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
