<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ShopItemImage;
use App\Models\ShopItemProperty;
use App\Models\ShopItemListItem;
use App\Models\Shop;
use App\Http\Controllers\ShopDiscountController;
use Illuminate\Support\Facades\Storage;

class ShopItem extends Model
{
    use HasFactory;

    /**
     * default price view:
     * 0 = show prices from - to, based on the modifications of item
     * 1 = show specific price of default modification 'default_modification' of item based on color or size 
    */
    public static $priceView = 1;

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

    public function CommentShopItem()
    {
        return $this->hasMany(CommentShopItem::class);
    }

    public function defaultModification()
    {

        return ShopItem::where("modification_id", $this->id)->where("default_modification", 1)->first() ?? false;
    }

    public function getImages($all = true)
    {
        $aReturn = [];

        foreach ($this->ShopItemImages as $k => $ShopItemImage) {

            if ($all === false && $k > 0) {
                break;
            }

            $path = $this->shortPath();

            if (!empty($ShopItemImage->image_small) && Storage::disk('shop')->exists($path . $ShopItemImage->image_small)) {
                $aReturn[$ShopItemImage->id]["image_small"] = Shop::$store_path . $path . $ShopItemImage->image_small;

                if (!empty($ShopItemImage->image_large) && Storage::disk('shop')->exists($path . $ShopItemImage->image_large)) {
                    $aReturn[$ShopItemImage->id]["image_large"] = Shop::$store_path . $path . $ShopItemImage->image_large;
                }
            }
        }

        return $aReturn;
    }

    public function path()
    {
        $object = $this->parentItemIfModification();
        if ($object->shop_group_id > 0) {
            return Shop::$store_path . 'group_' . $object->shop_group_id . '/item_' . $object->id . '/';
        }

        return false;
    }

    public function shortPath()
    {
        $object = $this->parentItemIfModification();
        if ($object->shop_group_id > 0) {
            return 'group_' . $object->shop_group_id . '/item_' . $object->id . '/';
        }

        return false;
    }

    public function url()
    {

        $object = $this->parentItemIfModification();

        return "/" . Shop::path() . ($object->shop_group_id > 0 ? $object->ShopGroup->path() . "/" : '') . $object->path;
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

            if (!is_null($Shop_Item_Property_For_Groups->ShopItemProperty)) {
                $aReturn[$k]["property_id"] = $Shop_Item_Property_For_Groups->ShopItemProperty->id;
                $aReturn[$k]["property_name"] = $Shop_Item_Property_For_Groups->ShopItemProperty->name;
                $aReturn[$k]["show_in_item"] = $Shop_Item_Property_For_Groups->ShopItemProperty->show_in_item;
                
                $object = ShopItemProperty::getObjectByType($Shop_Item_Property_For_Groups->ShopItemProperty->type);
    
                $values = [];
                
                foreach ($object::where("entity_id", $this->id)->where("property_id", $Shop_Item_Property_For_Groups->ShopItemProperty->id)->get() as $value) {
                    
                    switch ($Shop_Item_Property_For_Groups->ShopItemProperty->type) {
                        case 4:
                            $ShopItemListItem = ShopItemListItem::find($value->value);
                            if (!is_null($ShopItemListItem) && !empty($ShopItemListItem->value)) {
                                $values[] = $ShopItemListItem->value;
                            }
                            
                        break;
                        default:
                            if (!empty($value->value)) {
                                $values[] = $value->value;
                            }
                    }
                }
    
                $aReturn[$k]["property_values"] = $values;
            }

        }

        return $aReturn;
    }

    public function getModificationCount()
    {
        return ShopItem::where("modification_id", $this->id)->count();
    }

    public function price()
    {

        $aPrices[] = ShopDiscountController::getShopItemPriceWithDiscount($this);

        if ($this->midification_id == 0) {
            foreach (ShopDiscountController::getModificationsPricesWithDiscounts($this) as $price) {
                $aPrices[] = $price;
            }
        }

        return min($aPrices);
    }

    public function oldPrice()
    {
        return $this->price != $this->price() ? $this->price : false;
    }

    public function parentItemIfModification()
    {
        
        return $this->modification_id > 0
                    && !is_null($ShopItem = ShopItem::find($this->modification_id)) ? $ShopItem : $this;
    }

    /**
     * 
     * @return modification image if modification or first image of item
    */
    public function getShopItemImage(): ShopItemImage
    {
        if ($this->modification_id > 0) {
            if (!is_null($this->ShopModificationImage)) {
                return $this->ShopModificationImage->ShopItemImage;
            } else {
                //изображение основного товара
                return $this->parentItemIfModification()->ShopItemImage;
            }
        } else {
            foreach ($this->ShopItemImages as $ShopItemImage) {
                return $ShopItemImage;
            }
        }
    }
}
