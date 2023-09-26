<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    use HasFactory;

    public function StructureMenu()
    {
        return $this->belongsTo(StructureMenu::class);
    }

    public function url($aResult = array(), $activeAll = true)
    {
        
        if ($this->path != '/') {
            array_unshift($aResult, $this->path);
        }

        if ($this->parent_id > 0) {
            $oStructure = Structure::where("id", $this->parent_id)->whereIn('active', $activeAll ? [0,1] : [1])->first();
            if (!is_null($oStructure)) {
                return "/" . $oStructure->url($aResult);
            }
            return false;
            
        } else {
            return $this->path != '/' ? "/" . implode("/", $aResult) : $this->path;
        }
    }



    public function getChildCount()
	{
		$count = 0;

		$aStructures = Structure::where('parent_id', $this->id)->get();

		foreach ($aStructures as $oStructure)
		{
			$count++;
			$count += $oStructure->getChildCount();
		}

		return $count;
	}

}