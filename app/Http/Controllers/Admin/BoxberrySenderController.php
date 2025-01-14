<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoxberrySender;
use App\Models\Boxberry;

class BoxberrySenderController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BoxberrySender $boxberrySender)
    {
        return view('admin.boxberry.sender.edit', [
            'Boxberry' => Boxberry::find(1),
            'BoxberrySender' => $boxberrySender,
            'breadcrumbs' => \App\Http\Controllers\Admin\ShopController::breadcrumbs(),
       ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BoxberrySender $boxberrySender)
    {
        $boxberrySender->boxberry_office_id = $request->boxberry_office_id;
        $boxberrySender->name = $request->name;
        $boxberrySender->save();

        $text = "Данные были успешно изменены.";

        if ($request->apply) {
            return redirect(route("shop.index"))->withSuccess($text);
        } else {
            return redirect()->back()->withSuccess($text);
        }
    }
}
