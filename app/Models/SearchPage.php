<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchPage extends Model
{
    use HasFactory;

    public function ShopItem()
    {
        return $this->belongsTo(ShopItem::class);
    }

    public function SearchWords()
    {
        return $this->hasMany(SearchWord::class);
    }

    public function delete()
    {

        foreach ($this->SearchWords as $SearchWord) {
            $SearchWord->delete();
        }

        parent::delete();
    }
}
