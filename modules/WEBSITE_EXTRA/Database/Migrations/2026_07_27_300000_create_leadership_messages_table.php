<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leadership_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();            // Chairman, MD, CEO
            $table->string('role_line')->nullable();       // Full role description
            $table->string('eyebrow')->nullable();         // "Message from the Chairman"
            $table->string('title')->nullable();           // Main heading
            $table->string('quote')->nullable();           // Short quote/summary
            $table->text('credentials')->nullable();       // JSON array of credentials
            $table->string('photo_path')->nullable();
            $table->longText('message')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leadership_messages');
    }
};
