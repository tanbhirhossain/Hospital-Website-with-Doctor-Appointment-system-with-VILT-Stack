<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_rests', function (Blueprint $table): void {
            $table->id();
            $table->foreign('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('reason')->nullable();
            $table->enum('type', ['leave', 'emergency', 'holiday', 'Hajj', 'Umrah', 'Abroad', 'Others']);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            
            $table->timestamps();

            $table->index(['doctor_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_rests');
    }
};