<?php
namespace App\Models;

use App\Facades\GeoLocation;
use Illuminate\Database\Eloquent\Model;

class ServiceArea extends Model
{
    public $table = 'multi_location';


    public $timestamps = false;

    public $fillable = [
        'location_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'location_id' => 'int',
    ];

    /**
     * @return string
     */
    public function getLocationTypeLabelAttribute()
    {
        $title = '';
        switch ($this->location_type) {
            case 'PCLI':
                $title = 'Country';
                break;
            case 'ADM1':
                $title = 'State';
                break;
            case 'ADM2':
                $title = 'County';
                break;
            case 'PPL':
                $title = 'City';
                break;
        }

        return $title;
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        if (!$this->id || $this->wasChanged('location_id')) {
            try {
                $data = GeoLocation::get($this->location_id);
                $this->location_label = $data->name;
                if ($data->adminName2) {
                    $this->location_label .= ", {$data->adminName2}";
                }
                if ($data->adminName1) {
                    $this->location_label .= ", {$data->adminName1}";
                }
                if ($data->fcode != 'PCLI') {
                    if ($data->countryName) {
                        $this->location_label .= ", {$data->countryName}";
                    }
                }
                $this->location_type = $data->fcode;

                if ($hierarchy = GeoLocation::getHierarchy($this->location_id)) {
                    $this->hierarchy = implode('/', (array)GeoLocation::getHierarchy($this->location_id));
                } else {
                    return false;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        return parent::save($options);
    }
}
