<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import RichTextEditor from '../../Components/RichTextEditor.vue';

import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Briefcase, Image, Images, Save, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Services', href: '/admin/services' },
    { title: 'New Service', href: '#' },
];

const imagePreview = ref<string | null>(null);
const bannerPreview = ref<string | null>(null);
const slugEdited = ref(false);

// Keep files in SEPARATE refs (not inside form object)
// This prevents Vue reactivity issues with File objects in FormData
const thumbnailFile = ref<File | null>(null);
const bannerFile = ref<File | null>(null);

// Gallery state
interface GalleryItem {
    file: File;
    preview: string;
}
const galleryItems = ref<GalleryItem[]>([]);

const form = useForm({
    title: '',
    slug: '',
    icon: '',
    short_description: '',
    description: '',
    price: '',
    duration_minutes: '',
    sort_order: 0,
    is_active: true,
    is_featured: false,
    meta_title: '',
    meta_description: '',
    indexable: true,
    // File fields will be injected via transform() at submit time
    thumbnail: null as File | null,
    banner: null as File | null,
    gallery: [] as File[],
});

const toSlug = (s: string) =>
    s.toLowerCase().trim().replace(/[^\w\s-]/g, '').replace(/[\s_-]+/g, '-').replace(/^-+|-+$/g, '');

watch(
    () => form.title,
    (v) => {
        if (!slugEdited.value) form.slug = toSlug(v);
    }
);

// ---- Thumbnail upload ----
const handleThumbnail = (e: Event) => {
    const f = (e.target as HTMLInputElement).files?.[0];
    if (f) {
        thumbnailFile.value = f;
        imagePreview.value = URL.createObjectURL(f);
    }
};

// ---- Banner upload ----
const handleBanner = (e: Event) => {
    const f = (e.target as HTMLInputElement).files?.[0];
    if (f) {
        bannerFile.value = f;
        bannerPreview.value = URL.createObjectURL(f);
    }
};

// ---- Gallery multi-image upload ----
const handleGallery = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (files) {
        Array.from(files).forEach((file) => {
            galleryItems.value.push({
                file,
                preview: URL.createObjectURL(file),
            });
        });
    }
    // Reset input so same file can be re-selected
    (e.target as HTMLInputElement).value = '';
};

const removeGalleryItem = (index: number) => {
    URL.revokeObjectURL(galleryItems.value[index].preview);
    galleryItems.value.splice(index, 1);
};

