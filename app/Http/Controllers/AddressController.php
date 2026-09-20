<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Get all addresses for authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $addresses = Address::where('user_id', $user->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($addresses);
    }

    /**
     * Store a new address
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'label' => 'nullable|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            Address::where('user_id', $user->id)
                ->update(['is_default' => false]);
        }

        $address = Address::create([
            'user_id' => $user->id,
            ...$validated,
        ]);

        return response()->json($address, 201);
    }

    /**
     * Update an address
     */
    public function update(Request $request, Address $address)
    {
        $user = Auth::user();
        
        if (!$user || $address->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'label' => 'nullable|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            Address::where('user_id', $user->id)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json($address);
    }

    /**
     * Delete an address
     */
    public function destroy(Address $address)
    {
        $user = Auth::user();
        
        if (!$user || $address->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $address->delete();

        return response()->json(['message' => 'Address deleted successfully']);
    }
}

