<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { MessageSquareQuote, Pencil, Plus, Save, Star, Trash2, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Review {
    id: number; client_name: string; client_designation: string | null;
    client_company: string | null; review_text: string; rating: number;
    sort_order: number; is_active: boolean; is_featured: boolean;
    media: { avatar: string | null };
}
interface Props { reviews: { data: Review[]; total: number; links: any[] }; filters: any; }
const props = defineProps<Props>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Client Reviews', href: '/admin/client-reviews' }];

const editing = ref<Review | null>(null);
const imagePreview = ref<string | null>(null);
const fileKey = ref(0);

const form = useForm({
    _method: 'post' as 'post' | 'put',
    client_name: '', client_designation: '', client_company: '',
    review_text: '', rating: 5, sort_order: 0,
    is_active: true, is_featured: false, avatar: null as File | null,
});
const isEditing = computed(() => editing.value !== null);

const resetForm = () => { editing.value = null; imagePreview.value = null; fileKey.value++; form.reset(); form._method = 'post'; form.clearErrors(); };

const editItem = (r: Review) => {
    editing.value = r; form._method = 'put';
    form.client_name = r.client_name; form.client_designation = r.client_designation ?? '';
    form.client_company = r.client_company ?? ''; form.review_text = r.review_text;
    form.rating = r.rating; form.sort_order = r.sort_order;
    form.is_active = r.is_active; form.is_featured = r.is_featured;
    form.clearErrors();
};

const handleImage = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) { form.avatar = file; imagePreview.value = URL.createObjectURL(file); }
};

const submit = () => {
    const url = isEditing.value ? route('admin.client-reviews.update', editing.value!.id) : route('admin.client-reviews.store');
    form.post(url, { forceFormData: true, onSuccess: resetForm });
};

const deleteItem = (id: number) => { if (!confirm('Delete this review?')) return; router.delete(route('admin.client-reviews.destroy', id)); };
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Client Reviews" />
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 shadow-md">
                        <MessageSquareQuote class="h-5 w-5 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Client Reviews</h1>
                        <p class="text-sm text-gray-500">Manage client testimonials and reviews</p>
                    </div>
                </div>
                <Button @click="resetForm" class="gap-2 bg-amber-500 hover:bg-amber-600"><Plus class="h-4 w-4" />Add Review</Button>
            </div>

            <div v-if="$page.props.flash?.success" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ $page.props.flash.success }}</div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Form -->
                <div class="lg:col-span-1">
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <div class="border-b px-5 py-4 dark:border-gray-700">
                            <h2 class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                                <component :is="isEditing ? Pencil : Plus" class="h-4 w-4 text-amber-500" />
                                {{ isEditing ? 'Edit Review' : 'New Review' }}
                            </h2>
                        </div>
                        <form @submit.prevent="submit" class="space-y-4 p-5">
                            <div>
                                <Label>Client Name <span class="text-red-500">*</span></Label>
                                <Input v-model="form.client_name" placeholder="John Smith" class="mt-1" />
                                <InputError :message="form.errors.client_name" class="mt-1" />
                            </div>
                            <div>
                                <Label>Designation</Label>
                                <Input v-model="form.client_designation" placeholder="CEO, Director..." class="mt-1" />
                            </div>
                            <div>
                                <Label>Company</Label>
                                <Input v-model="form.client_company" placeholder="Company name..." class="mt-1" />
                            </div>
                            <div>
                                <Label>Review <span class="text-red-500">*</span></Label>
                                <textarea v-model="form.review_text" rows="4" placeholder="Review content..." class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-amber-400 focus:ring-2 focus:ring-amber-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                                <InputError :message="form.errors.review_text" class="mt-1" />
                            </div>
                            <div>
                                <Label>Rating</Label>
                                <div class="mt-2 flex gap-1">
                                    <button v-for="n in 5" :key="n" type="button" @click="form.rating = n">
                                        <Star class="h-6 w-6" :class="n <= form.rating ? 'text-amber-400 fill-amber-400' : 'text-gray-300'" />
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <Label>Sort Order</Label>
                                    <Input v-model.number="form.sort_order" type="number" class="mt-1" />
                                </div>
                                <div class="flex flex-col gap-2 pt-1">
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" v-model="form.is_active" class="h-4 w-4 rounded text-amber-500" />
                                        <span class="text-sm">Active</span>
                                    </label>
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" v-model="form.is_featured" class="h-4 w-4 rounded text-amber-500" />
                                        <span class="text-sm">Featured</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <Label>Avatar Photo</Label>
                                <div class="mt-1 rounded-lg border-2 border-dashed border-gray-200 p-4 text-center">
                                    <img v-if="imagePreview" :src="imagePreview" class="mx-auto mb-2 h-16 w-16 rounded-full object-cover" />
                                    <input :key="fileKey" type="file" accept="image/*" @change="handleImage" class="text-xs text-gray-500" />
                                </div>
                            </div>
                            <div class="flex gap-2 pt-1">
                                <Button type="submit" :disabled="form.processing" class="flex-1 gap-2 bg-amber-500 hover:bg-amber-600">
                                    <Save class="h-4 w-4" />{{ isEditing ? 'Update' : 'Create' }}
                                </Button>
                                <Button v-if="isEditing" type="button" variant="outline" @click="resetForm"><X class="h-4 w-4" /></Button>
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
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Client</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rating</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                        <th class="px-5 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <tr v-for="r in reviews.data" :key="r.id" class="transition hover:bg-gray-50 dark:hover:bg-gray-800/50" :class="{ 'bg-amber-50/40 ring-1 ring-inset ring-amber-200': editing?.id === r.id }">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-3">
                                                <img v-if="r.media?.avatar" :src="r.media.avatar" class="h-9 w-9 rounded-full object-cover ring-2 ring-amber-100" />
                                                <div v-else class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 text-sm font-semibold text-amber-700">
                                                    {{ r.client_name.charAt(0) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-900 dark:text-white">{{ r.client_name }}</p>
                                                    <p class="text-xs text-gray-400">{{ r.client_designation }}{{ r.client_company ? ` · ${r.client_company}` : '' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex gap-0.5">
                                                <Star v-for="n in 5" :key="n" class="h-4 w-4" :class="n <= r.rating ? 'text-amber-400 fill-amber-400' : 'text-gray-200'" />
                                            </div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex flex-col gap-1">
                                                <span :class="r.is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-gray-100 text-gray-500'" class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium">{{ r.is_active ? 'Active' : 'Inactive' }}</span>
                                                <span v-if="r.is_featured" class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-amber-200">Featured</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex justify-end gap-2">
                                                <button @click="editItem(r)" class="rounded-lg p-1.5 text-gray-400 hover:bg-amber-100 hover:text-amber-600 transition"><Pencil class="h-4 w-4" /></button>
                                                <button @click="deleteItem(r.id)" class="rounded-lg p-1.5 text-gray-400 hover:bg-red-100 hover:text-red-600 transition"><Trash2 class="h-4 w-4" /></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="reviews.data.length === 0">
                                        <td colspan="4" class="py-12 text-center text-sm text-gray-400">No reviews yet.</td>
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
