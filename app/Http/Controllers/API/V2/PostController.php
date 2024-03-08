<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pageSize = (int)$request->input('pageSize', 10);

        $posts = Post::with(['tags.tag.category'])
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize);

        return response()->json(PostResource::collection($posts));
    }
}
