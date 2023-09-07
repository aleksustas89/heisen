<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopDeliveryField extends Model
{
    use HasFactory;

    /**
     * type:
     * 1: input-text
     * 2: input-hidden
    */

    public function ShopDelivery()
    {
        return $this->belongsTo(ShopDelivery::class);
    }

    public static function getTypes() : array
    {

        $aTypes = [
            "1" => "Текстовое поле",
            "2" => "Скрытое поле",
        ];

        return $aTypes;
    }
}
