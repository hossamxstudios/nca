<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Governorate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        return [
            'name' => 'مدينة ' . $this->faker->unique()->numberBetween(1, 100),
            'governorate_id' => Governorate::factory(),
        ];
    }
}