// ---- Submit ----
const submit = () => {
    form.transform((data) => {
        return {
            ...data,
            thumbnail: thumbnailFile.value,
            banner: bannerFile.value,
            gallery: galleryItems.value.map((item) => item.file),
        };
    }).post(route('admin.services.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="New Service" />
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-md">
                    <Briefcase class="h-5 w-5 text-white" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">New Service</h1>
                    <p class="text-sm text-gray-500">Create a new hospital service</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Service Details -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Service Details</h2>
                        <div class="grid gap-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <Label>Title <span class="text-red-500">*</span></Label>
                                    <Input v-model="form.title" class="mt-1" />
                                    <InputError :message="form.errors.title" class="mt-1" />
                                </div>
                                <div>
                                    <Label>Slug</Label>
                                    <Input v-model="form.slug" @input="slugEdited = true" placeholder="auto-generated" class="mt-1" />
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <Label>Icon (emoji/class)</Label>
                                    <Input v-model="form.icon" placeholder="🏥 or fa-heart" class="mt-1" />
                                </div>
                                <div>
                                    <Label>Price (৳)</Label>
                                    <Input v-model="form.price" type="number" step="0.01" class="mt-1" />
                                </div>
                                <div>
                                    <Label>Duration (min)</Label>
                                    <Input v-model="form.duration_minutes" type="number" class="mt-1" />
                                </div>
                            </div>
                            <div>
                                <Label>Short Description</Label>
                                <Input v-model="form.short_description" class="mt-1" />
                            </div>

                            <div class="grid gap-1">
                                <Label>Full Description</Label>
                                <RichTextEditor
                                    id="service-description-create"
                                    v-model="form.description"
                                    placeholder="Write the full service description..."
                                    class="mt-1"
                                />
                                <InputError :message="form.errors.description" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <!-- Image Gallery Section -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <div class="mb-4 flex items-center gap-2">
                            <Images class="h-5 w-5 text-blue-600" />
                            <h2 class="font-semibold text-gray-900 dark:text-white">Service Gallery</h2>
                            <span class="ml-auto text-xs text-gray-400">{{ galleryItems.length }} image(s)</span>
                        </div>
                        <p class="mb-3 text-xs text-gray-500">
                            Upload multiple images to showcase this service. Supported: JPG, PNG, WEBP (max 4 MB each).
                        </p>

                        <div class="mb-4 rounded-lg border-2 border-dashed border-gray-200 p-6 text-center dark:border-gray-600">
                            <Images class="mx-auto mb-2 h-8 w-8 text-gray-300" />
                            <p class="mb-2 text-sm text-gray-500">Click to select images</p>
                            <input type="file" accept="image/*" multiple @change="handleGallery" class="mx-auto text-xs text-gray-500" />
                        </div>

                        <div v-if="galleryItems.length > 0" class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                            <div v-for="(item, index) in galleryItems" :key="index" class="group relative overflow-hidden rounded-lg border border-gray-200">
                                <img :src="item.preview" :alt="item.file.name" class="h-28 w-full object-cover" />
                                <div class="absolute inset-0 flex items-center justify-center bg-black/0 transition group-hover:bg-black/30">
                                    <button type="button" @click="removeGalleryItem(index)" class="rounded-full bg-red-500 p-1 text-white opacity-0 shadow transition group-hover:opacity-100 hover:bg-red-600">
                                        <X class="h-4 w-4" />
                                    </button>
                                </div>
                                <p class="truncate px-2 py-1 text-[10px] text-gray-400" :title="item.file.name">{{ item.file.name }}</p>
                            </div>
                        </div>

                        <InputError :message="form.errors.gallery" class="mt-2" />
                    </div>

                    <!-- SEO -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">SEO</h2>
                        <div class="grid gap-4">
                            <div>
                                <Label>Meta Title</Label>
                                <Input v-model="form.meta_title" class="mt-1" />
                            </div>
                            <div>
                                <Label>Meta Description</Label>
                                <textarea v-model="form.meta_description" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                            </div>
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="checkbox" v-model="form.indexable" class="h-4 w-4 rounded text-blue-600" />
                                <span class="text-sm">Allow indexing</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-4">
                    <!-- Thumbnail -->
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">Thumbnail</h2>
                        <div class="rounded-lg border-2 border-dashed border-gray-200 p-4 text-center">
                            <img v-if="imagePreview" :src="imagePreview" class="mx-auto mb-2 max-h-32 rounded object-cover" />
                            <Image v-else class="mx-auto mb-2 h-8 w-8 text-gray-200" />
                            <input type="file" accept="image/*" @change="handleThumbnail" class="text-xs text-gray-500" />
                            <InputError :message="form.errors.thumbnail" class="mt-1" />
                        </div>
                    </div>

                    <!-- Banner -->
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">Banner Image</h2>
                        <div class="rounded-lg border-2 border-dashed border-gray-200 p-4 text-center">
                            <img v-if="bannerPreview" :src="bannerPreview" class="mx-auto mb-2 max-h-24 rounded object-cover" />
                            <Image v-else class="mx-auto mb-2 h-8 w-8 text-gray-200" />
                            <input type="file" accept="image/*" @change="handleBanner" class="text-xs text-gray-500" />
                            <InputError :message="form.errors.banner" class="mt-1" />
                        </div>
                        <p class="mt-1 text-[10px] text-gray-400">Banner shown on the service detail page header.</p>
                    </div>

                    <!-- Settings -->
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">Settings</h2>
                        <div class="space-y-3">
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="checkbox" v-model="form.is_active" class="h-4 w-4 rounded text-blue-600" />
                                <span class="text-sm font-medium">Active</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="checkbox" v-model="form.is_featured" class="h-4 w-4 rounded text-blue-600" />
                                <span class="text-sm font-medium">Featured</span>
                            </label>
                        </div>
                        <div class="mt-3">
                            <Label>Sort Order</Label>
                            <Input v-model.number="form.sort_order" type="number" class="mt-1" />
                        </div>
                    </div>

                    <!-- Submit -->
                    <Button type="submit" :disabled="form.processing" class="w-full gap-2 bg-blue-600 hover:bg-blue-700">
                        <Save class="h-4 w-4" />{{ form.processing ? 'Saving...' : 'Create Service' }}
                    </Button>

                    <!-- Show general form errors -->
                    <div v-if="Object.keys(form.errors).length > 0" class="rounded-lg border border-red-200 bg-red-50 p-3">
                        <p class="text-xs font-semibold text-red-700 mb-1">Please fix the following errors:</p>
                        <ul class="text-xs text-red-600 space-y-0.5">
                            <li v-for="(error, field) in form.errors" :key="field">• {{ error }}</li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
