<?php

namespace Database\Factories;

use App\Models\CustomerDetail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomerDetailFactory extends Factory
{
    protected $model = CustomerDetail::class;

    public function definition()
    {
        return [
            'code' => 'CUST-' . Str::upper(Str::random(8)),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'address' => $this->faker->address,
            'status' => $this->faker->randomElement(['pending', 'on-going', 'completed']),
        ];
    }
}
