<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ShopItemImage;
use App\Models\ShopItemProperty;
use App\Models\ShopItemListItem;
use App\Models\Shop;

class ShopItem extends Model
{
    use HasFactory;

    public function ShopGroup()
    {
        return $this->belongsTo(ShopGroup::class);
    }

    public function ShopCurrency()
    {
        return $this->belongsTo(ShopCurrency::class);
    }

    public function ShopItemImages()
    {
        return $this->hasMany(ShopItemImage::class);
    }

    public function PropertyValueInts()
    {
        return $this->hasMany(PropertyValueInt::class, 'entity_id');
    }

    public function PropertyValueStrings()
    {
        return $this->hasMany(PropertyValueString::class, 'entity_id');
    }

    public function PropertyValueFloats()
    {
        return $this->hasMany(PropertyValueFloat::class, 'entity_id');
    }

    public function ShopModificationImage()
    {
        return $this->hasOne(ShopModificationImage::class);
    }

    public function ShopItemDiscount()
    {
        return $this->hasMany(ShopItemDiscount::class);
    }

    public function getImages($all = true)
    {
        $aReturn = [];

        foreach ($this->ShopItemImages as $k => $ShopItemImage) {
            if (!empty($ShopItemImage->image_large) || !empty($ShopItemImage->image_small)) {

                if ($all === false && $k > 0) {
                    break;
                }

                $aReturn[$ShopItemImage->id]["image_large"] = !empty($ShopItemImage->image_large) ? $this->path() . $ShopItemImage->image_large : '';
                $aReturn[$ShopItemImage->id]["image_small"] = !empty($ShopItemImage->image_small) ? $this->path() . $ShopItemImage->image_small : '';
            }
        }

        return $aReturn;
    }

    public function path()
    {

        return Shop::$store_path . 'group_' . $this->shop_group_id . '/item_' . $this->id . '/';
    }

    public function url()
    {

        return Shop::path() . ($this->shop_group_id > 0 ? $this->ShopGroup->path() : '') . $this->path;
    }

    public function delete()
    {
        foreach ($this->ShopItemImages as $ShopItemImage) {
            $ShopItemImage->delete();
        }

        parent::delete();
    }

    public function getProperties() : array
    {
        $aReturn = [];

        foreach ($this->ShopGroup->Shop_Item_Property_For_Groups as $k => $Shop_Item_Property_For_Groups) {
            $aReturn[$k]["property_id"] = $Shop_Item_Property_For_Groups->ShopItemProperty->id;
            $aReturn[$k]["property_name"] = $Shop_Item_Property_For_Groups->ShopItemProperty->name;
            
            $object = ShopItemProperty::getObjectByType($Shop_Item_Property_For_Groups->ShopItemProperty->type);

            $values = [];
            
            foreach ($object::where("entity_id", $this->id)->where("property_id", $Shop_Item_Property_For_Groups->ShopItemProperty->id)->get() as $value) {
                
                switch ($Shop_Item_Property_For_Groups->ShopItemProperty->type) {
                    case 4:
                        $values[] = ShopItemListItem::find($value->value)->value;
                    break;
                    default:
                        $values[] = $value->value;
                }
            }

            $aReturn[$k]["property_values"] = $values;

        }

        return $aReturn;
    }

    public function getModificationCount()
    {
        return ShopItem::where("modification_id", $this->id)->count();
    }

    public function price()
    {
        return $this->price;
    }

    public function parentItemIfModification()
    {

        return $this->modification_id > 0 ? ShopItem::find($this->modification_id) : $this;
    }
}
