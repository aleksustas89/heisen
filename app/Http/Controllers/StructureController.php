<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Models\Structure;

class StructureController extends Controller
{

    static public function show($path, $structure)
    {

        $breadcrumbs = [
            0 => [
                "name" => "Главная",
                "url" => '/'
            ]
        ];

        //echo $structure;

        //dd(self::breadcrumbs($structure));

        Route::view($path, 'structure', [
            'structure' => $structure,
            'breadcrumbs' => BreadcrumbsController::breadcrumbs(self::breadcrumbs($structure)) 
        ]);
    }

    static public function breadcrumbs($structure, $aResult = [])
    {
        
        if ($structure->parent_id > 0) {
            $oStructure = Structure::where("id", $structure->parent_id)->where('active', 1)->first();
            if (!is_null($oStructure)) {
                
                $Result["name"] = $structure->name;
                
                if (count($aResult) > 0) {
                    
                    $Result["url"] = $structure->path();
                }

                array_unshift($aResult, $Result);

                return self::breadcrumbs($oStructure, $aResult);
            }
        } else {

            $Result["name"] = $structure->name;
            
            if (count($aResult) > 0) {
                    
                $Result["url"] = $structure->path();
            }

            array_unshift($aResult, $Result);

            return $aResult;
        }
    }
}