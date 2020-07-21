<?php

namespace App\Jobs\Index;

use App\Models\Events\Event;
use Elasticquent\ElasticquentClientTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class EventsDelete
 * @package App\Jobs\Index
 */
class EventsDelete implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use ElasticquentClientTrait;

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
        $this->getElasticSearchClient()->delete([
            'index' => Event::getModel()->getIndexName(),
            'type' => Event::getModel()->getTypeName(),
            'id' => $this->id
        ]);
    }
}
