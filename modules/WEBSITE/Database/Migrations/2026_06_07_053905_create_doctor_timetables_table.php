<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
         Schema::create('doctor_timetables', function (Blueprint $table) {

                $table->id();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();

                // Day of week (0 = Sunday, 6 = Saturday)
                $table->tinyInteger('day_of_week');
                $table->time('start_time');
                $table->time('end_time');
                $table->string('location')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('max_patient')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->foreignId('updated_by')->constrained('users');
                $table->timestamps();
                $table->index(['doctor_id', 'day_of_week']);
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('doctor_timetables');
        }
    };