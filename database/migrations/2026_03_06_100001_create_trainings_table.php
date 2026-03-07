<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sport_category_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->json('title');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->string('age_group')->nullable();
            $table->string('gender')->nullable();
            $table->string('frequency')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->time('start_time')->nullable();
            $table->json('schedule_days')->nullable();
            $table->json('place_name')->nullable();
            $table->string('place_address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('gathering_place')->nullable();
            $table->unsignedSmallInteger('max_capacity')->nullable();
            $table->boolean('notify_on_available')->default(false);
            $table->string('pricing_type')->default('free');
            $table->decimal('price_amount', 8, 2)->nullable();
            $table->json('registration_form_schema')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
