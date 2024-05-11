<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use cijic\phpMorphy\Morphy;
use App\Models\SearchWord;
use App\Models\SearchLog;
use App\Models\ShopItem;
use App\Models\Shop;

class SearchController extends Controller
{

    public $morphyus;

    function __construct() {

        $this->morphyus = new Morphy('ru');
    }

    public function Autocomplete(Request $request)
    {

        $query = $request->input('query');

        $ShopItems = ShopItem::where('shop_items.name', 'LIKE', '%' . $query . '%')
                                ->where('shop_items.modification_id', 0)
                                ->limit(100)
                                ->get();

        foreach ($ShopItems as $ShopItem) {
            $items[] = ["value" => $ShopItem->name, "data" => $ShopItem->name];
        }

        $aResult["suggestions"] = $items;

        return response()->json($aResult);
    }


    public function show(Request $request)
    {

        if (!empty($request->q)) {

            $date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) - (60*60*8));

            $countLog = SearchLog::where("ip", $_SERVER['REMOTE_ADDR'])
                                        ->where("query", $request->q)
                                        ->where("created_at", ">=", $date)->count();

            if ($countLog == 0) {
                $SearchLog = new SearchLog();
                $SearchLog->query = $request->q;
                $SearchLog->ip = $_SERVER['REMOTE_ADDR'];
                $SearchLog->save();
            }

            $words = $this->prepareWords($request->q);

            $SearchWords = $this->get($words);
        }

        $Shop = Shop::get();

        return view('search.index', [
            "SearchWords" => isset($SearchWords) ? $SearchWords->paginate($Shop->items_on_page) : false,
            'q' => $request->q,
        ]);
    
    }

    public function get($words)
    {
        $SearchWords = SearchWord::select('search_words.search_page_id')
            ->distinct()
            ->where(function($query) use ($words) {

            foreach ($words as $k => $word) {
                $query->orWhere(function($query) use ($word) {
                    $query->where("hash", $word);
                });
            }
            });

        if (count($words) > 1) {
            $SearchWords->havingRaw('COUNT(DISTINCT `search_words`.`hash`) = ' . count($words));
        }

        $SearchWords->groupBy('search_words.search_page_id');

        return $SearchWords;
    }

    public function prepareWords($q)
    {

        $SearchController = new \App\Http\Controllers\Admin\SearchController;

        $words = [];

        foreach ($SearchController->getWords($q) as $word) {

            $weight = $SearchController->weight($word);
            if ($weight > 1) {
                $word = $SearchController->lemmatize($word);
            }

            $words[] = $SearchController->crc32($word);
        }

        return $words;
    }

    public function ajaxSearch(Request $request)
    {
        if (!empty($request->q)) {
            $words = $this->prepareWords($request->q);

            $SearchWords = $this->get($words);

            $oShop = Shop::get();

            return view('search.ajax', [
                "q" => $request->q,
                "SearchWords" => isset($SearchWords) ? $SearchWords->paginate($oShop->items_on_page) : [],
            ]);
        }
    }

}
