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
import { ref } from 'vue';

interface MediaItem {
    id: number;
    url: string;
    name: string;
}

interface Svc {
    id: number;
    title: string;
    slug: string;
    icon: string | null;
    short_description: string | null;
    description: string | null;
    price: string | null;
    duration_minutes: number | null;
    sort_order: number;
    is_active: boolean;
    is_featured: boolean;
    meta_title: string | null;
    meta_description: string | null;
    indexable: boolean;
}

interface Props {
    service: Svc;
    thumbnail: MediaItem | null;
    banner: MediaItem | null;
    gallery_images: MediaItem[];
}

const props = defineProps<Props>();
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Services', href: '/admin/services' },
    { title: 'Edit', href: '#' },
];

const imagePreview = ref<string | null>(null);
const bannerPreview = ref<string | null>(null);
const fileKey = ref(0);
const bannerFileKey = ref(0);
const existingThumb = ref(props.thumbnail);
const existingBanner = ref(props.banner);

// Existing gallery images from DB
const existingGallery = ref<MediaItem[]>([...(props.gallery_images || [])]);

// New gallery images being added
interface GalleryPreview {
    file: File;
    preview: string;
}
const galleryPreviews = ref<GalleryPreview[]>([]);

// IDs of gallery images to remove
const removeGalleryIds = ref<number[]>([]);

const form = useForm({
    _method: 'put',
    title: props.service.title,
    slug: props.service.slug,
    icon: props.service.icon ?? '',
    short_description: props.service.short_description ?? '',
    description: props.service.description ?? '',
    price: props.service.price ?? '',
    duration_minutes: props.service.duration_minutes?.toString() ?? '',
    sort_order: props.service.sort_order,
    is_active: props.service.is_active,
    is_featured: props.service.is_featured,
    meta_title: props.service.meta_title ?? '',
    meta_description: props.service.meta_description ?? '',
    indexable: props.service.indexable,
    thumbnail: null as File | null,
    banner: null as File | null,
    gallery: [] as File[],
    remove_gallery: [] as number[],
});

const handleImage = (field: 'thumbnail' | 'banner', e: Event) => {
    const f = (e.target as HTMLInputElement).files?.[0];
    if (f) {
        form[field] = f;
        if (field === 'thumbnail') {
            imagePreview.value = URL.createObjectURL(f);
            existingThumb.value = null;
        }
        if (field === 'banner') {
            bannerPreview.value = URL.createObjectURL(f);
            existingBanner.value = null;
        }
    }
};

// Handle multiple gallery upload
const handleGallery = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (files) {
        Array.from(files).forEach((file) => {
            form.gallery.push(file);
            galleryPreviews.value.push({
                file,
                preview: URL.createObjectURL(file),
            });
        });
    }
    (e.target as HTMLInputElement).value = '';
};

const removeNewGalleryItem = (index: number) => {
    URL.revokeObjectURL(galleryPreviews.value[index].preview);
    galleryPreviews.value.splice(index, 1);
    form.gallery.splice(index, 1);
};

const removeExistingGalleryItem = (index: number) => {
    const item = existingGallery.value[index];
    removeGalleryIds.value.push(item.id);
    form.remove_gallery = [...removeGalleryIds.value];
    existingGallery.value.splice(index, 1);
};

