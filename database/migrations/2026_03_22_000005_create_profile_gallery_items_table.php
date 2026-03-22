<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_gallery_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('profile_type');
            $table->json('description')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'profile_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_gallery_items');
    }
};
