<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CdekOffice extends Model
{
    use HasFactory;

    public function CdekCity()
    {
        return $this->belongsTo(CdekCity::class);
    }
}
