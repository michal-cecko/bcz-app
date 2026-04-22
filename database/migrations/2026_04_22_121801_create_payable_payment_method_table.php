<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payable_payment_method', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
            $table->uuidMorphs('payable');
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->json('instructions')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['payment_method_id', 'payable_type', 'payable_id'], 'payable_payment_method_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payable_payment_method');
    }
};
