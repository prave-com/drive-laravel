<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');

        return [
            'email' => fake()->email(),
            'name' => fake()->name(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'avatar' => null,
            'role' => UserRole::USER,
            'is_active' => true,
            'remember_token' => Str::random(10),
            'created_at' => $createdAt,
            'updated_at' => $this->faker->dateTimeBetween($createdAt, 'now'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Specify that the model should have an associated avatar.
     */
    public function withAvatar(): static
    {
        return $this->state(fn (array $attributes) => [
            'avatar' => Str::uuid()->toString().'.jpg',
        ]);
    }

    /**
     * Indicate that the model's activation status should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set the model's role to admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::ADMIN,
        ]);
    }

    /**
     * Set the model's role to superadmin.
     */
    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::SUPERADMIN,
        ]);
    }
}
