<?php

namespace App\Helpers;

class Place
{
    /**
     * @var null|mixed
     */
    protected $_data = null;

    public function __construct($data)
    {
        $this->_data = $data;
    }

    public function getCountry($short = false)
    {
        foreach ($this->_data->address_components as $component) {
            if (in_array('country', $component->types)) {
                return $short ? $component->short_name : $component->long_name;
            }
        }
        return null;
    }

    public function getState($short = false)
    {
        foreach ($this->_data->address_components as $component) {
            if (in_array('administrative_area_level_1', $component->types)) {
                return $short ? $component->short_name : $component->long_name;
            }
        }
        return null;
    }

    public function getCity()
    {
        foreach ($this->_data->address_components as $component) {
            if (in_array('locality', $component->types)) {
                return $component->long_name;
            }
        }
        return null;
    }

    public function getPostalCode()
    {
        foreach ($this->_data->address_components as $component) {
            if (in_array('postal_code', $component->types)) {
                return $component->short_name;
            }
        }
        return null;
    }

    public function getLat()
    {
        return $this->_data->geometry->location->lat;
    }

    public function getLng()
    {
        return $this->_data->geometry->location->lng;
    }
}
