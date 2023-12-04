<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopItemDiscount extends Model
{
    use HasFactory;

    protected $fillable = ['shop_item_id', 'shop_discount_id', 'value'];

    public function ShopDiscount()
    {
        return $this->belongsTo(ShopDiscount::class);
    }

    public function ShopItem()
    {
        return $this->belongsTo(ShopItem::class);
    }
}
