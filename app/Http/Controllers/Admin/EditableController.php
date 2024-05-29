<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;

class EditableController extends Controller
{
    public function query(Request $request)
    {
        $response = false;

        if ($request->data) {

            list($apply, $check, $model, $field, $id) = explode("_", $request->data);

            if ($Model = $this->getClass($model)) {
                $object = $Model::find($id);
                if (!is_null($object) && isset($object->$field)) {
                    $object->$field = $request->value;
                    $object->save();

                    if ($model == 'ShopItem' && !is_null($Shop = Shop::get()) && $Shop->apply_items_price_to_modifications == 1) {
                        foreach ($Model::where("modification_id", $object->id)->get() as $mShopItem) {
                            $mShopItem->price = $object->price;
                            $mShopItem->save();
                        }
                    }

                    $response = true;
                }
            }
        }


        return response()->json($response);
    }

    protected function getClass($className) 
    {
        $Class = false;

        if (class_exists($Model = "\App\Models\\" . $className)) {
            $Class = new $Model();
        }

        return $Class;
    }
}