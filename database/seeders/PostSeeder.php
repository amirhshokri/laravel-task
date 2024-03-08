<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostTag;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::all();

        if(!$tags)
            return;

        for ($i = 1; $i <= 1000000; $i++){
            $post = Post::factory()->create();

            $selectedTags = $tags->random(mt_rand(2, 10));
            foreach ($selectedTags as $selectedTag) {
                PostTag::create([
                    'post_id' => $post->id,
                    'tag_id' => $selectedTag->id
                ]);
            }
        }
    }
}
