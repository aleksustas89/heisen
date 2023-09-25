<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchWord extends Model
{
    use HasFactory;

    public function SearchPage()
    {
        return $this->belongsTo(SearchPage::class);
    }
}
