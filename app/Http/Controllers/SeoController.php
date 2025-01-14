<?php

namespace App\Http\Controllers;

use App\Models\PropertyValueInt;


class SeoController extends Controller
{

    public static function showGroupTitle($shop, $ShopGroup, $shopFilter = false)
    {

        $result = '';

        if ($shopFilter) {
            $result = $shopFilter->seo_title;
        } else if (!empty($ShopGroup->seo_title)) {
            $result = $ShopGroup->seo_title;
        } else if (!empty($shop->seo_group_title_template)) {
            $result = self::replace($shop->seo_group_title_template, ["{group.name}"], [$ShopGroup->name]);
        } else if (!empty($ShopGroup->name)) {
            $result = $ShopGroup->name;
        }

        echo $result;
    }

    public static function showGroupDescription($shop, $ShopGroup, $shopFilter = false)
    {

        $result = '';
        if ($shopFilter) {
            $result = $shopFilter->seo_description;
        } else if (!empty($ShopGroup->seo_description)) {
            $result = $ShopGroup->seo_description;
        } else if (!empty($shop->seo_group_description_template)) {
            $result = self::replace($shop->seo_group_description_template, ["{group.name}"], [$ShopGroup->name]);
        } 

        echo $result;
    }

    public static function showGroupKeywords($shop, $ShopGroup, $shopFilter = false)
    {

        $result = '';
        if ($shopFilter) {
            $result = $shopFilter->seo_keywords;
        } else if (!empty($ShopGroup->seo_keywords)) {
            $result = $ShopGroup->seo_keywords;
        }

        echo $result;
    }

    public static function showItemTitle($shop, $ShopItem, $Modification = false)
    {

        $result = '';

        if (!empty($ShopItem->seo_title)) {
            $result = $ShopItem->seo_title;
        } else if (!empty($shop->seo_item_title_template)) {

            $Search = [];
            $Replace = [];

            if (!is_null($ShopGroup = $ShopItem->ShopGroup)) {
                $Search[] = "{group.name}";
                $Replace[] = $ShopGroup->name;
            }

            $Search[] = "{Property60.ShopItemList.ShopItemListItem.declension}";

            if ($Modification) {
                if (!is_null($PropertyValueInt = PropertyValueInt::where("property_id", 60)->where("entity_id", $Modification->id)->first())
                    && !is_null($PropertyValueInt->ShopItemListItem)) {

                    $Replace[] = " " . $PropertyValueInt->ShopItemListItem->declension;
                } else {
                    $Replace[] = '';
                }
            } else {
                $Replace[] = '';
            }

            $Search[] = "{item.name}";
            $Replace[] = $ShopItem->name;

            $result = self::replace($shop->seo_item_title_template, $Search, $Replace);
        } else if (!empty($ShopItem->name)) {
            $result = $ShopItem->name;
        }

        echo $result;
    }

    public static function showItemDescription($shop, $ShopItem, $Modification = false)
    {

        $result = '';

        if (!empty($ShopItem->seo_description)) {
            $result = $ShopItem->seo_description;
        } else if (!empty($shop->seo_item_description_template)) {

            $Search = [];
            $Replace = [];

            if (!is_null($ShopGroup = $ShopItem->ShopGroup)) {
                $Search[] = "{group.name}";
                $Replace[] = $ShopGroup->name;
            }

            $Search[] = "{Property60.ShopItemList.ShopItemListItem.declension}";

            if ($Modification) {
                
                if (!is_null($PropertyValueInt = PropertyValueInt::where("property_id", 60)->where("entity_id", $Modification->id)->first())
                    && !is_null($PropertyValueInt->ShopItemListItem)) {

                    $Replace[] = " " . $PropertyValueInt->ShopItemListItem->declension;
                } else {
                    $Replace[] = '';
                }
            } else {
                $Replace[] = '';
            }

            $Search[] = "{item.name}";
            $Replace[] = $ShopItem->name;

            $result = self::replace($shop->seo_item_description_template, $Search, $Replace);

        } 

        echo $result;
    }

    public static function replace($str, $search, $replace)
    {
        return str_replace($search, $replace, $str);
    }

    public static function robots($aResult)
    {

        if (count($aResult) > 0) {
            echo '<meta name="robots" content="' . implode(", ", $aResult) . '" />';
        }

        echo '';
    }
}