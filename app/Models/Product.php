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

    protected $hidden = ['user_id', 'category_id'];
    public function category(){

        return $this->belongsTo(Category::class);

    }
}
