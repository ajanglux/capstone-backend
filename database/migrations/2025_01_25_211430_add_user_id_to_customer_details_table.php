<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_details', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->dropColumn(['first_name', 'last_name', 'phone_number', 'address', 'email']);
        });
    }

    public function down(): void
    {
        Schema::table('customer_details', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
        });
    }
};
