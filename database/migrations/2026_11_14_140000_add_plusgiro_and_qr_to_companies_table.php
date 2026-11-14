<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('plusgiro')->nullable()->after('bankgiro');
            // Swedish banking apps can scan this straight off the invoice.
            $table->boolean('payment_qr_enabled')->default(true)->after('plusgiro');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['plusgiro', 'payment_qr_enabled']);
        });
    }
};
