<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up()
    {
        Schema::table('product_infos', function (Blueprint $table) {
            $table->text('description_of_repair')->nullable()->after('device_type');
        });
    }

    public function down()
    {
        Schema::table('product_infos', function (Blueprint $table) {
            $table->dropColumn('description_of_repair');
        });
    }
};
