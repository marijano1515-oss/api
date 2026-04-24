<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orders extends Model
{
    // Since your class name is "Orders" (plural), we must explicitly set the table name
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'status',
        'total_price',
    ];

    /**
     * Relationship: An order belongs to a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: An order has many products through the order_items table.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'order_items',
            'order_id',
            'product_id'
        )
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
    public function Items(): HasMany
    {
        return $this->hasMany(OrderItems::class, 'order_id','quantity'
        );
    }
}
