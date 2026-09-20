<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'address',
        'city',
        'province',
        'postal_code',
        'phone',
        'is_default',
    ];

    /**
     * Get the user that owns this address.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
