<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'file_name' => $this->faker->words(3, true),
            'barcode' => 'FILE-' . $this->faker->unique()->numerify('######'),
            'original_name' => $this->faker->word() . '.pdf',
            'pages_count' => $this->faker->numberBetween(1, 50),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'failed']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
        ]);
    }
}
