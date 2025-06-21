<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{  
    public function up(): void
    {
        Schema::table('product_infos', function (Blueprint $table) {
            $table->string('device_type')->default('Laptop')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('product_infos', function (Blueprint $table) {
            $table->dropColumn('device_type');
        });
    }
};
