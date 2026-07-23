<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletters', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('source')->nullable()->index();
            $table->boolean('isActive')->default(true)->index();
            $table->string('status')->default('subscribed')->index();
            $table->string('audience_type')->nullable()->index();
            $table->json('tags')->nullable();
            $table->string('country')->nullable()->index();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('unsubscribe_token', 64)->nullable()->unique();
            $table->timestamps();

            $table->index(['isActive', 'status', 'audience_type']);
            $table->index(['source', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletters');
    }
};
