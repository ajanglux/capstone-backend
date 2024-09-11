<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_details', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Make 'code' unique
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('phone_number', 20);
            $table->string('email')->nullable();
            $table->string('address', 255);
            $table->timestamps();
        });

        Schema::create('product_infos', function (Blueprint $table) {
            $table->id();
            $table->string('brand', 255);
            $table->string('model', 255);
            $table->string('serial_number', 20);
            $table->date('purchase_date');
            $table->string('status')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_details');
        Schema::dropIfExists('product_infos');
    }
};
