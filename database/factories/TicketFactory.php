<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ticket;
use Illuminate\Support\Str;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::ulid(),   
            'subject' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['open', 'in_progress', 'closed']),
            'category' => $this->faker->optional()->word(),
            'note' => $this->faker->optional()->sentence(),
            'explanation' => $this->faker->optional()->sentence(),
            'confidence' => $this->faker->optional()->randomFloat(2, 0, 1),
        ];
    }
}
