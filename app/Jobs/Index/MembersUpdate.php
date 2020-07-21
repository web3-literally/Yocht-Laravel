<?php

namespace App\Jobs\Index;

use App\User;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class MembersUpdate
 * @package App\Jobs\Index
 */
class MembersUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    protected $model;

    /**
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->model->updateIndex();
        } catch (Missing404Exception $e) {
            $this->model->addToIndex();
        }
    }
}
