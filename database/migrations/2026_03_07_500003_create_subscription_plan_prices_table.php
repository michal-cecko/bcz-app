<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plan_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('subscription_plan_id')->constrained()->cascadeOnDelete();
            $table->char('currency_code', 3);
            $table->decimal('price_monthly', 10, 2);
            $table->decimal('price_yearly', 10, 2);
            $table->string('stripe_monthly_price_id')->nullable();
            $table->string('stripe_yearly_price_id')->nullable();
            $table->timestamps();

            $table->unique(['subscription_plan_id', 'currency_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_prices');
    }
};
