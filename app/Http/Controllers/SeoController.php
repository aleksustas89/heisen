<?php

namespace App\Http\Controllers;


class SeoController extends Controller
{

    public static function showGroupTitle($shop, $ShopGroup)
    {

        $result = '';

        if (!empty($ShopGroup->seo_title)) {
            $result = $ShopGroup->seo_title;
        } else if (!empty($shop->seo_group_title_template)) {
            $result = self::replace($shop->seo_group_title_template, ["{group.name}"], [$ShopGroup->name]);
        } else if (!empty($ShopGroup->name)) {
            $result = $ShopGroup->name;
        }

        echo $result;
    }

    public static function showGroupDescription($shop, $ShopGroup)
    {

        $result = '';

        if (!empty($ShopGroup->seo_description)) {
            $result = $ShopGroup->seo_description;
        } else if (!empty($shop->seo_group_description_template)) {
            $result = self::replace($shop->seo_group_description_template, ["{group.name}"], [$ShopGroup->name]);
        } 

        echo $result;
    }

    public static function showItemTitle($shop, $ShopItem)
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

            $Search[] = "{item.name}";
            $Replace[] = $ShopItem->name;

            $result = self::replace($shop->seo_item_title_template, $Search, $Replace);
        } else if (!empty($ShopItem->name)) {
            $result = $ShopItem->name;
        }

        echo $result;
    }

    public static function showItemDescription($shop, $ShopItem)
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
}