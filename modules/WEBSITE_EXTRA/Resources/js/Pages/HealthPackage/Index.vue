<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { HeartPulse, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Pkg { id: number; name: string; slug: string; badge: string|null; category: string; original_price: string; discounted_price: string|null; is_active: boolean; is_featured: boolean; media: {thumbnail: string|null}; }
interface Props { packages: { data: Pkg[]; total: number; links: any[] }; filters: any; categories: string[]; }
const props = defineProps<Props>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Health Packages', href: '/admin/health-packages' }];

const searchQuery = ref(props.filters.search ?? '');
const categoryFilter = ref(props.filters.category ?? '');

const applyFilters = () => router.get(route('admin.health-packages.index'), { search: searchQuery.value, category: categoryFilter.value }, { preserveState: true });

const deleteItem = (id: number) => { if (!confirm('Delete this health package?')) return; router.delete(route('admin.health-packages.destroy', id)); };

const categoryColors: Record<string, string> = {
    cardiac: 'bg-red-50 text-red-700 ring-red-200', diabetes: 'bg-blue-50 text-blue-700 ring-blue-200',
    cancer: 'bg-purple-50 text-purple-700 ring-purple-200', maternity: 'bg-pink-50 text-pink-700 ring-pink-200',
    pediatric: 'bg-cyan-50 text-cyan-700 ring-cyan-200', executive: 'bg-indigo-50 text-indigo-700 ring-indigo-200',
    general: 'bg-gray-100 text-gray-700 ring-gray-200', other: 'bg-gray-100 text-gray-500 ring-gray-200',
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Health Packages" />
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 shadow-md">
                        <HeartPulse class="h-5 w-5 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Health Packages</h1>
                        <p class="text-sm text-gray-500">Manage diagnostic & health check packages</p>
                    </div>
                </div>
                <Link :href="route('admin.health-packages.create')" class="inline-flex items-center gap-2 rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-teal-700 transition">
                    <Plus class="h-4 w-4" />New Package
                </Link>
            </div>

            <div v-if="$page.props.flash?.success" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ $page.props.flash.success }}</div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <Input v-model="searchQuery" placeholder="Search packages..." class="w-56" @keyup.enter="applyFilters" />
                <select v-model="categoryFilter" @change="applyFilters" class="rounded-md border border-gray-300 px-3 py-2 text-sm capitalize shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    <option value="">All Categories</option>
                    <option v-for="c in categories" :key="c" :value="c" class="capitalize">{{ c }}</option>
                </select>
            </div>

            <!-- Package Grid -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="pkg in packages.data" :key="pkg.id" class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900">
                    <div class="h-36 overflow-hidden bg-gradient-to-br from-teal-50 to-emerald-50">
                        <img v-if="pkg.media?.thumbnail" :src="pkg.media.thumbnail" class="h-full w-full object-cover" />
                        <HeartPulse v-else class="m-auto mt-10 h-14 w-14 text-teal-100" />
                    </div>
                    <div class="p-4">
                        <div class="mb-2 flex items-start justify-between">
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ pkg.name }}</h3>
                            <span v-if="pkg.badge" class="ml-2 flex-shrink-0 rounded-full bg-teal-50 px-2 py-0.5 text-xs font-semibold text-teal-700 ring-1 ring-teal-200">{{ pkg.badge }}</span>
                        </div>
                        <div class="mb-3 flex items-center gap-2">
                            <span :class="[categoryColors[pkg.category] ?? 'bg-gray-100 text-gray-600 ring-gray-200', 'rounded-full px-2.5 py-0.5 text-xs font-medium capitalize ring-1']">{{ pkg.category }}</span>
                            <span :class="pkg.is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-gray-100 text-gray-500 ring-gray-200'" class="rounded-full px-2.5 py-0.5 text-xs font-medium ring-1">{{ pkg.is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-lg font-bold text-teal-700">৳{{ pkg.discounted_price ?? pkg.original_price }}</span>
                            <span v-if="pkg.discounted_price" class="text-sm text-gray-400 line-through">৳{{ pkg.original_price }}</span>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 px-4 py-3 dark:border-gray-700">
                        <div class="flex justify-between">
                            <Link :href="route('admin.health-packages.edit', pkg.id)" class="flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium text-teal-700 hover:bg-teal-50 transition">
                                <Pencil class="h-3.5 w-3.5" />Edit
                            </Link>
                            <button @click="deleteItem(pkg.id)" class="flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 transition">
                                <Trash2 class="h-3.5 w-3.5" />Delete
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="packages.data.length === 0" class="col-span-full rounded-xl border border-dashed border-gray-300 py-16 text-center text-sm text-gray-400">
                    No health packages yet.
                    <Link :href="route('admin.health-packages.create')" class="ml-1 text-teal-600 underline">Create one</Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
