<?php

namespace Database\Factories;

use App\Models\Governorate;
use Illuminate\Database\Eloquent\Factories\Factory;

class GovernorateFactory extends Factory
{
    protected $model = Governorate::class;

    public function definition(): array
    {
        $governorates = ['القاهرة', 'الجيزة', 'الإسكندرية', 'الشرقية', 'الدقهلية', 'المنوفية', 'القليوبية'];

        return [
            'name' => $this->faker->unique()->randomElement($governorates) . ' ' . $this->faker->unique()->numberBetween(1, 100),
        ];
    }
}
