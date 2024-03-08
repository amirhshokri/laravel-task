<?php

namespace App\Elasticsearch;

use App\Models\Post;
use App\Models\Setting;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticSearch
{
    public \Elastic\Elasticsearch\Client $client;
    const INDEX_NAME = 'my_index';

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
                    '_index' => self::INDEX_NAME,
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

    public function search($page, $perPage): array
    {
        $posts = [];
        $from = ($page - 1) * $perPage;

        $params = [
            'index' => self::INDEX_NAME,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass(),
                ],
                'from' => $from,
                'size' => $perPage,
            ]
        ];

        $response = $this->client->search($params);

        if ($response['hits']['total']['value'] > 0){
            foreach ($response['hits']['hits'] as $hit) {
                $posts[] = [
                    "id" => $hit['_id'],
                    "title" => $hit['_source']["title"],
                    "content" => $hit['_source']["content"],
                ];
            }
        }

        return $posts;
    }

    public function searchByIds(array $ids): array
    {
        $posts = [];

        $params = [
            'index' => self::INDEX_NAME,
            'body' => [
                'query' => [
                    'ids' => [
                        "values" => $ids
                    ],
                ]
            ]
        ];

        $response = $this->client->search($params);

        if ($response['hits']['total']['value'] > 0){
            foreach ($response['hits']['hits'] as $hit) {
                $posts[] = [
                    "id" => $hit['_id'],
                    "title" => $hit['_source']["title"],
                    "content" => $hit['_source']["content"],
                ];
            }
        }

        return $posts;
    }
}
