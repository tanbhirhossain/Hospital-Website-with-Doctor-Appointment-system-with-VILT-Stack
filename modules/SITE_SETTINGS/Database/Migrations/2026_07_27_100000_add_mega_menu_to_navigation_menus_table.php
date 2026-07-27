<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('navigation_menus', function (Blueprint $table) {
            $table->string('menu_type')->default('link')->after('location');  // link, dropdown, mega_menu
            $table->json('config')->nullable()->after('menu_type');            // mega menu config (banner, columns, etc.)
            $table->string('badge_text')->nullable()->after('config');         // optional badge like "New", "Hot"
            $table->string('badge_color')->nullable()->after('badge_text');    // badge color class
            $table->string('description')->nullable()->after('badge_color');   // submenu item description
        });
    }

    public function down(): void
    {
        Schema::table('navigation_menus', function (Blueprint $table) {
            $table->dropColumn(['menu_type', 'config', 'badge_text', 'badge_color', 'description']);
        });
    }
};
