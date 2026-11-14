<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Lets the customer follow progress without an account.
            $table->string('public_token', 64)->nullable()->unique()->after('status');
            $table->boolean('customer_can_view')->default(true)->after('public_token');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['public_token', 'customer_can_view']);
        });
    }
};
