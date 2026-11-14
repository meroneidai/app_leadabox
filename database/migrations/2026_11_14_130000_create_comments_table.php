<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->morphs('commentable');

            // Exactly one of these is set, depending on who wrote it.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->string('author_name')->nullable();

            $table->text('body');
            // Internal notes are never rendered on customer-facing pages.
            $table->boolean('is_internal')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->index(['commentable_type', 'commentable_id', 'is_internal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
