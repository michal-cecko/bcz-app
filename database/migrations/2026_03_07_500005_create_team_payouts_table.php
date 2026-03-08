<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_payouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->decimal('gross_amount', 10, 2);
            $table->decimal('fee_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2);
            $table->char('currency', 3)->default('EUR');
            $table->string('status')->default('pending');
            $table->string('bank_account_iban')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_payouts');
    }
};
