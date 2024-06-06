<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShopGroup;
use App\Models\ShopItem;
use App\Models\Structure;
use App\Models\ShopItemImage;
use App\Services\Helpers\File;
use App\Models\Sitemap;

class SitemapController extends Controller
{

    protected $sitemap = NULL;

    protected $sitemapFilename = 'sitemap.xml';

    protected $imagemap = NULL;

    protected $imagemapFilename = 'imagemap.xml';

    protected $host = NULL;

    public function __construct()
    {
        $this->sitemap  = public_path() . '/' . $this->sitemapFilename;
        $this->imagemap = public_path() . '/' . $this->imagemapFilename;

        $this->host = 'https://' . request()->getHost();
    }

    public function index()
    {

        return view("admin.sitemap.index", [
            "breadcrumbs" => $this->breadcrumbs(),
            "sitemapInfo" => [
                "date" => file_exists($this->sitemap) && !is_null($Sitemap = Sitemap::whereTag("Sitemap")->first()) ? date("d.m.Y H:i", strtotime($Sitemap->updated_at)) : '',
                "filesize" => file_exists($this->sitemap) ? File::convertBytes(filesize($this->sitemap)) : '',
            ],
            "imagemapInfo" => [
                "date" => file_exists($this->imagemap) && !is_null($Sitemap = Sitemap::whereTag("Imagemap")->first()) ? date("d.m.Y H:i", strtotime($Sitemap->updated_at)) : '',
                "filesize" => file_exists($this->imagemap) ? File::convertBytes(filesize($this->imagemap)) : '',
            ]
        ]);
    }

    public function breadcrumbs()
    {
        $aResult[0]["name"] = 'Sitemap';
        $aResult[0]["url"] = route("adminSitemap");

        return $aResult;
    }
    
    public function getSitemap()
    {

        if (!is_null($Sitemap = Sitemap::whereTag("Sitemap")->first())) {
            if (file_exists($this->sitemap)) {

                $LastChangedStructure = Structure::where("active", 1)->where("updated_at", ">", $Sitemap->updated_at)->orderBy("updated_at", "DESC")->first();
                $LastChangedShopGroup = ShopGroup::where("active", 1)->where("updated_at", ">", $Sitemap->updated_at)->orderBy("updated_at", "DESC")->first();
                $LastChangedShopItem  = ShopItem::where("active", 1)->where("updated_at", ">", $Sitemap->updated_at)->orderBy("updated_at", "DESC")->first();
    
                if (!is_null($LastChangedStructure) || !is_null($LastChangedShopGroup) || !is_null($LastChangedShopItem)) {
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
                $changefreq = $domDocument->createElement('changefreq', 'daily');
                $priority = $domDocument->createElement('priority', '0.5');
    
                $url->appendChild($loc);
                $url->appendChild($changefreq);
                $url->appendChild($priority);
    
                $urlset->appendChild($url);
            }
    
            foreach (ShopGroup::where("active", 1)->get() as $ShopGroup) {
                $url = $domDocument->createElement('url');
    
                $loc = $domDocument->createElement('loc', $this->host . $ShopGroup->url);
                $changefreq = $domDocument->createElement('changefreq', 'daily');
                $priority = $domDocument->createElement('priority', '0.5');
    
                $url->appendChild($loc);
                $url->appendChild($changefreq);
                $url->appendChild($priority);
    
                $urlset->appendChild($url);
            }
    
            foreach (ShopItem::where("active", 1)->where("modification_id", 0)->get() as $ShopItem) {
                $url = $domDocument->createElement('url');
    
                $loc = $domDocument->createElement('loc', $this->host . $ShopItem->url);
                $changefreq = $domDocument->createElement('changefreq', 'daily');
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
    
            foreach (ShopGroup::where("active", 1)->get() as $ShopGroup) {
    
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
    
            foreach (ShopItem::where("active", 1)->where("modification_id", 0)->get() as $ShopItem) {
    
                if ($Images = $ShopItem->getImages()) {
    
                    $url = $domDocument->createElement('url');
                    $loc = $domDocument->createElement('loc', $ShopItem->url);
    
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
}
