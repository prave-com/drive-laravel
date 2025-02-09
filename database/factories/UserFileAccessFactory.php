<?php

namespace Database\Factories;

use App\Enums\PermissionType;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserFileAccess>
 */
class UserFileAccessFactory extends Factory
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
            'permission_type' => PermissionType::READ,
            'file_id' => File::factory(),
            'user_id' => User::factory(),
            'created_at' => $createdAt,
            'updated_at' => $this->faker->dateTimeBetween($createdAt, 'now'),
        ];
    }

    /**
     * Indicate that the model's permission should be read or write for anyone.
     */
    public function readWrite(): static
    {
        return $this->state(fn (array $attributes) => [
            'permission_type' => PermissionType::READ_WRITE,
        ]);
    }
}
