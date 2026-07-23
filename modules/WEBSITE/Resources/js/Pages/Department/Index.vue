<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Building2,
    Pencil,
    Plus,
    Search,
    Trash2,
    Image,
    X,
    FolderTree,
    MoreVertical,
    ToggleLeft,
    ToggleRight,
    Tag,
    Hash,
    Palette,
} from 'lucide-vue-next';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import DeleteDialog from '../../Components/DeleteDialog.vue';

interface DepartmentParent {
    id: number;
    name: string;
}

interface Department {
    id: number;
    name: string;
    slug: string;
    tagline: string | null;
    shortDesc: string | null;
    descriptions: string | null;
    serial: number | null;
    is_active: boolean;
    text_icon: string | null;
    'bg-color': string | null;
    'text-color': string | null;
    color: string | null;
    custom_css: string | null;
    parent_id: number | null;
    parent: DepartmentParent | null;
    media: {
        banner_image: string | null;
        image: string | null;
        icon_image: string | null;
    };
    meta_title: string | null;
    meta_description: string | null;
    canonical_url: string | null;
    og_title: string | null;
    og_description: string | null;
    indexable: boolean;
    created_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedDepartments {
    data: Department[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

interface Props {
    departments: PaginatedDepartments;
    parentOptions: DepartmentParent[];
    filters: {
        search: string;
        parent_id: number | null;
        is_active: boolean | null;
    };
    can: {
        create: boolean;
        edit: boolean;
        delete: boolean;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Departments', href: '/departments' },
];

const searchQuery = ref(props.filters.search);
const selectedParent = ref<string>(props.filters.parent_id?.toString() ?? '');
const selectedStatus = ref<string>(props.filters.is_active !== null && props.filters.is_active !== undefined ? String(props.filters.is_active) : '');

// Delete dialog
const showDeleteDialog = ref(false);
const departmentToDelete = ref<Department | null>(null);
const isDeleting = ref(false);

// Action dropdown
const openMenuId = ref<number | null>(null);

let debounceTimer: ReturnType<typeof setTimeout>;

watch(searchQuery, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => applyFilters(), 300);
});

const handleClickOutside = () => { openMenuId.value = null; };
onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));

const applyFilters = () => {
    router.get(
        route('admin.departments.index'),
        {
            search: searchQuery.value || undefined,
            parent_id: selectedParent.value || undefined,
            is_active: selectedStatus.value !== '' ? Number(selectedStatus.value) : undefined,
        },
        { preserveState: true, preserveScroll: true },
    );
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedParent.value = '';
    selectedStatus.value = '';
    router.get(route('admin.departments.index'), {}, { preserveState: true, preserveScroll: true });
};

const hasActiveFilters = () => searchQuery.value || selectedParent.value || selectedStatus.value !== '';

const stripHtml = (value: string | null) => {
    if (!value) return '';
    return value.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
};

const toggleMenu = (e: Event, id: number) => {
    e.stopPropagation();
    openMenuId.value = openMenuId.value === id ? null : id;
};

const confirmDelete = (department: Department) => {
    departmentToDelete.value = department;
    showDeleteDialog.value = true;
};

