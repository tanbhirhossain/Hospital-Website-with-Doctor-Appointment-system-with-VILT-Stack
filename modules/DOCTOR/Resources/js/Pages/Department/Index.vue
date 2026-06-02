<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { FolderTree, Pencil, Plus, Save, Trash2, Upload, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import RichTextEditor from '../../Components/RichTextEditor.vue';

interface DepartmentParent {
    id: number;
    name: string;
}

interface Department {
    id: number;
    name: string;
    slug: string;
    shortDesc: string | null;
    descriptions: string | null;
    text_icon: string | null;
    parent_id: number | null;
    parent: DepartmentParent | null;
    media: {
        banner_image: string | null;
        image: string | null;
        icon_image: string | null;
    };
    created_at: string | null;
}

interface DepartmentForm {
    _method: 'post' | 'put';
    name: string;
    slug: string;
    text_icon: string;
    parent_id: number | null;
    shortDesc: string;
    descriptions: string;
    banner_image: File | null;
    image: File | null;
    icon_image: File | null;
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
    can: {
        create: boolean;
        edit: boolean;
        delete: boolean;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Departments',
        href: '/departments',
    },
];

const editingDepartment = ref<Department | null>(null);
const slugWasEdited = ref(false);
const fileInputKey = ref(0);

const form = useForm<DepartmentForm>({
    _method: 'post',
    name: '',
    slug: '',
    text_icon: '',
    parent_id: null as number | null,
    shortDesc: '',
    descriptions: '',
    banner_image: null,
    image: null,
    icon_image: null,
});

const imagePreviews = ref({
    banner_image: null as string | null,
    image: null as string | null,
    icon_image: null as string | null,
});

const isEditing = computed(() => editingDepartment.value !== null);
const canSubmit = computed(() => (isEditing.value ? props.can.edit : props.can.create));
const availableParents = computed(() =>
    props.parentOptions.filter((department) => department.id !== editingDepartment.value?.id),
);

const toSlug = (value: string) =>
    value
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');

const stripHtml = (value: string | null) => {
    if (!value) {
        return '-';
    }

    return value.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim() || '-';
};

const resetUploadFields = () => {
    form.banner_image = null;
    form.image = null;
    form.icon_image = null;
    imagePreviews.value = {
        banner_image: null,
        image: null,
        icon_image: null,
    };
    fileInputKey.value += 1;
};

watch(
    () => form.name,
    (name) => {
        if (!slugWasEdited.value) {
            form.slug = toSlug(name);
        }
    },
);

const resetForm = () => {
    editingDepartment.value = null;
    slugWasEdited.value = false;
    form.reset();
    form.clearErrors();
    form._method = 'post';
    resetUploadFields();
};

const editDepartment = (department: Department) => {
    editingDepartment.value = department;
    slugWasEdited.value = true;
    form.name = department.name;
    form.slug = department.slug;
    form.text_icon = department.text_icon ?? '';
    form.parent_id = department.parent_id;
    form.shortDesc = department.shortDesc ?? '';
    form.descriptions = department.descriptions ?? '';
    form._method = 'put';
    resetUploadFields();
    imagePreviews.value = {
        banner_image: department.media.banner_image,
        image: department.media.image,
        icon_image: department.media.icon_image,
    };
    form.clearErrors();
};

const updateFile = (event: Event, field: 'banner_image' | 'image' | 'icon_image') => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;

    form[field] = file;
    imagePreviews.value[field] = file ? URL.createObjectURL(file) : editingDepartment.value?.media[field] ?? null;
};

const submit = () => {
    if (!canSubmit.value) {
        return;
    }

    if (editingDepartment.value) {
        form._method = 'put';
        form.post(route('departments.update', editingDepartment.value.id), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: resetForm,
        });

        return;
    }

    form._method = 'post';
    form.post(route('departments.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: resetForm,
    });
};

