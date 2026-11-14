<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('org_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->boolean('has_f_skatt')->default(true);

            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();

            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country', 2)->default('SE');

            $table->string('logo_path')->nullable();
            $table->string('currency', 3)->default('SEK');
            $table->string('locale', 5)->default('en');

            // Never assume a single VAT rate applies to every line (see PROJECT.md §10).
            $table->decimal('default_vat_rate', 5, 2)->default(25.00);
            $table->unsignedSmallInteger('payment_terms_days')->default(30);

            $table->string('quotation_prefix')->default('Q');
            $table->unsignedInteger('quotation_next_number')->default(1);
            $table->unsignedSmallInteger('quotation_valid_days')->default(30);
            $table->string('invoice_prefix')->default('F');
            $table->unsignedInteger('invoice_next_number')->default(1);

            $table->string('bankgiro')->nullable();
            $table->string('iban')->nullable();
            $table->string('bic')->nullable();
            $table->string('bank_name')->nullable();
            $table->text('invoice_footer')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
