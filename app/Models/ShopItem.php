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
use Illuminate\Filesystem\Filesystem;
use App\Models\ShopItemDiscount;
use App\Models\ShopModificationImage;

class ShopItem extends Model
{
    use HasFactory;

    /**
     * default price view:
     * 0 = show prices from - to, based on the modifications of item
     * 1 = show specific price of default modification 'default_modification' of item based on color or size 
    */
    public static $priceView = 1;

    protected $fillable = ['name', 'shop_group_id', 'modification_id', 'default_modification', 'user_id', 'description', 'text', 'seo_title', 'seo_description', 'seo_keywords', 'price', 'shop_currency_id', 'sorting', 'marking', 'weight', 'length', 'width', 'height', 'indexing', 'active'];

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

    public function ShopItemDiscounts()
    {
        return $this->hasMany(ShopItemDiscount::class);
    }

    public function CommentShopItem()
    {
        return $this->hasMany(CommentShopItem::class);
    }

    public function SearchPage()
    {
        return $this->hasOne(SearchPage::class);
    }

    public function ShopItemAssociatedGroups()
    {
        return $this->hasMany(ShopItemAssociatedGroup::class);
    }

    public function ShopItemAssociatedItems()
    {
        return $this->hasMany(ShopItemAssociatedItem::class);
    }

    public function ShopItemShortcuts()
    {
        return $this->hasMany(ShopItemShortcut::class);
    }

    public function Page()
    {
        return $this->hasOne(Page::class, 'entity_id');
    }

    public function defaultModification()
    {

        return ShopItem::where("modification_id", $this->id)->where("default_modification", 1)->first() ?? false;
    }

    public function modificationName()
    {

        $second = [];

        foreach ($this->PropertyValueInts as $propertyValueInt) {
            if ($propertyValueInt->ShopItemListItem) {
                $second[] = $propertyValueInt->ShopItemProperty->name . ": " . $propertyValueInt->ShopItemListItem->value;
            }
        }

        return [
            "name" => trim($this->parentItemIfModification()->name),
            "params" => implode(",", $second)
        ];
    }

    public function getImages($all = true)
    {
        $aReturn = [];

        $path = $this->shortPath();
        
        foreach ($this->ShopItemImages()->where("image_small", "!=", '')->orderBy("sorting", "asc")->get() as $k => $ShopItemImage) {

            if (!empty($ShopItemImage->image_large) && Storage::disk(Shop::$storage)->exists($path . $ShopItemImage->image_large)) {
                
                if ($all === false && $k > 0) {
                    break;
                }
                
                $aReturn[$ShopItemImage->id]["image_large"] = Shop::$store_path . $path . $ShopItemImage->image_large;
                
                if (!empty($ShopItemImage->image_small) && Storage::disk(Shop::$storage)->exists($path . $ShopItemImage->image_small)) {
                    $aReturn[$ShopItemImage->id]["image_small"] = Shop::$store_path . $path . $ShopItemImage->image_small;
                }
            } else {
                $ShopItemImage->delete();
            }
        }

        return $aReturn;
    }

    public function path()
    {
        $object = $this->parentItemIfModification();

        return Shop::$store_path . 'group_' . ($object->shop_group_id > 0 ? $object->shop_group_id : 0) . '/item_' . $object->id . '/';
    }

    public function shortPath()
    {
        $object = $this->parentItemIfModification();

        return 'group_' . ($object->shop_group_id > 0 ? $object->shop_group_id : 0) . '/item_' . $object->id . '/';
    }

    public function createDir()
    {
        if (!file_exists('../storage/app' . $this->path())) {
            $Filesystem = new Filesystem();
            $Filesystem->makeDirectory('../storage/app' . $this->path(), 0755, true);
        }
    }

    public function url()
    {

        $object = $this->parentItemIfModification();

        return ($object->shop_group_id > 0 ? $object->ShopGroup->url . "/" : '') . $object->path;
    }

    public function changeImagesDirFrom($path)
    {

        $this->createDir();

        foreach ($this->ShopItemImages()->where("image_small", "!=", '')->orderBy("sorting", "asc")->get() as $k => $ShopItemImage) {

            if (!empty($ShopItemImage->image_large) && Storage::disk(Shop::$storage)->exists($path . $ShopItemImage->image_large)) {
       
                Storage::disk(Shop::$storage)->move($path . $ShopItemImage->image_large, $this->shortPath() . $ShopItemImage->image_large);

                $eExt = explode(".", $ShopItemImage->image_large);

                $fileJpg = $eExt[0] .'.jpg';

                if (Storage::disk(Shop::$storage)->exists($path . $fileJpg)) {
                    Storage::disk(Shop::$storage)->move($path . $fileJpg, $this->shortPath() . $fileJpg);
                }
            }

            if (!empty($ShopItemImage->image_small) && Storage::disk(Shop::$storage)->exists($path . $ShopItemImage->image_small)) {
       
                Storage::disk(Shop::$storage)->move($path . $ShopItemImage->image_small, $this->shortPath() . $ShopItemImage->image_small);

                $eExt = explode(".", $ShopItemImage->image_small);

                $fileJpg = $eExt[0] .'.jpg';

                if (Storage::disk(Shop::$storage)->exists($path . $fileJpg)) {
                    Storage::disk(Shop::$storage)->move($path . $fileJpg, $this->shortPath() . $fileJpg);
                }
            }
        }


        $fullStoragePath = Shop::fullStoragePath();

        $Filesystem = new Filesystem();

        $Filesystem->deleteDirectory($fullStoragePath . '/' . Shop::$storage . '/' . $path);
   
    }

