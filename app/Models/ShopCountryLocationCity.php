<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopCountryLocationCity extends Model
{
    use HasFactory;

    public function ShopCountryLocation()
    {
        return $this->belongsTo(ShopCountryLocation::class);
    }
}
