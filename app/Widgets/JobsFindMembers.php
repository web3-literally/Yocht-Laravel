<?php

namespace App\Widgets;

use App\Models\Services\Service;
use App\Models\Services\ServiceCategory;
use App\Models\Services\ServiceGroup;
use App\Repositories\ServiceRepository;
use Arrilot\Widgets\AbstractWidget;
use Cache;

class JobsFindMembers extends AbstractWidget
{
    public $view = 'widgets.jobs_find_members';

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
        'formTitle' => 'Find Marine Contractor or Yacht Managers',
        'searchBtnLabel' => 'Search'
    ];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $this->config['formId'] = $this->config['formId'] ?? 'find-members';
        $this->config['action'] = $this->config['action'] ?? route('account.jobs.wizard.members');
        $this->config['searchBtnLabel'] = $this->config['searchBtnLabel'] ?? 'Search';

        $groups = Cache::rememberForever('JobsFindGroups', function () {
            $groups = ServiceGroup::pluck('label', 'slug')->all();

            return $groups;
        });

        $selectedCategories = request('categories', []);
        $titledCategories = [];
        if ($selectedCategories) {
            $titledCategories = ServiceCategory::whereIn('id', $selectedCategories)->orderBy('position', 'asc')->pluck('label')->all();
        }

        $selectedServices = request('services', []);
        $titledServices = [];
        if ($selectedServices) {
            $titledServices = Service::whereIn('id', $selectedServices)->orderBy('position', 'asc')->pluck('title')->all();
        }

        $businessCategoriesTitle = [];
        if ($titledCategories) {
            $businessCategoriesTitle += array_merge($businessCategoriesTitle, $titledCategories);
        }
        if ($titledServices) {
            $businessCategoriesTitle = array_merge($businessCategoriesTitle, $titledServices);
        }

        return view($this->view, [
            'config' => $this->config,
            'formId' => $this->config['formId'],
            'formTitle' => $this->config['formTitle'],
            'action' => $this->config['action'],
            'groups' => $groups,
            'businessCategoriesTitle' => (string)implode(',', $businessCategoriesTitle),
            'selectedCategories' => (array)$selectedCategories,
            'selectedServices' => (array)$selectedServices,
            'searchBtnLabel' => $this->config['searchBtnLabel'],
        ]);
    }
}
