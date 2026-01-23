<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        $items = ['عقد بيع', 'عقد إيجار', 'شهادة ميلاد', 'بطاقة رقم قومي', 'توكيل', 'إعلام وراثة', 'رخصة بناء', 'محضر', 'حكم محكمة'];

        return [
            'name' => $this->faker->unique()->randomElement($items) . ' ' . $this->faker->unique()->numberBetween(1, 1000),
        ];
    }
}
