<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerDetail;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CustomerDetailSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id');

        foreach (range(1, 10) as $index) {
            CustomerDetail::create([
                'code' => 'CUST-' . Str::upper(Str::random(8)),
                'user_id' => $users->random(),
                'description' => 'Repair request for device ' . $index,
                'status' => 'Pending',
                'comment' => 'Awaiting technician review.',
                'admin_comment_updated_at' => Carbon::now(),
                'status_updated_at' => Carbon::now(),
            ]);
        }
    }
}
