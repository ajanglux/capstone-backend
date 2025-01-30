<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('customer_details', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('description')->comment('Additional customer comments or notes');
            $table->timestamp('admin_comment_updated_at')->nullable()->after('comment');
        });
    }

    public function down()
    {
        Schema::table('customer_details', function (Blueprint $table) {
            $table->dropColumn(['email','comment', 'admin_comment_updated_at' ]);
        });
    }
};

