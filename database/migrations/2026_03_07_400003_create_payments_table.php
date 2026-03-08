<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->uuidMorphs('payable');
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('EUR');
            $table->string('status')->default('pending');
            $table->string('payment_method')->default('manual');
            $table->string('stripe_payment_id')->nullable();
            $table->string('stripe_checkout_session_id')->nullable();
            $table->string('stripe_transfer_id')->nullable();
            $table->string('variable_symbol')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('stripe_payment_id');
            $table->index('variable_symbol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
