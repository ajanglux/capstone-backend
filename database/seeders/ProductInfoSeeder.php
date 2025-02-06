<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductInfo;
use App\Models\CustomerDetail;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ProductInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerDetails = CustomerDetail::pluck('id'); // Get all customer detail IDs

        foreach (range(1, 10) as $index) {
            ProductInfo::create([
                'customer_detail_id' => $customerDetails->random(),
                'brand' => 'Brand ' . $index,
                'model' => 'Model ' . $index,
                'serial_number' => 'SN-' . strtoupper(uniqid()),
                'purchase_date' => Carbon::now()->subMonths(rand(1, 24)),
                'documentation' => 'Warranty Card Available',
                'warranty_status' => 'Warranty',
                'ac_adapter' => rand(0, 1),
                'vga_cable' => rand(0, 1),
                'dvi_cable' => rand(0, 1),
                'display_cable' => rand(0, 1),
                'bag_pn' => 'BAG-' . strtoupper(Str::random(5)),
                'hdd' => rand(128, 1024) . ' GB',
                'ram_brand' => 'RAMBrand ' . rand(1, 5),
                'ram_size_gb' => rand(4, 32),
                'power_cord_qty' => rand(1, 2),
                'description_of_repair' => 'Device repair for issue ' . $index,
                'address' => 'Address ' . $index,
            ]);
        }
    }
}