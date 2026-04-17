<?php

namespace App\Models;

use Illuminate\Console\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name','category','price','created_at','updated_at'])]
#[Hidden(['user_id','category_id'])]

class Product extends Model
{
    public function category(){

        return $this->belongsTo(Category::class);

    }
}
