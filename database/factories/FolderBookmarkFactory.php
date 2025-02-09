<?php

namespace Database\Factories;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FolderBookmark>
 */
class FolderBookmarkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');

        return [
            'is_starred' => false,
            'folder_id' => Folder::factory(),
            'user_id' => User::factory(),
            'created_at' => $createdAt,
            'updated_at' => $this->faker->dateTimeBetween($createdAt, 'now'),
        ];
    }

    /**
     * Indicate that the model's is bookmarked.
     */
    public function bookmarked(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_starred' => true,
        ]);
    }
}
