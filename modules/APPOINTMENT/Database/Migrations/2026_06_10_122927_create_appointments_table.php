<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->string('patient_name');
            $table->string('patient_email')->nullable();
            $table->string('patient_phone');
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('slot_duration')->comment('Duration in minutes, denormalized for record');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();

            //SERIALIZATION FIELDS
            $table->unsignedInteger('serial_number')->nullable()->after('id');
            $table->enum('serial_mode', ['auto', 'manual'])->nullable()->after('serial_number');
            $table->timestamp('confirmed_at')->nullable()->after('serial_mode');



            $table->timestamps();

            $table->index(['doctor_id', 'appointment_date', 'start_time'], 'appointments_doctor_date_start_idx');
            $table->index(['doctor_id', 'appointment_date', 'status'], 'appointments_doctor_date_status_idx');
            $table->index('appointment_date');
            $table->unique(
                    ['doctor_id', 'appointment_date', 'serial_number'],
                    'appointments_doctor_date_serial_unique'
                );        
                
                $table->unique(
                ['doctor_id', 'appointment_date', 'start_time', 'end_time'],
                'appointments_doctor_date_time_unique'
            );
            
            });

            
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};