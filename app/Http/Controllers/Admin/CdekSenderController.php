<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CdekSender;
use Illuminate\Http\Request;

class CdekSenderController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CdekSender $cdekSender)
    {
        return view('admin.cdek.sender.edit', [
            'CdekSender' => $cdekSender,
            'CdekRegions' => \App\Models\CdekRegion::get(),
            'CdekCities' => \App\Models\CdekCity::where("cdek_region_id", $cdekSender->cdek_region_id)->get(),
            'CdekOffices' => \App\Models\CdekOffice::where("cdek_city_id", $cdekSender->cdek_city_id)->get(),
            'Types' => CdekSender::$Types,
            'breadcrumbs' => \App\Http\Controllers\Admin\ShopController::breadcrumbs(),
       ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CdekSender $cdekSender)
    {
        $cdekSender->cdek_region_id = $request->cdek_region_id;
        $cdekSender->cdek_city_id = $request->cdek_city_id;
        $cdekSender->cdek_office_id = $request->cdek_office_id;
        $cdekSender->address = $request->address;
        $cdekSender->name = $request->name;
        $cdekSender->type = $request->type;
        $cdekSender->save();

        $text = "Данные были успешно изменены.";

        if ($request->apply) {
            return redirect(route("shop.index"))->withSuccess($text);
        } else {
            return redirect()->back()->withSuccess($text);
        }
    }

}
