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

            if ($Model = $this->getClass($model)) {
                $object = $Model::find($id);
                if (!is_null($object) && isset($object->$field)) {
                    $object->$field = $object->$field == 1 ? 0 : 1;
                    $object->save();

                    switch ($field) {
                        case "active":
                            if (isset ($object->active) && $object->active == 0) {
                                $response["trClass"] = "off";
                                $response["class"] = "ico-inactive pointer";
                            } else {
                                $response["class"] = "ico-active pointer";
                            }
                        break;
                        case "hidden":
                            if (isset ($object->hidden) && $object->hidden == 1) {
                                $response["trClass"] = "tr-hidden";
                                $response["class"] = "ico-inactive pointer";
                            } else {
                                $response["class"] = "ico-active pointer";
                            }
                        break;
                    }
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