    public function delete()
    {

        $ShopGroup = $this->ShopGroup;

        if ($this->modification_id > 0) {

            //картинки
            if (!is_null($ShopModificationImage = $this->ShopModificationImage)) {
                $ShopModificationImage->delete();
            }

            foreach ($this->ShopItemDiscounts as $ShopItemDiscount) {
                $ShopItemDiscount->delete();
            }

            foreach ($this->PropertyValueInts as $PropertyValueInt) {
                $PropertyValueInt->delete();
            }

            foreach ($this->PropertyValueStrings as $PropertyValueString) {
                $PropertyValueString->delete();
            }

            foreach ($this->PropertyValueFloats as $PropertyValueFloat) {
                $PropertyValueFloat->delete();
            }

            if (!is_null($Page = $this->Page)) {
                $Page->delete();
            }

            parent::delete();

        } else {
            /*модификации*/
            foreach (ShopItem::where("modification_id", $this->id)->get() as $Modification) {

                //картинки
                if (!is_null($ShopModificationImage = $Modification->ShopModificationImage)) {
                    $ShopModificationImage->delete();
                }

                $Modification->delete();
            }

            /*скидки*/
            foreach ($this->ShopItemDiscounts as $ShopItemDiscount) {
                $ShopItemDiscount->delete();
            }

            foreach ($this->ShopItemImages as $ShopItemImage) {
                $ShopItemImage->delete();
            }

            if (!is_null($SearchPage = $this->SearchPage)) {
                $SearchPage->delete();
            }

            foreach ($this->PropertyValueInts as $PropertyValueInt) {
                $PropertyValueInt->delete();
            }

            foreach ($this->PropertyValueStrings as $PropertyValueString) {
                $PropertyValueString->delete();
            }

            foreach ($this->PropertyValueFloats as $PropertyValueFloat) {
                $PropertyValueFloat->delete();
            }

            foreach ($this->ShopItemAssociatedGroups as $ShopItemAssociatedGroup) {
                $ShopItemAssociatedGroup->delete();
            }

            foreach ($this->ShopItemAssociatedItems as $ShopItemAssociatedItem) {
                $ShopItemAssociatedItem->delete();
            }

            $this->deleteDir();

            if (!is_null($Page = $this->Page)) {
                $Page->delete();
            }

            parent::delete();

            if (!is_null($ShopGroup)) {
                $ShopGroup->setSubCount();
            }
        }
    }

    public function deleteDir()
    {

        $fullStoragePath = Shop::fullStoragePath();

        if ($this->path() && file_exists($fullStoragePath . $this->path())) {
            $Filesystem = new Filesystem();

            $Filesystem->deleteDirectory($fullStoragePath . $this->path());
        }
    }

