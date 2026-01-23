<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'name' => 'غرفة ' . $this->faker->unique()->numberBetween(1, 100),
            'building_name' => 'مبنى ' . $this->faker->numberBetween(1, 10),
        ];
    }
}
