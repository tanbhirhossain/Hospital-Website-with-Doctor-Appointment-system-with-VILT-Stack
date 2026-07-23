<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table): void {

            $table->id();

            $table->foreignId('department_id')->constrained();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('specialty');
            $table->text('qualifications');

            $table->string('designation');
            $table->string('institute');

            $table->longText('biography')->nullable();

            $table->string('chamber_location')->nullable();

            $table->decimal('visit_fee', 10, 2)->nullable();
            $table->decimal('report_fee', 10, 2)->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('mis_code')->nullable();

            $table->text('skills')->nullable();
            $table->text('awards')->nullable();

            $table->integer('serial')->default(0);

            $table->boolean('is_active')->default(true);
            $table->boolean('is_home_page')->default(false);
            $table->boolean('is_featured')->default(false);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->boolean('indexable')->default(true);

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};