<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;


/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(6);
        $status = $this->faker->randomElement(['published', 'draft']);

        return [
            'user_id'      => User::inRandomOrder()->first()->id,
            'category_id'  => Category::inRandomOrder()->first()->id,
            'title'        => $title,
            'slug'         => Str::slug($title),
            'body'         => $this->faker->paragraphs(5, true),
            'status'       => $status,
            'published_at' => $status === 'published' ? $this->faker->dateTimeBetween('-6 months', 'now') : null,
            'created_at'   => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at'   => now(),
        ];
    }
}
