<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Briefcase, Image, Save } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Services', href: '/admin/services' }, { title: 'New Service', href: '#' }];
const imagePreview = ref<string | null>(null);
const fileKey = ref(0);
const slugEdited = ref(false);

const form = useForm({
    title: '', slug: '', icon: '', short_description: '', description: '',
    price: '', duration_minutes: '', sort_order: 0, is_active: true, is_featured: false,
    meta_title: '', meta_description: '', indexable: true,
    thumbnail: null as File | null, banner: null as File | null,
});

const toSlug = (s: string) => s.toLowerCase().trim().replace(/[^\w\s-]/g,'').replace(/[\s_-]+/g,'-').replace(/^-+|-+$/g,'');
watch(() => form.title, v => { if (!slugEdited.value) form.slug = toSlug(v); });

const handleImage = (field: 'thumbnail' | 'banner', e: Event) => {
    const f = (e.target as HTMLInputElement).files?.[0];
    if (f) { form[field] = f; if (field === 'thumbnail') imagePreview.value = URL.createObjectURL(f); }
};

const submit = () => form.post(route('admin.services.store'), { forceFormData: true });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="New Service" />
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-md"><Briefcase class="h-5 w-5 text-white" /></div>
                <div><h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">New Service</h1></div>
            </div>
            <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Service Details</h2>
                        <div class="grid gap-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div><Label>Title <span class="text-red-500">*</span></Label><Input v-model="form.title" class="mt-1" /><InputError :message="form.errors.title" class="mt-1" /></div>
                                <div><Label>Slug</Label><Input v-model="form.slug" @input="slugEdited=true" placeholder="auto-generated" class="mt-1" /></div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div><Label>Icon (emoji/class)</Label><Input v-model="form.icon" placeholder="🏥 or fa-heart" class="mt-1" /></div>
                                <div><Label>Price (৳)</Label><Input v-model="form.price" type="number" step="0.01" class="mt-1" /></div>
                                <div><Label>Duration (min)</Label><Input v-model="form.duration_minutes" type="number" class="mt-1" /></div>
                            </div>
                            <div><Label>Short Description</Label><Input v-model="form.short_description" class="mt-1" /></div>
                            <div><Label>Full Description</Label><textarea v-model="form.description" rows="6" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea></div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">SEO</h2>
                        <div class="grid gap-4">
                            <div><Label>Meta Title</Label><Input v-model="form.meta_title" class="mt-1" /></div>
                            <div><Label>Meta Description</Label><textarea v-model="form.meta_description" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea></div>
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="form.indexable" class="h-4 w-4 rounded text-blue-600" /><span class="text-sm">Allow indexing</span></label>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">Thumbnail</h2>
                        <div class="rounded-lg border-2 border-dashed border-gray-200 p-4 text-center">
                            <img v-if="imagePreview" :src="imagePreview" class="mx-auto mb-2 max-h-32 rounded object-cover" />
                            <Image v-else class="mx-auto mb-2 h-8 w-8 text-gray-200" />
                            <input :key="fileKey" type="file" accept="image/*" @change="handleImage('thumbnail', $event)" class="text-xs text-gray-500" />
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">Settings</h2>
                        <div class="space-y-3">
                            <label class="flex cursor-pointer items-center gap-2"><input type="checkbox" v-model="form.is_active" class="h-4 w-4 rounded text-blue-600" /><span class="text-sm font-medium">Active</span></label>
                            <label class="flex cursor-pointer items-center gap-2"><input type="checkbox" v-model="form.is_featured" class="h-4 w-4 rounded text-blue-600" /><span class="text-sm font-medium">Featured</span></label>
                        </div>
                        <div class="mt-3"><Label>Sort Order</Label><Input v-model.number="form.sort_order" type="number" class="mt-1" /></div>
                    </div>
                    <Button type="submit" :disabled="form.processing" class="w-full gap-2 bg-blue-600 hover:bg-blue-700">
                        <Save class="h-4 w-4" />{{ form.processing ? 'Saving...' : 'Create Service' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
