<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('newsletters')) {
            return;
        }

        Schema::table('newsletters', function (Blueprint $table): void {
            if (! Schema::hasColumn('newsletters', 'name')) {
                $table->string('name')->nullable()->after('id');
            }

            if (! Schema::hasColumn('newsletters', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (! Schema::hasColumn('newsletters', 'status')) {
                $table->string('status')->default('subscribed')->index()->after('isActive');
            }

            if (! Schema::hasColumn('newsletters', 'tags')) {
                $table->json('tags')->nullable()->after('audience_type');
            }

            if (! Schema::hasColumn('newsletters', 'subscribed_at')) {
                $table->timestamp('subscribed_at')->nullable()->after('country');
            }

            if (! Schema::hasColumn('newsletters', 'unsubscribed_at')) {
                $table->timestamp('unsubscribed_at')->nullable()->after('subscribed_at');
            }

            if (! Schema::hasColumn('newsletters', 'unsubscribe_token')) {
                $table->string('unsubscribe_token', 64)->nullable()->unique()->after('unsubscribed_at');
            }
        });

        DB::table('newsletters')
            ->whereNull('status')
            ->orWhere('status', '')
            ->update(['status' => 'subscribed']);

        DB::table('newsletters')
            ->whereNull('subscribed_at')
            ->update(['subscribed_at' => now()]);

        DB::table('newsletters')
            ->whereNull('unsubscribe_token')
            ->orderBy('id')
            ->select(['id'])
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('newsletters')
                        ->where('id', $row->id)
                        ->update(['unsubscribe_token' => hash('sha256', Str::uuid()->toString().$row->id)]);
                }
            });
    }

    public function down(): void
    {
        // Keep subscriber data safe on rollback. The base migration drops the table when needed.
    }
};