const deleteDepartment = (department: Department) => {
    if (!props.can.delete || !window.confirm(`Delete department "${department.name}"?`)) {
        return;
    }

    router.delete(route('departments.destroy', department.id), {
        preserveScroll: true,
        onSuccess: () => {
            if (editingDepartment.value?.id === department.id) {
                resetForm();
            }
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Departments" />

        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-normal text-foreground">Departments</h1>
                    <p class="text-sm text-muted-foreground">{{ departments.total }} total departments</p>
                </div>

                <Button v-if="isEditing" type="button" variant="outline" @click="resetForm">
                    <X class="mr-2 size-4" />
                    Cancel edit
                </Button>
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
                <section class="overflow-hidden rounded-lg border bg-background">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[860px] text-sm">
                            <thead class="border-b bg-muted/50 text-left text-xs font-medium uppercase text-muted-foreground">
                                <tr>
                                    <th class="px-4 py-3">Department</th>
                                    <th class="px-4 py-3">Parent</th>
                                    <th class="px-4 py-3">Short description</th>
                                    <th class="px-4 py-3">Created</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="department in departments.data" :key="department.id" class="hover:bg-muted/30">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex size-9 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                                                <img
                                                    v-if="department.media.icon_image"
                                                    :src="department.media.icon_image"
                                                    :alt="department.name"
                                                    class="size-9 rounded-md object-cover"
                                                />
                                                <span v-else-if="department.text_icon" class="text-sm font-semibold">{{ department.text_icon }}</span>
                                                <FolderTree v-else class="size-4" />
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-medium text-foreground">{{ department.name }}</div>
                                                <div class="truncate text-xs text-muted-foreground">{{ department.slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-muted-foreground">{{ department.parent?.name ?? '-' }}</td>
                                    <td class="max-w-sm truncate px-4 py-4 text-muted-foreground">
                                        {{ stripHtml(department.shortDesc) }}
                                    </td>
                                    <td class="px-4 py-4 text-muted-foreground">{{ department.created_at ?? '-' }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex justify-end gap-2">
                                            <Button
                                                type="button"
                                                variant="outline"
                                                size="sm"
                                                :disabled="!can.edit"
                                                title="Edit department"
                                                @click="editDepartment(department)"
                                            >
                                                <Pencil class="size-4" />
                                            </Button>
                                            <Button
                                                type="button"
                                                variant="destructive"
                                                size="sm"
                                                :disabled="!can.delete"
                                                title="Delete department"
                                                @click="deleteDepartment(department)"
                                            >
                                                <Trash2 class="size-4" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="departments.data.length === 0">
                                    <td colspan="5" class="px-4 py-12 text-center text-muted-foreground">No departments found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col gap-3 border-t px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-muted-foreground">
                            Showing {{ departments.from ?? 0 }} to {{ departments.to ?? 0 }} of {{ departments.total }}
                        </p>
                        <div class="flex flex-wrap gap-1">
                            <template v-for="link in departments.links" :key="link.label">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    preserve-scroll
                                    :class="[
                                        'rounded-md border px-3 py-1.5 text-sm',
                                        link.active ? 'bg-primary text-primary-foreground' : 'bg-background hover:bg-muted',
                                    ]"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    class="rounded-md border px-3 py-1.5 text-sm text-muted-foreground opacity-50"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </section>

                <section class="rounded-lg border bg-background p-5">
                    <form class="flex flex-col gap-5" @submit.prevent="submit">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold tracking-normal text-foreground">
                                    {{ isEditing ? 'Edit department' : 'Create department' }}
                                </h2>
                                <p class="text-sm text-muted-foreground">
                                    {{ isEditing ? editingDepartment?.name : 'Add a new clinical department' }}
                                </p>
                            </div>
                            <div class="rounded-md bg-primary/10 p-2 text-primary">
                                <Plus v-if="!isEditing" class="size-5" />
                                <Pencil v-else class="size-5" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="department-name">Name</Label>
                            <Input id="department-name" v-model="form.name" :disabled="!canSubmit || form.processing" placeholder="Cardiology" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="department-slug">Slug</Label>
                            <Input
                                id="department-slug"
                                v-model="form.slug"
                                :disabled="!canSubmit || form.processing"
                                placeholder="cardiology"
                                @input="slugWasEdited = true"
                            />
                            <InputError :message="form.errors.slug" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="department-icon">Text icon</Label>
                            <Input
                                id="department-icon"
                                v-model="form.text_icon"
                                :disabled="!canSubmit || form.processing"
                                placeholder="heart-pulse"
                            />
                            <InputError :message="form.errors.text_icon" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="department-parent">Parent department</Label>
                            <select
                                id="department-parent"
                                v-model="form.parent_id"
                                :disabled="!canSubmit || form.processing"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option :value="null">No parent</option>
                                <option v-for="department in availableParents" :key="department.id" :value="department.id">
                                    {{ department.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.parent_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="department-short-description">Short description</Label>
                            <RichTextEditor
                                id="department-short-description"
                                v-model="form.shortDesc"
                                :disabled="!canSubmit || form.processing"
                                min-height-class="min-h-24"
                                placeholder="Brief overview shown in listings"
                            />
                            <InputError :message="form.errors.shortDesc" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="department-descriptions">Description</Label>
                            <RichTextEditor
                                id="department-descriptions"
                                v-model="form.descriptions"
                                :disabled="!canSubmit || form.processing"
                                min-height-class="min-h-40"
                                placeholder="Detailed department information"
                            />
                            <InputError :message="form.errors.descriptions" />
                        </div>

                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <Label for="department-banner-image">Banner image</Label>
                                <div class="flex items-center gap-3">
                                    <Input
                                        :key="`banner-${fileInputKey}`"
                                        id="department-banner-image"
                                        type="file"
                                        accept="image/*"
                                        :disabled="!canSubmit || form.processing"
                                        @change="updateFile($event, 'banner_image')"
                                    />
                                    <Upload class="size-4 text-muted-foreground" />
                                </div>
                                <img
                                    v-if="imagePreviews.banner_image"
                                    :src="imagePreviews.banner_image"
                                    alt=""
                                    class="h-28 w-full rounded-md border object-cover"
                                />
                                <InputError :message="form.errors.banner_image" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="department-image">Main image</Label>
                                <Input
                                    :key="`image-${fileInputKey}`"
                                    id="department-image"
                                    type="file"
                                    accept="image/*"
                                    :disabled="!canSubmit || form.processing"
                                    @change="updateFile($event, 'image')"
                                />
                                <img
                                    v-if="imagePreviews.image"
                                    :src="imagePreviews.image"
                                    alt=""
                                    class="h-28 w-full rounded-md border object-cover"
                                />
                                <InputError :message="form.errors.image" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="department-icon-image">Icon image</Label>
                                <Input
                                    :key="`icon-${fileInputKey}`"
                                    id="department-icon-image"
                                    type="file"
                                    accept="image/*"
                                    :disabled="!canSubmit || form.processing"
                                    @change="updateFile($event, 'icon_image')"
                                />
                                <img
                                    v-if="imagePreviews.icon_image"
                                    :src="imagePreviews.icon_image"
                                    alt=""
                                    class="size-20 rounded-md border object-cover"
                                />
                                <InputError :message="form.errors.icon_image" />
                            </div>
                        </div>

                        <div v-if="form.progress" class="h-2 overflow-hidden rounded-full bg-muted">
                            <div class="h-full bg-primary transition-all" :style="{ width: `${form.progress.percentage}%` }" />
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <Button v-if="isEditing" type="button" variant="outline" :disabled="form.processing" @click="resetForm">
                                <X class="mr-2 size-4" />
                                Cancel
                            </Button>
                            <Button type="submit" :disabled="!canSubmit || form.processing">
                                <Save class="mr-2 size-4" />
                                {{ isEditing ? 'Update department' : 'Create department' }}
                            </Button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
</script>