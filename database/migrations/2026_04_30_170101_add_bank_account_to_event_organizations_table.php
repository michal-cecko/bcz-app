<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_organizations', function (Blueprint $table): void {
            $table->string('bank_account_iban')->nullable()->after('payment_note');
            $table->string('bank_account_name')->nullable()->after('bank_account_iban');
        });
    }

    public function down(): void
    {
        Schema::table('event_organizations', function (Blueprint $table): void {
            $table->dropColumn(['bank_account_iban', 'bank_account_name']);
        });
    }
};
