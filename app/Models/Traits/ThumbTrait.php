<?php

namespace App\Models\Traits;

use Intervention\Image\Facades\Image;

/**
 * Trait ThumbTrait
 * @package App\Models\Traits
 */
trait ThumbTrait
{
    /**
     * @param string $sourceImage
     * @param string $size
     * @return bool|string
     */
    public function genThumb($sourceImage, $size)
    {
        $sourceFolder = dirname($sourceImage) . '/';
        $sourceFileName = basename($sourceImage);

        $size = strtolower($size);
        try {
            if (!preg_match('/^([0-9]{0,4})x([0-9]{0,4})$/', $size)) {
                throw new \Exception('Wrong thumbnail image size');
            }
            $sourcepath = public_path() . $sourceImage;
            $path = public_path() . $sourceFolder . $size . '/';
            $filepath = $path . $sourceFileName;
            if (!file_exists($filepath)) {
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                list($width, $height) = explode('x', $size);
                Image::make($sourcepath)->fit($width, $height)->save($filepath);
            }
            return asset($sourceFolder . $size . '/' . $sourceFileName);
        } catch (\Throwable $e) {
            return false;
        }

        return asset($sourceFolder . $sourceFileName);
    }
}