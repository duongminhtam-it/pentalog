<?php

namespace Database\Factories;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(2, 1, 100000),
            'term' => fake()->numerify,
            'date' => now()
        ];
    }

    /**
     * Indicate that the model's status should be pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Loan::STATUS_PENDING,
        ]);
    }

    /**
     * Indicate that the model's status should be approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Loan::STATUS_APPROVED,
        ]);
    }

    /**
     * Indicate that the model's status should be paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Loan::STATUS_PAID,
        ]);
    }
}
