<?php

namespace App\Jobs\Index;

use App\Models\Reviews\Review;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class ReviewsUpdate
 * @package App\Jobs\Index
 */
class ReviewsUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Review
     */
    protected $model;

    /**
     * @param Review $model
     */
    public function __construct(Review $model)
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
        $model = Review::findOrFail($this->model->id);
        if ($model->status == Review::STATUS_APPROVED) {
            try {
                $model->updateIndex();
            } catch (Missing404Exception $e) {
                $model->addToIndex();
            }
        }
        if ($model->status == Review::STATUS_DECLINED) {
            try {
                $model->removeFromIndex();
            } catch (Missing404Exception $e) {
            }
        }
    }
}
