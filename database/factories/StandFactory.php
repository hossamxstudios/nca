<?php

namespace Database\Factories;

use App\Models\Stand;
use App\Models\Lane;
use Illuminate\Database\Eloquent\Factories\Factory;

class StandFactory extends Factory
{
    protected $model = Stand::class;

    public function definition(): array
    {
        return [
            'name' => 'حامل ' . $this->faker->unique()->numberBetween(1, 100),
            'lane_id' => Lane::factory(),
        ];
    }
}
