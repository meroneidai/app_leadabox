<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('sent_to')->nullable()->after('sent_at');
            // Set the first time the customer opens the link, so "delivered" is observable.
            $table->timestamp('viewed_at')->nullable()->after('sent_to');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['sent_to', 'viewed_at']);
        });
    }
};
