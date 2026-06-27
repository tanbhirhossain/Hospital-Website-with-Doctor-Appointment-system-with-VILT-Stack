<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Save,
    Upload,
    Type,
    FileText,
    Building2,
    X,
    ImagePlus,
    Paintbrush,
    Search,
    Code2,
    Settings2,
} from 'lucide-vue-next';
import { ref, watch } from 'vue';
import RichTextEditor from '../../Components/RichTextEditor.vue';

interface DepartmentParent {
    id: number;
    name: string;
}

interface DepartmentForm {
    name: string;
    slug: string;
    tagline: string;
    serial: number | null;
    is_active: boolean;
    text_icon: string;
    'bg-color': string;
    'text-color': string;
    color: string;
    custom_css: string;
    parent_id: number | null;
    shortDesc: string;
    descriptions: string;
    banner_image: File | null;
    image: File | null;
    icon_image: File | null;
    meta_title: string;
    meta_description: string;
    canonical_url: string;
    og_title: string;
    og_description: string;
    indexable: boolean;
}

defineProps<{
    parentOptions: DepartmentParent[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Departments', href: '/departments' },
    { title: 'Create', href: '/departments/create' },
];

const slugWasEdited = ref(false);
const showSeo = ref(false);
const showCustomCss = ref(false);

const form = useForm<DepartmentForm>({
    name: '',
    slug: '',
    tagline: '',
    serial: null as number | null,
    is_active: true,
    text_icon: '',
    'bg-color': '',
    'text-color': '',
    color: '',
    custom_css: '',
    parent_id: null as number | null,
    shortDesc: '',
    descriptions: '',
    banner_image: null,
    image: null,
    icon_image: null,
    meta_title: '',
    meta_description: '',
    canonical_url: '',
    og_title: '',
    og_description: '',
    indexable: true,
});

const imagePreviews = ref({
    banner_image: null as string | null,
    image: null as string | null,
    icon_image: null as string | null,
});

const toSlug = (value: string) =>
    value.toLowerCase().trim().replace(/[^\w\s-]/g, '').replace(/[\s_-]+/g, '-').replace(/^-+|-+$/g, '');

watch(() => form.name, (name) => {
    if (!slugWasEdited.value) form.slug = toSlug(name);
});

const submit = () => {
    form.post(route('admin.departments.store'), { forceFormData: true, preserveScroll: true });
};

const updateFile = (event: Event, field: 'banner_image' | 'image' | 'icon_image') => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;
    form[field] = file;
    imagePreviews.value[field] = file ? URL.createObjectURL(file) : null;
};

