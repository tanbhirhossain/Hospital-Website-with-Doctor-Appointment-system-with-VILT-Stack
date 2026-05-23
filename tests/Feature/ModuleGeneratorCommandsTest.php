<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::deleteDirectory(base_path('modules/Blog'));
    File::deleteDirectory(base_path('modules/Missing'));
});

afterEach(function () {
    File::deleteDirectory(base_path('modules/Blog'));
    File::deleteDirectory(base_path('modules/Missing'));
});

test('make module creates the standard module structure', function () {
    $this->artisan('make:module Blog')
        ->assertSuccessful();

    expect(File::isDirectory(base_path('modules/Blog/Http/Controllers')))->toBeTrue()
        ->and(File::isDirectory(base_path('modules/Blog/Models')))->toBeTrue()
        ->and(File::isDirectory(base_path('modules/Blog/Repositories')))->toBeTrue()
        ->and(File::exists(base_path('modules/Blog/Providers/BlogServiceProvider.php')))->toBeTrue()
        ->and(File::exists(base_path('modules/Blog/Routes/web.php')))->toBeTrue()
        ->and(File::exists(base_path('modules/Blog/Routes/api.php')))->toBeTrue()
        ->and(File::exists(base_path('modules/Blog/Resources/js/Pages/Index.vue')))->toBeTrue();
});

test('module generators create common module classes', function () {
    $this->artisan('make:module Blog')->assertSuccessful();

    $this->artisan('module:make-controller Blog Post --resource')->assertSuccessful();
    $this->artisan('module:make-model Blog Post --all')->assertSuccessful();
    $this->artisan('module:make-request Blog StorePost')->assertSuccessful();
    $this->artisan('module:make-service Blog Post')->assertSuccessful();
    $this->artisan('module:make-repository Blog Post')->assertSuccessful();
    $this->artisan('module:make-page Blog Posts/Index')->assertSuccessful();

    expect(File::exists(base_path('modules/Blog/Http/Controllers/PostController.php')))->toBeTrue()
        ->and(File::exists(base_path('modules/Blog/Models/Post.php')))->toBeTrue()
        ->and(File::glob(base_path('modules/Blog/Database/Migrations/*_create_posts_table.php')))->not->toBeEmpty()
        ->and(File::exists(base_path('modules/Blog/Database/Factories/PostFactory.php')))->toBeTrue()
        ->and(File::exists(base_path('modules/Blog/Database/Seeders/PostSeeder.php')))->toBeTrue()
        ->and(File::exists(base_path('modules/Blog/Requests/StorePostRequest.php')))->toBeTrue()
        ->and(File::exists(base_path('modules/Blog/Services/PostService.php')))->toBeTrue()
        ->and(File::exists(base_path('modules/Blog/Interfaces/PostRepositoryInterface.php')))->toBeTrue()
        ->and(File::exists(base_path('modules/Blog/Repositories/PostRepository.php')))->toBeTrue()
        ->and(File::exists(base_path('modules/Blog/Resources/js/Pages/Posts/Index.vue')))->toBeTrue();
});

test('module generators do not overwrite files without force', function () {
    $this->artisan('make:module Blog')->assertSuccessful();

    $path = base_path('modules/Blog/Services/PostService.php');
    File::ensureDirectoryExists(dirname($path));
    File::put($path, 'custom');

    $this->artisan('module:make-service Blog Post')->assertSuccessful();

    expect(File::get($path))->toBe('custom');

    $this->artisan('module:make-service Blog Post --force')->assertSuccessful();

    expect(File::get($path))->toContain('class PostService');
});

test('module generators fail when the target module is missing', function () {
    $this->artisan('module:make-controller Missing Post')
        ->assertFailed();
});
