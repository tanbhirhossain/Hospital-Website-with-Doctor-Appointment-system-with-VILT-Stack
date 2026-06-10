<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\DOCTOR\Models\Department;
use Modules\DOCTOR\Models\Doctor;

test('guests are redirected from doctors management', function () {
    $this->get(route('admin.doctors.index'))
        ->assertRedirect(route('login'));
});

test('doctors page renders doctor data', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create([
        'created_by' => $user->id,
        'name' => 'Internal Medicine',
        'slug' => 'internal-medicine',
    ]);

    Doctor::create([
        'department_id' => $department->id,
        'name' => 'Dr. Jane Doe',
        'slug' => 'dr-jane-doe',
        'specialty' => 'Cardiology',
        'qualifications' => 'MD, PhD',
        'designation' => 'Consultant',
        'institute' => 'Heart Care Center',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('admin.doctors.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DOCTOR::Index', false)
            ->has('doctors', 1)
            ->where('doctors.0.name', 'Dr. Jane Doe')
        );
});

test('authenticated users can access doctor create page', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create([
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('doctors.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DOCTOR::Doctor/Create', false)
            ->has('departments', 1)
            ->where('departments.0.id', $department->id)
        );
});

test('authenticated users can create doctors', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create([
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->post(route('admin.doctors.store'), [
            'department_id' => $department->id,
            'name' => 'Dr. Ann Wells',
            'slug' => 'dr-ann-wells',
            'specialty' => 'Dermatology',
            'qualifications' => 'MD',
            'designation' => 'Consultant',
            'institute' => 'Skin Care Clinic',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ])
        ->assertRedirect(route('admin.doctors.index'));

    $doctor = Doctor::where('slug', 'dr-ann-wells')->first();

    expect($doctor)->not->toBeNull()
        ->and($doctor->name)->toBe('Dr. Ann Wells');
});

test('authenticated users can upload doctor gallery images', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create([
        'created_by' => $user->id,
    ]);

    $galleryFiles = [
        UploadedFile::fake()->image('gallery-1.jpg'),
        UploadedFile::fake()->image('gallery-2.jpg'),
    ];

    $this->actingAs($user)
        ->post(route('admindoctors.store'), [
            'department_id' => $department->id,
            'name' => 'Dr. Gallery Test',
            'slug' => 'dr-gallery-test',
            'specialty' => 'Radiology',
            'qualifications' => 'MD',
            'designation' => 'Consultant',
            'institute' => 'Imaging Center',
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'gallery' => $galleryFiles,
        ])
        ->assertRedirect(route('admin.doctors.index'));

    $doctor = Doctor::where('slug', 'dr-gallery-test')->first();

    expect($doctor)->not->toBeNull();
    expect($doctor->getMedia('gallery')->count())->toBe(2);
});

test('authenticated users can upload doctor profile image', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create([
        'created_by' => $user->id,
    ]);

    $profileImage = UploadedFile::fake()->image('profile.jpg');

    $this->actingAs($user)
        ->post(route('admin.doctors.store'), [
            'department_id' => $department->id,
            'name' => 'Dr. Profile Test',
            'slug' => 'dr-profile-test',
            'specialty' => 'Surgery',
            'qualifications' => 'MD, FACS',
            'designation' => 'Chief Surgeon',
            'institute' => 'Surgery Center',
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'profile_image' => $profileImage,
        ])
        ->assertRedirect(route('admin.doctors.index'));

    $doctor = Doctor::where('slug', 'dr-profile-test')->first();

    expect($doctor)->not->toBeNull();
    expect($doctor->getMedia('profile_image')->count())->toBe(1);
});

test('authenticated users can upload doctor certificates', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create([
        'created_by' => $user->id,
    ]);

    $certificates = [
        UploadedFile::fake()->image('cert-1.jpg'),
        UploadedFile::fake()->image('cert-2.png'),
    ];

    $this->actingAs($user)
        ->post(route('admin.doctors.store'), [
            'department_id' => $department->id,
            'name' => 'Dr. Cert Test',
            'slug' => 'dr-cert-test',
            'specialty' => 'Ophthalmology',
            'qualifications' => 'MD, Fellowship',
            'designation' => 'Eye Specialist',
            'institute' => 'Eye Care Institute',
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'certificates' => $certificates,
        ])
        ->assertRedirect(route('admin.doctors.index'));

    $doctor = Doctor::where('slug', 'dr-cert-test')->first();

    expect($doctor)->not->toBeNull();
    expect($doctor->getMedia('certificates')->count())->toBe(2);
});

test('authenticated users can upload all media types together', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create([
        'created_by' => $user->id,
    ]);

    $profileImage = UploadedFile::fake()->image('profile.jpg');
    $galleryFiles = [
        UploadedFile::fake()->image('gallery-1.jpg'),
        UploadedFile::fake()->image('gallery-2.jpg'),
    ];
    $certificates = [
        UploadedFile::fake()->image('cert-1.jpg'),
    ];

    $this->actingAs($user)
        ->post(route('admin.doctors.store'), [
            'department_id' => $department->id,
            'name' => 'Dr. Complete Test',
            'slug' => 'dr-complete-test',
            'specialty' => 'Orthopedics',
            'qualifications' => 'MD, Orthopedic Surgery',
            'designation' => 'Orthopedic Consultant',
            'institute' => 'Bone & Joint Center',
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'profile_image' => $profileImage,
            'gallery' => $galleryFiles,
            'certificates' => $certificates,
        ])
        ->assertRedirect(route('admin.doctors.index'));

    $doctor = Doctor::where('slug', 'dr-complete-test')->first();

    expect($doctor)->not->toBeNull();
    expect($doctor->getMedia('profile_image')->count())->toBe(1);
    expect($doctor->getMedia('gallery')->count())->toBe(2);
    expect($doctor->getMedia('certificates')->count())->toBe(1);
});

test('authenticated users can access doctor edit page', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create([
        'created_by' => $user->id,
    ]);

    $doctor = Doctor::create([
        'department_id' => $department->id,
        'name' => 'Dr. Helen Grant',
        'slug' => 'dr-helen-grant',
        'specialty' => 'Pediatrics',
        'qualifications' => 'MD',
        'designation' => 'Pediatrician',
        'institute' => 'Children Care Clinic',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->delete(route('doctors.destroy', $doctor))
        ->assertRedirect(route('admin.doctors.index'));

    expect(Doctor::find($doctor->id))->toBeNull();
});
