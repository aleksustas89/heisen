<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopDelivery extends Model
{
    use HasFactory;

    public function ShopDeliveryFields()
    {
        return $this->hasMany(ShopDeliveryField::class);
    }
}
