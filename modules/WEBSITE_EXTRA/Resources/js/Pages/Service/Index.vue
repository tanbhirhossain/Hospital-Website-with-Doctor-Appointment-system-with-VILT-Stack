<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Briefcase, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Service { id: number; title: string; slug: string; icon: string|null; short_description: string|null; price: string|null; duration_minutes: number|null; sort_order: number; is_active: boolean; is_featured: boolean; media: { thumbnail: string|null }; }
interface Props { services: { data: Service[]; total: number; links: any[] }; filters: any; }
const props = defineProps<Props>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Services', href: '/admin/services' }];
const searchQuery = ref(props.filters.search ?? '');

const applyFilters = () => router.get(route('admin.services.index'), { search: searchQuery.value }, { preserveState: true });
const deleteItem = (id: number) => { if (!confirm('Delete this service?')) return; router.delete(route('admin.services.destroy', id)); };
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Services" />
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-md">
                        <Briefcase class="h-5 w-5 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Services</h1>
                        <p class="text-sm text-gray-500">Manage hospital services and offerings</p>
                    </div>
                </div>
                <Link :href="route('admin.services.create')" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                    <Plus class="h-4 w-4" />New Service
                </Link>
            </div>
            <div v-if="$page.props.flash?.success" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ $page.props.flash.success }}</div>
            <div class="flex gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <Input v-model="searchQuery" placeholder="Search services..." class="w-64" @keyup.enter="applyFilters" />
                <Button @click="applyFilters" variant="outline">Search</Button>
            </div>
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Service</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Price</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Duration</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="s in services.data" :key="s.id" class="transition hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-100 bg-blue-50 dark:border-gray-700">
                                        <img v-if="s.media?.thumbnail" :src="s.media.thumbnail" class="h-full w-full object-cover" />
                                        <span v-else-if="s.icon" class="text-sm">{{ s.icon }}</span>
                                        <Briefcase v-else class="h-5 w-5 text-blue-300" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ s.title }}</p>
                                        <p class="text-xs text-gray-400">{{ s.short_description }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-300">{{ s.price ? `৳${s.price}` : '—' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-300">{{ s.duration_minutes ? `${s.duration_minutes} min` : '—' }}</td>
                            <td class="px-5 py-3">
                                <div class="flex flex-col gap-1">
                                    <span :class="s.is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-gray-100 text-gray-500 ring-gray-200'" class="inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1">{{ s.is_active ? 'Active' : 'Inactive' }}</span>
                                    <span v-if="s.is_featured" class="inline-flex w-fit items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-blue-200">Featured</span>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-2">
                                    <Link :href="route('admin.services.edit', s.id)" class="rounded-lg p-1.5 text-gray-400 hover:bg-blue-100 hover:text-blue-600 transition"><Pencil class="h-4 w-4" /></Link>
                                    <button @click="deleteItem(s.id)" class="rounded-lg p-1.5 text-gray-400 hover:bg-red-100 hover:text-red-600 transition"><Trash2 class="h-4 w-4" /></button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="services.data.length === 0">
                            <td colspan="5" class="py-12 text-center text-sm text-gray-400">No services found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
