<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UkassaOrder extends Model
{
    use HasFactory;

    public function ShopOrder()
    {
        return $this->belongsTo(ShopOrder::class);
    }
}
