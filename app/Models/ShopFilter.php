<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopFilter extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function ShopGroup()
    {
        return $this->belongsTo(ShopGroup::class);
    }

    public function ShopFilterPropertyValues()
    {
        return $this->hasMany(ShopFilterPropertyValue::class);
    }

    public function delete()
    {
        foreach ($this->ShopFilterPropertyValues as $ShopFilterPropertyValue) {
            $ShopFilterPropertyValue->delete();
        }

        parent::delete();
    }
}