const clearFile = (field: 'banner_image' | 'image' | 'icon_image') => {
    form[field] = null;
    imagePreviews.value[field] = null;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Create Department" />

        <div class="min-h-screen bg-[#f8f9fb] p-4 md:p-8">
            <div class="mx-auto max-w-5xl space-y-6">

                <!-- Header -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center gap-1.5 text-sm text-gray-400">
                            <Link :href="route('admin.departments.index')" class="transition hover:text-gray-700">Departments</Link>
                            <span class="text-gray-300">/</span>
                            <span class="font-medium text-gray-700">Create</span>
                        </div>
                        <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-gray-900">New Department</h1>
                        <p class="mt-1 text-base text-gray-500">Fill in the details to add a new department</p>
                    </div>
                    <Button type="button" variant="outline" as-child class="shrink-0 rounded-xl border-gray-200 bg-white shadow-sm hover:bg-gray-50">
                        <Link :href="route('admin.departments.index')">
                            <ArrowLeft class="mr-2 size-4" />
                            Back
                        </Link>
                    </Button>
                </div>

                <form class="space-y-6" @submit.prevent="submit">
                    <!-- ═══ General Information ═══ -->
                    <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                            <div class="flex size-9 items-center justify-center rounded-xl bg-gray-900 text-white">
                                <Type class="size-4" />
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-gray-900">General Information</h2>
                                <p class="text-xs text-gray-400">Core details about this department</p>
                            </div>
                        </div>
                        <div class="space-y-5 p-6">
                            <!-- Name + Slug -->
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="space-y-1.5">
                                    <Label for="name" class="text-sm font-semibold text-gray-700">Name <span class="text-red-400">*</span></Label>
                                    <Input id="name" v-model="form.name" :disabled="form.processing" placeholder="e.g. Cardiology" class="h-11 rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white" />
                                    <InputError :message="form.errors.name" />
                                </div>
                                <div class="space-y-1.5">
                                    <Label for="slug" class="text-sm font-semibold text-gray-700">Slug</Label>
                                    <Input id="slug" v-model="form.slug" :disabled="form.processing" placeholder="auto-generated" class="h-11 rounded-xl border-gray-200 bg-gray-50/50 font-mono text-sm focus:bg-white" @input="slugWasEdited = true" />
                                    <InputError :message="form.errors.slug" />
                                </div>
                            </div>

                            <!-- Tagline + Serial -->
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="space-y-1.5">
                                    <Label for="tagline" class="text-sm font-semibold text-gray-700">Tagline</Label>
                                    <Input id="tagline" v-model="form.tagline" :disabled="form.processing" placeholder="A short catchy phrase" class="h-11 rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white" />
                                    <InputError :message="form.errors.tagline" />
                                </div>
                                <div class="space-y-1.5">
                                    <Label for="serial" class="text-sm font-semibold text-gray-700">Serial / Order</Label>
                                    <Input id="serial" v-model.number="form.serial" type="number" :disabled="form.processing" placeholder="1" class="h-11 rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white" />
                                    <InputError :message="form.errors.serial" />
                                </div>
                            </div>

                            <!-- Parent + Active -->
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="space-y-1.5">
                                    <Label for="parent" class="text-sm font-semibold text-gray-700">Parent Department</Label>
                                    <select
                                        id="parent"
                                        v-model="form.parent_id"
                                        :disabled="form.processing"
                                        class="flex h-11 w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 text-sm text-gray-700 focus:border-gray-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gray-900 disabled:opacity-50"
                                    >
                                        <option :value="null">No parent (root level)</option>
                                        <option v-for="p in parentOptions" :key="p.id" :value="p.id">{{ p.name }}</option>
                                    </select>
                                    <InputError :message="form.errors.parent_id" />
                                </div>
                                <div class="space-y-1.5">
                                    <Label class="text-sm font-semibold text-gray-700">Status</Label>
                                    <div class="flex h-11 items-center">
                                        <button
                                            type="button"
                                            class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
                                            :class="form.is_active ? 'bg-gray-900' : 'bg-gray-200'"
                                            @click="form.is_active = !form.is_active"
                                        >
                                            <span
                                                class="pointer-events-none inline-flex size-6 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-gray-200 transition-transform duration-200"
                                                :class="form.is_active ? 'translate-x-5' : 'translate-x-0'"
                                            />
                                        </button>
                                        <span class="ml-3 text-sm font-medium" :class="form.is_active ? 'text-gray-900' : 'text-gray-400'">
                                            {{ form.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <InputError :message="form.errors.is_active" />
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- ═══ Content ═══ -->
                    <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                            <div class="flex size-9 items-center justify-center rounded-xl bg-violet-600 text-white">
                                <FileText class="size-4" />
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-gray-900">Content</h2>
                                <p class="text-xs text-gray-400">Department descriptions and information</p>
                            </div>
                        </div>
                        <div class="space-y-5 p-6">
                            <div class="space-y-1.5">
                                <Label for="shortDesc" class="text-sm font-semibold text-gray-700">Short Description</Label>
                                <RichTextEditor id="shortDesc" v-model="form.shortDesc" :disabled="form.processing" min-height-class="min-h-24" placeholder="Brief overview shown in listings..." />
                                <InputError :message="form.errors.shortDesc" />
                            </div>
                            <div class="space-y-1.5">
                                <Label for="descriptions" class="text-sm font-semibold text-gray-700">Full Description</Label>
                                <RichTextEditor id="descriptions" v-model="form.descriptions" :disabled="form.processing" min-height-class="min-h-40" placeholder="Detailed department information..." />
                                <InputError :message="form.errors.descriptions" />
                            </div>
                        </div>
                    </section>

                    <!-- ═══ Styling ═══ -->
                    <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                            <div class="flex size-9 items-center justify-center rounded-xl bg-pink-600 text-white">
                                <Paintbrush class="size-4" />
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-gray-900">Styling</h2>
                                <p class="text-xs text-gray-400">Visual customization for this department</p>
                            </div>
                        </div>
                        <div class="space-y-5 p-6">
                            <!-- Text Icon + Color Pickers row -->
                            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                                <div class="space-y-1.5">
                                    <Label for="text_icon" class="text-sm font-semibold text-gray-700">Text Icon</Label>
                                    <Input id="text_icon" v-model="form.text_icon" :disabled="form.processing" placeholder="🫀 or icon-name" class="h-11 rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white" />
                                    <InputError :message="form.errors.text_icon" />
                                </div>
                                <div class="space-y-1.5">
                                    <Label class="text-sm font-semibold text-gray-700">Background Color</Label>
                                    <div class="flex h-11 items-center gap-2 rounded-xl border border-gray-200 bg-gray-50/50 px-3">
                                        <input type="color" v-model="form['bg-color']" class="size-7 cursor-pointer rounded-lg border border-gray-200 p-0.5" />
                                        <input type="text" v-model="form['bg-color']" placeholder="#f3f4f6" class="flex-1 border-0 bg-transparent text-sm text-gray-700 focus:outline-none" />
                                    </div>
                                    <InputError :message="form.errors['bg-color']" />
                                </div>
                                <div class="space-y-1.5">
                                    <Label class="text-sm font-semibold text-gray-700">Text Color</Label>
                                    <div class="flex h-11 items-center gap-2 rounded-xl border border-gray-200 bg-gray-50/50 px-3">
                                        <input type="color" v-model="form['text-color']" class="size-7 cursor-pointer rounded-lg border border-gray-200 p-0.5" />
                                        <input type="text" v-model="form['text-color']" placeholder="#1f2937" class="flex-1 border-0 bg-transparent text-sm text-gray-700 focus:outline-none" />
                                    </div>
                                    <InputError :message="form.errors['text-color']" />
                                </div>
                                <div class="space-y-1.5">
                                    <Label class="text-sm font-semibold text-gray-700">Primary Color</Label>
                                    <div class="flex h-11 items-center gap-2 rounded-xl border border-gray-200 bg-gray-50/50 px-3">
                                        <input type="color" v-model="form.color" class="size-7 cursor-pointer rounded-lg border border-gray-200 p-0.5" />
                                        <input type="text" v-model="form.color" placeholder="#3b82f6" class="flex-1 border-0 bg-transparent text-sm text-gray-700 focus:outline-none" />
                                    </div>
                                    <InputError :message="form.errors.color" />
                                </div>
                            </div>

                            <!-- Custom CSS toggle -->
                            <div>
                                <button type="button" class="flex items-center gap-2 text-sm font-semibold text-gray-700 transition hover:text-gray-900" @click="showCustomCss = !showCustomCss">
                                    <Code2 class="size-4" :class="showCustomCss ? 'text-gray-900' : 'text-gray-400'" />
                                    Custom CSS
                                    <span class="text-[10px] font-medium text-gray-400" :class="showCustomCss && 'text-gray-600'">{{ showCustomCss ? 'Hide' : 'Show' }}</span>
                                </button>
                                <Transition name="slide">
                                    <div v-if="showCustomCss" class="mt-3 space-y-1.5">
                                        <textarea
                                            v-model="form.custom_css"
                                            rows="4"
                                            placeholder=".department-card { border-radius: 16px; }"
                                            class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 font-mono text-sm text-gray-700 focus:border-gray-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gray-900"
                                        />
                                        <InputError :message="form.errors.custom_css" />
                                    </div>
                                </Transition>
                            </div>

                            <!-- Color preview -->
                            <div v-if="form['bg-color'] || form['text-color'] || form.color" class="rounded-xl border border-gray-200 p-4">
                                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Preview</p>
                                <div
                                    class="flex h-20 items-center justify-center rounded-xl transition-all"
                                    :style="{
                                        backgroundColor: form['bg-color'] || '#ffffff',
                                        color: form['text-color'] || '#1f2937',
                                    }"
                                >
                                    <div class="text-center">
                                        <span v-if="form.text_icon" class="text-2xl">{{ form.text_icon }}</span>
                                        <p class="mt-1 text-sm font-bold">{{ form.name || 'Department Name' }}</p>
                                        <p v-if="form.tagline" class="text-xs opacity-70">{{ form.tagline }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- ═══ Media ═══ -->
                    <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
                        <div class="flex items-center gap-3 border-b border-gray-100 px-6 py-4">
                            <div class="flex size-9 items-center justify-center rounded-xl bg-emerald-600 text-white">
                                <ImagePlus class="size-4" />
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-gray-900">Media</h2>
                                <p class="text-xs text-gray-400">Upload images and icons</p>
                            </div>
                        </div>
                        <div class="space-y-6 p-6">
                            <!-- Banner -->
                            <div class="space-y-2">
                                <Label class="text-sm font-semibold text-gray-700">Banner Image</Label>
                                <div
                                    class="group relative flex min-h-[150px] cursor-pointer items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50 transition hover:border-gray-400 hover:bg-gray-50"
                                    @click="($refs.bannerInput as HTMLInputElement)?.click()"
                                >
                                    <img v-if="imagePreviews.banner_image" :src="imagePreviews.banner_image" alt="Banner" class="absolute inset-0 h-full w-full object-cover" />
                                    <div v-if="imagePreviews.banner_image" class="absolute inset-0 flex items-center justify-center bg-black/0 opacity-0 transition group-hover:bg-black/30 group-hover:opacity-100">
                                        <button type="button" class="rounded-full bg-white/90 p-2.5 text-gray-700 shadow-lg transition hover:bg-red-500 hover:text-white" @click.stop="clearFile('banner_image')">
                                            <X class="size-4" />
                                        </button>
                                    </div>
                                    <div v-if="!imagePreviews.banner_image" class="flex flex-col items-center gap-2 py-4">
                                        <div class="flex size-12 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
                                            <Upload class="size-5 text-gray-400" />
                                        </div>
                                        <div class="text-center">
                                            <p class="text-sm font-semibold text-gray-500">Click to upload</p>
                                            <p class="text-[11px] text-gray-400">1200 × 400px recommended • JPG, PNG, WebP</p>
                                        </div>
                                    </div>
                                </div>
                                <input ref="bannerInput" type="file" accept="image/*" class="hidden" :disabled="form.processing" @change="updateFile($event, 'banner_image')" />
                                <InputError :message="form.errors.banner_image" />
                            </div>

                            <!-- Image + Icon row -->
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <Label class="text-sm font-semibold text-gray-700">Main Image</Label>
                                    <div
                                        class="group relative flex min-h-[130px] cursor-pointer items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50 transition hover:border-gray-400 hover:bg-gray-50"
                                        @click="($refs.mainInput as HTMLInputElement)?.click()"
                                    >
                                        <img v-if="imagePreviews.image" :src="imagePreviews.image" alt="Main" class="absolute inset-0 h-full w-full object-cover" />
                                        <div v-if="imagePreviews.image" class="absolute inset-0 flex items-center justify-center bg-black/0 opacity-0 transition group-hover:bg-black/30 group-hover:opacity-100">
                                            <button type="button" class="rounded-full bg-white/90 p-2.5 text-gray-700 shadow-lg transition hover:bg-red-500 hover:text-white" @click.stop="clearFile('image')">
                                                <X class="size-4" />
                                            </button>
                                        </div>
                                        <div v-if="!imagePreviews.image" class="flex flex-col items-center gap-2 py-4">
                                            <div class="flex size-10 items-center justify-center rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                                                <Upload class="size-4 text-gray-400" />
                                            </div>
                                            <p class="text-[11px] text-gray-400">600 × 400px</p>
                                        </div>
                                    </div>
                                    <input ref="mainInput" type="file" accept="image/*" class="hidden" :disabled="form.processing" @change="updateFile($event, 'image')" />
                                    <InputError :message="form.errors.image" />
                                </div>

                                <div class="space-y-2">
                                    <Label class="text-sm font-semibold text-gray-700">Icon Image</Label>
                                    <div
                                        class="group relative flex size-[130px] cursor-pointer items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50 transition hover:border-gray-400 hover:bg-gray-50"
                                        @click="($refs.iconInput as HTMLInputElement)?.click()"
                                    >
                                        <img v-if="imagePreviews.icon_image" :src="imagePreviews.icon_image" alt="Icon" class="absolute inset-0 size-full object-cover" />
                                        <div v-if="imagePreviews.icon_image" class="absolute inset-0 flex items-center justify-center bg-black/0 opacity-0 transition group-hover:bg-black/30 group-hover:opacity-100">
                                            <button type="button" class="rounded-full bg-white/90 p-2 text-gray-700 shadow-lg transition hover:bg-red-500 hover:text-white" @click.stop="clearFile('icon_image')">
                                                <X class="size-3.5" />
                                            </button>
                                        </div>
                                        <div v-if="!imagePreviews.icon_image" class="flex flex-col items-center gap-1.5">
                                            <div class="flex size-9 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-gray-100">
                                                <Upload class="size-3.5 text-gray-400" />
                                            </div>
                                            <p class="text-[11px] text-gray-400">Icon</p>
                                        </div>
                                    </div>
                                    <input ref="iconInput" type="file" accept="image/*" class="hidden" :disabled="form.processing" @change="updateFile($event, 'icon_image')" />
                                    <InputError :message="form.errors.icon_image" />
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- ═══ SEO ═══ -->
                    <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
                        <button type="button" class="flex w-full items-center gap-3 px-6 py-4 text-left transition hover:bg-gray-50/50" @click="showSeo = !showSeo">
                            <div class="flex size-9 items-center justify-center rounded-xl bg-amber-500 text-white">
                                <Search class="size-4" />
                            </div>
                            <div class="flex-1">
                                <h2 class="text-sm font-bold text-gray-900">SEO Settings</h2>
                                <p class="text-xs text-gray-400">Search engine optimization</p>
                            </div>
                            <svg class="size-5 text-gray-400 transition-transform" :class="showSeo && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <Transition name="slide">
                            <div v-if="showSeo" class="space-y-5 border-t border-gray-100 p-6">
                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div class="space-y-1.5">
                                        <Label for="meta_title" class="text-sm font-semibold text-gray-700">Meta Title</Label>
                                        <Input id="meta_title" v-model="form.meta_title" :disabled="form.processing" placeholder="SEO title for search engines" class="h-11 rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white" />
                                        <InputError :message="form.errors.meta_title" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <Label for="canonical_url" class="text-sm font-semibold text-gray-700">Canonical URL</Label>
                                        <Input id="canonical_url" v-model="form.canonical_url" :disabled="form.processing" placeholder="https://example.com/department" class="h-11 rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white" />
                                        <InputError :message="form.errors.canonical_url" />
                                    </div>
                                </div>
                                <div class="space-y-1.5">
                                    <Label for="meta_description" class="text-sm font-semibold text-gray-700">Meta Description</Label>
                                    <textarea id="meta_description" v-model="form.meta_description" rows="2" :disabled="form.processing" placeholder="Brief description for search result snippets..." class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm text-gray-700 focus:border-gray-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gray-900 disabled:opacity-50" />
                                    <InputError :message="form.errors.meta_description" />
                                </div>
                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div class="space-y-1.5">
                                        <Label for="og_title" class="text-sm font-semibold text-gray-700">OG Title</Label>
                                        <Input id="og_title" v-model="form.og_title" :disabled="form.processing" placeholder="Open Graph title" class="h-11 rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white" />
                                        <InputError :message="form.errors.og_title" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <Label for="og_description" class="text-sm font-semibold text-gray-700">OG Description</Label>
                                        <Input id="og_description" v-model="form.og_description" :disabled="form.processing" placeholder="Open Graph description" class="h-11 rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white" />
                                        <InputError :message="form.errors.og_description" />
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button
                                        type="button"
                                        class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
                                        :class="form.indexable ? 'bg-gray-900' : 'bg-gray-200'"
                                        @click="form.indexable = !form.indexable"
                                    >
                                        <span
                                            class="pointer-events-none inline-flex size-6 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-gray-200 transition-transform duration-200"
                                            :class="form.indexable ? 'translate-x-5' : 'translate-x-0'"
                                        />
                                    </button>
                                    <div>
                                        <span class="text-sm font-medium" :class="form.indexable ? 'text-gray-900' : 'text-gray-400'">Indexable</span>
                                        <p class="text-[11px] text-gray-400">Allow search engines to index this page</p>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </section>

                    <!-- Progress -->
                    <div v-if="form.progress" class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
                        <div class="mb-3 flex items-center justify-between text-sm">
                            <span class="font-semibold text-gray-900">Uploading…</span>
                            <span class="rounded-lg bg-gray-900 px-2.5 py-1 font-mono text-xs font-bold text-white">{{ form.progress.percentage }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-gray-900 transition-all duration-300" :style="{ width: `${form.progress.percentage}%` }" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="sticky bottom-4 flex items-center justify-end gap-3 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
                        <Button type="button" variant="ghost" :disabled="form.processing" as-child class="h-11 rounded-xl px-6 text-gray-500 hover:text-gray-900">
                            <Link :href="route('admin.departments.index')">Discard</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing" class="h-11 rounded-xl bg-gray-900 px-8 text-white shadow-lg shadow-gray-900/20 hover:bg-gray-800">
                            <Save class="mr-2 size-4" />
                            Create Department
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: all 0.25s ease;
    overflow: hidden;
}
.slide-enter-from,
.slide-leave-to {
    opacity: 0;
    max-height: 0;
    padding-top: 0;
    padding-bottom: 0;
}
.slide-enter-to,
.slide-leave-from {
    max-height: 1000px;
}
</style>
