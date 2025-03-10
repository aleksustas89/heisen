<?php

namespace App\Observers;

use App\Models\Structure;
use App\Models\Page;

class StructureObserver
{
    /**
     * Handle the Structure "created" event.
     */
    public function created(Structure $structure): void
    {

        $Page = new Page();
        $Page->type = Page::getType(Structure::class);
        $Page->entity_id = $structure->id;
        $Page->save();
    }

    /**
     * Handle the Structure "updated" event.
     */
    public function updated(Structure $structure): void
    {

        foreach (Structure::where("parent_id", $structure->id)->get() as $Structure) {
            $Structure->setUrl();
        }
    }

    /**
     * Handle the Structure "deleted" event.
     */
    public function deleted(Structure $structure): void
    {

        $type = Page::getType(Structure::class);

        if ($type && !is_null($Page = Page::where("entity_id", $structure->id)->where("type", $type)->first())) {
            $Page->delete();
        }
    }

}
