<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ShopItem;
use Illuminate\Support\Facades\Storage;

class ShopItemImage extends Model
{
    use HasFactory;

    public function ShopItem()
    {
        return $this->belongsTo(ShopItem::class);
    }

    public function delete() 
    {

        $path = $this->ShopItem->path();

        Storage::delete([
            $path . $this->image_large,
            $path . $this->image_small,
            $path . $this->file,
        ]);

        parent::delete();

    }
}
