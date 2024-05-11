<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopGroup;
use App\Models\ShopItem;
use App\Models\Str;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\Facades\Image;

class ShopGroupController extends Controller
{

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parent = Arr::get($_REQUEST, 'parent_id', 0);

        return view('admin.shop.group.create', [
            'breadcrumbs' => self::breadcrumbs($parent > 0 ? ShopGroup::find($parent) : false, [], true),
            'parent_id' => $parent,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->saveShopGroup($request);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShopGroup $shopGroup)
    {
        return view('admin.shop.group.edit', [
            'shopGroup' => $shopGroup,
            'breadcrumbs' => self::breadcrumbs($shopGroup),
            'store_path' => Shop::$store_path,
        ]);
     }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShopGroup $shopGroup)
    {
        return $this->saveShopGroup($request, $shopGroup);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopGroup $shopGroup)
    {
        $shopGroup->delete();

        return redirect()->back()->withSuccess("Группа была успешно удалена!");
    }

    
    public function show()
    {
        return redirect()->to(route("shop.index"));
    }

    public function index()
    {
        return redirect()->to(route("shop.index"));
    }

    public function saveShopGroup (Request $request, $shopGroup = false) 
    {

        $oldPath = '';

        if (!$shopGroup) {
            $shopGroup = new ShopGroup();
            $oldPath = $shopGroup->path;
        }

        $oShop = Shop::get();

        //$request->validate([
            // 'name' => ['required', 'string', 'max:255'],
            // 'seo_title' => ['required', 'string', 'max:255'],
            // 'seo_keywords' => ['required', 'string', 'max:255'],
            // 'path' => ['required', 'string', 'max:255'],
        //]);

        $shopGroup->name = $request->name;
        $shopGroup->description = $request->description;
        $shopGroup->text = $request->text;
        $shopGroup->active = $request->active == 'on' ? 1 : 0;
        $shopGroup->parent_id = $request->parent_id ?? 0;
        $shopGroup->sorting = $request->sorting ?? 0;
        $shopGroup->path = !empty(trim($request->path)) ? $request->path : Str::transliteration($request->name);
        $shopGroup->seo_title = $request->seo_title;
        $shopGroup->seo_description = $request->seo_description;
        $shopGroup->seo_keywords = $request->seo_keywords;

        $shopGroup->save();

        $this->setUrl($shopGroup);

        if ($request->image_large || $request->image_small) {

            $Filesystem = new Filesystem();

            if (!file_exists('../storage/app' . $shopGroup->dir())) {
                $Filesystem->makeDirectory('../storage/app' . $shopGroup->dir(), 0755, true);
            }

            if ($request->file('image_large')) {
                //сохраняем оригинал
                $request->file('image_large')->storeAs($shopGroup->dir(), $request->file('image_large')->getClientOriginalName());

                //большое изображение
                $image_large = Image::make(Storage::path($shopGroup->dir()) . $request->file('image_large')->getClientOriginalName());
                if ($oShop->preserve_aspect_ratio_group == 1) {
                    $image_large->resize($oShop->group_image_large_max_width, $oShop->group_image_large_max_height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                } else {
                    $image_large->fit($oShop->group_image_large_max_width, $oShop->group_image_large_max_height);
                }
                $sImageLargeName = 'image_large.' . $request->file('image_large')->getClientOriginalExtension();
                $image_large->save(Storage::path($shopGroup->dir()) . $sImageLargeName);
                $shopGroup->image_large = $sImageLargeName;
            }

            if ($request->file('image_small')) {

                $request->file('image_small')->storeAs($shopGroup->dir(), $request->file('image_small')->getClientOriginalName());

                //большое изображение
                $image_small = Image::make(Storage::path($shopGroup->dir()) . $request->file('image_small')->getClientOriginalName());
                if ($oShop->preserve_aspect_ratio_group_small == 1) {
                    $image_small->resize($oShop->group_image_small_max_width, $oShop->group_image_small_max_height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                } else {
                    $image_small->fit($oShop->group_image_small_max_width, $oShop->group_image_small_max_height);
                }
                $sImageSmallName = 'image_small.' . $request->file('image_small')->getClientOriginalExtension();
                $image_small->save(Storage::path($shopGroup->dir()) . $sImageSmallName);
                $shopGroup->image_small = $sImageSmallName;
            }

                    
            $shopGroup->save();
        }

        $message = "Группа была успешно сохранена!";

        if (Arr::get($_REQUEST, 'apply') > 0) {
            return redirect()->to(route("shop.index") . ($shopGroup->parent_id > 0 ? '?parent_id=' . $shopGroup->parent_id : ''))->withSuccess($message);
        } else {
            return redirect()->back()->withSuccess($message);
        }
            
    }

    protected function setUrl(ShopGroup $shopGroup)
    {
        if ($shopGroup->parent_id > 0) {
            if (!is_null($oShopGroup = ShopGroup::find($shopGroup->parent_id))) {
                $shopGroup->url = $oShopGroup->url() . "/" . $shopGroup->path;  
            }
        } else {
            $shopGroup->url = "/" . Shop::path() . $shopGroup->path;  
        }

        $shopGroup->save();

        if ($subGroups = ShopGroup::where("parent_id", $shopGroup->id)->get()) {
            foreach ($subGroups as $subGroup) {
                $this->setUrl($subGroup);
            }
        }

        if ($subItems = ShopItem::where("shop_group_id", $shopGroup->id)->get()) {
            $ShopItemController = new ShopItemController();
            foreach ($subItems as $subItem) {
                $ShopItemController->setUrl($subItem);
            }
        }
    }


    public function deleteImage ($id, $field)
    {

        $oShopGroup = ShopGroup::find($id);
        if ($oShopGroup) {

            Storage::delete(Shop::$store_path . 'group_' . $oShopGroup->id . '/' . $oShopGroup->$field);

            $oShopGroup->$field = '';
            $oShopGroup->save();

            return response()->json('true');
        } else {
            return response()->json('false');
        }

        return response()->json($id);
    }

    public static function breadcrumbs($shopGroup, $aResult = array(), $lastItemIsLink = false)
    {
        if ($shopGroup) {

            $Result["name"] = $shopGroup->name;
            $Result["url"] = '';
            if (!$lastItemIsLink) {
                $Result["url"] = route("shop.index") . '?parent_id=' . $shopGroup->id;
            } 
            array_unshift($aResult, $Result);

            if ($shopGroup->parent_id > 0) {
                return self::breadcrumbs(ShopGroup::find($shopGroup->parent_id), $aResult, false);
            } else {

                $Result["url"] = route("shop.index");
                $Result["name"] = 'Интернет-магазин';

                array_unshift($aResult, $Result);

                return $aResult;
            }

        } else {
            //
            $Result["url"] = route("shop.index");
            $Result["name"] = 'Интернет-магазин';

            array_unshift($aResult, $Result);

            return $aResult;
        }

        return $aResult;
    }
}
