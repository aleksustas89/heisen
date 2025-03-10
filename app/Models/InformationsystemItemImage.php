<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class InformationsystemItemImage extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function InformationsystemItem()
    {
        return $this->belongsTo(InformationsystemItem::class);
    }

    public function delete() 
    {

        $informationsystemItem = $this->InformationsystemItem;

        if (!is_null($informationsystemItem)) {

            $path = $informationsystemItem->path();

            $eLarge = explode(".", $this->image_large);
            $eSmall = explode(".", $this->image_small);
    
            Storage::disk('informationsystem_' . $informationsystemItem->informationsystem_id)->delete([
                $path . $this->image_large,
                $path . $this->image_small,
                $path . $eLarge[0] .".jpg",
                $path . $eSmall[0] .".jpg",
            ]);
    
            parent::delete();
        }
    }
}
