<?php
namespace App\Services\Helpers;

class File
{

    public static function filectime($file)
    {

        return fileatime(public_path() . $file);
    }

    public static function js($file)
    {
        
        echo '<script src="'. $file .'?v='. self::filectime($file) .'"></script>';
    }

    public static function css($file)
    {

        echo '<link rel="stylesheet" href="'. $file .'?v='. self::filectime($file) .'">';
    }
}