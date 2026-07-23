<?php

namespace Modules\EMAILMARKETING\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\EMAILMARKETING\Models\EmailTemplate;

class EmailMarketingSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->value('id');

        foreach ($this->templates() as $template) {
            EmailTemplate::query()->updateOrCreate(
                ['slug' => $template['slug']],
                array_merge($template, [
                    'created_by' => $userId,
                    'updated_by' => null,
                ]),
            );
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function templates(): array
    {
        return [
            [
                'name' => 'Blog Post Notification',
                'slug' => 'blog-post-notification',
                'category' => 'blog_notification',
                'subject' => 'New blog post: {{ campaign_name }}',
                'preheader' => 'Read the latest hospital article and health guidance.',
                'status' => 'active',
                'builder_json' => [
                    ['type' => 'hero', 'data' => ['eyebrow' => 'New blog post', 'title' => '{{ campaign_name }}', 'subtitle' => 'We published a new article with helpful health information for you and your family.', 'button_label' => 'Read the article', 'button_url' => '{{ app_url }}/blog']],
                    ['type' => 'text', 'data' => ['content' => 'Hello {{ first_name }},\n\nStay informed with trusted healthcare updates from our doctors and hospital team.']],
                    ['type' => 'cta', 'data' => ['title' => 'Need help choosing a doctor?', 'text' => 'Our team can guide you to the right department and appointment slot.', 'button_label' => 'Book appointment', 'button_url' => '{{ app_url }}/appointment']],
                ],
            ],
            [
                'name' => 'Weekly Health Tips',
                'slug' => 'weekly-health-tips',
                'category' => 'health_tip',
                'subject' => 'Health tips for a better week',
                'preheader' => 'Simple steps to protect your health this week.',
                'status' => 'active',
                'builder_json' => [
                    ['type' => 'hero', 'data' => ['eyebrow' => 'Health tips', 'title' => 'Small habits, big health benefits', 'subtitle' => 'Practical guidance from our care team to help you stay well.', 'button_label' => 'Explore services', 'button_url' => '{{ app_url }}']],
                    ['type' => 'tips', 'data' => ['title' => 'This week’s top tips', 'items' => ['Drink enough water throughout the day.', 'Walk at least 20–30 minutes if your doctor allows it.', 'Do not ignore chest pain, breathing difficulty, or sudden weakness.', 'Keep routine checkups on your calendar.']]],
                ],
            ],
            [
                'name' => 'Awareness Tips Campaign',
                'slug' => 'awareness-tips-campaign',
                'category' => 'awareness_tip',
                'subject' => 'Awareness message from {{ hospital_name }}',
                'preheader' => 'Important prevention and early detection guidance.',
                'status' => 'active',
                'builder_json' => [
                    ['type' => 'hero', 'data' => ['eyebrow' => 'Awareness', 'title' => 'Prevention starts with awareness', 'subtitle' => 'Learn symptoms, risk factors, and when to seek medical care.', 'button_label' => 'Learn more', 'button_url' => '{{ app_url }}']],
                    ['type' => 'text', 'data' => ['content' => 'Dear {{ first_name }},\n\nEarly action can save lives. Share this message with your family and contact a qualified physician if symptoms appear.']],
                    ['type' => 'tips', 'data' => ['title' => 'Remember', 'items' => ['Do regular screening based on age and risk.', 'Follow medical advice, not rumors.', 'Call emergency support for severe symptoms.']]],
                ],
            ],
            [
                'name' => 'Tips and Tricks Email',
                'slug' => 'tips-and-tricks-email',
                'category' => 'tips_tricks',
                'subject' => 'Smart health tips & tricks',
                'preheader' => 'Easy healthcare actions for daily life.',
                'status' => 'active',
                'builder_json' => [
                    ['type' => 'hero', 'data' => ['eyebrow' => 'Tips & tricks', 'title' => 'Make healthcare easier', 'subtitle' => 'Use these simple ideas to prepare for appointments and manage care better.', 'button_label' => 'Visit hospital website', 'button_url' => '{{ app_url }}']],
                    ['type' => 'tips', 'data' => ['title' => 'Before your appointment', 'items' => ['Bring previous reports and prescriptions.', 'Write down symptoms and questions.', 'Arrive early for registration.', 'Keep your emergency contact updated.']]],
                ],
            ],
        ];
    }
}
