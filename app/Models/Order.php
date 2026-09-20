<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'buyer_id',
        'total_price',
        'status',
        'payment_method',
    ];

    /**
     * Get the buyer (user) that made the order.
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get all items in the order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
