<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;

class InformationsystemItem extends Model
{
    use HasFactory;

    protected $fillable = ['sorting', 'indexing', 'active', 'informationsystem_id', 'informationsystem_group_id'];


    public function Informationsystem()
    {
        return $this->belongsTo(Informationsystem::class);
    }

    public function InformationsystemItemImages()
    {
        return $this->hasMany(InformationsystemItemImage::class);
    }

    public function informationsystemItemTags()
    {
        return $this->hasMany(InformationsystemItemTag::class);
    }


    public function Page()
    {
        return $this->hasOne(Page::class, 'entity_id');
    }


    public function url()
    {

        return "/" . $this->Informationsystem->path . '/' . $this->path;
    }

    public function getImages($all = true)
    {
        
        $aReturn = [];

        foreach ($this->InformationsystemItemImages()->where("image_small", "!=", '')->orderBy("sorting", "asc")->get() as $k => $InformationsystemItemImage) {

            if ($all === false && $k > 0) {
                break;
            }

            $path = $this->path();

            if (!empty($InformationsystemItemImage->image_small) && Storage::disk($this->Informationsystem->path())->exists($path . $InformationsystemItemImage->image_small)) {
                $aReturn[$InformationsystemItemImage->id]["image_small"] = "/" . $this->Informationsystem->path() . $path . $InformationsystemItemImage->image_small;

                if (!empty($InformationsystemItemImage->image_large) ) {
                    $aReturn[$InformationsystemItemImage->id]["image_large"] = "/" . $this->Informationsystem->path() . $path . $InformationsystemItemImage->image_large;
                }
            }
        }

        return $aReturn;
    }

    public function path()
    {
        return '/item_' . $this->id . '/';
    }

    public function fullStoragePath()
    {
        return base_path() . "/storage/app/informationsystem_" . $this->informationsystem_id . $this->path();
    }
    
  
    public function createDir()
    {

        if (!file_exists($fullStoragePath = $this->fullStoragePath())) {
            $Filesystem = new Filesystem();
            $Filesystem->makeDirectory($fullStoragePath, 0777, true);
        }
    }

    public function deleteDir()
    {

        if ($this->path() && file_exists($fullStoragePath = $this->fullStoragePath())) {
            $Filesystem = new Filesystem();

            $Filesystem->deleteDirectory($fullStoragePath);
        }
    }

    public function delete()
    {

        foreach ($this->InformationsystemItemImages as $InformationsystemItemImage) {
            $InformationsystemItemImage->delete();
        }

        if (!is_null($InformationsystemItemLanguageEntity = $this->InformationsystemItemLanguageEntity)) {
           
            if (!is_null($LanguageEntity = $InformationsystemItemLanguageEntity->LanguageEntity)) {
                $LanguageEntity->delete();
            }

            $InformationsystemItemLanguageEntity->delete();
        }

        if (!is_null($Page = $this->Page)) {
            $Page->delete();
        }
   
        $this->deleteDir();

        parent::delete();
    }

    public function copy()
    {

        $this->path = $this->path . "-copy-" . time();
        $this->url = $this->url . "-copy-" . time();

        $nInformationsystemItem = $this->replicate()->fill([
            'type' => 'billing'
        ]);

        $nInformationsystemItem->push();

        $nInformationsystemItem->createDir();

        //картинки
        $oldPath = $this->fullStoragePath();
        $newPath = $nInformationsystemItem->fullStoragePath(); 
   
        foreach ($this->InformationsystemItemImages as $InformationItemImage) {

            if (!empty($InformationItemImage->image_large) && !empty($InformationItemImage->image_small)) {
                $imageLarge = $oldPath . $InformationItemImage->image_large;
                $newImageLarge = $newPath . $InformationItemImage->image_large;
                $imageSmall = $oldPath . $InformationItemImage->image_small;
                $newImageSmall = $newPath . $InformationItemImage->image_small;
    
                if (file_exists($imageLarge)) {
                    $copyLarge = copy($imageLarge, $newImageLarge);
                }
    
                if (file_exists($imageSmall)) {
                    $copySmall = copy($imageSmall, $newImageSmall);
                }
    
                if ($copyLarge || $copySmall) {
                    $nInformationsystemItemImage = new InformationsystemItemImage();
                    $nInformationsystemItemImage->informationsystem_item_id = $nInformationsystemItem->id;
                    $nInformationsystemItemImage->image_large = $copyLarge ? $InformationItemImage->image_large : '';
                    $nInformationsystemItemImage->image_small = $copySmall ? $InformationItemImage->image_small : '';
                    $nInformationsystemItemImage->sorting = $InformationItemImage->sorting;
                    $nInformationsystemItemImage->save();
    
                    $imagesHistory[$InformationItemImage->id] = $nInformationsystemItemImage->id;
                    
                }
            }
        }
    }

    
}
