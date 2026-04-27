<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
#[Fillable(['product_id','name','locale'])]

class ProductTranslations extends Model
{
    public function product()
    {
        return $this->belongsTo(ProductTranslations::class);
    }
}
