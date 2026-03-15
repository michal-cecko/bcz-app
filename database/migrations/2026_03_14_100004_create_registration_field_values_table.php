<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_field_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_registration_id')->constrained()->cascadeOnDelete();
            $table->string('field_key');
            $table->string('field_type');
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_field_values');
    }
};
