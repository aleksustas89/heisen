<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyValueString;
use App\Models\ShopItemListItem;
use App\Models\PropertyValueInt;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\ShopItem;
use App\Models\ShopItemShortcut;
use App\Models\ShopItemProperty;
use App\Models\Structure;
use App\Models\ShopFilter;
use App\Models\ShopItemImage;
use App\Services\Helpers\File;
use App\Models\Sitemap;
use App\Models\SitemapField;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SitemapController extends Controller
{

    protected $sitemap = NULL;

    protected $sitemapFilename = 'sitemap.xml';

    protected $imagemap = NULL;

    protected $imagemapFilename = 'imagemap.xml';

    protected $yml = NULL;

    protected $ymlFilename = 'yml.xml';

    protected $csvCatalogFilename = 'catalog.csv';

    protected $csvCatalog = NULL;

    protected $xlsxCatalogFilename = 'catalog.xlsx';

    protected $xlsxCatalog = NULL;

    protected $host = NULL;

    public function __construct()
    {
        $this->sitemap  = public_path() . '/' . $this->sitemapFilename;
        $this->imagemap = public_path() . '/' . $this->imagemapFilename;
        $this->yml = public_path() . '/' . $this->ymlFilename;
        $this->csvCatalog = public_path() . '/' . $this->csvCatalogFilename;
        $this->xlsxCatalog = public_path() . '/' . $this->xlsxCatalogFilename;

        $this->host = 'https://' . env('APP_NAME');
    }

    public function index()
    {

        return view("admin.sitemap.index", [
            "breadcrumbs" => $this->breadcrumbs(),
            "sitemapInfo" => [
                "date" => file_exists($this->sitemap) && !is_null($Sitemap = Sitemap::whereTag("Sitemap")->first()) ? date("d.m.Y H:i", strtotime($Sitemap->updated_at)) : '',
                "filesize" => file_exists($this->sitemap) ? File::convertBytes(filesize($this->sitemap)) : '',
                "edit" => false
            ],
            "imagemapInfo" => [
                "date" => file_exists($this->imagemap) && !is_null($Sitemap = Sitemap::whereTag("Imagemap")->first()) ? date("d.m.Y H:i", strtotime($Sitemap->updated_at)) : '',
                "filesize" => file_exists($this->imagemap) ? File::convertBytes(filesize($this->imagemap)) : '',
                "edit" => false
            ],
            "ymlInfo" => [
                "date" => file_exists($this->yml) && !is_null($Sitemap = Sitemap::whereTag("Yml")->first()) ? date("d.m.Y H:i", strtotime($Sitemap->updated_at)) : '',
                "filesize" => file_exists($this->yml) ? File::convertBytes(filesize($this->yml)) : '',
                "edit" => true
            ],
            "csvInfo" => [
                "date" => file_exists($this->csvCatalog) && !is_null($Sitemap = Sitemap::whereTag("csvCatalog")->first()) ? date("d.m.Y H:i", strtotime($Sitemap->updated_at)) : '',
                "filesize" => file_exists($this->csvCatalog) ? File::convertBytes(filesize($this->csvCatalog)) : '',
                "edit" => false
            ],
            "xlsxInfo" => [
                "date" => file_exists($this->xlsxCatalog) && !is_null($Sitemap = Sitemap::whereTag("xlsxCatalog")->first()) ? date("d.m.Y H:i", strtotime($Sitemap->updated_at)) : '',
                "filesize" => file_exists($this->xlsxCatalog) ? File::convertBytes(filesize($this->xlsxCatalog)) : '',
                "edit" => false
            ],
        ]);
    }

    public function edit(Sitemap $sitemap)
    {

        $breadcrumbs[1]["name"] = $sitemap->tag;

        return view('admin.sitemap.edit', [
            "breadcrumbs" => $this->breadcrumbs() + $breadcrumbs,
            'sitemap' => $sitemap,
            'sitemapFields' => SitemapField::where("sitemap_id", $sitemap->id)->get()
        ]);
    }

    public function update(Request $request, Sitemap $sitemap)
    {
        
        foreach (SitemapField::where("sitemap_id", $sitemap->id)->get() as $sitemapField) {

            $field = $sitemapField->field;

            $sitemapField->value = $request->$field ?? '';
            $sitemapField->save();
            
        }

        if ($request->apply) {
            return redirect(route("sitemap.index"))->withSuccess('Данные были успешно сохраненны!');
        } else {
            return redirect(route("sitemap.edit", $sitemap->id))->withSuccess('Данные были успешно сохраненны!');
        }


    }

    public function breadcrumbs()
    {
        $aResult[0]["name"] = 'Sitemap';
        $aResult[0]["url"] = route("sitemap.index");

        return $aResult;
    }

    public function getYml()
    {
        if (!is_null($Sitemap = Sitemap::whereTag("Yml")->first())) {
            if (file_exists($this->yml)) {

                $LastChangedShopGroup = ShopGroup::where("active", 1)->where("deleted", 0)->where("updated_at", ">", $Sitemap->updated_at)->orderBy("updated_at", "DESC")->first();
                $LastChangedShopItem  = ShopItem::where("active", 1)->where("deleted", 0)->where("updated_at", ">", $Sitemap->updated_at)->orderBy("updated_at", "DESC")->first();
        
                if (!is_null($LastChangedShopGroup) || !is_null($LastChangedShopItem)) {
                    $this->setYml();
                }
            } else {
                $this->setYml();
            }

            return response()->redirectTo("/" . $this->ymlFilename);
        } else {
            return response()->to(route("adminSitemap"))->withError("Объект не найден");
        }
    }

    public function setYml()
    {

        if (!is_null($Sitemap = Sitemap::whereTag("Yml")->first())) { 

            $oShop = Shop::get();

            $domDocument = new \DOMDocument('1.0', 'utf-8');

            $yml_catalog = $domDocument->createElement('yml_catalog');

            $date = $domDocument->createAttribute('date');

            $date->value = date("Y-m-d") . "T". date("H:i");

            $yml_catalog->appendChild($date);

            $shop = $domDocument->createElement('shop');

            $shopName = $domDocument->createElement('name', $oShop->name);
            $shop->appendChild($shopName);

            $shopCompany = $domDocument->createElement('company', $oShop->name);
            $shop->appendChild($shopCompany);

            $shopUrl = $domDocument->createElement('url', $this->host);
            $shop->appendChild($shopUrl);

            $deliveryOptionsCost = '';
            $deliveryOptionsDays = '';
            $deliveryOptionsOrderBefore = '';

            foreach (SitemapField::where("sitemap_id", 3)->get() as $SitemapField) {
                if ($SitemapField->field == 'delivery-options-cost' && trim($SitemapField->value) !='') {
                    $deliveryOptionsCost = $SitemapField->value;
                }
                if ($SitemapField->field == 'delivery-options-days' && trim($SitemapField->value) !='') {
                    $deliveryOptionsDays = $SitemapField->value;
                }
                if ($SitemapField->field == 'delivery-options-order-before' && trim($SitemapField->value) !='') {
                    $deliveryOptionsOrderBefore = $SitemapField->value;
                }
            }

            if ($deliveryOptionsCost !='' && $deliveryOptionsDays !='') {

                $delivery_options = $domDocument->createElement('delivery-options');

                $delivery_option = $domDocument->createElement('option');
                $cost = $domDocument->createAttribute('cost');
                $cost->value = $deliveryOptionsCost;
                $days = $domDocument->createAttribute('days');
                $days->value = $deliveryOptionsDays;

                $delivery_option->appendChild($cost);
                $delivery_option->appendChild($days);

                if ($deliveryOptionsOrderBefore) {
                    $order_before = $domDocument->createAttribute('order-before');
                    $order_before->value = 18;
                    $delivery_option->appendChild($order_before);
                }
    
                $delivery_options->appendChild($delivery_option);
    
                $shop->appendChild($delivery_options);
            }

            $categories = $domDocument->createElement('categories');

            foreach (ShopGroup::where("active", 1)->where("deleted", 0)->get() as $ShopGroup) {
                $category = $domDocument->createElement('category', $ShopGroup->name);
                $categoryId = $domDocument->createAttribute('id');
                $categoryId->value = $ShopGroup->id;
                $category->appendChild($categoryId);

                if ($ShopGroup->parent_id > 0) {
                    $categoryParentId = $domDocument->createAttribute('parentId');
                    $categoryParentId->value = $ShopGroup->parent_id;
                    $category->appendChild($categoryParentId);
                }
    
                $categories->appendChild($category);
            }

            $shop->appendChild($categories);


            $offers = $domDocument->createElement('offers');

            foreach (ShopItem::where("active", 1)->where("deleted", 0)->get() as $ShopItem) {
                $offer = $domDocument->createElement('offer');
                $offerId = $domDocument->createAttribute('id');
                $offerId->value = $ShopItem->id;
                $offer->appendChild($offerId);

                $offerName = $domDocument->createElement('name', $ShopItem->name);
                $offer->appendChild($offerName);

                $offerVendorCode = $domDocument->createElement('vendorCode', $ShopItem->marking);
                $offer->appendChild($offerVendorCode);

                $offerUrl = $domDocument->createElement('url', $this->host . $ShopItem->url);
                $offer->appendChild($offerUrl);

                $offerPrice = $domDocument->createElement('price', $ShopItem->price());
                $offer->appendChild($offerPrice);

                if ($oldPrice = $ShopItem->oldPrice()) {
                    $offerOldPrice = $domDocument->createElement('oldprice', $oldPrice);
                    $offer->appendChild($offerOldPrice);
                }

                $offerCurrency = $domDocument->createElement('currencyId', 'RUR');
                $offer->appendChild($offerCurrency);

                $categoryId = $ShopItem->shop_group_id;

                if ($ShopItem->modification_id > 0) {
                    $oShopItem = $ShopItem->parentItemIfModification();
                    $categoryId = $oShopItem->shop_group_id;
                }

                $offerCategoryId = $domDocument->createElement('categoryId', $categoryId);
                $offer->appendChild($offerCategoryId);

                foreach ($ShopItem->getImages() as $image) {
                    if (isset($image["image_large"])) {
                        $offerImage = $domDocument->createElement('picture', $this->host . $image["image_large"]);
                        $offer->appendChild($offerImage);
                    }
                }

                if (!empty($ShopItem->description)) {

                    $offerDescription = $domDocument->createElement('description', '<![CDATA[' . htmlspecialchars($ShopItem->description) . ']]>');
                    $offer->appendChild($offerDescription);
                }

                $offerWarranty = $domDocument->createElement('manufacturer_warranty', 'true');
                $offer->appendChild($offerWarranty);


                $PropertyValueInts = \App\Models\PropertyValueInt::select("property_value_ints.*")->whereIn("property_value_ints.entity_id", function ($query) use ($ShopItem) {
                    $query->select('id')->from('shop_items')->where("modification_id", $ShopItem->id)->where("deleted", 0);
                })->whereNot("value", 0)->get();

                foreach ($PropertyValueInts as $PropertyValueInt) {

                    if (!is_null($PropertyValueInt->ShopItemProperty) && !is_null($PropertyValueInt->ShopItemListItem)) {
                        $offerParam = $domDocument->createElement('param', $PropertyValueInt->ShopItemListItem->value);
                        $offerParamName = $domDocument->createAttribute('name');
                        $offerParamName->value = $PropertyValueInt->ShopItemProperty->name;
                        $offerParam->appendChild($offerParamName);

                        $offer->appendChild($offerParam);
                    }
                }


                if ($ShopItem->weight > 0) {
                    $offerWeight = $domDocument->createElement('weight', $ShopItem->weight / 1000);
                    $offer->appendChild($offerWeight);
                }

                if ($ShopItem->width > 0 && $ShopItem->height > 0 && $ShopItem->length > 0) { 

                    $width = $ShopItem->width / 10;
                    $height = $ShopItem->height / 10;
                    $length = $ShopItem->length / 10;

                    $offerDimensions = $domDocument->createElement('dimensions', "$length/$width/$height");
                    $offer->appendChild($offerDimensions);
                }


                $offers->appendChild($offer);

            }

            $shop->appendChild($offers);
            

            $yml_catalog->appendChild($shop);
 
            $domDocument->appendChild($yml_catalog);

            $domDocument->save($this->yml);

            $Sitemap->updated_at = date("Y-m-d H:i:s");
            $Sitemap->save();

        } else {
            return response()->to(route("adminSitemap"))->withError("Объект не найден");
        }
    }
    
    public function getSitemap()
    {

        if (!is_null($Sitemap = Sitemap::whereTag("Sitemap")->first())) {
            if (file_exists($this->sitemap)) {

                $LastChangedStructure = Structure::where("active", 1)->where("deleted", 0)->where("updated_at", ">", $Sitemap->updated_at)->orderBy("updated_at", "DESC")->first();
                $LastChangedShopGroup = ShopGroup::where("active", 1)->where("deleted", 0)->where("updated_at", ">", $Sitemap->updated_at)->orderBy("updated_at", "DESC")->first();
                $LastChangedShopItem  = ShopItem::where("active", 1)->where("deleted", 0)->where("updated_at", ">", $Sitemap->updated_at)->orderBy("updated_at", "DESC")->first();
                $LastChangedShopFilter  = ShopFilter::where("updated_at", ">", $Sitemap->updated_at)->where("deleted", 0)->orderBy("updated_at", "DESC")->first();
    
                if (!is_null($LastChangedStructure) || !is_null($LastChangedShopGroup) || !is_null($LastChangedShopItem) || !is_null($LastChangedShopFilter)) {
                    $this->setSitemap();
                }
            } else {
                $this->setSitemap();
            }

            return response()->redirectTo("/" . $this->sitemapFilename);
        } else {
            return response()->to(route("adminSitemap"))->withError("Объект не найден");
        }
    }

    public function setSitemap()
    {

        if (!is_null($Sitemap = Sitemap::whereTag("Sitemap")->first())) {

            $domDocument = new \DOMDocument('1.0', 'utf-8');

            $urlset = $domDocument->createElement('urlset');
            $xmlns = $domDocument->createAttribute('xmlns');
    
            $xmlns->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
    
            $urlset->appendChild($xmlns);
    
            $domDocument->appendChild($urlset);
    
            foreach (Structure::where("active", 1)->get() as $Structure) {
                $url = $domDocument->createElement('url');
    
                $loc = $domDocument->createElement('loc', $this->host . $Structure->url);
                $changefreq = $domDocument->createElement('lastmod', date("Y-m-d", strtotime($Structure->updated_at)));
                $priority = $domDocument->createElement('priority', '0.5');
    
                $url->appendChild($loc);
                $url->appendChild($changefreq);
                $url->appendChild($priority);
    
                $urlset->appendChild($url);
            }
    
            foreach (ShopGroup::where("active", 1)->where("deleted", 0)->get() as $ShopGroup) {
                $url = $domDocument->createElement('url');
    
                $loc = $domDocument->createElement('loc', $this->host . $ShopGroup->url);
                $changefreq = $domDocument->createElement('lastmod', date("Y-m-d", strtotime($ShopGroup->updated_at)));
                $priority = $domDocument->createElement('priority', '0.5');
    
                $url->appendChild($loc);
                $url->appendChild($changefreq);
                $url->appendChild($priority);
    
                $urlset->appendChild($url);
            }
    
            foreach (ShopItem::where("active", 1)->where("deleted", 0)->get() as $ShopItem) {
                $url = $domDocument->createElement('url');
    
                $loc = $domDocument->createElement('loc', $this->host . $ShopItem->url);
                $changefreq = $domDocument->createElement('lastmod', date("Y-m-d", strtotime($ShopItem->updated_at)));
                $priority = $domDocument->createElement('priority', '0.5');
    
                $url->appendChild($loc);
                $url->appendChild($changefreq);
                $url->appendChild($priority);
    
                $urlset->appendChild($url);
            }

            foreach (ShopFilter::get() as $ShopFilter) {
                $url = $domDocument->createElement('url');
    
                $loc = $domDocument->createElement('loc', $this->host . $ShopFilter->url);
                $changefreq = $domDocument->createElement('lastmod', date("Y-m-d", strtotime($ShopFilter->updated_at)));
                $priority = $domDocument->createElement('priority', '0.5');
    
                $url->appendChild($loc);
                $url->appendChild($changefreq);
                $url->appendChild($priority);
    
                $urlset->appendChild($url);
            }
    
            $domDocument->save($this->sitemap);

            $Sitemap->updated_at = date("Y-m-d H:i:s");
            $Sitemap->save();

        } else {
            return response()->to(route("adminSitemap"))->withError("Объект не найден");
        }

        
    }

    public function getImagemap()
    {

        if (!is_null($Imagemap = Sitemap::whereTag("Imagemap")->first())) {
            if (file_exists($this->imagemap)) {

                $LastChangedShopItemImage = ShopItemImage::where("image_large", "!=", "")->where("updated_at", ">", $Imagemap->updated_at)->orderBy("updated_at", "DESC")->first();
    
                if (!is_null($LastChangedShopItemImage)) {
                    $this->setImagemap();
                }
            } else {
                $this->setImagemap();
            }
    
            return response()->redirectTo("/" . $this->imagemapFilename);
        } else {
            return response()->to(route("adminSitemap"))->withError("Объект не найден");
        }


    }

    public function setImagemap()
    {

        if (!is_null($Imagemap = Sitemap::whereTag("Imagemap")->first())) {
         
            $domDocument = new \DOMDocument('1.0', 'utf-8');

            $urlset = $domDocument->createElement('urlset');
            $xmlns = $domDocument->createAttribute('xmlns');
            $xmlns_image = $domDocument->createAttribute('xmlns:image');
    
            $xmlns->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
            $xmlns_image->value = 'http://www.google.com/schemas/sitemap-image/1.1';
    
            $urlset->appendChild($xmlns);
            $urlset->appendChild($xmlns_image);
    
            foreach (ShopGroup::where("active", 1)->where("deleted", 0)->get() as $ShopGroup) {
    
                if (file_exists(public_path() . $ShopGroup->dir() . $ShopGroup->image_large)) {
    
                    $url = $domDocument->createElement('url');
                    $loc = $domDocument->createElement('loc', $this->host . $ShopGroup->url);
    
                    $url->appendChild($loc);
    
                    $image_image = $domDocument->createElement('image:image');
                    $image_loc = $domDocument->createElement('image:loc', $this->host . $ShopGroup->dir() . $ShopGroup->image_large);
        
                    $image_image->appendChild($image_loc);
                    $url->appendChild($image_image);
    
                    $urlset->appendChild($url);
                }
            }
    
            foreach (ShopItem::where("active", 1)->where("deleted", 0)->where("modification_id", 0)->get() as $ShopItem) {
    
                if ($Images = $ShopItem->getImages()) {
    
                    $url = $domDocument->createElement('url');
                    $loc = $domDocument->createElement('loc', $this->host . $ShopItem->url);
    
                    $url->appendChild($loc);
           
                    foreach ($Images as $key => $Image) {
    
                        if (isset($Image["image_large"])) {
                            $image_image = $domDocument->createElement('image:image');
                            $image_loc = $domDocument->createElement('image:loc', $this->host . $Image["image_large"]);
                
                            $image_image->appendChild($image_loc);
                            $url->appendChild($image_image);
                        }
                    }
    
                    $urlset->appendChild($url);
                }
            }
    
            $domDocument->appendChild($urlset);
    
            $domDocument->save($this->imagemap);

            $Imagemap->updated_at = date("Y-m-d H:i:s");
            $Imagemap->save();

        } else {
            return response()->to(route("adminSitemap"))->withError("Объект не найден");
        }
    }

    public function getCsvCatalog()
    {
        if (!is_null($csvCatalog = Sitemap::whereTag("csvCatalog")->first())) {
            if (file_exists($this->csvCatalog)) {

                $LastChangedShopItem  = ShopItem::where("active", 1)->where("deleted", 0)->where("updated_at", ">", $csvCatalog->updated_at)->orderBy("updated_at", "DESC")->first();
  
                if (!is_null($LastChangedShopItem)) {
                    $this->setCsvCatalog();
                }

            } else {
                $this->setCsvCatalog();
            }
    
            return response()->redirectTo("/" . $this->csvCatalogFilename);
        } else {
            return response()->to(route("adminSitemap"))->withError("Объект не найден");
        }
    }

    public function setCsvCatalog()
    {
        if (!is_null($csvCatalog = Sitemap::whereTag("csvCatalog")->first())) {

            $aList = [
                ['Url', 'Title', 'Offer minimal price', 'Currency', 'Image url 1', 'Image url 2', 'Image url 3', 'Image url 4', 'Image url 5']
            ];

            foreach (ShopItem::where("active", 1)->where("deleted", 0)->where("modification_id", 0)->get() as $ShopItem) {

                $List = [];

                $List[] = $this->host . $ShopItem->url;
                $List[] = $ShopItem->name;
                $List[] = $ShopItem->price();
                $List[] = 'RUB';

                $k = 0;
                foreach ($ShopItem->getImages() as $image) {

                    if ($k < 5) {
                        $List[] = $this->host . $image['image_large'];
                    }
                    $k++;
                }

                $aList[] = $List;
            }
            
            $fp = fopen($this->csvCatalogFilename, 'w');

            // Преобразуем в Windows-1251
            foreach ($aList as $fields) {
                fputcsv($fp, array_map(fn($v) => iconv('UTF-8', 'Windows-1251//TRANSLIT', $v), $fields), ',');
            }
            
            fclose($fp);

            $csvCatalog->updated_at = date("Y-m-d H:i:s");
            $csvCatalog->save();
        }
    }

    public function getXlsxCatalog()
    {
        if (!is_null($xlsxCatalog = Sitemap::whereTag("xlsxCatalog")->first())) {

            if (file_exists($this->xlsxCatalog)) {

                $LastChangedShopGroup = ShopGroup::where("active", 1)->where("deleted", 0)->where("updated_at", ">", $xlsxCatalog->updated_at)->orderBy("updated_at", "DESC")->first();
                $LastChangedShopItem  = ShopItem::where("active", 1)->where("deleted", 0)->where("updated_at", ">", $xlsxCatalog->updated_at)->orderBy("updated_at", "DESC")->first();

                if (!is_null($LastChangedShopGroup) || !is_null($LastChangedShopItem)) {
                    $this->setXlsxCatalog();
                }
            } else {
                $this->setXlsxCatalog();
            }

            //$this->setXlsxCatalog();

            return response()->redirectTo("/" . $this->xlsxCatalogFilename);
        } else {
            return response()->to(route("adminSitemap"))->withError("Объект не найден");
        }
    }

    public function setXlsxCatalog() 
    {

        if (!is_null($xlsxCatalog = Sitemap::whereTag("xlsxCatalog")->first())) {

            $spreadsheet = new Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();

            $worksheet->getCell('A1')->setValue('Группа');
            $worksheet->getCell('B1')->setValue('Подгруппа');
            $worksheet->getCell('C1')->setValue('Id товара');
            $worksheet->getCell('D1')->setValue('Артикул');
            $worksheet->getCell('E1')->setValue('Название товара');
            $worksheet->getCell('F1')->setValue('Цена');
            $worksheet->getCell('G1')->setValue('Цвет');
            $worksheet->getCell('H1')->setValue('Пол');
            $worksheet->getCell('I1')->setValue("Вес");
            $worksheet->getCell('J1')->setValue("Длина");
            $worksheet->getCell('K1')->setValue("Ширина");
            $worksheet->getCell('L1')->setValue("Высота");
            $worksheet->getCell('M1')->setValue("Изображение");

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(30);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(65);

            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(10);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(10);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);
            $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(10);
            $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(10);
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(50);

            $worksheet->getStyle("A1:M1")->getFont()->setBold(true);

            $worksheet->getStyle('E:M')->getAlignment()->setHorizontal('left');
            $worksheet->getStyle('E:M')->getAlignment()->setVertical('center');

            $i = 2;

            foreach (ShopGroup::where("active", 1)->where("deleted", 0)->where("parent_id", 0)->orderBy("sorting", "ASC")->get() as $ShopGroup) {

                $worksheet->getCell('A' . $i)->setValue($ShopGroup->name);

                $i++;

                foreach (ShopGroup::where("active", 1)->where("deleted", 0)->where("parent_id", $ShopGroup->id)->orderBy("sorting", "ASC")->get() as $ShopGroup2) {
                    $worksheet->getCell('A' . $i)->setValue($ShopGroup->name);
                    $worksheet->getCell('B' . $i)->setValue($ShopGroup2->name);

                    $i++;

                    foreach (ShopItem::where("active", 1)
                                ->where("deleted", 0)
                                ->where("shop_group_id", $ShopGroup2->id)
                                ->where("modification_id", 0)
                                ->orderBy("sorting", "ASC")->get() as $ShopItem1) {

                        $worksheet->getCell('A' . $i)->setValue($ShopGroup->name);
                        $worksheet->getCell('B' . $i)->setValue($ShopGroup2->name);
                        $worksheet->getCell('C' . $i)->setValue($ShopItem1->id);
                        $worksheet->getCell('D' . $i)->setValue($ShopItem1->marking);

                        $name = $ShopItem1->name . ".";
                        
                        $worksheet->getCell('F' . $i)->setValue($ShopItem1->price);

                        $MainMod = ShopItem::where("active", 1)->where("deleted", 0)->where("modification_id", $ShopItem1->id)->where("default_modification", 1)->first();

                        if (!is_null($MainMod)) {
                            if (!is_null($Value = PropertyValueInt::where("entity_id", $MainMod->id)->where("property_id", 60)->first())) {

                                $ShopItemListItem = ShopItemListItem::find($Value->value);
                                
                                $worksheet->getCell('A' . $i)->setValue($ShopGroup->name);
                                $worksheet->getCell('B' . $i)->setValue($ShopGroup2->name);
                                $worksheet->getCell('G' . $i)->setValue($ShopItemListItem->value ?? '');

                                if (!empty($ShopItemListItem->value)) {
                                    $name .= " Натуральная кожа, цвет " . $ShopItemListItem->value;
                                }
                            }
                        }

                        $worksheet->getCell('E' . $i)->setValue($name);

                        if (!is_null($Value = PropertyValueInt::where("entity_id", $ShopItem1->id)->where("property_id", 63)->first())) {
                            $val = '';
                            switch ($Value->value)
                            {
                                case 860:
                                    $val = "Женский";
                                    break;
                                case 861:
                                    $val = "Мужской";
                                    break;
                                case 862:
                                    $val = "Унисекс";
                                    break;
                            }
                            $worksheet->getCell('A' . $i)->setValue($ShopGroup->name);
                            $worksheet->getCell('B' . $i)->setValue($ShopGroup2->name);
                            $worksheet->getCell('H' . $i)->setValue($val);
                        }

                        $worksheet->getCell('I' . $i)->setValue($ShopItem1->weight);
                        $worksheet->getCell('J' . $i)->setValue($ShopItem1->length);
                        $worksheet->getCell('K' . $i)->setValue($ShopItem1->width);
                        $worksheet->getCell('L' . $i)->setValue($ShopItem1->height);

                        foreach ($ShopItem1->getImages(false) as $image) {
                            if (!empty($image["image_large"])) {
                                $worksheet->getCell('M' . $i)->setValue($this->host . $image["image_large"]);
                            }
                        }

                        $i++;
                    }

                    foreach (ShopItemShortcut::join("shop_items", "shop_items.id", "=", "shop_item_shortcuts.shop_item_id")
                                ->where("shop_items.deleted", 0)
                                ->where("shop_items.active", 1)
                                ->where("shop_items.modification_id", 0)
                                ->where("shop_item_shortcuts.shop_group_id", $ShopGroup2->id)->get() as $shopItem3) {

                        if (!is_null($ShopItem1 = ShopItem::find($shopItem3->shop_item_id))) {
                            
                            $worksheet->getCell('A' . $i)->setValue($ShopGroup->name);
                            $worksheet->getCell('B' . $i)->setValue($ShopGroup2->name);
                            $worksheet->getCell('C' . $i)->setValue($ShopItem1->id);
                            $worksheet->getCell('D' . $i)->setValue($ShopItem1->marking);

                            $name = $ShopItem1->name . ".";
                            
                            $worksheet->getCell('F' . $i)->setValue($ShopItem1->price);

                            $MainMod = ShopItem::where("active", 1)->where("deleted", 0)->where("modification_id", $ShopItem1->id)->where("default_modification", 1)->first();

                            if (!is_null($MainMod)) {
                                if (!is_null($Value = PropertyValueInt::where("entity_id", $MainMod->id)->where("property_id", 60)->first())) {

                                    $ShopItemListItem = ShopItemListItem::find($Value->value);
                                    
                                    $worksheet->getCell('A' . $i)->setValue($ShopGroup->name);
                                    $worksheet->getCell('B' . $i)->setValue($ShopGroup2->name);
                                    $worksheet->getCell('G' . $i)->setValue($ShopItemListItem->value ?? '');

                                    if (!empty($ShopItemListItem->value)) {
                                        $name .= " Натуральная кожа, цвет " . $ShopItemListItem->value;
                                    }
                                }
                            }

                            $worksheet->getCell('E' . $i)->setValue($name);

                            if (!is_null($Value = PropertyValueInt::where("entity_id", $ShopItem1->id)->where("property_id", 63)->first())) {
                                $val = '';
                                switch ($Value->value)
                                {
                                    case 860:
                                        $val = "Женский";
                                        break;
                                    case 861:
                                        $val = "Мужской";
                                        break;
                                    case 862:
                                        $val = "Унисекс";
                                        break;
                                }
                                $worksheet->getCell('A' . $i)->setValue($ShopGroup->name);
                                $worksheet->getCell('B' . $i)->setValue($ShopGroup2->name);
                                $worksheet->getCell('H' . $i)->setValue($val);
                            }

                            $worksheet->getCell('I' . $i)->setValue($ShopItem1->weight);
                            $worksheet->getCell('J' . $i)->setValue($ShopItem1->length);
                            $worksheet->getCell('K' . $i)->setValue($ShopItem1->width);
                            $worksheet->getCell('L' . $i)->setValue($ShopItem1->height);

                            foreach ($ShopItem1->getImages(false) as $image) {
                                if (!empty($image["image_large"])) {
                                    $worksheet->getCell('M' . $i)->setValue($this->host . $image["image_large"]);
                                }
                            }
                        }

                        $i++;
                    }
                }

                foreach (ShopItem::where("active", 1)
                    ->where("deleted", 0)
                    ->where("shop_group_id", $ShopGroup->id)
                    ->where("modification_id", 0)
                    ->orderBy("sorting", "ASC")->get() as $ShopItem1) {

                        $worksheet->getCell('A' . $i)->setValue($ShopGroup->name);
                        $worksheet->getCell('B' . $i)->setValue($ShopGroup2->name);
                        $worksheet->getCell('C' . $i)->setValue($ShopItem1->id);
                        $worksheet->getCell('D' . $i)->setValue($ShopItem1->marking);

                        $name = $ShopItem1->name . ".";
                        
                        $worksheet->getCell('F' . $i)->setValue($ShopItem1->price);

                        $MainMod = ShopItem::where("active", 1)->where("deleted", 0)->where("modification_id", $ShopItem1->id)->where("default_modification", 1)->first();

                        if (!is_null($MainMod)) {
                            if (!is_null($Value = PropertyValueInt::where("entity_id", $MainMod->id)->where("property_id", 60)->first())) {

                                $ShopItemListItem = ShopItemListItem::find($Value->value);
                                
                                $worksheet->getCell('A' . $i)->setValue($ShopGroup->name);
                                $worksheet->getCell('B' . $i)->setValue($ShopGroup2->name);
                                $worksheet->getCell('G' . $i)->setValue($ShopItemListItem->value ?? '');

                                if (!empty($ShopItemListItem->value)) {
                                    $name .= " Натуральная кожа, цвет " . $ShopItemListItem->value;
                                }
                            }
                        }

                        $worksheet->getCell('E' . $i)->setValue($name);

                        if (!is_null($Value = PropertyValueInt::where("entity_id", $ShopItem1->id)->where("property_id", 63)->first())) {
                            $val = '';
                            switch ($Value->value)
                            {
                                case 860:
                                    $val = "Женский";
                                    break;
                                case 861:
                                    $val = "Мужской";
                                    break;
                                case 862:
                                    $val = "Унисекс";
                                    break;
                            }
                            $worksheet->getCell('A' . $i)->setValue($ShopGroup->name);
                            $worksheet->getCell('B' . $i)->setValue($ShopGroup2->name);
                            $worksheet->getCell('H' . $i)->setValue($val);
                        }

                        $worksheet->getCell('I' . $i)->setValue($ShopItem1->weight);
                        $worksheet->getCell('J' . $i)->setValue($ShopItem1->length);
                        $worksheet->getCell('K' . $i)->setValue($ShopItem1->width);
                        $worksheet->getCell('L' . $i)->setValue($ShopItem1->height);

                        foreach ($ShopItem1->getImages(false) as $image) {
                            if (!empty($image["image_large"])) {
                                $worksheet->getCell('M' . $i)->setValue($this->host . $image["image_large"]);
                            }
                        }

                    $i++;
                }
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save($this->xlsxCatalogFilename);

            $xlsxCatalog->updated_at = date("Y-m-d H:i:s");
            $xlsxCatalog->save();

            return response()->redirectTo("/" . $this->xlsxCatalogFilename);
        }
    }
}
