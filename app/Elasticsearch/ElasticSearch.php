<?php

namespace App\Elasticsearch;

use App\Models\Post;
use App\Models\Setting;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticSearch
{
    public \Elastic\Elasticsearch\Client $client;

    public function __construct()
    {
        $host = "http://" . env('ELASTIC_HOST') . ":" . env('ELASTIC_PORT');
        $username = env('ELASTIC_USERNAME');
        $password = env('ELASTIC_PASSWORD');

        $this->client = ClientBuilder::create()
            ->setHosts([$host])
            ->setBasicAuthentication($username, $password)
            ->setSSLVerification(false)
            ->build();
    }

    public function postsBulkIndex($offset)
    {
        $limit = 1000;
        $posts = Post::skip($offset)->take($limit)->get();

        $params = ['body' => []];

        foreach ($posts as $post) {
            $params['body'][] = [
                'index' => [
                    '_index' => 'my_index',
                    '_id' => $post->id,
                ]
            ];

            $params['body'][] = [
                'title' => $post->title,
                'content' => $post->content,
            ];

            $response = $this->client->bulk($params);
        }

        // Send the last batch if it exists
        if (!empty($params['body'])) {
            $response = $this->client->bulk($params);
        }

        if($response->getStatusCode() === 200) {
            //get related settings
            $lastIDSettings = Setting::where([
                'option_key' => 'elastic_bulk_index_last_id',
                'active' => 1
            ])->first();

            if(!$lastIDSettings)
                return;

            $lastIDSettings->option_value = $offset + $limit;
            $lastIDSettings->save();
        }
    }
}
