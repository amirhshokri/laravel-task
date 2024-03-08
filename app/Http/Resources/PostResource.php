<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'categories' => $this->getCategoryNames(),
        ];
    }

    private function getCategoryNames()
    {
        return $this->tags->map(function ($tag) {
            return $tag->tag->category->name;
        });
    }
}
