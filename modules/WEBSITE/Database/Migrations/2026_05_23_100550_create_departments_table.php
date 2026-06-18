<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('tagline')->nullable();
            $table->text('shortDesc')->nullable();
            $table->longText('descriptions')->nullable();
            $table->integer('serial')->nullable();
            $table->boolean('is_active')->default(true);

            //styling
            $table->string('text_icon')->nullable();
            $table->string('bg-color')->nullable();
            $table->string('text-color')->nullable();
            $table->string('color')->nullable();
            $table->longText('custom_css')->nullable();

                        // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->boolean('indexable')->default(true);
                    
            
            $table->foreignId('parent_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};