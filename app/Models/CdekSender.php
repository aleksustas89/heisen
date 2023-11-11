<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CdekSender extends Model
{
    use HasFactory;

    public function CdekRegion()
    {
        return $this->belongsTo(CdekRegion::class);
    }

    public function CdekCity()
    {
        return $this->belongsTo(CdekCity::class);
    }

    public function CdekOffice()
    {
        return $this->belongsTo(CdekOffice::class);
    }

    public function CdekTariffCode()
    {
        return $this->belongsTo(CdekTariffCode::class);
    }

    public static $Types = [
        0 => [
            "name" => "С офиса Cdek",
            "cdek_tariff_codes" => [136, 137]
        ],
        1 => [
            "name" => "С адреса",
            "cdek_tariff_codes" => [138, 139]
        ],
    ];
}
