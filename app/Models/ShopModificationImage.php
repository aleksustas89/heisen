<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopModificationImage extends Model
{
    use HasFactory;

    public function ShopItemImage()
    {
        return $this->belongsTo(ShopItemImage::class, 'shop_item_image_id');
    }
}
