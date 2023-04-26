<?php

namespace Database\Factories;

use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inquiry>
 */
class InquiryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'title' => fake()->sentence(3),
            'content' => fake()->paragraph(),
            'category_id' => rand(1, 3),
            'status' => \App\Enums\Inquiry\Status::Open->value,
            'severity' => \App\Enums\Inquiry\Severity::Low->value,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Inquiry $inquiry) {
            foreach (range(1, rand(1, 3)) as $i) {
                $inquiry->addMediaFromUrl("https://picsum.photos/1200/800")->toMediaCollection('images');
            }
        });
    }
}
