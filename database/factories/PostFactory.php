<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
final class PostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence();

        return [
            'author_id' => UserFactory::new(),

            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $this->faker->text(500),
            'published_at' => random_int(0, 1) ? $this->faker->dateTime() : null,
        ];
    }
}
