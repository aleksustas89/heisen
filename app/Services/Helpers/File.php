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

    public static function fileInfoFromStr($file)
    {
        return pathinfo($file);
    }

    /**
     * @return full path to original or new webp file
    */
    public static function webpConvert($dir, $file)
    {

        $fileInfo = self::fileInfoFromStr($file);

        $nameWithoutExt =  $fileInfo["filename"];

        $return = $fullPath = $dir . $file;

        switch (exif_imagetype($fullPath)) {
            case IMAGETYPE_JPEG:
                $img = imagecreatefromjpeg($fullPath);
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
                $return = $dir . $nameWithoutExt . '.webp';
                imagewebp($img, $return, 80);
                imagedestroy($img);

                unlink($fullPath);
            break;

            case IMAGETYPE_PNG:

                $img = imagecreatefrompng($fullPath);
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
                $return = $dir . $nameWithoutExt . '.webp';
                imagewebp($img, $return, 80);

                unlink($fullPath);

            break;
        }

        return $return;
    }
}