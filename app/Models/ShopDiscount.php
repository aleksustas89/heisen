<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ShopCurrency;

class ShopDiscount extends Model
{
    use HasFactory;

    public static function getTypes() : array
    {

        $types = [
            0 => "%",
        ];

        if (!is_null($ShopCurrency = ShopCurrency::where("default", 1)->first())) {
            $types[1] = $ShopCurrency->name;
        }

        return $types;
    }
}
