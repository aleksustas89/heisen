<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ShopCurrency;

class ShopDiscount extends Model
{
    use HasFactory;

    public function ShopItemDiscounts()
    {
        return $this->hasMany(ShopItemDiscount::class);
    }

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

    public function check()
    {
        if (strtotime(date("Y-m-d H:i:s")) >= strtotime($this->start_datetime) && strtotime(date("Y-m-d H:i:s")) <= strtotime($this->end_datetime)) {
            return true;
        }

        return false;
    }
}