    public function getProperties() : array
    {
        $aReturn = [];

        $object = $this->parentItemIfModification();

        foreach ($object->ShopGroup->ShopItemPropertyForGroups as $k => $ShopItemPropertyForGroup) {

            if (!is_null($ShopItemPropertyForGroup->ShopItemProperty)) {
                $aReturn[$k]["property_id"] = $ShopItemPropertyForGroup->ShopItemProperty->id;
                $aReturn[$k]["property_name"] = $ShopItemPropertyForGroup->ShopItemProperty->name;
                $aReturn[$k]["show_in_item"] = $ShopItemPropertyForGroup->ShopItemProperty->show_in_item;
                
                $object = ShopItemProperty::getObjectByType($ShopItemPropertyForGroup->ShopItemProperty->type);
    
                $values = [];
                
                foreach ($object::where("entity_id", $this->id)->where("property_id", $ShopItemPropertyForGroup->ShopItemProperty->id)->get() as $value) {
                    
                    switch ($ShopItemPropertyForGroup->ShopItemProperty->type) {
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

        return min($aPrices);
    }

    public function oldPrice()
    {
        return $this->price != $this->price() ? $this->price : false;
    }

    public function getPriceApplyCurrency(ShopCurrency $ShopCurrency)
    {

        return $this->applyCurrency($this->price(), $ShopCurrency);
    }

    public function getOldPriceApplyCurrency(ShopCurrency $ShopCurrency)
    {

        return $this->applyCurrency($this->oldPrice(), $ShopCurrency);
    }

    protected function applyCurrency($price, ShopCurrency $ShopCurrency)
    {
        if ($price) {
            return $ShopCurrency->default != 1 ? $price * $ShopCurrency->exchange_rate : $price;
        }
        return false;
    }

    public function parentItemIfModification()
    {

        if ($this->modification_id > 0) {
            if (!is_null($ShopItem = ShopItem::find($this->modification_id))) {
                return $ShopItem;
            } else {
                return false;
            }
        } else {
            return $this;
        }
        
        // return $this->modification_id > 0
        //             && !is_null($ShopItem = ShopItem::find($this->modification_id)) ? $ShopItem : $this;
    }

    /**
     * 
     * @return modification image if modification or first image of item
    */
    public function getShopItemImage()
    {
        if ($this->modification_id > 0) {
            if (!is_null($this->ShopModificationImage) && !is_null($this->ShopModificationImage->ShopItemImage)) {
                return $this->ShopModificationImage->ShopItemImage;
            } else {
                //изображение основного товара
                foreach ($this->parentItemIfModification()->ShopItemImages as $ShopItemImage) {
                    return $ShopItemImage;
                }
            }
        } else {
            foreach ($this->ShopItemImages as $ShopItemImage) {
                return $ShopItemImage;
            }
        }
    }

    public function copy($modification_id = false, $imagesHistory = false)
    {
        $this->modification_id = $modification_id ?? 0;
        $this->name = $this->name . ($this->modification_id == 0 ? " [copy-" . date("d.m.Y-H:i:s") . "]" : "");
        $this->path = $this->path . "-copy-" . time();
        $this->url = $this->url . "-copy-" . time();
        

        $nShopItem = $this->replicate()->fill([
            'type' => 'billing'
        ]);

        $nShopItem->push();

        $Page = new Page();
        $Page->entity_id = $nShopItem->id;
        $Page->type = 2;
        $Page->save();

        if ($this->modification_id > 0) {

            //картинки модификаций
            if (!is_null($ShopModificationImage = ShopModificationImage::where("shop_item_id", $this->id)->first())) {
                $nShopModificationImage = new ShopModificationImage();
                $nShopModificationImage->shop_item_id = $nShopItem->id;
                $nShopModificationImage->shop_item_image_id = $imagesHistory[$ShopModificationImage->shop_item_image_id] ?? $ShopModificationImage->shop_item_image_id;
                $nShopModificationImage->save();
            }
        }


        $nShopItem->createDir();

        //скидки
        foreach ($this->ShopItemDiscounts as $ShopItemDiscount) {
            $ShopItemDiscount->shop_item_id = $nShopItem->id;
            $nShopItemDiscount = $ShopItemDiscount->replicate()->fill([
                'type' => 'billing'
            ]);

            $nShopItemDiscount->save();
        }

        //картинки
        $oldPath = Storage::path($this->path());
        $newPath = Storage::path($nShopItem->path());
   
        foreach ($this->ShopItemImages as $ShopItemImage) {
            
            $imageLarge = $oldPath . $ShopItemImage->image_large;
            $newImageLarge = $newPath . $ShopItemImage->image_large;
            $imageSmall = $oldPath . $ShopItemImage->image_small;
            $newImageSmall = $newPath . $ShopItemImage->image_small;

            $copyLarge = copy($imageLarge, $newImageLarge);
            $copySmall = copy($imageSmall, $newImageSmall);

            if ($copyLarge || $copySmall) {
                $nShopItemImage = new ShopItemImage();
                $nShopItemImage->shop_item_id = $nShopItem->id;
                $nShopItemImage->image_large = $copyLarge ? $ShopItemImage->image_large : '';
                $nShopItemImage->image_small = $copySmall ? $ShopItemImage->image_small : '';
                $nShopItemImage->sorting = $ShopItemImage->sorting;
                $nShopItemImage->save();

                $imagesHistory[$ShopItemImage->id] = $nShopItemImage->id;
                
            }
        }

        //свойства
        foreach ([new PropertyValueInt(), new PropertyValueFloat(), new PropertyValueString()] as $Object) {
            foreach ($Object::whereEntityId($this->id)->get() as $PropertyValue) {
                $PropertyValue->entity_id = $nShopItem->id;
                $nPropertyValueInt = $PropertyValue->replicate()->fill([
                    'type' => 'billing'
                ]);
                $nPropertyValueInt->push();
            }
        }

        //модификации
        foreach (ShopItem::whereModificationId($this->id)->get() as $ShopItem) {
            $ShopItem->copy($nShopItem->id, $imagesHistory);
        }

    }
}
