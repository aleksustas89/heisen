<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopQuickOrder extends Model
{
    use HasFactory;

    public function ShopItem()
    {
        return $this->belongsTo(ShopItem::class);
    }
}