const handleDelete = () => {
    if (!departmentToDelete.value || !props.can.delete) return;
    isDeleting.value = true;
    router.delete(route('admin.departments.destroy', departmentToDelete.value.id), {
        preserveScroll: true,
        onFinish: () => {
            isDeleting.value = false;
            showDeleteDialog.value = false;
            departmentToDelete.value = null;
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Departments" />

        <div class="min-h-screen bg-[#f8f9fb] p-4 md:p-8">
            <div class="mx-auto max-w-7xl space-y-6">

                <!-- Page Header -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Departments</h1>
                        <p class="mt-1 text-base text-gray-500">{{ departments.total }} departments registered</p>
                    </div>
                    <Button v-if="can.create" as-child class="shrink-0 rounded-xl bg-gray-900 px-6 py-3 text-white shadow-lg shadow-gray-900/20 hover:bg-gray-800">
                        <Link :href="route('admin.departments.create')">
                            <Plus class="mr-2 size-4" />
                            Add Department
                        </Link>
                    </Button>
                </div>

                <!-- Filters -->
                <div class="flex flex-col gap-3 rounded-2xl bg-white p-4 shadow-sm sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <Search class="absolute left-4 top-1/2 size-4 -translate-y-1/2 text-gray-400" />
                        <input
                            v-model="searchQuery"
                            placeholder="Search by name or slug..."
                            class="h-11 w-full rounded-xl border-0 bg-gray-50 pl-11 pr-4 text-sm text-gray-900 placeholder-gray-400 ring-1 ring-gray-200 transition focus:bg-white focus:ring-2 focus:ring-gray-900 focus:outline-none"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <select
                            v-model="selectedParent"
                            class="h-11 rounded-xl border-0 bg-gray-50 px-4 text-sm text-gray-700 ring-1 ring-gray-200 focus:ring-2 focus:ring-gray-900 focus:outline-none"
                            @change="applyFilters"
                        >
                            <option value="">All Parents</option>
                            <option v-for="parent in parentOptions" :key="parent.id" :value="parent.id">{{ parent.name }}</option>
                        </select>
                        <select
                            v-model="selectedStatus"
                            class="h-11 rounded-xl border-0 bg-gray-50 px-4 text-sm text-gray-700 ring-1 ring-gray-200 focus:ring-2 focus:ring-gray-900 focus:outline-none"
                            @change="applyFilters"
                        >
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <button
                            v-if="hasActiveFilters()"
                            class="flex h-11 items-center gap-1.5 rounded-xl px-4 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                            @click="clearFilters"
                        >
                            <X class="size-4" />
                            Clear
                        </button>
                    </div>
                </div>

                <!-- Department Cards -->
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="dept in departments.data"
                        :key="dept.id"
                        class="group relative overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 transition-all duration-200 hover:shadow-md hover:ring-gray-200"
                    >
                        <!-- Banner -->
                        <div class="relative h-28 overflow-hidden bg-gray-100">
                            <img
                                v-if="dept.media.banner_image"
                                :src="dept.media.banner_image"
                                :alt="dept.name"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                            <div v-else class="flex h-full items-center justify-center">
                                <Building2 class="size-8 text-gray-300" />
                            </div>
                            <!-- Overlay gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent" />

                            <!-- Status badge -->
                            <div class="absolute left-3 top-3">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold shadow-sm"
                                    :class="dept.is_active
                                        ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200'
                                        : 'bg-gray-100 text-gray-500 ring-1 ring-gray-200'"
                                >
                                    <span class="size-1.5 rounded-full" :class="dept.is_active ? 'bg-emerald-500' : 'bg-gray-400'" />
                                    {{ dept.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <!-- Actions menu -->
                            <div class="absolute right-3 top-3" @click.stop>
                                <button
                                    class="flex size-8 items-center justify-center rounded-lg bg-white/90 text-gray-500 shadow-sm backdrop-blur-sm transition hover:bg-white hover:text-gray-900"
                                    @click="toggleMenu($event, dept.id)"
                                >
                                    <MoreVertical class="size-4" />
                                </button>
                                <Transition name="menu">
                                    <div
                                        v-if="openMenuId === dept.id"
                                        class="absolute right-0 top-10 z-30 min-w-[170px] overflow-hidden rounded-xl bg-white py-1 shadow-xl ring-1 ring-gray-100"
                                    >
                                        <Link
                                            v-if="can.edit"
                                            :href="route('admin.departments.edit', dept.id)"
                                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 transition hover:bg-gray-50"
                                        >
                                            <Pencil class="size-3.5" />
                                            Edit
                                        </Link>
                                        <button
                                            v-if="can.delete"
                                            class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 transition hover:bg-red-50"
                                            @click="confirmDelete(dept); openMenuId = null"
                                        >
                                            <Trash2 class="size-3.5" />
                                            Delete
                                        </button>
                                    </div>
                                </Transition>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="relative px-5 pb-5 pt-2">
                            <!-- Icon avatar -->
                            <div class="-mt-10 mb-3 flex items-end justify-between">
                                <div class="flex size-14 items-center justify-center rounded-2xl bg-white shadow-md ring-1 ring-gray-100">
                                    <div
                                        class="flex size-11 items-center justify-center rounded-xl"
                                        :style="dept['bg-color'] ? { backgroundColor: dept['bg-color'] + '18' } : {}"
                                    >
                                        <img v-if="dept.media.icon_image" :src="dept.media.icon_image" :alt="dept.name" class="size-11 rounded-xl object-cover" />
                                        <span v-else-if="dept.text_icon" class="text-lg font-bold" :style="dept['text-color'] ? { color: dept['text-color'] } : {}">{{ dept.text_icon }}</span>
                                        <Building2 v-else class="size-5" :style="dept.color ? { color: dept.color } : { color: '#6b7280' }" />
                                    </div>
                                </div>
                                <div v-if="dept.serial" class="flex size-8 items-center justify-center rounded-lg bg-gray-100 text-xs font-bold text-gray-500">
                                    #{{ dept.serial }}
                                </div>
                            </div>

                            <!-- Name & Tagline -->
                            <h3 class="text-[15px] font-bold text-gray-900">{{ dept.name }}</h3>
                            <p v-if="dept.tagline" class="mt-0.5 text-xs font-medium text-gray-400 italic">{{ dept.tagline }}</p>
                            <p class="mt-0.5 font-mono text-[11px] text-gray-400">{{ dept.slug }}</p>

                            <!-- Short desc -->
                            <p v-if="stripHtml(dept.shortDesc)" class="mt-2 line-clamp-2 text-[13px] leading-relaxed text-gray-500">
                                {{ stripHtml(dept.shortDesc) }}
                            </p>

                            <!-- Meta row -->
                            <div class="mt-4 flex items-center gap-2 border-t border-gray-100 pt-3">
                                <span v-if="dept.parent" class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-2 py-0.5 text-[11px] font-semibold text-gray-600">
                                    {{ dept.parent.name }}
                                </span>
                                <span v-else class="inline-flex items-center gap-1 rounded-lg bg-gray-50 px-2 py-0.5 text-[11px] text-gray-400">
                                    Root
                                </span>

                                <span class="text-[11px] text-gray-400">{{ dept.created_at ?? '' }}</span>

                                <div class="ml-auto flex gap-1">
                                    <span
                                        v-for="(url, key) in dept.media"
                                        :key="key"
                                        class="flex size-5 items-center justify-center rounded-md"
                                        :class="url ? 'bg-emerald-50 text-emerald-500' : 'bg-gray-100 text-gray-300'"
                                    >
                                        <Image class="size-2.5" />
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="departments.data.length === 0"
                        class="col-span-full flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-white py-24"
                    >
                        <div class="flex size-16 items-center justify-center rounded-2xl bg-gray-100">
                            <FolderTree class="size-7 text-gray-400" />
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-gray-900">No departments found</h3>
                        <p class="mt-1 text-sm text-gray-500">Create your first department to get started.</p>
                        <Button v-if="can.create" as-child class="mt-6 rounded-xl bg-gray-900 px-6 py-3 text-white shadow-lg shadow-gray-900/20 hover:bg-gray-800">
                            <Link :href="route('admin.departments.create')">
                                <Plus class="mr-2 size-4" />
                                Add Department
                            </Link>
                        </Button>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="departments.total > 0" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-500">
                        Showing <span class="font-semibold text-gray-900">{{ departments.from ?? 0 }}</span>–<span class="font-semibold text-gray-900">{{ departments.to ?? 0 }}</span> of <span class="font-semibold text-gray-900">{{ departments.total }}</span>
                    </p>
                    <div class="flex flex-wrap gap-1.5">
                        <template v-for="link in departments.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                preserve-scroll
                                :class="[
                                    'inline-flex h-9 min-w-[36px] items-center justify-center rounded-xl px-3 text-sm font-medium transition',
                                    link.active
                                        ? 'bg-gray-900 text-white shadow-md shadow-gray-900/20'
                                        : 'bg-white text-gray-700 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50',
                                ]"
                                v-html="link.label"
                            />
                            <span
                                v-else
                                class="inline-flex h-9 min-w-[36px] items-center justify-center rounded-xl px-3 text-sm text-gray-300"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Dialog -->
        <DeleteDialog
            :show="showDeleteDialog"
            title="Delete Department"
            description="This will permanently remove the department and all its associated data. This action cannot be undone."
            :item-name="departmentToDelete?.name"
            :processing="isDeleting"
            @confirm="handleDelete"
            @cancel="showDeleteDialog = false"
        />
    </AppLayout>
</template>

<style scoped>
.menu-enter-active,
.menu-leave-active {
    transition: all 0.15s ease;
}
.menu-enter-from,
.menu-leave-to {
    opacity: 0;
    transform: translateY(-4px) scale(0.97);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
