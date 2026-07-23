<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->longText('content_section_1')->nullable();
            $table->longText('content_section_2')->nullable();
            $table->longText('content_section_3')->nullable();
            $table->longText('content_section_4')->nullable();
            $table->longText('content_section_5')->nullable();
            $table->json('faq')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn([
                'content_section_1',
                'content_section_2',
                'content_section_3',
                'content_section_4',
                'content_section_5',
                'faq'
            ]);
        });
    }
};
