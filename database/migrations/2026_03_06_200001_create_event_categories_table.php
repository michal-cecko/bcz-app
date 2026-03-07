<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('title');
            $table->string('slug')->unique();
            $table->string('color', 7)->nullable();
            $table->json('card_subtitle')->nullable();
            $table->json('card_description')->nullable();
            $table->string('card_image')->nullable();
            $table->string('detail_image')->nullable();
            $table->json('detail_title')->nullable();
            $table->string('hero_image')->nullable();
            $table->json('about_title')->nullable();
            $table->json('about_description')->nullable();
            $table->string('about_image')->nullable();
            $table->json('types_section_title')->nullable();
            $table->json('types_section_subtitle')->nullable();
            $table->json('types_cards')->nullable();
            $table->json('stats')->nullable();
            $table->json('cta_title')->nullable();
            $table->json('cta_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_categories');
    }
};
