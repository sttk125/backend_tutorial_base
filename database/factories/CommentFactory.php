<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Article;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    
    public function definition(): array
    {
        return [
            'article_id' => Article::factory(),
            'user_id'    => User::factory(),
            'text'       => $this->faker->realTextBetween(20, 80),
        ];
    }
}
