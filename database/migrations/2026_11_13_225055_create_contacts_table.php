<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('job_title')->nullable();

            $table->boolean('is_primary')->default(false);

            // Which notifications this person should actually receive.
            $table->boolean('receives_quotations')->default(false);
            $table->boolean('receives_invoices')->default(false);
            $table->boolean('receives_project_updates')->default(false);
            $table->boolean('receives_renewals')->default(false);

            $table->timestamps();

            $table->index(['customer_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
