<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToggleController extends Controller
{
    public function query(Request $request)
    {
        $response = [];
        $response["trClass"] = '';

        if ($request->data) {

            list($toggle, $model, $field, $id) = explode("_", $request->data);

            $Model = false;

            switch ($model) {
                case "structure":
                    $Model = new \App\Models\Structure();
                    break;
                case "shopItem":
                    $Model = new \App\Models\shopItem();
                    break;
                case "shopDiscount":
                    $Model = new \App\Models\shopDiscount();
                    break;
            }

            if ($Model) {
                $object = $Model::find($id);
                if (!is_null($object) && isset($object->$field)) {
                    $object->$field = $object->$field == 1 ? 0 : 1;
                    $object->save();

                    $response["value"] = $object->$field;

                    if (isset ($object->active) && $object->active == 0) {
                        $response["trClass"] = "off";
                    }
                }
            }

        }

        return response()->json($response);
    }
}