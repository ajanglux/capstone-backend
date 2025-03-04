<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('customer_details', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('user_id');
            $table->string('code')->unique(); // unique 'code'
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('phone_number', 20);
            $table->string('email')->nullable();
            $table->string('address', 255);
            $table->longText('description')->nullable();
            $table->longText('cancel_reason')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamp('cancel_reason_updated_at')->nullable();
            $table->timestamp('description_updated_at')->nullable();
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamp('on_going_updated_at')->nullable();
            $table->timestamp('finished_updated_at')->nullable();
            $table->timestamp('ready_for_pickup_updated_at')->nullable();
            $table->timestamp('completed_updated_at')->nullable();
            $table->timestamp('cancelled_updated_at')->nullable();
            $table->timestamp('unrepairable_updated_at')->nullable();
            $table->timestamp('responded_updated_at')->nullable();

            $table->timestamps(); 

            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('product_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_detail_id');
            $table->string('brand', 255);
            $table->string('model', 255);
            $table->string('serial_number', 20);
            $table->date('purchase_date');
            $table->longText('documentation')->nullable();
            $table->string('warranty_status')->default('warranty');

            $table->string('ac_adapter')->nullable();
            $table->string('vga_cable')->nullable();
            $table->string('dvi_cable')->nullable();
            $table->string('display_cable')->nullable();
            $table->string('bag_pn')->nullable();

            $table->string('hdd')->nullable();
            $table->string('ram_brand')->nullable();
            $table->string('ram_size_gb')->nullable();
            $table->string('power_cord_qty')->nullable();

            $table->timestamps();

            $table->foreign('customer_detail_id')
                  ->references('id')->on('customer_details')
                  ->onDelete('cascade');

            $table->index('customer_detail_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_infos');
        Schema::dropIfExists('customer_details');
    }
};
