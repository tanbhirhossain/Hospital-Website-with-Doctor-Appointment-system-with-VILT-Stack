<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_menus', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();      // Laravel named route
            $table->string('icon')->nullable();
            $table->string('target')->default('_self');    // _self, _blank
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('location')->default('header'); // header, footer
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('navigation_menus')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_menus');
    }
};
