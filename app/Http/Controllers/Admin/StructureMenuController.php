<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StructureMenu;
use Illuminate\Http\Request;

class StructureMenuController extends Controller
{



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.structure.menu.index', [
            "breadcrumbs" => self::breadcrumbs(),
            "menus" => StructureMenu::get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.structure.menu.create', [
            'breadcrumbs' => self::breadcrumbs(true),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        return $this->saveMenu($request);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StructureMenu $structureMenu)
    {
        return view('admin.structure.menu.edit', [
            'menu' => $structureMenu,
            'breadcrumbs' => self::breadcrumbs(true),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StructureMenu $structureMenu)
    {
        return $this->saveMenu($request, $structureMenu);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StructureMenu $structureMenu)
    {
        $structureMenu->delete();

        return redirect()->back()->withSuccess('Меню было успешно удалено!');
    }

    protected function saveMenu(Request $request, $StructureMenu = false) 
    {
        if (!$StructureMenu) {
            $StructureMenu = new StructureMenu();
        }

        $StructureMenu = new StructureMenu();
        $StructureMenu->name = $request->name;
        $StructureMenu->save();

        $message = 'Меню было успешно сохранено!';

        if ($request->apply) {
            return redirect()->to(route('structure-menu.index'))->withSuccess($message);
        } else {
           return redirect()->back()->withSuccess($message);
        }

    }

    public static function breadcrumbs($lastItemIsLink = false)
    {
        $aResult[1]["name"] = 'Меню';
        if ($lastItemIsLink) {
            $aResult[1]["url"] = route('structure-menu.index');
        }

        return StructureController::breadcrumbs(false, [], true) + $aResult;
    }
}
