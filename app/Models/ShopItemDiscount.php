<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopItemDiscount extends Model
{
    use HasFactory;

    public function ShopDiscount()
    {
        return $this->belongsTo(ShopDiscount::class);
    }
}
