<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'name',
        'price',
        'stock',
        'description',
        'image',
        'status',
        'category_id',
    ];

    /**
     * Get the owner (user) that owns the product.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the category of the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all images for the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}

