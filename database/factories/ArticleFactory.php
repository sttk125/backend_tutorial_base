<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'  => $this->faker->sentence(4),
            'body'   => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(['draft', 'published']),
        ];
    }
}
