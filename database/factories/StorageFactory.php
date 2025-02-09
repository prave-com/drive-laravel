<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Storage>
 */
class StorageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $total_quota = $this->faker->numberBetween(3221225472, 10737418240);
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');

        return [
            'total_quota' => $total_quota,
            'user_id' => User::factory(),
            'created_at' => $createdAt,
            'updated_at' => $this->faker->dateTimeBetween($createdAt, 'now'),
        ];
    }
}
