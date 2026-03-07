<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('athlete_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('registration_fee_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending');
            $table->dateTime('registered_at')->nullable();
            $table->json('form_data')->nullable();
            $table->decimal('weight_in', 6, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_registrations');
    }
};
