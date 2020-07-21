<?php

namespace App\Jobs\Index;

use App\Models\Events\Event;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class EventsUpdate
 * @package App\Jobs\Index
 */
class EventsUpdate implements ShouldQueue
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
        /** @var Event $model */
        $model = Event::findOrFail($this->id);

        try {
            $model->updateIndex();
        } catch (Missing404Exception $e) {
            $model->addToIndex();
        }
    }
}
