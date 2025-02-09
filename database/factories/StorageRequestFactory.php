<?php

namespace Database\Factories;

use App\Enums\StorageRequestStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StorageRequest>
 */
class StorageRequestFactory extends Factory
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
            'request_quota' => $this->faker->randomElement([5368709120, 10737418240]),
            'reason' => $this->faker->sentence(),
            'status' => StorageRequestStatus::PENDING,
            'user_id' => User::factory(),
            'created_at' => $createdAt,
            'updated_at' => $this->faker->dateTimeBetween($createdAt, 'now'),
        ];
    }

    /**
     * Indicate that the model's status should be approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StorageRequestStatus::APPROVED,
        ]);
    }

    /**
     * Indicate that the model's status should be rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StorageRequestStatus::REJECTED,
        ]);
    }
}
