<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Informationsystem;
use App\Models\InformationsystemItem;
use Illuminate\Http\Request;

class InformationsystemController extends Controller
{

    /**
     * Display the specified resource.
     */
    public function show(Informationsystem $informationsystem)
    {

        if (request()->operation == 'delete') {

            return $this->deletion(request());
        }

        return view('admin.informationsystem.index', [
            "breadcrumbs" => self::breadcrumbs($informationsystem),
            "informationsystemItems" => $informationsystem->InformationsystemItems()->paginate(),
            "informationsystem" => $informationsystem
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Informationsystem $informationsystem)
    {

        return view('admin.informationsystem.edit', [
            "breadcrumbs" => [],
            "informationsystem" => $informationsystem,
            "breadcrumbs" => self::breadcrumbs($informationsystem, true),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Informationsystem $informationsystem)
    {

        $informationsystem->name = $request->name;
        $informationsystem->active = $request->active;
        $informationsystem->path = $request->path;
        $informationsystem->items_on_page = $request->items_on_page;
        $informationsystem->image_large_max_width = $request->image_large_max_width;
        $informationsystem->image_large_max_height = $request->image_large_max_height;
        $informationsystem->image_small_max_width = $request->image_small_max_width;
        $informationsystem->image_small_max_height = $request->image_small_max_height;
        $informationsystem->preserve_aspect_ratio = $request->preserve_aspect_ratio;
        $informationsystem->preserve_aspect_ratio_small = $request->preserve_aspect_ratio_small;
        $informationsystem->convert_webp = $request->convert_webp ?? 0;

        $informationsystem->save();

        if ($request->apply) {
            return redirect()->to(route("informationsystem.show", ["informationsystem" => $informationsystem]))->withSuccess(__(":saved"));
        } else {
            return redirect()->back()->withSuccess(__(":saved"));
        }
    }

    public function deletion(Request $request)
    {

        $count = 0;

        if ($request->informationsystem_items) {
            foreach ($request->informationsystem_items as $informationsystem_item_id => $on) {

                if (!is_null($informationsystemItem = InformationsystemItem::find($informationsystem_item_id))) {
                    $informationsystemItem->delete();
                    $count++;
                }
            }
        }

        if ($count > 0) {
            return redirect()->back()->withSuccess(__(":deletion"). $count . "!");
        } else {
            return redirect()->back()->withError(__("There ara not chosen items"));
        }
    }

    public static function breadcrumbs(Informationsystem $informationsystem, $lastItemIsLink = false)
    {

        $aResult = [];

        if ($lastItemIsLink) {
            $Result["url"] = route("informationsystem.show", ['informationsystem' => $informationsystem->id]);
        }

        $Result["name"] = 'Информационная система - ' . $informationsystem->name;

        array_unshift($aResult, $Result);

        return $aResult;
    }
    
}
