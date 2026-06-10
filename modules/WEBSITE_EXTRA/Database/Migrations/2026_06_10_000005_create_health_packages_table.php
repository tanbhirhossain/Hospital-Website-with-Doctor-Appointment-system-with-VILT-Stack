<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('health_packages', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('badge')->nullable();
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('includes')->nullable();
            $table->decimal('original_price', 10, 2);
            $table->decimal('discounted_price', 10, 2)->nullable();
            $table->integer('duration_days')->nullable();
            $table->enum('category', ['general','cardiac','diabetes','cancer','maternity','pediatric','executive','other'])->default('general');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('indexable')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('health_packages'); }
};
