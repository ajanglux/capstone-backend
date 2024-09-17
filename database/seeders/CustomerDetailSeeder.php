<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerDetail;

class CustomerDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerDetail::factory()->count(100)->create();
    }
}
