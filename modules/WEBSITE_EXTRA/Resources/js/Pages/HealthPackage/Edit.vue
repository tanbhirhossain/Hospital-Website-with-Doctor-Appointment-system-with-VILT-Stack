<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { HeartPulse, Image, Save, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Pkg { id: number; name: string; slug: string; badge: string|null; category: string; short_description: string|null; description: string|null; includes: string|null; original_price: string; discounted_price: string|null; duration_days: number|null; sort_order: number; is_active: boolean; is_featured: boolean; meta_title: string|null; meta_description: string|null; indexable: boolean; }
interface Props { package: Pkg; categories: string[]; thumbnail: { id: number; url: string; name: string } | null; }
const props = defineProps<Props>();
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Health Packages', href: '/admin/health-packages' },
    { title: 'Edit Package', href: '#' },
];

const imagePreview = ref<string | null>(null);
const fileKey = ref(0);
const existingThumbnail = ref(props.thumbnail);

const form = useForm({
    _method: 'put',
    name: props.package.name, slug: props.package.slug, badge: props.package.badge ?? '',
    short_description: props.package.short_description ?? '', description: props.package.description ?? '',
    includes: props.package.includes ?? '', original_price: props.package.original_price,
    discounted_price: props.package.discounted_price ?? '', duration_days: props.package.duration_days?.toString() ?? '',
    category: props.package.category, sort_order: props.package.sort_order,
    is_active: props.package.is_active, is_featured: props.package.is_featured,
    meta_title: props.package.meta_title ?? '', meta_description: props.package.meta_description ?? '',
    indexable: props.package.indexable, thumbnail: null as File | null,
});

const handleImage = (e: Event) => {
    const f = (e.target as HTMLInputElement).files?.[0];
    if (f) { form.thumbnail = f; imagePreview.value = URL.createObjectURL(f); existingThumbnail.value = null; }
};

const submit = () => form.post(route('admin.health-packages.update', props.package.id), { forceFormData: true });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Edit: ${package.name}`" />
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 shadow-md">
                    <HeartPulse class="h-5 w-5 text-white" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Edit Package</h1>
                    <p class="text-sm text-gray-500">{{ package.name }}</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Package Details</h2>
                        <div class="grid gap-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <Label>Package Name <span class="text-red-500">*</span></Label>
                                    <Input v-model="form.name" class="mt-1" />
                                    <InputError :message="form.errors.name" class="mt-1" />
                                </div>
                                <div>
                                    <Label>Slug</Label>
                                    <Input v-model="form.slug" class="mt-1" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <Label>Category <span class="text-red-500">*</span></Label>
                                    <select v-model="form.category" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm capitalize shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                        <option v-for="c in categories" :key="c" :value="c" class="capitalize">{{ c }}</option>
                                    </select>
                                </div>
                                <div>
                                    <Label>Badge</Label>
                                    <Input v-model="form.badge" class="mt-1" />
                                </div>
                            </div>
                            <div>
                                <Label>Short Description</Label>
                                <Input v-model="form.short_description" class="mt-1" />
                            </div>
                            <div>
                                <Label>Full Description</Label>
                                <textarea v-model="form.description" rows="5" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                            </div>
                            <div>
                                <Label>What's Included</Label>
                                <textarea v-model="form.includes" rows="5" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Pricing</h2>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <Label>Original Price (৳) <span class="text-red-500">*</span></Label>
                                <Input v-model="form.original_price" type="number" step="0.01" class="mt-1" />
                                <InputError :message="form.errors.original_price" class="mt-1" />
                            </div>
                            <div>
                                <Label>Discounted Price (৳)</Label>
                                <Input v-model="form.discounted_price" type="number" step="0.01" class="mt-1" />
                            </div>
                            <div>
                                <Label>Duration (Days)</Label>
                                <Input v-model="form.duration_days" type="number" class="mt-1" />
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">SEO Settings</h2>
                        <div class="grid gap-4">
                            <div><Label>Meta Title</Label><Input v-model="form.meta_title" class="mt-1" /></div>
                            <div><Label>Meta Description</Label><textarea v-model="form.meta_description" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea></div>
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="form.indexable" class="h-4 w-4 rounded text-teal-600" /><span class="text-sm">Allow indexing</span></label>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Thumbnail</h2>
                        <div class="rounded-lg border-2 border-dashed border-gray-200 p-4 text-center dark:border-gray-600">
                            <div v-if="existingThumbnail || imagePreview" class="mb-3">
                                <img :src="imagePreview ?? existingThumbnail!.url" class="mx-auto max-h-40 rounded-lg object-cover shadow" />
                            </div>
                            <Image v-else class="mx-auto mb-3 h-10 w-10 text-gray-200" />
                            <input :key="fileKey" type="file" accept="image/*" @change="handleImage" class="text-xs text-gray-500" />
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Settings</h2>
                        <div class="space-y-3">
                            <label class="flex cursor-pointer items-center gap-3"><input type="checkbox" v-model="form.is_active" class="h-4 w-4 rounded text-teal-600" /><span class="text-sm font-medium">Active</span></label>
                            <label class="flex cursor-pointer items-center gap-3"><input type="checkbox" v-model="form.is_featured" class="h-4 w-4 rounded text-teal-600" /><span class="text-sm font-medium">Featured</span></label>
                        </div>
                        <div class="mt-4"><Label>Sort Order</Label><Input v-model.number="form.sort_order" type="number" class="mt-1" /></div>
                    </div>
                    <Button type="submit" :disabled="form.processing" class="w-full gap-2 bg-teal-600 hover:bg-teal-700">
                        <Save class="h-4 w-4" />{{ form.processing ? 'Saving...' : 'Update Package' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
