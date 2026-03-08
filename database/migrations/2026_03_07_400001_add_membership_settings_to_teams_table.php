<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->boolean('membership_enabled')->default(false);
            $table->decimal('membership_fee_amount', 10, 2)->nullable();
            $table->char('membership_fee_currency', 3)->default('EUR');
            $table->string('membership_period')->nullable();
            $table->text('membership_description')->nullable();
            $table->string('bank_account_iban')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('stripe_connect_account_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn([
                'membership_enabled',
                'membership_fee_amount',
                'membership_fee_currency',
                'membership_period',
                'membership_description',
                'bank_account_iban',
                'bank_account_name',
                'stripe_connect_account_id',
            ]);
        });
    }
};
