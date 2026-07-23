<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { CheckCircle, MessageCircleHeart, Pencil, Plus, Save, Star, Trash2, X, XCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Doctor { id: number; name: string; }
interface Review {
    id: number; patient_name: string; patient_phone: string | null;
    doctor_id: number | null; doctor: Doctor | null; department: string | null;
    review_text: string; rating: number; status: 'pending' | 'approved' | 'rejected';
    is_featured: boolean; admin_note: string | null;
    media: { avatar: string | null };
}
interface Props { reviews: { data: Review[]; total: number; links: any[] }; filters: any; doctors: Doctor[]; }
const props = defineProps<Props>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Patient Reviews', href: '/admin/patient-reviews' }];

const editing = ref<Review | null>(null);
const fileKey = ref(0);
const imagePreview = ref<string | null>(null);
const selectedStatus = ref(props.filters.status ?? '');
const searchQuery = ref(props.filters.search ?? '');

const form = useForm({
    _method: 'post' as 'post' | 'put',
    patient_name: '', patient_phone: '', doctor_id: null as number | null,
    department: '', review_text: '', rating: 5, status: 'pending',
    is_featured: false, admin_note: '', avatar: null as File | null,
});
const isEditing = computed(() => editing.value !== null);
const statusColors = { pending: 'bg-amber-50 text-amber-700 ring-amber-200', approved: 'bg-emerald-50 text-emerald-700 ring-emerald-200', rejected: 'bg-red-50 text-red-700 ring-red-200' };

const resetForm = () => { editing.value = null; imagePreview.value = null; fileKey.value++; form.reset(); form._method = 'post'; form.clearErrors(); };

const editItem = (r: Review) => {
    editing.value = r; form._method = 'put';
    form.patient_name = r.patient_name; form.patient_phone = r.patient_phone ?? '';
    form.doctor_id = r.doctor_id; form.department = r.department ?? '';
    form.review_text = r.review_text; form.rating = r.rating;
    form.status = r.status; form.is_featured = r.is_featured; form.admin_note = r.admin_note ?? '';
    form.clearErrors();
};

const handleImage = (e: Event) => { const f = (e.target as HTMLInputElement).files?.[0]; if (f) { form.avatar = f; imagePreview.value = URL.createObjectURL(f); } };

const submit = () => {
    const url = isEditing.value ? route('admin.patient-reviews.update', editing.value!.id) : route('admin.patient-reviews.store');
    form.post(url, { forceFormData: true, onSuccess: resetForm });
};

const approveReview = (id: number) => { if (!confirm('Approve this review?')) return; router.post(route('admin.patient-reviews.approve', id)); };
const rejectReview = (id: number) => { const reason = prompt('Reason for rejection (optional):') ?? ''; router.post(route('admin.patient-reviews.reject', id), { admin_note: reason }); };
const deleteItem = (id: number) => { if (!confirm('Delete this review?')) return; router.delete(route('admin.patient-reviews.destroy', id)); };

const applyFilters = () => {
    router.get(route('admin.patient-reviews.index'), { search: searchQuery.value, status: selectedStatus.value }, { preserveState: true });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Patient Reviews" />
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 shadow-md">
                        <MessageCircleHeart class="h-5 w-5 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Patient Reviews</h1>
                        <p class="text-sm text-gray-500">Moderate and manage patient feedback</p>
                    </div>
                </div>
                <Button @click="resetForm" class="gap-2 bg-rose-500 hover:bg-rose-600"><Plus class="h-4 w-4" />Add Review</Button>
            </div>

            <div v-if="$page.props.flash?.success" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ $page.props.flash.success }}</div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <Input v-model="searchQuery" placeholder="Search by name or phone..." class="w-56" @keyup.enter="applyFilters" />
                <select v-model="selectedStatus" @change="applyFilters" class="rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-rose-400 focus:ring-2 focus:ring-rose-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
                <Button @click="applyFilters" variant="outline" class="gap-2">Search</Button>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Form -->
                <div class="lg:col-span-1">
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <div class="border-b px-5 py-4 dark:border-gray-700">
                            <h2 class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                                <component :is="isEditing ? Pencil : Plus" class="h-4 w-4 text-rose-500" />
                                {{ isEditing ? 'Edit Review' : 'New Review' }}
                            </h2>
                        </div>
                        <form @submit.prevent="submit" class="space-y-4 p-5">
                            <div>
                                <Label>Patient Name <span class="text-red-500">*</span></Label>
                                <Input v-model="form.patient_name" class="mt-1" />
                                <InputError :message="form.errors.patient_name" class="mt-1" />
                            </div>
                            <div>
                                <Label>Phone</Label>
                                <Input v-model="form.patient_phone" class="mt-1" />
                            </div>
                            <div>
                                <Label>Doctor</Label>
                                <select v-model="form.doctor_id" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                    <option :value="null">— Select Doctor —</option>
                                    <option v-for="d in doctors" :key="d.id" :value="d.id">{{ d.name }}</option>
                                </select>
                            </div>
                            <div>
                                <Label>Department</Label>
                                <Input v-model="form.department" class="mt-1" />
                            </div>
                            <div>
                                <Label>Review <span class="text-red-500">*</span></Label>
                                <textarea v-model="form.review_text" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
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
                            <div>
                                <Label>Status</Label>
                                <select v-model="form.status" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div>
                                <Label>Admin Note</Label>
                                <textarea v-model="form.admin_note" rows="2" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                            </div>
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="checkbox" v-model="form.is_featured" class="h-4 w-4 rounded text-rose-500" />
                                <span class="text-sm">Featured</span>
                            </label>
                            <div class="flex gap-2">
                                <Button type="submit" :disabled="form.processing" class="flex-1 gap-2 bg-rose-500 hover:bg-rose-600">
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
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Patient</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Doctor</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rating</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                        <th class="px-5 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <tr v-for="r in reviews.data" :key="r.id" class="transition hover:bg-gray-50 dark:hover:bg-gray-800/50" :class="{ 'bg-rose-50/30 ring-1 ring-inset ring-rose-200': editing?.id === r.id }">
                                        <td class="px-5 py-3">
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ r.patient_name }}</p>
                                            <p class="text-xs text-gray-400">{{ r.patient_phone }}</p>
                                        </td>
                                        <td class="px-5 py-3 text-sm text-gray-600">{{ r.doctor?.name ?? '—' }}</td>
                                        <td class="px-5 py-3">
                                            <div class="flex gap-0.5">
                                                <Star v-for="n in 5" :key="n" class="h-3.5 w-3.5" :class="n <= r.rating ? 'text-amber-400 fill-amber-400' : 'text-gray-200'" />
                                            </div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <span :class="[statusColors[r.status], 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1']">
                                                {{ r.status.charAt(0).toUpperCase() + r.status.slice(1) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex justify-end gap-1.5">
                                                <button v-if="r.status === 'pending'" @click="approveReview(r.id)" class="rounded-lg p-1.5 text-gray-400 hover:bg-emerald-100 hover:text-emerald-600 transition" title="Approve">
                                                    <CheckCircle class="h-4 w-4" />
                                                </button>
                                                <button v-if="r.status !== 'rejected'" @click="rejectReview(r.id)" class="rounded-lg p-1.5 text-gray-400 hover:bg-amber-100 hover:text-amber-600 transition" title="Reject">
                                                    <XCircle class="h-4 w-4" />
                                                </button>
                                                <button @click="editItem(r)" class="rounded-lg p-1.5 text-gray-400 hover:bg-rose-100 hover:text-rose-600 transition"><Pencil class="h-4 w-4" /></button>
                                                <button @click="deleteItem(r.id)" class="rounded-lg p-1.5 text-gray-400 hover:bg-red-100 hover:text-red-600 transition"><Trash2 class="h-4 w-4" /></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="reviews.data.length === 0">
                                        <td colspan="5" class="py-12 text-center text-sm text-gray-400">No reviews found.</td>
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
