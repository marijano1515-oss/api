<?php

namespace App\Models;

use App\Http\Controllers\ProductController;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['name','name','locale'])]

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
