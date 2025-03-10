<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformationsystemItemTag extends Model
{
    use HasFactory;

    protected $fillable = ['informationsystem_item_id', 'tag_id'];

    public $timestamps = false;

    public function InformationsystemItem()
    {
        return $this->belongsTo(InformationsystemItem::class);
    }

    public function Tag()
    {
        return $this->belongsTo(Tag::class);
    }

}