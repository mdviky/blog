<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Post;
use App\Models\Tag;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::all();

        Post::factory(50)->create()->each(function ($post) use ($tags) {
            // Attach 1-3 random tags to each post
            $post->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')
            );
        });
    }
}
