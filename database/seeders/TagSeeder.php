<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        if(!$categories)
            return;

        foreach ($categories as $category)
        {
            Tag::factory()
                ->count(3)
                ->for($category)
                ->create();
        }
    }
}
