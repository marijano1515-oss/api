<?php

namespace App\Models;

use Illuminate\Console\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Product extends Model
{
    protected $fillable = ['name', 'price','category_id','user_id'];

    public function category()
    {

        return $this->belongsToMany(Orders::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function favoritesUsers()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function favorites()
    {
        return $this->hasMany(Favorites::class);
    }
    public function translations()
    {
        return $this->hasMany(ProductTranslations::class);
    }
}
