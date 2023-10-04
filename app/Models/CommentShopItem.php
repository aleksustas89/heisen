<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentShopItem extends Model
{
    use HasFactory;

    public function ShopItem()
    {
        return $this->belongsTo(ShopItem::class);
    }
}
