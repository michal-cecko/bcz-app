<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('training_id')->constrained()->cascadeOnDelete();
            $table->string('day');
            $table->time('start_time')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Migrate existing data from schedule_days JSON + start_time
        $trainings = DB::table('trainings')
            ->whereNotNull('schedule_days')
            ->where('is_recurring', true)
            ->get(['id', 'schedule_days', 'start_time']);

        foreach ($trainings as $training) {
            $days = json_decode($training->schedule_days, true) ?: [];

            foreach ($days as $order => $day) {
                // Handle both old format (plain string) and any existing object format
                $dayName = is_array($day) ? ($day['day'] ?? $day) : $day;
                $time = is_array($day) ? ($day['start_time'] ?? $training->start_time) : $training->start_time;

                DB::table('training_schedules')->insert([
                    'training_id' => $training->id,
                    'day' => $dayName,
                    'start_time' => $time,
                    'sort_order' => $order,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn('schedule_days');
        });
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->json('schedule_days')->nullable();
        });

        // Migrate data back
        $schedules = DB::table('training_schedules')
            ->select('training_id', 'day', 'start_time')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('training_id');

        foreach ($schedules as $trainingId => $entries) {
            DB::table('trainings')
                ->where('id', $trainingId)
                ->update([
                    'schedule_days' => json_encode($entries->pluck('day')->values()->toArray()),
                ]);
        }

        Schema::dropIfExists('training_schedules');
    }
};
