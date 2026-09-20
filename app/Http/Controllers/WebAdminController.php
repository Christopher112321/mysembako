<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebAdminController extends Controller
{
    /**
     * Dashboard admin
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_owners' => User::where('role_id', 1)->count(),
            'total_buyers' => User::where('role_id', 2)->count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'recent_activities' => ActivityLog::latest()->take(10)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Manage Users
     */
    public function users()
    {
        $users = User::with('role')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user details
     */
    public function showUser(User $user)
    {
        $user->load('role');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Edit user
     */
    public function editUser(User $user)
    {
        $roles = \App\Models\Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Admin mengupdate user',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => "Admin mengupdate user: {$user->name}",
        ]);

        return redirect()->route('admin.users')->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Admin menghapus user',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => "Admin menghapus user: {$user->name}",
        ]);

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus!');
    }

    /**
     * Manage Products (All products from all owners)
     */
    public function products()
    {
        $products = Product::with(['owner', 'category'])->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show product details
     */
    public function showProduct(Product $product)
    {
        $product->load(['owner', 'category', 'images']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Delete product
     */
    public function deleteProduct(Product $product)
    {
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Admin menghapus produk',
            'model_type' => Product::class,
            'model_id' => $product->id,
            'description' => "Admin menghapus produk: {$product->name}",
        ]);

        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Produk berhasil dihapus!');
    }

    /**
     * Manage Orders
     */
    public function orders()
    {
        $orders = Order::with(['buyer', 'items.product'])->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show order details
     */
    public function showOrder(Order $order)
    {
        $order->load(['buyer', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,shipped,done,cancel',
        ]);

        $order->update($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Admin mengupdate status pesanan',
            'model_type' => Order::class,
            'model_id' => $order->id,
            'description' => "Admin mengupdate status pesanan #{$order->id} menjadi: {$validated['status']}",
        ]);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Status pesanan berhasil diperbarui!');
    }

    /**
     * Activity Logs
     */
    public function activityLogs()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(20);
        return view('admin.activity-logs.index', compact('logs'));
    }
}
