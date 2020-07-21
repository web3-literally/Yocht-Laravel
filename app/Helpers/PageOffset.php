<?php
namespace App\Helpers;

/**
 * Class PageOffset
 * @package App\Helpers
 */
class PageOffset
{
    /**
     * @param int $limit
     * @param string $param
     * @return int
     */
    public static function offset($limit = 10, $param = 'page')
    {
        $page = request($param, 0);
        if ($page < 0) {
            $page = 0;
        }
        if ($page > 0) {
            $page -= 1;
        }

        return $page * $limit;
    }
}