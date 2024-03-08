<?php

namespace App\Http\Controllers\API\V1;

use App\Elasticsearch\ElasticSearch;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = (int)$request->input('page', 1);
        $pageSize = (int)$request->input('pageSize', 10);

        $posts = (new ElasticSearch())->search($page, $pageSize);

        return response()->json($posts);
    }

    public function show(Post $post): JsonResponse
    {
        $post = (new ElasticSearch())->searchByIds([$post->id]);
        return response()->json($post);
    }
}
