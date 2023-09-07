<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyValueFloat extends Model
{
    use HasFactory;

    public function ShopItemProperty()
    {
        return $this->belongsTo(ShopItemProperty::class, 'property_id');
    }
}
