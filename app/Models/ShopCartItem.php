<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopCartItem extends Model
{
    use HasFactory;

    public function ShopItem()
    {
        return $this->belongsTo(ShopItem::class);
    }
}