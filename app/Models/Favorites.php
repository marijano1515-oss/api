<?php

namespace App\Models;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Favorites extends Model
{
    protected $fillable = ['user_id', 'product_id'];

    public function products()
    {
        return $this->belongsTo(Product::class);
    }
}
