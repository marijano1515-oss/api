<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cart extends Model
{
    protected $fillable = ['user_id'];

    public function items()
    {
        return $this->hasMany(CartItems::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function product(): BelongsToMany
{
        return $this->belongsToMany(Product::class,'cart_items')->withPivot('quantity');
}
}
