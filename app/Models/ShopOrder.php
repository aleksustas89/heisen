<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOrder extends Model
{
    use HasFactory;

    public function ShopOrderItems()
    {
        return $this->hasMany(ShopOrderItem::class);
    }

    public function ShopDeliveryFieldValues()
    {
        return $this->hasMany(ShopDeliveryFieldValue::class);
    }

    public function Source()
    {
        return $this->belongsTo(Source::class);
    }

    public function ShopCurrency()
    {
        return $this->belongsTo(ShopCurrency::class);
    }

    public function Client()
    {
        return $this->belongsTo(Client::class);
    }

    public function ShopDelivery()
    {
        return $this->belongsTo(ShopDelivery::class);
    } 

    public function ShopPaymentSystem()
    {
        return $this->belongsTo(ShopPaymentSystem::class);
    } 

    public function getSum()
    {
        $sum = 0;
        foreach ($this->ShopOrderItems as $orderItem) {
            if ($orderItem->shop_item_id > 0 && $orderItem->price > 0) {
                $sum += ($orderItem->price * $orderItem->quantity);
            }
        }

        return $sum;
    }
}
