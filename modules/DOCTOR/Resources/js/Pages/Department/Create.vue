<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, Upload } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import RichTextEditor from '../../Components/RichTextEditor.vue';

interface DepartmentParent {
    id: number;
    name: string;
}

interface DepartmentForm {
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

defineProps<{
    parentOptions: DepartmentParent[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Departments',
        href: '/departments',
    },
    {
        title: 'Create',
        href: '/departments/create',
    },
];

const slugWasEdited = ref(false);

const form = useForm<DepartmentForm>({
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

const toSlug = (value: string) =>
    value
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');

watch(
    () => form.name,
    (name) => {
        if (!slugWasEdited.value) {
            form.slug = toSlug(name);
        }
    },
);

const submit = () => {
    form.post(route('departments.store'), {
        forceFormData: true,
        preserveScroll: true,
    });
};

const updateFile = (event: Event, field: 'banner_image' | 'image' | 'icon_image') => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;

    form[field] = file;
    imagePreviews.value[field] = file ? URL.createObjectURL(file) : null;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Create Department" />

        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-normal text-foreground">Create department</h1>
                    <p class="text-sm text-muted-foreground">Add a department to the doctor module.</p>
                </div>

                <Button type="button" variant="outline" as-child>
                    <Link :href="route('departments.index')">
                        <ArrowLeft class="mr-2 size-4" />
                        Back
                    </Link>
                </Button>
            </div>

            <section class="max-w-3xl rounded-lg border bg-background p-5">
                <form class="grid gap-5" @submit.prevent="submit">
                    <div class="grid gap-2">
                        <Label for="department-name">Name</Label>
                        <Input id="department-name" v-model="form.name" :disabled="form.processing" placeholder="Cardiology" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="department-slug">Slug</Label>
                        <Input
                            id="department-slug"
                            v-model="form.slug"
                            :disabled="form.processing"
                            placeholder="cardiology"
                            @input="slugWasEdited = true"
                        />
                        <InputError :message="form.errors.slug" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="department-icon">Text icon</Label>
                        <Input id="department-icon" v-model="form.text_icon" :disabled="form.processing" placeholder="heart-pulse" />
                        <InputError :message="form.errors.text_icon" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="department-parent">Parent department</Label>
                        <select
                            id="department-parent"
                            v-model="form.parent_id"
                            :disabled="form.processing"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option :value="null">No parent</option>
                            <option v-for="department in parentOptions" :key="department.id" :value="department.id">
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
                            :disabled="form.processing"
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
                            :disabled="form.processing"
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
                                    id="department-banner-image"
                                    type="file"
                                    accept="image/*"
                                    :disabled="form.processing"
                                    @change="updateFile($event, 'banner_image')"
                                />
                                <Upload class="size-4 text-muted-foreground" />
                            </div>
                            <img v-if="imagePreviews.banner_image" :src="imagePreviews.banner_image" alt="" class="h-32 w-full rounded-md border object-cover" />
                            <InputError :message="form.errors.banner_image" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="department-image">Main image</Label>
                            <Input id="department-image" type="file" accept="image/*" :disabled="form.processing" @change="updateFile($event, 'image')" />
                            <img v-if="imagePreviews.image" :src="imagePreviews.image" alt="" class="h-32 w-full rounded-md border object-cover" />
                            <InputError :message="form.errors.image" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="department-icon-image">Icon image</Label>
                            <Input id="department-icon-image" type="file" accept="image/*" :disabled="form.processing" @change="updateFile($event, 'icon_image')" />
                            <img v-if="imagePreviews.icon_image" :src="imagePreviews.icon_image" alt="" class="size-20 rounded-md border object-cover" />
                            <InputError :message="form.errors.icon_image" />
                        </div>
                    </div>

                    <div v-if="form.progress" class="h-2 overflow-hidden rounded-full bg-muted">
                        <div class="h-full bg-primary transition-all" :style="{ width: `${form.progress.percentage}%` }" />
                    </div>

                    <div class="flex justify-end">
                        <Button type="submit" :disabled="form.processing">
                            <Save class="mr-2 size-4" />
                            Create department
                        </Button>
                    </div>
                </form>
            </section>
        </div>
    </AppLayout>
</template>
