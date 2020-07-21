<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

/**
 * Class Pages
 * @package App\Helpers
 */
class Pages
{
    /**
     * @return array
     */
    public static function getPageLayouts()
    {
        $layouts = [];
        $files = Storage::disk('page_layouts')->files();
        if ($files) {
            foreach($files as $file) {
                if (preg_match('/^([a-z0-9-_]+)/i', $file, $matches)) {
                    $layouts[$matches[1]] = mb_convert_case(str_replace(['-', '_'], ' ', $matches[1]), MB_CASE_TITLE);
                }
            }
        }

        return $layouts;
    }
}