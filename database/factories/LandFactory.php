<?php

namespace Database\Factories;

use App\Models\Land;
use App\Models\Client;
use App\Models\Governorate;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class LandFactory extends Factory
{
    protected $model = Land::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'governorate_id' => Governorate::factory(),
            'city_id' => null,
            'district_id' => null,
            'zone_id' => null,
            'area_id' => null,
            'land_no' => $this->faker->numerify('###'),
            'unit_no' => $this->faker->optional()->numerify('##'),
            'address' => $this->faker->optional()->address(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
