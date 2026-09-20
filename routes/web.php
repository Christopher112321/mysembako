<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthPagesController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WebAdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OwnerOrderController;

Route::get('/', function () {
    return view('app');
})->name('intro');

Route::get('/home', function () {
    return view('app');
})->name('home');

Route::get('/login', [AuthPagesController::class, 'showLogin'])->name('login');

Route::get('/register', [AuthPagesController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthPagesController::class, 'submitRegister'])->name('register.submit');

Route::get('/register-admin', [AuthPagesController::class, 'showRegisterAdmin'])->name('register.admin');
Route::get('/buka-toko', [AuthPagesController::class, 'showRegisterAdmin'])->name('buka-toko');
Route::post('/register-admin', [AuthPagesController::class, 'submitRegisterAdmin'])->name('register.admin.submit');

Route::post('/login', [AuthPagesController::class, 'submitLogin'])->name('login.submit');

Route::post('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    return redirect()->route('home')->with('success', 'Anda telah logout.');
})->name('logout');

Route::get('/index', [AuthPagesController::class, 'index'])->name('index');

// API Routes untuk produk (public access)
Route::get('/api/products', [ProductController::class, 'apiIndex'])->name('api.products');
Route::get('/api/categories', [ProductController::class, 'apiCategories'])->name('api.categories');

// API Routes untuk cart (requires authentication)
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    
    // User API
    Route::get('/user', function () {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'location' => $user->location,
            'role_id' => $user->role_id,
        ]);
    })->name('user');
    
    // Update user profile (nama, email, telepon, lokasi)
    Route::patch('/user', function (\Illuminate\Http\Request $request) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:20',
            'location' => 'sometimes|nullable|string|max:255',
        ]);

        $user->fill($validated);
        $user->save();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'location' => $user->location,
            'role_id' => $user->role_id,
        ]);
    })->name('user.update');
    
    // Addresses API
    Route::apiResource('addresses', AddressController::class);
    
    // Checkout API
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout');
    
    // Orders API
    Route::get('/orders', function () {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $orders = \App\Models\Order::where('buyer_id', $user->id)
            ->with(['items.product'])
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total_price' => $order->total_price,
                    'payment_method' => $order->payment_method,
                    'created_at' => $order->created_at->format('d F Y'),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_name' => $item->product->name ?? 'Produk dihapus',
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                        ];
                    }),
                ];
            });
        return response()->json($orders);
    })->name('orders.index');
    
    // Cancel order API
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    // Complete order API (buyer marks shipped -> done)
    Route::patch('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
});

// Owner routes (pemilik toko)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/update-stok', [ProductController::class, 'updateStok'])->name('products.update-stok');
    
    // Owner Orders Management
    Route::get('owner/orders', [OwnerOrderController::class, 'index'])->name('owner.orders.index');
    Route::get('owner/orders/{order}', [OwnerOrderController::class, 'show'])->name('owner.orders.show');
    Route::patch('owner/orders/{order}/status', [OwnerOrderController::class, 'updateStatus'])->name('owner.orders.update-status');
});

// Web Admin routes (pengelola web)
Route::middleware(['auth', 'webadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [WebAdminController::class, 'dashboard'])->name('dashboard');
    
    // Users Management
    Route::get('/users', [WebAdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [WebAdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit', [WebAdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [WebAdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [WebAdminController::class, 'deleteUser'])->name('users.delete');
    
    // Products Management
    Route::get('/products', [WebAdminController::class, 'products'])->name('products');
    Route::get('/products/{product}', [WebAdminController::class, 'showProduct'])->name('products.show');
    Route::delete('/products/{product}', [WebAdminController::class, 'deleteProduct'])->name('products.delete');
    
    // Orders Management
    Route::get('/orders', [WebAdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [WebAdminController::class, 'showOrder'])->name('orders.show');
    Route::patch('/orders/{order}/status', [WebAdminController::class, 'updateOrderStatus'])->name('orders.update-status');
    
    // Activity Logs
    Route::get('/activity-logs', [WebAdminController::class, 'activityLogs'])->name('activity-logs');
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
