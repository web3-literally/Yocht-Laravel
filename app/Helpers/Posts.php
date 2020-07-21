<?php
namespace App\Helpers;

use App\Blog;

/**
 * Class Posts
 * @package App\Helpers
 */
class Posts
{
    /**
     * @return array
     */
    public static function getPostStatuses()
    {
        $statuses = [];
        foreach(Blog::STATUSES as $status) {
            $statuses[$status] = mb_convert_case($status, MB_CASE_TITLE);
        }

        return $statuses;
    }
}