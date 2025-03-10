<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InformationsystemItem;
use App\Models\Informationsystem;
use Illuminate\Http\Request;
use App\Services\Helpers\Str;
use App\Services\Helpers\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Models\InformationsystemItemImage;
use App\Models\InformationsystemItemTag;
use App\Models\Page;
use App\Models\Tag;

class InformationsystemItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Informationsystem $informationsystem)
    {

        return view('admin.informationsystem.item.create', [
            'breadcrumbs' => [],
            'informationsystem' => $informationsystem,
            "breadcrumbs" => \App\Http\Controllers\Admin\InformationsystemController::breadcrumbs($informationsystem, true),
            "BadgeClasses" => \App\Models\ShopItemShortcut::$BadgeClasses
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Informationsystem $informationsystem)
    {
        return $this->saveInformationsystemItem($request, $informationsystem);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Informationsystem $informationsystem, InformationsystemItem $informationsystemItem)
    {

        return view('admin.informationsystem.item.edit', [
            'breadcrumbs' => [],
            'informationsystemItem' => $informationsystemItem,
            'images' => $informationsystemItem->getImages(),
            "breadcrumbs" => \App\Http\Controllers\Admin\InformationsystemController::breadcrumbs($informationsystem, true),
            "BadgeClasses" => \App\Models\ShopItemShortcut::$BadgeClasses
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Informationsystem $informationsystem, InformationsystemItem $informationsystemItem)
    {
        return $this->saveInformationsystemItem($request, $informationsystem, $informationsystemItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InformationsystemItem $informationsystemItem)
    {
        $informationsystemItem->delete();

        return redirect()->back()->withSuccess(__(":deleted"));
    }

    public function saveInformationsystemItem(Request $request, Informationsystem $informationsystem, $informationsystemItem = new InformationsystemItem())
    {

        if (!$informationsystemItem) {
            $informationsystemItem = new InformationsystemItem();
            $informationsystemItem->informationsystem_id = $informationsystem->id;
            $informationsystemItem->save();
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:1'],
        ]);
        
        $informationsystemItem->name = $request->name;
        $informationsystemItem->informationsystem_id = $informationsystem->id;
        $informationsystemItem->description = $request->description;
        $informationsystemItem->text = $request->text;
        $informationsystemItem->seo_title = $request->seo_title;
        $informationsystemItem->seo_description = $request->seo_description;
        $informationsystemItem->seo_keywords = $request->seo_keywords;
        $informationsystemItem->active = $request->active;
        $informationsystemItem->sorting = $request->sorting ?? 0;

        $path = trim($request->path);

        $informationsystemItem->path = empty($path) ? Str::transliteration($informationsystemItem->name) : $path;
        $informationsystemItem->url = $informationsystemItem->url();
        $informationsystemItem->save();

        if ($request->apply) {
            return redirect()
                        ->to(route("informationsystem.show", ["informationsystem" => $informationsystem->id]))
                        ->withSuccess(__(':saved'));
        } else {
            return redirect()
                        ->to(route("informationsystem.informationsystem-item.edit", ["informationsystem" => $informationsystem->id, "informationsystem_item" => $informationsystemItem]))
                        ->withSuccess(__(':saved'));
        }
    }


    public function uploadInformationsystemItemImage(InformationsystemItem $informationsystemItem, Request $request)
    {

        if (!is_null($Informationsystem = $informationsystemItem->Informationsystem)) {

            $informationsystemItem->createDir();

            //сохраняем оригинал

            $storePath = "/" . $informationsystemItem->Informationsystem->path() .  $informationsystemItem->path();

            if ($request->file->storeAs($storePath, $request->file->getClientOriginalName())) {
                $fileInfo = File::fileInfoFromStr($request->file->getClientOriginalName());

                $path = $storePath . $request->file->getClientOriginalName();
        
                $oInformationsystemItemImage = new InformationsystemItemImage();
                $oInformationsystemItemImage->informationsystem_item_id = $informationsystemItem->id;
                $oInformationsystemItemImage->save();
        
                foreach (["large", "small"] as $format) {
        
                    $Image = Image::make(Storage::path($path));
        
                    $image_x_max_width  = 'image_' . $format . '_max_width';
                    $image_x_max_height = 'image_' . $format . '_max_height';
        
                    if ($format == 'large' ? $Informationsystem->preserve_aspect_ratio == 1 : $Informationsystem->preserve_aspect_ratio_small == 1) {
                        $Image->resize($Informationsystem->$image_x_max_width, $Informationsystem->$image_x_max_height, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    } else {
                        $Image->fit($Informationsystem->$image_x_max_width, $Informationsystem->$image_x_max_height);
                    }
        
                    $sName = 'image_' . $format . $oInformationsystemItemImage->id .'.' . $fileInfo["extension"];
        
                    $Image->save(Storage::path($storePath) . $sName);
        
                    if ($Informationsystem->convert_webp == 1) {
                        
                        File::webpConvert(Storage::path($storePath), $sName);
        
                        $sName = 'image_' . $format . $oInformationsystemItemImage->id .'.webp';
                    }
        
                    $format == 'large' ? $oInformationsystemItemImage->image_large = $sName : $oInformationsystemItemImage->image_small = $sName;
        
                }
        
                $oInformationsystemItemImage->save();
        
                //удаление оригинального изображения
                Storage::delete($path);
            }
        }
    }

    public function getInformationsystemItemGallery(InformationsystemItem $informationsystemItem)
    {
        return response()->view('admin.informationsystem.item.gallery', [
            'images' => $informationsystemItem->getImages(),
            'informationsystemItem' => $informationsystemItem,
        ]);
    }

    public function deleteImage(InformationsystemItem $informationsystemItem, InformationsystemItemImage $informationsystemItemImage) 
    {

        $response = false;

        if (!is_null($informationsystemItemImage)) {

            $informationsystemItemImage->delete();

            $response = true;
        }

        return response()->json($response);
    }

    public function sortInformationsystemItemImages(Request $request)
    {

        foreach (json_decode($request->images) as $key => $item_image_id) {
            if (!is_null($ShopItemImage = InformationsystemItemImage::find($item_image_id))) {
                $ShopItemImage->sorting = $key;
                $ShopItemImage->save();
            }
        }

        return response()->json(true);
    }

    public function copy(InformationsystemItem $informationsystemItem)
    {

        $informationsystemItem->copy();

        return redirect()->back()->withSucces("Элемент был успешно скопирован!");
    }

    public function getTags(Request $request)
    {
        $aResult = [];


        if (!empty($term = $request->input('term'))) {

            $aResult[] = ["value" => $request->input('term'), "data" => 0];

            foreach (Tag::where('name', "like", "%" . $term . "%")
                        ->orderBy("name", "ASC")->get() as $Tag) {

                $aResult[] = ["value" => $Tag->name, "data" => $Tag->id];
            }
        }

        return response()->json($aResult);
    }

    public function addTag(Request $request)
    {
        if (is_null($Tag = Tag::where('name', $request->tag)->first())) {
            $Tag = new Tag();
            $Tag->name = $request->tag;
            $Tag->save();
        }

        $informationsystemItemTag = new InformationsystemItemTag();
        $informationsystemItemTag->tag_id = $Tag->id;
        $informationsystemItemTag->informationsystem_item_id = $request->informationsystem_item_id;
        $informationsystemItemTag->save();

        return response()->json($informationsystemItemTag->id);
    }

    public function deleteTag(Request $request)
    {
        if (!is_null($informationsystemItemTag = InformationsystemItemTag::find($request->id))) {
            $informationsystemItemTag->delete();

            return response()->json(true);
        }

        return response()->json(false);
    }
}
