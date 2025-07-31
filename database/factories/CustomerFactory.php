<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'last_time_message' => $this->faker->optional(0.7)->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Customer yang baru saja mengirim pesan (dalam 1 minggu terakhir)
     */
    public function recentMessage(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_time_message' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Customer yang sudah lama tidak mengirim pesan (lebih dari 3 bulan)
     */
    public function oldMessage(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_time_message' => $this->faker->dateTimeBetween('-1 year', '-3 months'),
        ]);
    }

    /**
     * Customer yang belum pernah mengirim pesan
     */
    public function noMessage(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_time_message' => null,
        ]);
    }

    /**
     * Customer dengan nomor HP Indonesia
     */
    public function indonesianPhone(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone' => '+62' . $this->faker->numerify('8##########'),
        ]);
    }
}
