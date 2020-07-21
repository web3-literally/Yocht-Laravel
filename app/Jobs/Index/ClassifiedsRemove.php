<?php

namespace App\Jobs\Index;

use App\Models\Classifieds\Classifieds;
use Elasticquent\ElasticquentClientTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class ClassifiedsRemove
 * @package App\Jobs\Index
 */
class ClassifiedsRemove implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use ElasticquentClientTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * ClassifiedsRemove constructor.
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
            'index' => Classifieds::getModel()->getIndexName(),
            'type' => Classifieds::getModel()->getTypeName(),
            'id' => $this->id
        ]);
    }
}
