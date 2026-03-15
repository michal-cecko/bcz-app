<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('max_capacity')->nullable();
            $table->string('pricing_type')->default('free');
            $table->decimal('price_amount', 10, 2)->nullable();
            $table->string('price_currency', 3)->default('EUR');
            $table->json('registration_form_schema')->nullable();
            $table->dateTime('registration_opens_at')->nullable();
            $table->dateTime('registration_closes_at')->nullable();
            $table->boolean('is_public_registration')->default(true);
            $table->boolean('show_countdown')->default(false);
            $table->string('external_link')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_organizations');
    }
};
