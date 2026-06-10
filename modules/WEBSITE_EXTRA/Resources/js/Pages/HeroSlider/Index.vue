<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { GalleryHorizontalEnd, Image, Pencil, Plus, Save, SlidersHorizontal, Trash2, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Slider {
    id: number;
    title: string;
    subtitle: string | null;
    description: string | null;
    button_text: string | null;
    button_url: string | null;
    button_text_secondary: string | null;
    button_url_secondary: string | null;
    badge_text: string | null;
    sort_order: number;
    is_active: boolean;
    media: { slide_image: string | null };
}
interface Props {
    sliders: { data: Slider[]; total: number; links: any[] };
    filters: { search?: string };
}
const props = defineProps<Props>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Hero Sliders', href: '/admin/hero-sliders' }];

const editingSlider = ref<Slider | null>(null);
const imagePreview = ref<string | null>(null);
const fileKey = ref(0);

const form = useForm({
    _method: 'post' as 'post' | 'put',
    title: '',
    subtitle: '',
    description: '',
    button_text: '',
    button_url: '',
    button_text_secondary: '',
    button_url_secondary: '',
    badge_text: '',
    sort_order: 0,
    is_active: true,
    slide_image: null as File | null,
});

const isEditing = computed(() => editingSlider.value !== null);

const resetForm = () => {
    editingSlider.value = null;
    imagePreview.value = null;
    fileKey.value++;
    form.reset();
    form._method = 'post';
    form.clearErrors();
};

const editSlider = (s: Slider) => {
    editingSlider.value = s;
    form._method = 'put';
    form.title = s.title;
    form.subtitle = s.subtitle ?? '';
    form.description = s.description ?? '';
    form.button_text = s.button_text ?? '';
    form.button_url = s.button_url ?? '';
    form.button_text_secondary = s.button_text_secondary ?? '';
    form.button_url_secondary = s.button_url_secondary ?? '';
    form.badge_text = s.badge_text ?? '';
    form.sort_order = s.sort_order;
    form.is_active = s.is_active;
    form.clearErrors();
};

const handleImage = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) { form.slide_image = file; imagePreview.value = URL.createObjectURL(file); }
};

const submit = () => {
    const url = isEditing.value
        ? route('admin.hero-sliders.update', editingSlider.value!.id)
        : route('admin.hero-sliders.store');
    form.post(url, { forceFormData: true, onSuccess: resetForm });
};

