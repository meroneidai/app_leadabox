<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('sent_at')->nullable()->after('issued_at');
            $table->string('sent_to')->nullable()->after('sent_at');
            $table->timestamp('viewed_at')->nullable()->after('sent_to');
            // Drives the reminder ladder without sending the same nudge twice a day.
            $table->timestamp('last_reminder_at')->nullable()->after('viewed_at');
            $table->unsignedSmallInteger('reminder_count')->default(0)->after('last_reminder_at');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['sent_at', 'sent_to', 'viewed_at', 'last_reminder_at', 'reminder_count']);
        });
    }
};
