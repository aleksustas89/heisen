<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;

class ShopGroup extends Model
{
    use HasFactory;

    public function ShopItems()
    {
        return $this->hasMany(ShopItem::class);
    }

    public function Shop_Item_Property_For_Groups()
    {
        return $this->hasMany(ShopItemPropertyForGroup::class);
    }

    public function getFullPath()
    {
        return Shop::path() . $this->path();
    }

    public function path($aResult = array(), $activeAll = true)
    {
        
        // if ($this->path != '/') {
             array_unshift($aResult, $this->path);
        // }

        if ($this->parent_id > 0) {
            $oShopGroup = ShopGroup::where("id", $this->parent_id)->whereIn('active', $activeAll ? [0,1] : [1])->first();
            if (!is_null($oShopGroup)) {
                return $oShopGroup->path($aResult);
            }
            return false;
            
        } else {
            return $this->path != '/' ? implode("/", $aResult) : $this->path;
        }
    }

    public function dir()
    {

        return Shop::$store_path . 'group_' . $this->id . '/';
    }

    public function getChildCount()
	{
        $aResult = [
            "groupsCount" => 0,
            "itemsCount" => \App\Models\ShopItem::where('shop_group_id', $this->id)->count()
        ];

		$aShopGroups = ShopGroup::where('parent_id', $this->id)->get();

		foreach ($aShopGroups as $aShopGroup)
		{

            $count = $aShopGroup->getChildCount();

			$aResult["groupsCount"]++;
			$aResult["groupsCount"] += $count["groupsCount"];

            $aResult["itemsCount"] += \App\Models\ShopItem::where('shop_group_id', $aShopGroup->id)->count();
		}

		return $aResult;
	}

    public function getChildId()
	{
		$count = [];

		$aShopGroups = ShopGroup::where('parent_id', $this->id)->get();

		foreach ($aShopGroups as $aShopGroup)
		{
			$count[] = $aShopGroup->id;
			$count[] = $aShopGroup->getChildId();
		}

		return $count;
	}

    public function delete()
    {

        $aShopGroups = ShopGroup::where('parent_id', $this->id)->get();

		foreach ($aShopGroups as $aShopGroup)
		{
            $aShopGroup->delete();
		}

        foreach ($this->ShopItems as $oShopItem) {
            $oShopItem->delete();
        }

        parent::delete();
    }
}
