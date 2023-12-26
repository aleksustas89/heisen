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
     * 4: hidden + radio, must have children
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
            "3" => "Список",
            "4" => "Переключатель",
        ];

        return $aTypes;
    }
}
