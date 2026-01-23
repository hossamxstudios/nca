<?php

namespace Database\Factories;

use App\Models\Rack;
use App\Models\Stand;
use Illuminate\Database\Eloquent\Factories\Factory;

class RackFactory extends Factory
{
    protected $model = Rack::class;

    public function definition(): array
    {
        return [
            'name' => 'رف ' . $this->faker->unique()->numberBetween(1, 100),
            'stand_id' => Stand::factory(),
        ];
    }
}
