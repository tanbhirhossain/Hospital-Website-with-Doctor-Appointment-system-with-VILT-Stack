# EMAILMARKETING Module

Full email marketing module for the hospital VILT stack.

## Features

- Subscriber/newsletter database with segments, sources, countries, tags, active/unsubscribed state.
- Public subscribe endpoint: `POST /newsletter/subscribe` and API endpoint `POST /api/newsletter/subscribe`.
- One-click unsubscribe endpoint with secure token.
- Email templates with categories:
  - Blog Post Notification
  - Health Tips Email
  - Awareness Tips Email
  - Tips & Tricks Email
  - Newsletter
  - Custom Email
- Drag-and-drop template builder with hero, text, image, button, tips list, CTA, divider, and footer blocks.
- Custom HTML template mode and plain-text fallback field.
- Campaign manager with audience filters, sender/reply-to fields, draft/scheduled/sending/sent/cancelled/failed statuses.
- Bulk campaign sending through Laravel queues.
- Test-send actions for templates and campaigns.
- Open tracking pixel and click tracking redirect.
- CSV subscriber import.
- Default seed templates for common hospital marketing emails.

## Admin UI

Open:

```text
/email-marketing
```

The sidebar now includes **Email Marketing**.

## Setup

After pulling the code, run:

```bash
composer install
npm install
php artisan migrate
php artisan db:seed --class=AclPermissionSeeder
php artisan db:seed --class=Modules\\EMAILMARKETING\\Database\\Seeders\\EmailMarketingSeeder
npm run build
```

Configure `.env` mail values, for example:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@your-domain.com
MAIL_FROM_NAME="AMZ Hospital"
QUEUE_CONNECTION=database
```

Run the queue worker for bulk emails:

```bash
php artisan queue:work
```

Scheduled campaigns are checked every minute by `routes/console.php` through:

```bash
php artisan schedule:work
```

or a production cron that runs Laravel schedule.
