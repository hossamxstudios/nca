<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'national_id' => $this->faker->unique()->numerify('##############'),
            'client_code' => 'NCA-' . str_pad($this->faker->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'telephone' => $this->faker->optional()->numerify('02########'),
            'mobile' => $this->faker->optional()->numerify('01#########'),
            'notes' => $this->faker->optional()->sentence(),
            'files_code' => $this->faker->optional()->randomElements(['A001', 'B002', 'C003'], rand(0, 3)),
        ];
    }

    public function withoutNationalId(): static
    {
        return $this->state(fn (array $attributes) => [
            'national_id' => null,
        ]);
    }

    public function withoutCode(): static
    {
        return $this->state(fn (array $attributes) => [
            'client_code' => null,
        ]);
    }
}
