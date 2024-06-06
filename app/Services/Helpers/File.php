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

        $pathToFile = public_path() . $file;

        if (file_exists($pathToFile)) {

            echo '<script>' . file_get_contents($pathToFile) . '</script>';
        }
    }

    public static function css($file)
    {

        $pathToFile = public_path() . $file;

        if (file_exists($pathToFile)) {

            echo '<style>' . file_get_contents($pathToFile) . '</style>';
        }
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

    public static function convertBytes($size)
    {
        $i = 0;
        while (floor($size / 1024) > 0) {
            ++$i;
            $size /= 1024;
        }
    
        $size = str_replace('.', ',', round($size, 1));
        switch ($i) {
            case 0: return $size .= ' байт';
            case 1: return $size .= ' КБ';
            case 2: return $size .= ' МБ';
        }
    }
}