<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopDeliveryFieldValue extends Model
{
    use HasFactory;

    public function ShopDeliveryField()
    {
        return $this->belongsTo(ShopDeliveryField::class);
    }
}
