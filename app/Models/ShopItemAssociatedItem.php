<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopItemAssociatedItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function ShopItem()
    {
        return $this->belongsTo(ShopItem::class, 'shop_item_associated_id');
    }
}
