<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
    ];

    /**
     * Get the user that performed this action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent model (polymorphic).
     */
    public function model()
    {
        return $this->morphTo();
    }
}
