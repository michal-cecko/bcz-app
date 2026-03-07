<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_category_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->json('title');
            $table->string('slug')->unique();
            $table->json('card_description')->nullable();
            $table->string('card_image')->nullable();
            $table->date('date')->nullable();
            $table->date('date_end')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('detail_image')->nullable();
            $table->json('content')->nullable();
            $table->unsignedInteger('attendee_count')->nullable();
            $table->string('client')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
