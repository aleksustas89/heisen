<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditableController extends Controller
{
    public function query(Request $request)
    {
        $response = false;

        if ($request->data) {

            list($apply, $check, $model, $field, $id) = explode("_", $request->data);

            $Model = false;

            switch ($model) {
                case "structure":
                    $Model = new \App\Models\Structure();
                    break;
                case "shopOrderItem":
                    $Model = new \App\Models\ShopOrderItem();
                    break;
                case "delivery":
                    $Model = new \App\Models\ShopDelivery();
                    break;
                case "deliveryField":
                    $Model = new \App\Models\ShopDeliveryField();
                    break;
                case "shopItem":
                    $Model = new \App\Models\ShopItem();
                    break;
                case "shopGroup":
                    $Model = new \App\Models\ShopGroup();
                    break;
                case "shopDiscount":
                    $Model = new \App\Models\shopDiscount();
                    break;
            }

            if ($Model) {
                $object = $Model::find($id);
                if (!is_null($object) && isset($object->$field)) {
                    $object->$field = $request->value;
                    $object->save();

                    $response = true;
                }
            }
        }


        return response()->json($response);
    }
}