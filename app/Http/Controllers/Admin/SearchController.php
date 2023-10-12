<?php

namespace App\Http\Controllers\Admin;

use cijic\phpMorphy\Morphy;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShopItem;
use App\Models\ShopGroup;
use App\Models\SearchWord;
use App\Models\SearchPage;

class SearchController extends Controller
{

    public $morphyus;
    private $regexp_entity = '/&([a-zA-Z0-9]+);/';
	public  $regexp_word   = '/([a-zа-я0-9]+)/ui';
    public $limit = 100;

    function __construct() {

        $this->morphyus = new Morphy('ru');
    }

    public function index()
    {
        return view('admin.search.index', [
            'breadcrumbs' => self::breadcrumbs(),
            'count' => SearchPage::count(),
        ]);
    }

    public static function breadcrumbs()
    {
        $aResult[0]["name"] = 'Поиск';
        $aResult[0]["url"] = route("adminSearch");

        return $aResult;
    }

    private function objectsToIndex()
    {
        return [
            0 => ShopItem::where("modification_id", 0)->where("active", 1),
            //1 => ShopGroup::where("active", 1),
        ];
    }

    public function indexing(Request $request)
    {

        $type = $request->type ?? 0;

        $data["offset"] = $request->offset ?? 0;

        if ($data["offset"] == 0 && $type == 0) {

            SearchWord::truncate();
            SearchPage::truncate();
        }

        $data["indexed"] = 0;

        $objects = self::objectsToIndex();

        $objectToIndex = $objects[$type];

        $data["type"] = $type;

        $data["finished"] = false;  

        $count = $objectToIndex->count();

        $aItems = $objectToIndex->offset($data["offset"])->limit($this->limit)->get();

            foreach ($aItems as $aItem) {

                if (isset($aItem->name) && $words = $this->getWords($aItem->name)) {

                    $this->addWords($words, $aItem);
                }
                if (isset($aItem->description) && $words = $this->getWords($aItem->description)) {
    
                    $this->addWords($words, $aItem);
                }
            }
            $data["indexed"] = count($aItems) + $request->indexed;
            $data["finished"] = $data["indexed"] >= $count ? true : false;  
    

        if ($data["finished"] && isset($objects[$type + 1])) {
            $data["offset"] = 0;
            $data["type"] = $type + 1;
            $data["finished"] = false;
        } else {
            $data["offset"] += $this->limit;
        }

        
        return response()->json($data);
    }


    public function addWords($words, $object)
    {
        foreach ($words as $word) {

            $weight = $this->weight($word);
            if ($weight > 1) {
                $this->addWord($this->lemmatize($word), $object, $weight);
            }

            $this->addWord($word, $object, $weight);
        }
    }

    public function addWord($word, $object, $weight)
    {

        if (is_null($SearchPage = SearchPage::where("shop_item_id", $object->id)->first())) {

            $SearchPage = new SearchPage();
            $SearchPage->shop_item_id = $object->id;
            $SearchPage->save();
        }

        $hash = self::crc32($word);
        if (is_null($SearchWord = SearchWord::where("hash", $hash)->where("search_page_id", $SearchPage->id)->first())) {
            $SearchWord = new SearchWord(); 
            $SearchWord->hash = $hash;
            $SearchWord->weight = $weight;
            $SearchWord->search_page_id = $SearchPage->id;
            $SearchWord->save();
        }
        
    }

    public function getWords( $content, $filter=true ) {
        // Фильтрация HTML-тегов и HTML-сущностей //
        if ( $filter ) {
            $content = strip_tags( $content );
            $content = preg_replace( $this->regexp_entity, ' ', $content );
        }

        // Перевод в верхний регистр //
        $content = mb_strtoupper( $content, 'UTF-8' );

        // Замена ё на е //
        $content = str_ireplace( 'Ё', 'Е', $content );

        // Выделение слов из контекста //
        preg_match_all( $this->regexp_word, $content, $words_src );
        return $words_src[ 1 ];
    }

    public function lemmatize($word) {

        // Получение базовой формы слова //
        $lemmas = $this->morphyus->getPseudoRoot($word);

        return $lemmas[0] ?? $word;
    }

    public function weight($word, $profile = false) {

        // Попытка определения части речи //
        $partsOfSpeech = $this->morphyus->getPartOfSpeech($word);

        // Профиль по умолчанию //
        if (!$profile) {
            $profile = [
                // Служебные части речи //
                'ПРЕДЛ' => 0,
                'СОЮЗ'  => 0,
                'МЕЖД'  => 0,
                'ВВОДН' => 0,
                'ЧАСТ'  => 0,
                'МС'    => 0,

                // Наиболее значимые части речи //
                'С'     => 5,
                'Г'     => 5,
                'П'     => 3,
                'Н'     => 3,

                // Остальные части речи //
                'DEFAULT' => 1
            ];
        }

        // Если не удалось определить возможные части речи //
        if (!$partsOfSpeech) {
            return $profile['DEFAULT'];
        }

        // Определение ранга //
        for ($i = 0; $i < count($partsOfSpeech); $i++) {
            if (isset($profile[$partsOfSpeech[$i]])) {
                $range[] = $profile[$partsOfSpeech[$i]];
            } else {
                $range[] = $profile['DEFAULT'];
            }
        }

        return min($range);
    }

    static public function convert64b32($int)
	{
		if ($int > 2147483647 || $int < -2147483648)
		{
			$int = $int ^ -4294967296;
		}

		return $int;
	}

	/**
	 * Get CRC32 from source string
	 * @param string $value value
	 * @return int
	 */
	static public function crc32($value)
	{
		return self::convert64b32(crc32($value));
	}
}
