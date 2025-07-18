<?php

namespace App\Observers;

use App\Models\ShopFilter;
use App\Models\Page;

class ShopFilterObserver
{
    /**
     * Handle the Structure "created" event.
     */
    public function created(ShopFilter $shopFilter): void
    {

        $Page = new Page();
        $Page->type = Page::getType(ShopFilter::class);
        $Page->entity_id = $shopFilter->id;
        $Page->save();
    }
        
    public function deleted(ShopFilter $shopFilter): void
    {

        $type = Page::getType(ShopFilter::class);

        if ($type && !is_null($Page = Page::where("entity_id", $shopFilter->id)->where("type", $type)->first())) {
            $Page->delete();
        }
    }
}