<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyValueInt extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'entity_id', 'value'];

    public function ShopItemProperty()
    {
        return $this->belongsTo(ShopItemProperty::class, 'property_id');
    }

    public function ShopItemListItem()
    {
        return $this->belongsTo(ShopItemListItem::class, 'value');
    }
}