const deleteSlider = (id: number) => {
    if (!confirm('Delete this hero slider?')) return;
    router.delete(route('admin.hero-sliders.destroy', id));
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Hero Sliders" />
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-md">
                        <SlidersHorizontal class="h-5 w-5 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Hero Sliders</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Manage homepage hero banner slides</p>
                    </div>
                </div>
                <Button @click="resetForm" variant="default" class="gap-2 bg-violet-600 hover:bg-violet-700">
                    <Plus class="h-4 w-4" /> Add Slider
                </Button>
            </div>

            <!-- Flash -->
            <div v-if="$page.props.flash?.success" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400">
                {{ $page.props.flash.success }}
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Form Panel -->
                <div class="lg:col-span-1">
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
                            <h2 class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                                <component :is="isEditing ? Pencil : Plus" class="h-4 w-4 text-violet-500" />
                                {{ isEditing ? 'Edit Slider' : 'New Slider' }}
                            </h2>
                        </div>
                        <form @submit.prevent="submit" class="space-y-4 p-5">
                            <div>
                                <Label>Title <span class="text-red-500">*</span></Label>
                                <Input v-model="form.title" placeholder="Hero title..." class="mt-1" />
                                <InputError :message="form.errors.title" class="mt-1" />
                            </div>
                            <div>
                                <Label>Subtitle</Label>
                                <Input v-model="form.subtitle" placeholder="Optional subtitle..." class="mt-1" />
                            </div>
                            <div>
                                <Label>Description</Label>
                                <textarea v-model="form.description" rows="3" placeholder="Short description..." class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-violet-400 focus:ring-2 focus:ring-violet-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                            </div>
                            <div>
                                <Label>Badge Text</Label>
                                <Input v-model="form.badge_text" placeholder="e.g. New, Featured..." class="mt-1" />
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <Label>Button 1 Text</Label>
                                    <Input v-model="form.button_text" placeholder="Book Now" class="mt-1" />
                                </div>
                                <div>
                                    <Label>Button 1 URL</Label>
                                    <Input v-model="form.button_url" placeholder="/contact" class="mt-1" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <Label>Button 2 Text</Label>
                                    <Input v-model="form.button_text_secondary" placeholder="Learn More" class="mt-1" />
                                </div>
                                <div>
                                    <Label>Button 2 URL</Label>
                                    <Input v-model="form.button_url_secondary" placeholder="/about" class="mt-1" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <Label>Sort Order</Label>
                                    <Input v-model.number="form.sort_order" type="number" class="mt-1" />
                                </div>
                                <div class="flex items-end pb-1">
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" v-model="form.is_active" class="h-4 w-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" />
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                                    </label>
                                </div>
                            </div>
                            <!-- Image Upload -->
                            <div>
                                <Label>Slide Image</Label>
                                <div class="mt-1 rounded-lg border-2 border-dashed border-gray-200 p-4 text-center dark:border-gray-600">
                                    <div v-if="imagePreview" class="mb-2">
                                        <img :src="imagePreview" class="mx-auto max-h-32 rounded object-cover" />
                                    </div>
                                    <Image class="mx-auto mb-2 h-8 w-8 text-gray-300" v-if="!imagePreview" />
                                    <input :key="fileKey" type="file" accept="image/*" @change="handleImage" class="text-sm text-gray-500" />
                                </div>
                                <InputError :message="form.errors.slide_image" class="mt-1" />
                            </div>
                            <div class="flex gap-2 pt-2">
                                <Button type="submit" :disabled="form.processing" class="flex-1 gap-2 bg-violet-600 hover:bg-violet-700">
                                    <Save class="h-4 w-4" />
                                    {{ isEditing ? 'Update' : 'Create' }}
                                </Button>
                                <Button v-if="isEditing" type="button" variant="outline" @click="resetForm">
                                    <X class="h-4 w-4" />
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="lg:col-span-2">
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Image</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Details</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Order</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                        <th class="px-5 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <tr v-for="s in sliders.data" :key="s.id" class="transition hover:bg-gray-50 dark:hover:bg-gray-800/50" :class="{ 'ring-1 ring-inset ring-violet-200 bg-violet-50/30': editingSlider?.id === s.id }">
                                        <td class="px-5 py-3">
                                            <div class="h-12 w-20 overflow-hidden rounded-lg border border-gray-200 bg-gray-100">
                                                <img v-if="s.media?.slide_image" :src="s.media.slide_image" class="h-full w-full object-cover" />
                                                <GalleryHorizontalEnd v-else class="m-auto mt-3 h-6 w-6 text-gray-300" />
                                            </div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ s.title }}</p>
                                            <p class="text-xs text-gray-400">{{ s.subtitle }}</p>
                                        </td>
                                        <td class="px-5 py-3 text-sm text-gray-600">{{ s.sort_order }}</td>
                                        <td class="px-5 py-3">
                                            <span :class="s.is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-gray-100 text-gray-500 ring-1 ring-gray-200'" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
                                                {{ s.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex justify-end gap-2">
                                                <button @click="editSlider(s)" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-violet-100 hover:text-violet-600">
                                                    <Pencil class="h-4 w-4" />
                                                </button>
                                                <button @click="deleteSlider(s.id)" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-red-100 hover:text-red-600">
                                                    <Trash2 class="h-4 w-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="sliders.data.length === 0">
                                        <td colspan="5" class="py-12 text-center text-sm text-gray-400">No sliders found. Add your first one!</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
