<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopItemList extends Model
{
    use HasFactory;

    public function listItems()
    {
        return $this->hasMany(ShopItemListItem::class);
    }

    public function delete()
    {

        foreach ($this->listItems as $listItem) {

            $listItem->delete();
        }

        parent::delete();
    }
}
