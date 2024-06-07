<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopItemAssociatedGroup extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function ShopGroup()
    {
        return $this->belongsTo(ShopGroup::class, 'shop_group_associated_id');
    }
}
