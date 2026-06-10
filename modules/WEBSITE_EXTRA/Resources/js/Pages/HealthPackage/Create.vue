<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { HeartPulse, Image, Save } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Props { categories: string[]; }
const props = defineProps<Props>();
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Health Packages', href: '/admin/health-packages' },
    { title: 'New Package', href: '#' },
];

const imagePreview = ref<string | null>(null);
const fileKey = ref(0);
const slugEdited = ref(false);

const form = useForm({
    name: '', slug: '', badge: '', short_description: '', description: '',
    includes: '', original_price: '', discounted_price: '', duration_days: '',
    category: 'general', sort_order: 0, is_active: true, is_featured: false,
    meta_title: '', meta_description: '', indexable: true,
    thumbnail: null as File | null,
});

const toSlug = (s: string) => s.toLowerCase().trim().replace(/[^\w\s-]/g,'').replace(/[\s_-]+/g,'-').replace(/^-+|-+$/g,'');

watch(() => form.name, v => { if (!slugEdited.value) form.slug = toSlug(v); });

const handleImage = (e: Event) => {
    const f = (e.target as HTMLInputElement).files?.[0];
    if (f) { form.thumbnail = f; imagePreview.value = URL.createObjectURL(f); }
};

const submit = () => form.post(route('admin.health-packages.store'), { forceFormData: true });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="New Health Package" />
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 shadow-md">
                    <HeartPulse class="h-5 w-5 text-white" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">New Health Package</h1>
                    <p class="text-sm text-gray-500">Create a new diagnostic or health check package</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <!-- Basic Info -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Package Details</h2>
                        <div class="grid gap-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <Label>Package Name <span class="text-red-500">*</span></Label>
                                    <Input v-model="form.name" placeholder="e.g. Executive Health Check" class="mt-1" />
                                    <InputError :message="form.errors.name" class="mt-1" />
                                </div>
                                <div>
                                    <Label>Slug</Label>
                                    <Input v-model="form.slug" @input="slugEdited = true" placeholder="auto-generated" class="mt-1" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <Label>Category <span class="text-red-500">*</span></Label>
                                    <select v-model="form.category" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm capitalize shadow-sm focus:border-teal-400 focus:ring-2 focus:ring-teal-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                        <option v-for="c in categories" :key="c" :value="c" class="capitalize">{{ c }}</option>
                                    </select>
                                    <InputError :message="form.errors.category" class="mt-1" />
                                </div>
                                <div>
                                    <Label>Badge Text</Label>
                                    <Input v-model="form.badge" placeholder="e.g. Popular, New" class="mt-1" />
                                </div>
                            </div>
                            <div>
                                <Label>Short Description</Label>
                                <Input v-model="form.short_description" placeholder="Brief summary..." class="mt-1" />
                            </div>
                            <div>
                                <Label>Full Description</Label>
                                <textarea v-model="form.description" rows="5" placeholder="Detailed package description..." class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-teal-400 focus:ring-2 focus:ring-teal-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                            </div>
                            <div>
                                <Label>What's Included</Label>
                                <textarea v-model="form.includes" rows="5" placeholder="List each test/service on a new line..." class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-teal-400 focus:ring-2 focus:ring-teal-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Pricing</h2>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <Label>Original Price (৳) <span class="text-red-500">*</span></Label>
                                <Input v-model="form.original_price" type="number" step="0.01" min="0" placeholder="0.00" class="mt-1" />
                                <InputError :message="form.errors.original_price" class="mt-1" />
                            </div>
                            <div>
                                <Label>Discounted Price (৳)</Label>
                                <Input v-model="form.discounted_price" type="number" step="0.01" min="0" placeholder="Leave empty if no discount" class="mt-1" />
                            </div>
                            <div>
                                <Label>Duration (Days)</Label>
                                <Input v-model="form.duration_days" type="number" min="1" placeholder="e.g. 1" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">SEO Settings</h2>
                        <div class="grid gap-4">
                            <div>
                                <Label>Meta Title</Label>
                                <Input v-model="form.meta_title" class="mt-1" />
                            </div>
                            <div>
                                <Label>Meta Description</Label>
                                <textarea v-model="form.meta_description" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" v-model="form.indexable" class="h-4 w-4 rounded text-teal-600" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">Allow search engine indexing</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-4">
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Thumbnail</h2>
                        <div class="rounded-lg border-2 border-dashed border-gray-200 p-5 text-center dark:border-gray-600">
                            <div v-if="imagePreview" class="mb-3">
                                <img :src="imagePreview" class="mx-auto max-h-40 rounded-lg object-cover shadow" />
                            </div>
                            <Image v-else class="mx-auto mb-3 h-10 w-10 text-gray-200" />
                            <input :key="fileKey" type="file" accept="image/*" @change="handleImage" class="text-xs text-gray-500" />
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Settings</h2>
                        <div class="space-y-3">
                            <label class="flex cursor-pointer items-center gap-3">
                                <input type="checkbox" v-model="form.is_active" class="h-4 w-4 rounded text-teal-600" />
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</p>
                                    <p class="text-xs text-gray-400">Visible on website</p>
                                </div>
                            </label>
                            <label class="flex cursor-pointer items-center gap-3">
                                <input type="checkbox" v-model="form.is_featured" class="h-4 w-4 rounded text-teal-600" />
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Featured</p>
                                    <p class="text-xs text-gray-400">Show in featured section</p>
                                </div>
                            </label>
                        </div>
                        <div class="mt-4">
                            <Label>Sort Order</Label>
                            <Input v-model.number="form.sort_order" type="number" class="mt-1" />
                        </div>
                    </div>
                    <Button type="submit" :disabled="form.processing" class="w-full gap-2 bg-teal-600 hover:bg-teal-700">
                        <Save class="h-4 w-4" />
                        {{ form.processing ? 'Saving...' : 'Create Package' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
