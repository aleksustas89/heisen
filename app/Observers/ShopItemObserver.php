<?php

namespace App\Observers;

use App\Models\ShopItem;
use App\Models\Page;

class ShopItemObserver
{
    /**
     * Handle the ShopItem "created" event.
     */
    public function created(ShopItem $shopItem): void
    {
        $Page = new Page();
        $Page->type = Page::getType(ShopItem::class);
        $Page->entity_id = $shopItem->id;
        $Page->save();

        if (!is_null($shopGroup = $shopItem->shopGroup)) {
            $shopGroup->setSubCount();
        }
    }

    /**
     * Handle the ShopItem "updated" event.
     */
    public function updated(ShopItem $shopItem): void
    {

        if (!is_null($shopGroup = $shopItem->shopGroup)) {
            $shopGroup->setSubCount();
        }
    }

    /**
     * Handle the ShopItem "deleted" event.
     */
    public function deleted(ShopItem $shopItem): void
    {

        $type = Page::getType(ShopItem::class);

        if ($type && !is_null($Page = Page::where("entity_id", $shopItem->id)->where("type", $type)->first())) {
            $Page->delete();
        }

        if ($shopItem->shop_group_id > 0 && !is_null($shopGroup = $shopItem->shopGroup)) {
            $shopGroup->setSubCount();
        }
    }

    /**
     * Handle the ShopItem "restored" event.
     */
    public function restored(ShopItem $shopItem): void
    {
        //
    }

    /**
     * Handle the ShopItem "force deleted" event.
     */
    public function forceDeleted(ShopItem $shopItem): void
    {
        //
    }
}
