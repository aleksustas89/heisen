<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFavorite extends Model
{
    use HasFactory;

    /**
     * 0 = save in bd
     * 1 = save in cookie
    */
    public static $Type = 1;

    /**
     * 14 days * 24 hours * 60 * 60 = 1209600
    */
    public static $CookieTime = 1209600;
}