const submit = () => form.post(route('admin.services.update', props.service.id), { forceFormData: true });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Edit: ${service.title}`" />
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-md"
                >
                    <Briefcase class="h-5 w-5 text-white" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Edit Service
                    </h1>
                    <p class="text-sm text-gray-500">{{ service.title }}</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Service Details -->
                    <div
                        class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900"
                    >
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">
                            Service Details
                        </h2>
                        <div class="grid gap-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <Label>Title <span class="text-red-500">*</span></Label>
                                    <Input v-model="form.title" class="mt-1" />
                                    <InputError :message="form.errors.title" class="mt-1" />
                                </div>
                                <div>
                                    <Label>Slug</Label>
                                    <Input v-model="form.slug" class="mt-1" />
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <Label>Icon</Label>
                                    <Input v-model="form.icon" class="mt-1" />
                                </div>
                                <div>
                                    <Label>Price (৳)</Label>
                                    <Input
                                        v-model="form.price"
                                        type="number"
                                        step="0.01"
                                        class="mt-1"
                                    />
                                </div>
                                <div>
                                    <Label>Duration (min)</Label>
                                    <Input
                                        v-model="form.duration_minutes"
                                        type="number"
                                        class="mt-1"
                                    />
                                </div>
                            </div>
                            <div>
                                <Label>Short Description</Label>
                                <Input v-model="form.short_description" class="mt-1" />
                            </div>

                            <!-- Fixed RichTextEditor with required id prop -->
                            <div class="grid gap-1">
                                <Label>Full Description</Label>
                                <RichTextEditor
                                    id="service-description-edit"
                                    v-model="form.description"
                                    placeholder="Write the full service description..."
                                    class="mt-1"
                                />
                                <InputError :message="form.errors.description" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <!-- Image Gallery Section -->
                    <div
                        class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900"
                    >
                        <div class="mb-4 flex items-center gap-2">
                            <Images class="h-5 w-5 text-blue-600" />
                            <h2 class="font-semibold text-gray-900 dark:text-white">
                                Service Gallery
                            </h2>
                            <span class="ml-auto text-xs text-gray-400"
                                >{{ existingGallery.length + galleryPreviews.length }} image(s)</span
                            >
                        </div>
                        <p class="mb-3 text-xs text-gray-500">
                            Upload multiple images to showcase this service. Hover over an image to
                            remove it.
                        </p>

                        <!-- Upload area -->
                        <div
                            class="mb-4 rounded-lg border-2 border-dashed border-gray-200 p-6 text-center dark:border-gray-600"
                        >
                            <Images class="mx-auto mb-2 h-8 w-8 text-gray-300" />
                            <p class="mb-2 text-sm text-gray-500">
                                Click to add more images to gallery
                            </p>
                            <input
                                type="file"
                                accept="image/*"
                                multiple
                                @change="handleGallery"
                                class="mx-auto text-xs text-gray-500"
                            />
                        </div>

                        <!-- Existing gallery images -->
                        <div
                            v-if="existingGallery.length > 0"
                            class="mb-3"
                        >
                            <p class="mb-2 text-xs font-medium text-gray-500">Current Images</p>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                                <div
                                    v-for="(item, index) in existingGallery"
                                    :key="'existing-' + item.id"
                                    class="group relative overflow-hidden rounded-lg border border-gray-200"
                                >
                                    <img
                                        :src="item.url"
                                        :alt="item.name"
                                        class="h-28 w-full object-cover"
                                    />
                                    <div
                                        class="absolute inset-0 flex items-center justify-center bg-black/0 transition group-hover:bg-black/30"
                                    >
                                        <button
                                            type="button"
                                            @click="removeExistingGalleryItem(index)"
                                            class="rounded-full bg-red-500 p-1 text-white opacity-0 shadow transition group-hover:opacity-100 hover:bg-red-600"
                                        >
                                            <X class="h-4 w-4" />
                                        </button>
                                    </div>
                                    <p
                                        class="truncate px-2 py-1 text-[10px] text-gray-400"
                                        :title="item.name"
                                    >
                                        {{ item.name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- New gallery previews -->
                        <div v-if="galleryPreviews.length > 0">
                            <p class="mb-2 text-xs font-medium text-gray-500">
                                New Images (not yet saved)
                            </p>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                                <div
                                    v-for="(item, index) in galleryPreviews"
                                    :key="'new-' + index"
                                    class="group relative overflow-hidden rounded-lg border border-green-200 ring-1 ring-green-100"
                                >
                                    <img
                                        :src="item.preview"
                                        :alt="item.file.name"
                                        class="h-28 w-full object-cover"
                                    />
                                    <div
                                        class="absolute inset-0 flex items-center justify-center bg-black/0 transition group-hover:bg-black/30"
                                    >
                                        <button
                                            type="button"
                                            @click="removeNewGalleryItem(index)"
                                            class="rounded-full bg-red-500 p-1 text-white opacity-0 shadow transition group-hover:opacity-100 hover:bg-red-600"
                                        >
                                            <X class="h-4 w-4" />
                                        </button>
                                    </div>
                                    <div
                                        class="absolute top-1 right-1 rounded bg-green-500 px-1.5 py-0.5 text-[9px] font-bold text-white"
                                    >
                                        NEW
                                    </div>
                                    <p
                                        class="truncate px-2 py-1 text-[10px] text-gray-400"
                                        :title="item.file.name"
                                    >
                                        {{ item.file.name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="existingGallery.length === 0 && galleryPreviews.length === 0"
                            class="py-6 text-center text-sm text-gray-400"
                        >
                            No gallery images yet. Upload some above.
                        </div>

                        <InputError :message="form.errors.gallery" class="mt-2" />
                    </div>

                    <!-- SEO -->
                    <div
                        class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900"
                    >
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">SEO</h2>
                        <div class="grid gap-4">
                            <div>
                                <Label>Meta Title</Label>
                                <Input v-model="form.meta_title" class="mt-1" />
                            </div>
                            <div>
                                <Label>Meta Description</Label>
                                <textarea
                                    v-model="form.meta_description"
                                    rows="3"
                                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                ></textarea>
                            </div>
                            <label class="flex cursor-pointer items-center gap-2">
                                <input
                                    type="checkbox"
                                    v-model="form.indexable"
                                    class="h-4 w-4 rounded text-blue-600"
                                />
                                <span class="text-sm">Allow indexing</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-4">
                    <!-- Thumbnail -->
                    <div
                        class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900"
                    >
                        <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">Thumbnail</h2>
                        <div
                            class="rounded-lg border-2 border-dashed border-gray-200 p-4 text-center"
                        >
                            <img
                                v-if="existingThumb || imagePreview"
                                :src="imagePreview ?? existingThumb!.url"
                                class="mx-auto mb-2 max-h-36 rounded object-cover"
                            />
                            <Image v-else class="mx-auto mb-2 h-8 w-8 text-gray-200" />
                            <input
                                :key="fileKey"
                                type="file"
                                accept="image/*"
                                @change="handleImage('thumbnail', $event)"
                                class="text-xs text-gray-500"
                            />
                        </div>
                    </div>

                    <!-- Banner -->
                    <div
                        class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900"
                    >
                        <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">
                            Banner Image
                        </h2>
                        <div
                            class="rounded-lg border-2 border-dashed border-gray-200 p-4 text-center"
                        >
                            <img
                                v-if="existingBanner || bannerPreview"
                                :src="bannerPreview ?? existingBanner!.url"
                                class="mx-auto mb-2 max-h-24 rounded object-cover"
                            />
                            <Image v-else class="mx-auto mb-2 h-8 w-8 text-gray-200" />
                            <input
                                :key="bannerFileKey"
                                type="file"
                                accept="image/*"
                                @change="handleImage('banner', $event)"
                                class="text-xs text-gray-500"
                            />
                        </div>
                    </div>

                    <!-- Settings -->
                    <div
                        class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900"
                    >
                        <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">Settings</h2>
                        <div class="space-y-3">
                            <label class="flex cursor-pointer items-center gap-2">
                                <input
                                    type="checkbox"
                                    v-model="form.is_active"
                                    class="h-4 w-4 rounded text-blue-600"
                                />
                                <span class="text-sm font-medium">Active</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2">
                                <input
                                    type="checkbox"
                                    v-model="form.is_featured"
                                    class="h-4 w-4 rounded text-blue-600"
                                />
                                <span class="text-sm font-medium">Featured</span>
                            </label>
                        </div>
                        <div class="mt-3">
                            <Label>Sort Order</Label>
                            <Input v-model.number="form.sort_order" type="number" class="mt-1" />
                        </div>
                    </div>

                    <!-- Submit -->
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full gap-2 bg-blue-600 hover:bg-blue-700"
                    >
                        <Save class="h-4 w-4" />{{
                            form.processing ? 'Saving...' : 'Update Service'
                        }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
