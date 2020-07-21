<?php

namespace App\Widgets;

use App\Helpers\Business;
use App\Helpers\Vessel;
use Arrilot\Widgets\AbstractWidget;

class BusinessEmployeesProfiles extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @return \App\Models\Business\Business
     */
    public function getBusiness()
    {
        return Business::currentBusiness();
    }

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $business = $this->getBusiness();
        if (!$business) {
            return '';
        }

        $employees = $business->employees;

        return view('widgets.business_employees_profiles', [
            'config' => $this->config,
            'business' => $business,
            'employees' => $employees,
        ]);
    }
}
