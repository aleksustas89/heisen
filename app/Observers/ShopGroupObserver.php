<?php

namespace App\Observers;

use App\Models\ShopGroup;
use App\Models\Page;

class ShopGroupObserver
{
    /**
     * Handle the ShopGroup "created" event.
     */
    public function created(ShopGroup $shopGroup): void
    {
        $Page = new Page();
        $Page->type = Page::getType(ShopGroup::class);
        $Page->entity_id = $shopGroup->id;
        $Page->save();
    }


    /**
     * Handle the ShopGroup "deleted" event.
     */
    public function deleted(ShopGroup $shopGroup): void
    {
        foreach (ShopGroup::where('parent_id', $shopGroup->id)->get() as $ShopGroup)
		{
            $ShopGroup->delete();
		}

        foreach ($shopGroup->ShopItems as $oShopItem) {
            $oShopItem->delete();
        }

        if (!is_null($Page = $shopGroup->Page)) {
            $Page->delete();
        }

        $type = Page::getType(ShopGroup::class);

        if ($type && !is_null($Page = Page::where("entity_id", $shopGroup->id)->where("type", $type)->first())) {
            $Page->delete();
        }

        $shopGroup->deleteDir();

        if ($shopGroup->parent_id > 0) {
            if (!is_null($oShopGroup = ShopGroup::find($shopGroup->parent_id))) {
                $oShopGroup->setSubCount();
            }
        }
    }

    /**
     * Handle the ShopGroup "restored" event.
     */
    public function restored(ShopGroup $shopGroup): void
    {
        //
    }

    /**
     * Handle the ShopGroup "force deleted" event.
     */
    public function forceDeleted(ShopGroup $shopGroup): void
    {
        //
    }
}
