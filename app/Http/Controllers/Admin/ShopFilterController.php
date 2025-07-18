<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\ShopFilter;
use App\Models\ShopItemProperty;
use App\Models\ShopItemListItem;
use App\Models\ShopGroup;
use App\Models\ShopFilterPropertyValue;
use App\Services\Helpers\Str;

class ShopFilterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.shop.filter.index", [
            "shopFilters" => ShopFilter::where("deleted", 0)->paginate(),
            "breadcrumbs" => $this->breadcrumbs(),
            "shop" => Shop::get()
        ]);
    }

    public function getShopItemProperties()
    {
        return ShopItemProperty::where("show_in_filter", 1)->where("deleted", 0)->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Shop $shop)
    {
        return view("admin.shop.filter.create", [
            "breadcrumbs" => $this->breadcrumbs(),
            "shop" => $shop,
            "shopItemProperties" => $this->getShopItemProperties()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Shop $shop)
    {
        return $this->saveShopFilter($request);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop, ShopFilter $shopFilter)
    {

        $aValue = [];

        foreach ($shopFilter->ShopFilterPropertyValues as $ShopFilterPropertyValue) {
            $aValue[] = $ShopFilterPropertyValue->value;
        }

        return view("admin.shop.filter.edit", [
            "breadcrumbs" => $this->breadcrumbs(),
            "shop" => $shop, 
            "shopFilter" => $shopFilter,
            "shopItemProperties" => $this->getShopItemProperties(),
            "values" => $aValue
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shop $shop, ShopFilter $shopFilter)
    {
        return $this->saveShopFilter($request, $shopFilter);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Shop $shop, ShopFilter $shopFilter)
    {
        $shopFilter->deleted = 1;
        $shopFilter->save();

        return redirect()->back()->withSuccess("Фильтр был успешно удален!");
    }

    public function saveShopFilter(Request $request, $shopFilter = false)
    {

        $request->validate([
            'shop_group_id' => ['required', 'numeric', 'gt:0'],
        ]);

        $oShop = Shop::get();

        if ($request->static_url_checker > 0 && !empty($request->static_url)) {
            $url = $request->static_url;
        } else {
            $url = $this->getUrl($shopFilter);
        }

        $checkShopFilter = $this->checkUrl($url, $shopFilter);

        if ($checkShopFilter === true) {
            if (!$shopFilter) {
                $shopFilter = new ShopFilter();
                $shopFilter->save();
            }
    
            $shopFilter->seo_title = $request->seo_title;
            $shopFilter->seo_description = $request->seo_description;
            $shopFilter->seo_keywords = $request->seo_keywords;
            $shopFilter->text = $request->text;
            $shopFilter->h1 = $request->h1;
            $shopFilter->seo_h1 = $request->seo_h1;
            $shopFilter->seo_text = $request->seo_text;
            
            $shopFilter->indexing = $request->indexing;
            $shopFilter->sorting = $request->sorting;
            $shopFilter->shop_group_id = $request->shop_group_id ?? 0;
            $shopFilter->updated_at = date("Y-m-d H:i:s");
            $shopFilter->static_url = $request->static_url_checker > 0 ? 1 : 0;
    
            $shopFilter->save();

            foreach ($this->getShopItemProperties() as $property) {

                //старые
                foreach (ShopFilterPropertyValue::where("property_id", $property->id)->where("shop_filter_id", $shopFilter->id)->get() as $Value) {
                    $property_id = 'property_' . $property->id . '_' . $Value->id;

                    if (isset($request->$property_id)) {
                        $Value->value = $request->$property_id;
                        $Value->save();
                    } else {
                        $Value->delete();
                    }
                }

                $property_id = 'property_' . $property->id;
    
                if (isset($request->$property_id) && is_array($request->$property_id)) {
    
                    foreach ($request->$property_id as $Value) {
    
                        if ($Value > 0 && !is_null($ShopItemListItem = ShopItemListItem::find($Value))) {

                            $ShopFilterPropertyValue = new ShopFilterPropertyValue(); 
                            $ShopFilterPropertyValue->shop_filter_id = $shopFilter->id;       
                            $ShopFilterPropertyValue->property_id = $property->id;
                            $ShopFilterPropertyValue->value = $Value;
                            $ShopFilterPropertyValue->save();
                        }
                    }
                }
            }
    
            $shopFilter->url = $url;
    
            $shopFilter->save();
    
            $message = 'Данные были успешно изменены';
    
            if ($request->apply) {
              return redirect()->to(route("shop.shop-filter.index", ["shop" => $oShop->id]))->withSuccess($message);
            } else {
              return redirect()->to(route("shop.shop-filter.edit", ["shop" => $oShop->id, "shop_filter" => $shopFilter->id]))->withSuccess($message);
            }
        } else {
            return redirect()->back()->withError("Такой фильтр уже существует #id " . $checkShopFilter->id . ", #url " . $checkShopFilter->url);
        }
    }

    public function checkUrl($url, $shopFilter)
    {

        $ShopFilter = ShopFilter::where("url", $url);

        if ($shopFilter) {
            $ShopFilter->where("id", "!=", $shopFilter->id);
        }

        if (!is_null($ShopFilter = $ShopFilter->first())) {
            return $ShopFilter;
        }
        return true;
    }

    public function getUrl($shopFilter)
    {

        $aUrls = [];
        $Result = '';

        if (!is_null($ShopGroup = ShopGroup::find(request()->shop_group_id))) {
            foreach ($this->getShopItemProperties() as $property) {
        
                $property_id = 'property_' . $property->id;
    
                $Url = [];
    
                if (isset(request()->$property_id) && is_array(request()->$property_id)) {
    
                    foreach (request()->$property_id as $Value) {
    
                        if ($Value > 0 && !is_null($ShopItemListItem = ShopItemListItem::find($Value))) {
    
                            $Url["values"][] = !empty($ShopItemListItem->static_filter_path) ? $ShopItemListItem->static_filter_path : Str::transliteration($ShopItemListItem->value);
                        }
                    }
                }

                if ($shopFilter) {
                    foreach (ShopFilterPropertyValue::where("property_id", $property->id)->where("shop_filter_id", $shopFilter->id)->get() as $Value) {
                        $property_id = 'property_' . $property->id . '_' . $Value->id;

                        if (isset(request()->$property_id) && !is_null($ShopItemListItem = ShopItemListItem::find($Value->value))) {
                            $Url["values"][] = !empty($ShopItemListItem->static_filter_path) ? $ShopItemListItem->static_filter_path : Str::transliteration($ShopItemListItem->value);
                        } 
                    }
                }
    
                $aUrls[] = $Url;
            }
            
            foreach ($aUrls as $aUrl) {
                if (isset($aUrl["values"]) && count($aUrl["values"]) > 0) {
    
                    if (!empty($Result)) {
                        $Result .= "-";
                    }
    
                    $Result .= implode("-", $aUrl["values"]);
                }
            }

            $Result = $ShopGroup->url . "/" . $Result;

            if (request()->sorting > 0) {
                $Result .= (request()->sorting == 1 ? "/v-nachale-novie" : "/v-nachale-starie");
            } 
        }

        return $Result;
    }

    public function breadcrumbs()
    {

        $shop = Shop::get();

        $aResult[0]["url"] = route("shop.shop-filter.index", ["shop" => $shop->id]);
        $aResult[0]["name"] = 'Статические фильтры';

        return $aResult;
    }
}
