<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;
use App\Models\ShopGroup;
use App\Models\ShopItem;
use App\Models\Informationsystem;
use App\Models\InformationsystemItem;

class Page extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * types:
     * 0: structure
     * 1: shop_group
     * 2: shop_item
     * 3: informationsystem
     * 4: informationsystem_group
     * 5: informationsystem_item
     * 6: shop_filters
    */

    public static $aObjectsTypes = [
        'Structure' => 1,
        'ShopGroup' => 2,
        'ShopItem'  => 3,
        'Informationsystem' => 4,
        'InformationsystemGroup' => 5,
        'InformationsystemItem'  => 6,
        'ShopFilter'  => 7,
    ];

    public function ShopItem()
    {
        return $this->belongsTo(ShopItem::class, 'entity_id');
    }

    public function ShopGroup()
    {
        return $this->belongsTo(ShopGroup::class, 'entity_id');
    }

    public function Informationsystem()
    {
        return $this->belongsTo(Informationsystem::class, 'entity_id');
    }

    public function InformationsystemItem()
    {
        return $this->belongsTo(InformationsystemItem::class, 'entity_id');
    }

    public function Structure()
    {
        return $this->belongsTo(Structure::class, 'entity_id');
    }

    public function ShopFilter()
    {
        return $this->belongsTo(ShopFilter::class, 'entity_id');
    }

    public static function getType($Object)
    {

        $aObjects = explode('\\', $Object);

        $ClassName = end($aObjects);
        
        return isset(self::$aObjectsTypes[$ClassName]) ? self::$aObjectsTypes[$ClassName] : false;
    }

}
