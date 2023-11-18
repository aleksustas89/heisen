<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CdekCity extends Model
{
    use HasFactory;

    public function CdekRegion()
    {
        return $this->belongsTo(CdekRegion::class);
    }
}
