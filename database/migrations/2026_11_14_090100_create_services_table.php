<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_category_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('unit')->default('item');

            // Money is always decimal; floating point is never used for currency.
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('cost', 12, 2)->nullable();

            // Null inherits the company default rather than assuming 25%.
            $table->decimal('vat_rate', 5, 2)->nullable();

            $table->string('billing_type')->default('one_time');
            $table->string('billing_interval')->nullable();
            $table->boolean('tracks_expiry')->default(false);

            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'billing_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
