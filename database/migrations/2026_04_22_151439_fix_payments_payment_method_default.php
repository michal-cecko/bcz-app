<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The Stripe→GoPay rename removed 'manual' from PaymentMethodEnum, but
        // payments.payment_method still has 'manual' as its DB default. Any row
        // inserted without an explicit value then fails on cast read.
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE payments ALTER COLUMN payment_method SET DEFAULT 'bank_transfer'");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE payments ALTER COLUMN payment_method SET DEFAULT 'manual'");
        }
    }
};
