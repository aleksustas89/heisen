<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopItemShortcut extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    public function ShopGroup()
    {
        return $this->belongsTo(ShopGroup::class);
    }

        public function ShopItem()
    {
        return $this->belongsTo(ShopItem::class);
    }

    public static $BadgeClasses = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'purple', 'dark'];
}
