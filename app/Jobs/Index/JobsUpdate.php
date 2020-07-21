<?php

namespace App\Jobs\Index;

use App\Models\Jobs\Job;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class JobsUpdate
 * @package App\Jobs\Index
 */
class JobsUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    protected $id;

    /**
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var Job $model */
        $model = Job::findOrFail($this->id);

        try {
            $model->updateIndex();
        } catch (Missing404Exception $e) {
            $model->addToIndex();
        }
    }
}
