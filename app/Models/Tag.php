<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\InformationsystemItemTag;
use App\Services\Helpers\Str;

class Tag extends Model
{
    protected $fillable = ['name'];

    public $timestamps = false;

    protected static function booted()
    {
        static::deleting(function ($tag) {
            InformationsystemItemTag::whereTagId($tag->id)->delete();
        });

        static::creating(function ($tag) {
            $tag->path = Str::transliteration($tag->name);
        });
    }

    public function informationsystemItemTags()
    {
        return $this->hasMany(InformationsystemItemTag::class);
    }

    public function getRouteKeyName()
    {

        if (request()->is('admin/*')) {
            return 'id';
        }

        return 'path';
    }

}