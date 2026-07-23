# Category-wise Gallery Module

A Laravel 12 + Inertia/Vue gallery module built around SOLID layers and `spatie/laravel-medialibrary`.

## Included

- Category CRUD (active/hidden state and manual ordering)
- Image CRUD with one Spatie Media Library image per gallery record
- JPG, PNG, WebP and GIF validation (10 MB maximum)
- `thumb` (480×360) and `preview` (up to 1600×1200) conversions
- Draft/published, scheduled publishing, featured images and ordering
- Search, category/status filtering and pagination
- Permission-protected administration at `/gallery`
- Public category gallery with keyboard-accessible lightbox at `/photo-gallery`
- Public JSON API at `/api/gallery`
- Repository interfaces, repository implementations and service layer
- Database transactions and restrictive category deletion

## Installation

The parent project already contains Media Library. If installing this module elsewhere:

```bash
composer require spatie/laravel-medialibrary
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
```

Run the migrations, expose the public disk, then add gallery permissions:

```bash
php artisan migrate
php artisan storage:link
php artisan db:seed --class="Modules\\GALLERY\\Database\\Seeders\\GalleryPermissionSeeder"
```

For queued conversions, run a queue worker. The current project defaults to the configured `QUEUE_CONNECTION`; use `sync` locally if no worker is available.

```bash
php artisan queue:work
```

## Routes

| Method | URL | Purpose |
|---|---|---|
| GET | `/gallery` | Authenticated admin interface |
| POST | `/gallery-items` | Upload an image |
| POST/PUT | `/gallery-items/{gallery_item}` | Update an image (the UI uses POST with `_method=put` for file uploads) |
| DELETE | `/gallery-items/{gallery_item}` | Delete image and associated media |
| POST/PUT/DELETE | `/gallery-categories/...` | Category management |
| GET | `/photo-gallery?category=events` | Public gallery |
| GET | `/api/gallery` | Published gallery JSON |
| GET | `/api/gallery/category/{slug}` | Published category JSON |

API query parameters: `search`, `category`, `featured=1`, and `per_page` (maximum 48).

## Permissions

- `gallery.view`
- `gallery.create`
- `gallery.edit`
- `gallery.delete`

The included seeder grants all four permissions to the `Super Admin` role.

## Design

Controllers only handle HTTP concerns. Services coordinate business rules and transactions. Repositories own persistence and querying through interfaces bound by `GALLERYServiceProvider`. Eloquent models own relationships/scopes and the Spatie media collection/conversions. Form Requests own authorization and validation.
