<?php

namespace App\Models;

use App\Http\Controllers\ProductController;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function products()
    {
        return $this->hasMany(ProductController::class);
    }

    public function translations()
    {
        return $this->hasmany(CategoryTranslations::class);
    }

}
