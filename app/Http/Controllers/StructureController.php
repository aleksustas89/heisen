<?php

namespace App\Http\Controllers;

use App\Models\Structure;

class StructureController extends Controller
{

    static public function show($Structure)
    {

        return view('structure', [
            'structure' => $Structure,
            'breadcrumbs' => BreadcrumbsController::breadcrumbs(self::breadcrumbs($Structure)),
        ]);
    }

    static public function breadcrumbs($structure, $aResult = [])
    {
        
        if ($structure->parent_id > 0) {
            $oStructure = Structure::where("id", $structure->parent_id)->where('active', 1)->where('deleted', 0)->first();
            if (!is_null($oStructure)) {
                
                $Result["name"] = $structure->name;
                
                if (count($aResult) > 0) {
                    
                    $Result["url"] = $structure->url();
                }

                array_unshift($aResult, $Result);

                return self::breadcrumbs($oStructure, $aResult);
            }
        } else {

            $Result["name"] = $structure->name;
            
            if (count($aResult) > 0) {
                    
                $Result["url"] = $structure->url();
            }

            array_unshift($aResult, $Result);

            return $aResult;
        }
    }

    public static function getStructure()
    {
        $aUrls = explode("/", request()->path());
        $count = count($aUrls);

        if ($count > 0) {
            return self::getChildStructure($aUrls); 
        }

        return false;
         
    }

    public static function getChildStructure($aUrls, $parent = 0, $level = 0)
    {    

        if (isset($aUrls[$level])) {
            $Structure = Structure::where("active", 1)->where("path", $aUrls[$level])->where("parent_id", $parent)->where('deleted', 0)->first();
            if (!is_null($Structure) && $level < count($aUrls) - 1) {
                $level++;
                return self::getChildStructure($aUrls, $Structure->id, $level);
            } else if (!is_null($Structure) && $level == count($aUrls) - 1) {
                return $Structure;
            } else {
                return false;
            }
        }
    }

    public static function buildStructureTree($menu = false)
    {
        $aResult = [];

        $Structure = Structure::where("parent_id", 0)->where("active", 1)->where('deleted', 0)->orderBy("sorting", "ASC");

        if ($menu) {
            $Structure->where("structure_menu_id", $menu);
        }

        foreach ($Structure->get() as $Structure) {
            $aResult[$Structure->id]["id"] = $Structure->id;
            $aResult[$Structure->id]["name"] = $Structure->name;
            $aResult[$Structure->id]["url"] = $Structure->url;
            $aResult[$Structure->id]["sub"] = [];
            foreach (Structure::where("parent_id", $Structure->id)->where("active", 1)->where('deleted', 0)->orderBy("sorting", "ASC")->get() as $sStructure) {

                $aResult[$Structure->id]["sub"][$sStructure->id]["id"] = $sStructure->id;
                $aResult[$Structure->id]["sub"][$sStructure->id]["name"] = $sStructure->name;
                $aResult[$Structure->id]["sub"][$sStructure->id]["url"] = $sStructure->url;
            }
            
        }

        return $aResult;
    }
}