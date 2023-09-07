<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopItemProperty extends Model
{
    use HasFactory;

    public function shopItemPropertyForGroup()
    {
        return $this->hasMany(ShopItemPropertyForGroup::class);
    }

    public function shopItemList()
    {
        return $this->belongsTo(shopItemList::class);
    }

    public static function types()
    {
        return [
            0 => 'Строка',
            1 => 'Целое число',
            2 => 'Число с плавающей запятой',
            3 => 'Флажок',
            4 => 'Список',
        ];
    }

    public function delete()
    {

        foreach ($this->shopItemPropertyForGroup as $shopItemPropertyForGroup) {
            $shopItemPropertyForGroup->truncate();
        }

        return parent::delete();
    }

    public static function getObjectByType($type)
    {
        switch ($type) {
            case 0:
                $object = new PropertyValueString();
            break;
            case 1:
                $object = new PropertyValueInt();
            break;
            case 2:
                $object = new PropertyValueFloat();
            break;
            case 3:
                $object = new PropertyValueInt();
            break;
            case 4:
                $object = new PropertyValueInt();
            break;
        }

        return $object;
    }
}
