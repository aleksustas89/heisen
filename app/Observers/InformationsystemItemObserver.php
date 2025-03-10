<?php

namespace App\Observers;

use App\Models\InformationsystemItem;
use App\Models\Page;

class InformationsystemItemObserver
{

    public function created(InformationsystemItem $informationsystemItem): void
    {
        $Page = new Page();
        $Page->type = Page::getType(InformationsystemItem::class);
        $Page->entity_id = $informationsystemItem->id;
        $Page->save();
    }

    /**
     * Handle the InformationsystemItem "deleted" event.
     */
    public function deleted(InformationsystemItem $informationsystemItem): void
    {

        $type = Page::getType(InformationsystemItem::class);

        if ($type && !is_null($Page = Page::where("entity_id", $informationsystemItem->id)->where("type", $type)->first())) {
            $Page->delete();
        }

        foreach ($informationsystemItem->InformationsystemItemImages as $InformationsystemItemImage) {
            $InformationsystemItemImage->delete();
        }

        $informationsystemItem->deleteDir();
    }
}
