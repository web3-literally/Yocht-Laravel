<?php

namespace App\Widgets;

use App\Repositories\ServiceRepository;
use Arrilot\Widgets\AbstractWidget;
use Cache;

class FindMembers extends AbstractWidget
{
    public $view = 'widgets.find_members';

    /**
     * @var ServiceRepository
     */
    protected $serviceRepository;

    /**
     * @param ServiceRepository $serviceRepository
     * @param array $config
     */
    public function __construct(ServiceRepository $serviceRepository, array $config = [])
    {
        parent::__construct($config);

        $this->serviceRepository = $serviceRepository;
    }

    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [
        'formId' => 'find-members',
        'searchBtnLabel' => 'Search Now'
    ];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $this->config['formId'] = $this->config['formId'] ?? 'find-members';
        $this->config['action'] = $this->config['action'] ?? route('members.search');
        $this->config['searchBtnLabel'] = $this->config['searchBtnLabel'] ?? 'Search';

        $groups = Cache::rememberForever('FindGroups', function () {
            $groups = [
                'businesses' => trans('businesses.businesses'),
                'vessels' => trans('vessels.vessels')
            ];

            return $groups;
        });

        return view($this->view, [
            'config' => $this->config,
            'formId' => $this->config['formId'],
            'action' => $this->config['action'],
            'groups' => $groups,
            'searchBtnLabel' => $this->config['searchBtnLabel'],
        ]);
    }
}
