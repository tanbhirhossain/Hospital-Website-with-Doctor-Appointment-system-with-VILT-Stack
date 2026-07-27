<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Crown, Image, Pencil, Plus, Save, Trash2, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Leadership Messages', href: '/admin/leadership-messages' }];
const flash = computed(() => (usePage().props as any).flash);
const props = defineProps<{ messages: Array<any> }>();

const editingId = ref<number | null>(null);
const photoPreview = ref<string | null>(null);
const fileKey = ref(0);

const form = useForm({
    name: '', role: '', role_line: '', eyebrow: '', title: '', quote: '',
    credentials: '', message: '', photo: null as File | null,
    is_active: true, sort_order: 0,
});

function resetForm() {
    form.reset(); form.is_active = true; editingId.value = null;
    photoPreview.value = null; fileKey.value++;
}

function editMessage(m: any) {
    editingId.value = m.id;
    form.name = m.name; form.role = m.role || ''; form.role_line = m.role_line || '';
    form.eyebrow = m.eyebrow || ''; form.title = m.title || ''; form.quote = m.quote || '';
    form.credentials = Array.isArray(m.credentials) ? m.credentials.join('\n') : (m.credentials || '');
    form.message = m.message || ''; form.is_active = m.is_active; form.sort_order = m.sort_order;
    photoPreview.value = m.photo_url || null;
}

function handlePhoto(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) { form.photo = file; photoPreview.value = URL.createObjectURL(file); }
}

function submit() {
    const url = editingId.value ? `/admin/leadership-messages/${editingId.value}` : '/admin/leadership-messages';
    form.post(url, { forceFormData: true, onSuccess: resetForm, ...(editingId.value ? { data: { _method: 'put' } } : {}) });
}

function deleteMessage(id: number) {
    if (!confirm('Delete this leadership message?')) return;
    router.delete(`/admin/leadership-messages/${id}`);
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Leadership Messages" />
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-md">
                        <Crown class="h-5 w-5 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Leadership Messages</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Manage chairman, MD, CEO messages on homepage</p>
                    </div>
                </div>
                <Button @click="resetForm" variant="default" class="gap-2 bg-violet-600 hover:bg-violet-700">
                    <Plus class="h-4 w-4" /> Add Message
                </Button>
            </div>

            <!-- Flash -->
            <div v-if="flash?.success" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400">
                {{ flash.success }}
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Form Panel -->
                <div class="lg:col-span-1">
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
                            <h2 class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                                <component :is="editingId ? Pencil : Plus" class="h-4 w-4 text-violet-500" />
                                {{ editingId ? 'Edit Message' : 'New Message' }}
                            </h2>
                        </div>
                        <form @submit.prevent="submit" class="space-y-4 p-5">
                            <div>
                                <Label>Name <span class="text-red-500">*</span></Label>
                                <Input v-model="form.name" placeholder="Prof. Dr. Ahmedul Kabir" class="mt-1" />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div><Label>Role</Label><Input v-model="form.role" placeholder="Chairman" class="mt-1" /></div>
                                <div><Label>Sort Order</Label><Input v-model.number="form.sort_order" type="number" class="mt-1" /></div>
                            </div>
                            <div><Label>Role Line</Label><Input v-model="form.role_line" placeholder="Chairman, AMZ Hospital Ltd." class="mt-1" /></div>
                            <div><Label>Eyebrow</Label><Input v-model="form.eyebrow" placeholder="Message from the Chairman" class="mt-1" /></div>
                            <div><Label>Title</Label><Input v-model="form.title" placeholder="Main heading..." class="mt-1" /></div>
                            <div><Label>Quote</Label><Input v-model="form.quote" placeholder="Short impactful quote..." class="mt-1" /></div>
                            <div>
                                <Label>Credentials (one per line)</Label>
                                <textarea v-model="form.credentials" rows="3" placeholder="MBBS, FCPS&#10;Healthcare Expert" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-violet-400 focus:ring-2 focus:ring-violet-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                            </div>
                            <div>
                                <Label>Full Message</Label>
                                <textarea v-model="form.message" rows="6" placeholder="Full message text..." class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-violet-400 focus:ring-2 focus:ring-violet-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                            </div>
                            <!-- Photo Upload -->
                            <div>
                                <Label>Photo</Label>
                                <div class="mt-1 rounded-lg border-2 border-dashed border-gray-200 p-4 text-center dark:border-gray-600">
                                    <div v-if="photoPreview" class="mb-2">
                                        <img :src="photoPreview" class="mx-auto max-h-32 rounded object-cover" />
                                    </div>
                                    <Image v-else class="mx-auto mb-2 h-8 w-8 text-gray-300" />
                                    <input :key="fileKey" type="file" accept="image/*" @change="handlePhoto" class="text-sm text-gray-500" />
                                </div>
                                <InputError :message="form.errors.photo" />
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div></div>
                                <div class="flex items-end pb-1">
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input type="checkbox" v-model="form.is_active" class="h-4 w-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" />
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                                    </label>
                                </div>
                            </div>
                            <div class="flex gap-2 pt-2">
                                <Button type="submit" :disabled="form.processing" class="flex-1 gap-2 bg-violet-600 hover:bg-violet-700">
                                    <Save class="h-4 w-4" /> {{ editingId ? 'Update' : 'Create' }}
                                </Button>
                                <Button v-if="editingId" type="button" variant="outline" @click="resetForm">
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
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Photo</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Details</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Order</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                        <th class="px-5 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <tr v-for="m in messages" :key="m.id" class="transition hover:bg-gray-50 dark:hover:bg-gray-800/50" :class="{ 'ring-1 ring-inset ring-violet-200 bg-violet-50/30': editingId === m.id }">
                                        <td class="px-5 py-3">
                                            <div class="h-14 w-14 overflow-hidden rounded-full border-2 border-gray-200 bg-gray-100">
                                                <img v-if="m.photo_url" :src="m.photo_url" class="h-full w-full object-cover" />
                                                <Crown v-else class="m-auto mt-3 h-6 w-6 text-gray-300" />
                                            </div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ m.name }}</p>
                                            <p class="text-xs text-gray-400">{{ m.role_line || m.role }}</p>
                                            <p v-if="m.title" class="text-xs text-gray-500 truncate max-w-[200px] mt-0.5">{{ m.title }}</p>
                                        </td>
                                        <td class="px-5 py-3 text-sm text-gray-600">{{ m.sort_order }}</td>
                                        <td class="px-5 py-3">
                                            <span :class="m.is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-gray-100 text-gray-500 ring-1 ring-gray-200'" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
                                                {{ m.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex justify-end gap-2">
                                                <button @click="editMessage(m)" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-violet-100 hover:text-violet-600">
                                                    <Pencil class="h-4 w-4" />
                                                </button>
                                                <button @click="deleteMessage(m.id)" class="rounded-lg p-1.5 text-gray-400 transition hover:bg-red-100 hover:text-red-600">
                                                    <Trash2 class="h-4 w-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="messages.length === 0">
                                        <td colspan="5" class="py-12 text-center text-sm text-gray-400">No leadership messages yet. Add the first one!</td>
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
