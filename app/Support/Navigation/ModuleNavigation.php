<?php

namespace App\Support\Navigation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ModuleNavigation
{
    /** @return array<string, mixed> */
    public function build(Request $request): array
    {
        if (! $request->user()) {
            return [
                'groups' => [],
                'moduleBoundaries' => $this->moduleBoundaries(),
            ];
        }

        $groups = collect($this->curatedGroups())
            ->map(function (array $group) use ($request): array {
                $items = collect($group['items'] ?? [])
                    ->map(fn (array $item): ?array => $this->resolveItem($item, $request))
                    ->filter()
                    ->values()
                    ->all();

                return array_merge($group, ['items' => $items]);
            })
            ->filter(fn (array $group): bool => count($group['items'] ?? []) > 0)
            ->values()
            ->all();

        return [
            'groups' => $groups,
            'moduleBoundaries' => $this->moduleBoundaries(),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function curatedGroups(): array
    {
        return [
            [
                'id' => 'workspace',
                'title' => 'Workspace',
                'icon' => 'LayoutDashboard',
                'description' => 'Daily operations and command center',
                'items' => [
                    ['title' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'LayoutDashboard', 'exact' => true],
                    ['title' => 'Appointments', 'route' => 'appointments.index', 'icon' => 'CalendarDays'],
                    ['title' => 'AI Chat Agent', 'route' => 'healthai.chat', 'icon' => 'BrainCircuit'],
                ],
            ],
            [
                'id' => 'access',
                'title' => 'Access Control',
                'icon' => 'ShieldCheck',
                'description' => 'Roles, permissions, and team access',
                'items' => [
                    ['title' => 'Roles', 'route' => 'roles.index', 'icon' => 'ShieldCheck', 'permission' => 'role.view'],
                    ['title' => 'Users', 'route' => 'users.index', 'icon' => 'Users', 'permission' => 'user.view'],
                ],
            ],
            [
                'id' => 'website',
                'title' => 'Hospital Website',
                'icon' => 'Hospital',
                'description' => 'Departments, doctors, services, and homepage content',
                'items' => [
                    ['title' => 'Departments', 'route' => 'admin.departments.index', 'icon' => 'Building2'],
                    ['title' => 'Doctors', 'route' => 'admin.doctors.index', 'icon' => 'Stethoscope'],
                    ['title' => 'Centers of Excellence', 'route' => 'admin.coe.index', 'icon' => 'BadgePlus'],
                    ['title' => 'Hero Sliders', 'route' => 'admin.hero-sliders.index', 'icon' => 'GalleryHorizontalEnd'],
                    ['title' => 'Services', 'route' => 'admin.services.index', 'icon' => 'HeartPulse'],
                    ['title' => 'Health Packages', 'route' => 'admin.health-packages.index', 'icon' => 'PackageCheck'],
                ],
            ],
            [
                'id' => 'content',
                'title' => 'Content Studio',
                'icon' => 'Newspaper',
                'description' => 'Marketing content, blogs, galleries, and reviews',
                'items' => [
                    ['title' => 'Blogs', 'route' => 'blogs.index', 'icon' => 'Newspaper', 'permission' => 'blog.view'],
                    ['title' => 'Gallery', 'route' => 'gallery.index', 'icon' => 'Images', 'permission' => 'gallery.view'],
                    ['title' => 'Client Reviews', 'route' => 'admin.client-reviews.index', 'icon' => 'MessageSquareHeart'],
                    ['title' => 'Patient Reviews', 'route' => 'admin.patient-reviews.index', 'icon' => 'MessagesSquare'],
                ],
            ],
            [
                'id' => 'growth',
                'title' => 'Growth & Marketing',
                'icon' => 'Megaphone',
                'description' => 'Campaigns, subscriber lists, and email growth tools',
                'items' => [
                    ['title' => 'Email Marketing', 'route' => 'emailmarketing.index', 'icon' => 'Mail', 'permission' => 'emailmarketing.view'],
                ],
            ],
            [
                'id' => 'settings',
                'title' => 'Account Settings',
                'icon' => 'Settings',
                'description' => 'Profile, security, and preferences',
                'items' => [
                    ['title' => 'Profile', 'route' => 'profile.edit', 'icon' => 'UserRoundCog'],
                    ['title' => 'Password', 'route' => 'password.edit', 'icon' => 'KeyRound'],
                    ['title' => 'Appearance', 'route' => 'appearance', 'icon' => 'Palette'],
                ],
            ],
        ];
    }

    /** @return array<string, mixed>|null */
    private function resolveItem(array $item, Request $request): ?array
    {
        $routeName = (string) ($item['route'] ?? '');

        if ($routeName === '' || ! Route::has($routeName)) {
            return null;
        }

        $permission = $item['permission'] ?? null;
        if ($permission && $request->user()?->can($permission) === false) {
            return null;
        }

        return [
            'title' => $item['title'],
            'route' => $routeName,
            'href' => route($routeName, [], false),
            'icon' => $item['icon'] ?? 'Circle',
            'badge' => $item['badge'] ?? null,
            'exact' => (bool) ($item['exact'] ?? false),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function moduleBoundaries(): array
    {
        $modulesPath = base_path('modules');
        $modules = glob($modulesPath.'/*', GLOB_ONLYDIR) ?: [];

        return collect($modules)
            ->map(function (string $modulePath): array {
                $name = basename($modulePath);
                $routeFiles = glob($modulePath.'/Routes/*.php') ?: [];

                return [
                    'name' => $name,
                    'label' => Str::of($name)->replace('_', ' ')->lower()->headline()->toString(),
                    'path' => 'modules/'.$name,
                    'routeFiles' => collect($routeFiles)
                        ->map(fn (string $file): string => str_replace(base_path().'/', '', $file))
                        ->values()
                        ->all(),
                ];
            })
            ->sortBy('name')
            ->values()
            ->all();
    }
}
