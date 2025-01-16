<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blogs>
 */
class BlogsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $desc = $this->faker->paragraph;
        $desc = substr($desc, 0, 100);
        return [
            'title' => $this->faker->sentence,
            'image' => $this->faker->imageUrl(640, 480, 'cats', true, 'Faker'),
            'description' => $desc,
            'tag' => $this->faker->word,
        ];
    }
}
