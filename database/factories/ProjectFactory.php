<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=> fake()->sentence(3),
            'description'=>fake()->text(100),
            'status'=> Arr::random(['planning', 'active', 'on_hold', 'completed', 'archived']),
            'user_id' => User::inRandomOrder()->value('id'),
        ];
    }
}
