<?php

namespace Database\Factories;

use App\Models\Lane;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class LaneFactory extends Factory
{
    protected $model = Lane::class;

    public function definition(): array
    {
        return [
            'name' => 'ممر ' . $this->faker->unique()->numberBetween(1, 100),
            'room_id' => Room::factory(),
        ];
    }
}
