<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_team', function (Blueprint $table): void {
            $table->foreignUuid('member_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->date('joined_at')->nullable();
            $table->boolean('continuous_membership')->default(false);
            $table->timestamps();
            $table->unique(['member_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_team');
    }
};
