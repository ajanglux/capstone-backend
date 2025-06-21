<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductInfo;
use App\Models\CustomerDetail;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ProductInfoSeeder extends Seeder
{
    public function run(): void
    {
        $customerDetails = CustomerDetail::pluck('id');

        foreach (range(1, 10) as $index) {
            ProductInfo::create([
                'customer_detail_id' => $customerDetails->random(),
                'brand' => 'Brand ' . $index,
                'model' => 'Model ' . $index,
                'serial_number' => 'SN-' . strtoupper(uniqid()),
                'purchase_date' => Carbon::now()->subMonths(rand(1, 24)),
                'documentation' => 'Warranty Card Available',
                'warranty_status' => 'Warranty',
                'ac_adapter' => null,
                'vga_cable' => null,
                'dvi_cable' => null,
                'display_cable' => null,
                'bag_pn' => null,
                'hdd' => null,
                'ram_brand' => null,
                'ram_size_gb' => null,
                'power_cord_qty' => null,
                'description_of_repair' => 'Device repair for issue ' . $index,
                'address' => 'Address ' . $index,
            ]);
        }
    }
}