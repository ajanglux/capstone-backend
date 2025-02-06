<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('product_infos', function (Blueprint $table) {
            $table->string('warranty_status')->nullable()->change();  // Allow NULL values
        });
    }

    public function down() {
        Schema::table('product_infos', function (Blueprint $table) {
            $table->string('warranty_status')->nullable(false)->change();  // Revert if needed
        });
    }
};

