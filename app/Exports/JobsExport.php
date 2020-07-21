<?php

namespace App\Exports;

use App\Models\Jobs\Job;
use App\Role;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JobsExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @var int
     */
    protected $boat_id;

    /**
     * @var string|null
     */
    protected $status;

    /**
     * @var int|null
     */
    protected $period_id;

    /**
     * @var array
     */
    protected $roles = [];

    /**
     * JobsExport constructor.
     * @param int $boat_id
     * @param null|string $status
     * @param null|int $period_id
     */
    public function __construct($boat_id, $status = null, $period_id = null)
    {
        $this->boat_id = $boat_id;
        $this->status = $status;
        $this->period_id = $period_id;
        $this->roles = Role::pluck('name', 'slug')->all();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        /** @var \Illuminate\Database\Eloquent\Builder $builder */
        $builder = Job::my($this->boat_id);
        if ($this->status) {
            $builder->where('status', $this->status);
        }
        if ($this->period_id) {
            $builder->join('jobs_periods', 'jobs_periods.job_id', '=', 'jobs.id');
            $builder->where('jobs_periods.period_id', $this->period_id);
        }

        return $builder->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '#',
            'Vessel',
            'Title',
            'For',
            'Visibility',
            'Description',
            'Address',
            'Applicant',
            'Employment Start Date',
            'P/O Number',
            'Warranty',
        ];
    }

    /**
     * @param Job $job
     * @return array
     */
    public function map($job): array
    {
        return [
            $job->id,
            $job->vessel ? $job->vessel->title : '',
            $job->title,
            $this->roles[$job->job_for] ?? '?',
            ucfirst($job->visibility),
            strip_tags($job->content),
            $job->address,
            $job->applicant ? $job->applicant->member_title : '',
            $job->starts_at ? $job->starts_at->format('M j, Y') : '',
            $job->p_o_number,
            $job->warranty,
        ];
    }
}
