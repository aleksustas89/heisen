<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tag;

class TagController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.tag.index', [
            "breadcrumbs" => $this->breadcrumbs(),
            "tags" => Tag::paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Tag $tag)
    {
        return view('admin.tag.create', [
            "breadcrumbs" => $this->breadcrumbs(true),
            "tag" => $tag,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|unique:tags,name',
        ], [
            'name.required' => 'Поле "Название" обязательно для заполнения.',
            'name.unique' => 'Тег с таким названием уже существует. Попробуйте другое имя.',
        ]);

        Tag::create($validated);
    
        return redirect()->route('tag.index')->with('success', 'Тег успешно создан!');
    }

    /**
     * Display the specified resource.
     */
    // public function show(InformationsystemItemTag $informationsystemItemTag)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(InformationsystemItemTag $informationsystemItemTag)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Tag $Tag)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {

        $tag->delete();

        return redirect()->route('tag.index')->withSuccess('Тег был успешно удален!');
    }

    public static function breadcrumbs($link = false)
    {
        $Result[1]["name"] = 'Теги';

        if ($link) {
            $Result[1]["url"] = route('tag.index');
        }

        return $Result;
    }

}
