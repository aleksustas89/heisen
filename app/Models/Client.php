<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    use HasFactory;

    protected $guard = 'client';

    protected $fillable = [
        'name',
        'email',
        'password',
        'force_logout'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function ClientFavorites()
    {
        return $this->hasMany(ClientFavorite::class);
    }

    public function getClientFavorites()
    {
        $clientFavorites = [];
        foreach ($this->ClientFavorites as $favorite) {
            $clientFavorites[] = $favorite->shop_item_id;
        }
        
        return $clientFavorites;
    }
}