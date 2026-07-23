<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('patient_reviews', function (Blueprint $table): void {
            $table->id();
            $table->string('patient_name');
            $table->string('patient_phone')->nullable();
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            $table->string('department')->nullable();
            $table->text('review_text');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->text('admin_note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('patient_reviews'); }
};
