<?php

namespace App\Console\Commands\Index;

use Elasticsearch\Common\Exceptions\Missing404Exception as ElasticsearchMissing404Exception;
use App\Models\Vessels\VesselsAttachment as Model;
use Elasticquent\ElasticquentClientTrait;
use Illuminate\Console\Command;

class VesselAttachments extends Command
{
    use ElasticquentClientTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:vessel-attachments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index vessel attachments';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return bool|mixed|string
     * @throws \Exception
     */
    protected function preparePipeline()
    {
        $connection = $this->getElasticSearchClient()->transport->getConnection();

        $data = [
            "description" => "Extract attachment information encoded in Base64 with UTF-8 charset",
            "processors" => [
                [
                    "attachment" => [
                        "field" => "data"
                    ]
                ]
            ]
        ];
        $dataString = json_encode($data);

        $ch = curl_init($connection->getTransportSchema() . '://' . $connection->getHost() . '/_ingest/pipeline/attachment');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($dataString))
        );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code != 200) {
            throw new \Exception('Failed to create pipeline for attachment', 500);
        }

        $result = json_decode($result);

        if (empty($result) || !$result->acknowledged) {
            throw new \Exception('Unknown response', 500);
        }

        return $result;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        try {
            $this->getElasticSearchClient()->get([
                'index' => '_ingest',
                'type' => 'pipeline',
                'id' => 'attachment',
            ]);
        } catch (ElasticsearchMissing404Exception $e) {
            $this->preparePipeline();
        }

        try {
            Model::deleteIndex();
        } catch (\Throwable $e) {
            // Index doesn't exists
        } finally {
            Model::createIndex();
        }

        Model::putMapping();

        Model::whereIn('global_folder', ['documents', 'templates'])->get()->each(function ($item, $key) {
            $item->addToIndex();
        });
    }
}
