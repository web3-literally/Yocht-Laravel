<?php

namespace App\Helpers;

/**
 * Class Crew
 * @package App\Helpers
 */
class Crew
{
    const IMPORTANT_MEMBERS = ['captain', 'chief-mate', 'chief-engineer', 'chief-steward', 'chief-cook', 'deck-officer', 'engineering-officer', 'electro-technical-officer'];

    /**
     * @return array
     */
    static public function colors()
    {
        return [
            'transparent' => '',
            '#00c0e4' => 'Sky Blue',
            '#5bd999' => 'Green',
            '#ffd772' => 'Yellow',
            '#cc687f' => 'Red',
            '#cb70d7' => 'Pink',
            '#7658f8' => 'Purple',
        ];
    }
}