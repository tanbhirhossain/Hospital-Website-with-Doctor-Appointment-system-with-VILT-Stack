<?php

namespace Modules\EMAILMARKETING\Database\Seeders;

use Illuminate\Database\Seeder;

class NewsletterSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(EmailMarketingSeeder::class);
    }
}
