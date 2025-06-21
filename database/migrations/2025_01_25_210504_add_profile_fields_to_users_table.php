<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
            $table->string('phone_number')->after('email')->nullable();
            $table->text('address')->after('phone_number')->nullable();
            $table->date('birthday')->after('address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'phone_number', 'address', 'birthday']);
        });
    }
};
