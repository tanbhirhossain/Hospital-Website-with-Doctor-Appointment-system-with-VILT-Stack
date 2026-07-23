# Blog Module

Department-based Laravel 12 + Inertia/Vue blog module using repository interfaces and a service layer.

## Features

- Blog CRUD based on the supplied `blogs` schema
- Department relationship
- Automatic unique slugs with optional manual slug
- SEO title, description, canonical URL, Open Graph image and robots control
- Local Open Graph image upload or external image URL
- `created_by` and `updated_by` audit tracking
- Soft delete, trash view, restore and permanent deletion
- Search, department, indexability and trash filters
- Permission-protected responsive admin UI
- Public article listing and SEO-rendered detail page
- Public paginated JSON API
- Repository/service separation and transactional writes

## Installation

```bash
php artisan migrate
php artisan storage:link
php artisan db:seed --class="Modules\\BLOG\\Database\\Seeders\\BlogPermissionSeeder"
```

The module provider is discovered automatically by the project's `ModuleServiceProvider`.

## Routes

| Method | URL | Purpose |
|---|---|---|
| GET | `/blogs` | Authenticated administration |
| POST | `/blogs` | Create blog |
| POST/PUT | `/blogs/{blog}` | Update blog; UI uses `_method=put` for file upload |
| DELETE | `/blogs/{blog}` | Soft delete |
| PATCH | `/blogs/{blog}/restore` | Restore |
| DELETE | `/blogs/{blog}/force` | Permanently delete |
| GET | `/blog` | Public listing |
| GET | `/blog/{slug}` | Public article with SEO metadata |
| GET | `/api/blogs` | Public paginated API |
| GET | `/api/blogs/{slug}` | Public article API |

API/list filters: `search`, `department`, and `per_page` (API only, maximum 50).

## Permissions

- `blog.view`
- `blog.create`
- `blog.edit`
- `blog.delete`
- `blog.restore`

The included seeder grants these permissions to `Super Admin`.

## Content safety

The public article page renders `descriptions` as HTML. Only grant blog create/edit permissions to trusted staff. If untrusted users can author content, add an HTML sanitizer such as HTML Purifier in `BlogService` before persistence.
