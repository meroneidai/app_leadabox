<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('number')->nullable();

            $table->string('name');
            $table->string('type')->default('company');
            $table->string('status')->default('prospect');

            $table->string('org_number')->nullable();
            $table->string('vat_number')->nullable();

            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();

            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country', 2)->default('SE');

            // Null means "inherit the company default" rather than a hardcoded value.
            $table->unsignedSmallInteger('payment_terms_days')->nullable();
            $table->decimal('vat_rate', 5, 2)->nullable();
            $table->boolean('reverse_charge')->default(false);

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->unique(['company_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
