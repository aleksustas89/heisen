<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Informationsystem extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function InformationsystemItems()
    {
        return $this->hasMany(InformationsystemItem::class);
    }

    public function Page()
    {
        return $this->hasOne(Page::class, 'entity_id');
    }

    public function path()
    {
        return "informationsystem_" . $this->id;
    }

    public function delete()
    {
        
        if (!is_null($Page = $this->Page)) {
            $Page->delete();
        }

        parent::delete();
    }
}
