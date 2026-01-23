<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class DistrictFactory extends Factory
{
    protected $model = District::class;

    public function definition(): array
    {
        return [
            'name' => 'حي ' . $this->faker->unique()->numberBetween(1, 100),
            'city_id' => City::factory(),
        ];
    }
}
