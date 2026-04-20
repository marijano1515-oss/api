<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use mysql_xdevapi\Table;

class Favorites extends Model
{
protected $table = 'favourites';

protected $fillable = ['user_id', 'product_id'];

}
