<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { CheckCircle2, FolderPlus, ImageIcon, Pencil, Plus, Search, Star, Trash2, Upload, X } from 'lucide-vue-next';
import { computed, onBeforeUnmount, ref } from 'vue';

interface Category {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    is_active: boolean;
    sort_order: number;
    items_count: number;
}

interface GalleryItem {
    id: number;
    category_id: number;
    category: Pick<Category, 'id' | 'name' | 'slug'>;
    title: string;
    slug: string;
    description: string | null;
    alt_text: string | null;
    is_published: boolean;
    is_featured: boolean;
    sort_order: number;
    published_at: string | null;
    image_url: string | null;
    thumbnail_url: string | null;
    created_at: string | null;
}

interface PaginationLink { url: string | null; label: string; active: boolean }
interface PaginatedItems {
    data: GalleryItem[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

const props = defineProps<{
    items: PaginatedItems;
    categories: Category[];
    filters: { search?: string; category?: string | number; status?: string; featured?: string };
    can: { create: boolean; edit: boolean; delete: boolean };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Gallery', href: '/gallery' }];
const editingItem = ref<GalleryItem | null>(null);
const editingCategory = ref<Category | null>(null);
const showCategories = ref(false);
const imagePreview = ref<string | null>(null);

const filters = ref({
    search: props.filters.search ?? '',
    category: String(props.filters.category ?? ''),
    status: props.filters.status ?? '',
    featured: props.filters.featured ?? '',
});

const itemForm = useForm({
    _method: 'post',
    category_id: props.categories.find((category) => category.is_active)?.id ?? null as number | null,
    title: '',
    slug: '',
    description: '',
    alt_text: '',
    image: null as File | null,
    is_published: true,
    is_featured: false,
    sort_order: 0,
    published_at: '',
});

const categoryForm = useForm({
    name: '',
    slug: '',
    description: '',
    is_active: true,
    sort_order: 0,
});

const activeCategories = computed(() => props.categories.filter((category) => category.is_active));
const publishedCount = computed(() => props.items.data.filter((item) => item.is_published).length);
const canSubmitItem = computed(() => editingItem.value ? props.can.edit : props.can.create);
const canSubmitCategory = computed(() => editingCategory.value ? props.can.edit : props.can.create);

const applyFilters = () => router.get(route('gallery.index'), filters.value, { preserveState: true, replace: true });
const clearFilters = () => {
    filters.value = { search: '', category: '', status: '', featured: '' };
    applyFilters();
};

const releasePreview = () => {
    if (imagePreview.value?.startsWith('blob:')) URL.revokeObjectURL(imagePreview.value);
    imagePreview.value = null;
};

const selectImage = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    releasePreview();
    itemForm.image = file;
    if (file) imagePreview.value = URL.createObjectURL(file);
};

const resetItem = () => {
    editingItem.value = null;
    releasePreview();
    itemForm.reset();
    itemForm._method = 'post';
    itemForm.category_id = activeCategories.value[0]?.id ?? null;
    itemForm.clearErrors();
};

const editItem = (item: GalleryItem) => {
    editingItem.value = item;
    releasePreview();
    itemForm._method = 'put';
    itemForm.category_id = item.category_id;
    itemForm.title = item.title;
    itemForm.slug = item.slug;
    itemForm.description = item.description ?? '';
    itemForm.alt_text = item.alt_text ?? '';
    itemForm.image = null;
    itemForm.is_published = item.is_published;
    itemForm.is_featured = item.is_featured;
    itemForm.sort_order = item.sort_order;
    itemForm.published_at = item.published_at ?? '';
    imagePreview.value = item.thumbnail_url;
    itemForm.clearErrors();
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const submitItem = () => {
    if (!canSubmitItem.value) return;
    const url = editingItem.value ? route('gallery-items.update', editingItem.value.id) : route('gallery-items.store');
    itemForm.post(url, { forceFormData: true, preserveScroll: true, onSuccess: resetItem });
};

const deleteItem = (item: GalleryItem) => {
    if (props.can.delete && window.confirm(`Delete “${item.title}” and its image?`)) {
        router.delete(route('gallery-items.destroy', item.id), { preserveScroll: true, onSuccess: () => editingItem.value?.id === item.id && resetItem() });
    }
};

const resetCategory = () => {
    editingCategory.value = null;
    categoryForm.reset();
    categoryForm.clearErrors();
};

const editCategory = (category: Category) => {
    editingCategory.value = category;
    categoryForm.name = category.name;
    categoryForm.slug = category.slug;
    categoryForm.description = category.description ?? '';
    categoryForm.is_active = category.is_active;
    categoryForm.sort_order = category.sort_order;
    categoryForm.clearErrors();
};

const submitCategory = () => {
    if (!canSubmitCategory.value) return;
    if (editingCategory.value) {
        categoryForm.put(route('gallery-categories.update', editingCategory.value.id), { preserveScroll: true, onSuccess: resetCategory });
    } else {
        categoryForm.post(route('gallery-categories.store'), { preserveScroll: true, onSuccess: resetCategory });
    }
};

const deleteCategory = (category: Category) => {
    if (props.can.delete && category.items_count === 0 && window.confirm(`Delete category “${category.name}”?`)) {
        router.delete(route('gallery-categories.destroy', category.id), { preserveScroll: true });
    }
};

onBeforeUnmount(releasePreview);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Gallery" />
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <header class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-foreground">Gallery management</h1>
                    <p class="text-sm text-muted-foreground">Organize, publish, and feature images category-wise.</p>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" @click="showCategories = !showCategories"><FolderPlus class="size-4" /> Categories</Button>
                    <Button v-if="editingItem" variant="outline" @click="resetItem"><X class="size-4" /> Cancel edit</Button>
                </div>
            </header>

            <section class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border bg-background p-4"><div class="text-sm text-muted-foreground">Total images</div><div class="mt-1 text-2xl font-semibold">{{ items.total }}</div></div>
                <div class="rounded-xl border bg-background p-4"><div class="text-sm text-muted-foreground">Categories</div><div class="mt-1 text-2xl font-semibold">{{ categories.length }}</div></div>
                <div class="rounded-xl border bg-background p-4"><div class="text-sm text-muted-foreground">Published on this page</div><div class="mt-1 text-2xl font-semibold">{{ publishedCount }}</div></div>
            </section>

            <section v-if="showCategories" class="grid gap-6 rounded-xl border bg-background p-5 xl:grid-cols-[minmax(0,1fr)_360px]">
                <div>
                    <h2 class="mb-4 text-lg font-semibold">Categories</h2>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="category in categories" :key="category.id" class="rounded-lg border p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div><div class="font-medium">{{ category.name }}</div><div class="text-xs text-muted-foreground">{{ category.items_count }} images · {{ category.is_active ? 'Active' : 'Hidden' }}</div></div>
                                <span class="rounded bg-muted px-2 py-1 text-xs">#{{ category.sort_order }}</span>
                            </div>
                            <p v-if="category.description" class="mt-3 line-clamp-2 text-sm text-muted-foreground">{{ category.description }}</p>
                            <div class="mt-4 flex gap-2">
                                <Button size="sm" variant="outline" :disabled="!can.edit" @click="editCategory(category)"><Pencil class="size-4" /></Button>
                                <Button size="sm" variant="destructive" :disabled="!can.delete || category.items_count > 0" title="Only empty categories can be deleted" @click="deleteCategory(category)"><Trash2 class="size-4" /></Button>
                            </div>
                        </div>
                        <div v-if="categories.length === 0" class="py-8 text-sm text-muted-foreground">Create your first gallery category.</div>
                    </div>
                </div>
                <form class="space-y-4 rounded-lg border bg-muted/20 p-4" @submit.prevent="submitCategory">
                    <div class="flex items-center justify-between"><h3 class="font-semibold">{{ editingCategory ? 'Edit category' : 'New category' }}</h3><Button v-if="editingCategory" type="button" size="sm" variant="ghost" @click="resetCategory"><X /></Button></div>
                    <div class="grid gap-2"><Label for="category-name">Name</Label><Input id="category-name" v-model="categoryForm.name" /><InputError :message="categoryForm.errors.name" /></div>
                    <div class="grid gap-2"><Label for="category-slug">Slug (optional)</Label><Input id="category-slug" v-model="categoryForm.slug" placeholder="auto-generated" /><InputError :message="categoryForm.errors.slug" /></div>
                    <div class="grid gap-2"><Label for="category-description">Description</Label><textarea id="category-description" v-model="categoryForm.description" rows="3" class="rounded-md border border-input bg-background px-3 py-2 text-sm" /></div>
                    <div class="grid gap-2"><Label for="category-order">Sort order</Label><Input id="category-order" v-model.number="categoryForm.sort_order" type="number" min="0" /></div>
                    <label class="flex items-center gap-2 text-sm"><input v-model="categoryForm.is_active" type="checkbox" class="rounded border-input" /> Active</label>
                    <InputError :message="categoryForm.errors.category" />
                    <Button class="w-full" :disabled="!canSubmitCategory || categoryForm.processing"><FolderPlus /> {{ editingCategory ? 'Save category' : 'Create category' }}</Button>
                </form>
            </section>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_390px]">
                <main class="min-w-0 space-y-5">
                    <form class="grid gap-3 rounded-xl border bg-background p-4 md:grid-cols-[minmax(180px,1fr)_180px_150px_auto]" @submit.prevent="applyFilters">
                        <div class="relative"><Search class="absolute left-3 top-2.5 size-4 text-muted-foreground" /><Input v-model="filters.search" class="pl-9" placeholder="Search gallery…" /></div>
                        <select v-model="filters.category" class="h-9 rounded-md border border-input bg-background px-3 text-sm"><option value="">All categories</option><option v-for="category in categories" :key="category.id" :value="String(category.id)">{{ category.name }}</option></select>
                        <select v-model="filters.status" class="h-9 rounded-md border border-input bg-background px-3 text-sm"><option value="">Any status</option><option value="published">Published</option><option value="draft">Draft</option></select>
                        <div class="flex gap-2"><Button type="submit">Filter</Button><Button type="button" variant="ghost" @click="clearFilters">Clear</Button></div>
                    </form>

                    <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-3">
                        <article v-for="item in items.data" :key="item.id" class="group overflow-hidden rounded-xl border bg-background">
                            <div class="relative aspect-[4/3] overflow-hidden bg-muted">
                                <img v-if="item.thumbnail_url" :src="item.thumbnail_url" :alt="item.alt_text || item.title" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" />
                                <div v-else class="flex h-full items-center justify-center"><ImageIcon class="size-10 text-muted-foreground" /></div>
                                <div class="absolute left-3 top-3 flex gap-2"><span class="rounded-full bg-background/90 px-2.5 py-1 text-xs font-medium shadow">{{ item.category.name }}</span><span v-if="item.is_featured" class="rounded-full bg-amber-400 p-1.5 text-amber-950"><Star class="size-3 fill-current" /></span></div>
                                <span :class="['absolute bottom-3 left-3 rounded-full px-2.5 py-1 text-xs font-medium', item.is_published ? 'bg-emerald-500 text-white' : 'bg-slate-700 text-white']">{{ item.is_published ? 'Published' : 'Draft' }}</span>
                            </div>
                            <div class="p-4"><h3 class="truncate font-semibold">{{ item.title }}</h3><p class="mt-1 line-clamp-2 min-h-10 text-sm text-muted-foreground">{{ item.description || 'No description' }}</p><div class="mt-4 flex items-center justify-between"><span class="text-xs text-muted-foreground">Order {{ item.sort_order }}</span><div class="flex gap-2"><Button size="sm" variant="outline" :disabled="!can.edit" @click="editItem(item)"><Pencil /></Button><Button size="sm" variant="destructive" :disabled="!can.delete" @click="deleteItem(item)"><Trash2 /></Button></div></div></div>
                        </article>
                        <div v-if="items.data.length === 0" class="col-span-full rounded-xl border border-dashed py-16 text-center"><ImageIcon class="mx-auto mb-3 size-10 text-muted-foreground" /><div class="font-medium">No gallery images found</div><div class="text-sm text-muted-foreground">Change the filters or upload a new image.</div></div>
                    </div>

                    <div class="flex flex-col gap-3 rounded-xl border bg-background px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-muted-foreground">Showing {{ items.from ?? 0 }}–{{ items.to ?? 0 }} of {{ items.total }}</p>
                        <div class="flex flex-wrap gap-1"><template v-for="link in items.links" :key="link.label"><Link v-if="link.url" :href="link.url" preserve-scroll :class="['rounded-md border px-3 py-1.5 text-sm', link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted']" v-html="link.label" /><span v-else class="rounded-md border px-3 py-1.5 text-sm opacity-40" v-html="link.label" /></template></div>
                    </div>
                </main>

                <aside class="xl:sticky xl:top-6 xl:self-start">
                    <form class="space-y-5 rounded-xl border bg-background p-5" @submit.prevent="submitItem">
                        <div class="flex items-center justify-between"><div><h2 class="text-lg font-semibold">{{ editingItem ? 'Edit image' : 'Add image' }}</h2><p class="text-sm text-muted-foreground">{{ editingItem ? editingItem.title : 'JPG, PNG, WebP or GIF · max 10 MB' }}</p></div><div class="rounded-lg bg-primary/10 p-2 text-primary"><Pencil v-if="editingItem" /><Plus v-else /></div></div>
                        <div class="grid gap-2"><Label for="gallery-image">Image {{ editingItem ? '(leave empty to keep)' : '' }}</Label><label for="gallery-image" class="group flex aspect-video cursor-pointer items-center justify-center overflow-hidden rounded-lg border border-dashed bg-muted/40"><img v-if="imagePreview" :src="imagePreview" class="h-full w-full object-cover" /><div v-else class="text-center text-sm text-muted-foreground"><Upload class="mx-auto mb-2 size-7" />Choose an image</div></label><input id="gallery-image" type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="sr-only" @change="selectImage" /><InputError :message="itemForm.errors.image" /></div>
                        <div class="grid gap-2"><Label for="gallery-title">Title</Label><Input id="gallery-title" v-model="itemForm.title" placeholder="Hospital annual event" /><InputError :message="itemForm.errors.title" /></div>
                        <div class="grid gap-2"><Label for="gallery-category">Category</Label><select id="gallery-category" v-model.number="itemForm.category_id" class="h-9 rounded-md border border-input bg-background px-3 text-sm"><option :value="null" disabled>Select category</option><option v-for="category in activeCategories" :key="category.id" :value="category.id">{{ category.name }}</option></select><InputError :message="itemForm.errors.category_id" /></div>
                        <div class="grid gap-2"><Label for="gallery-description">Description</Label><textarea id="gallery-description" v-model="itemForm.description" rows="3" class="rounded-md border border-input bg-background px-3 py-2 text-sm" /><InputError :message="itemForm.errors.description" /></div>
                        <div class="grid gap-2"><Label for="gallery-alt">Image alt text</Label><Input id="gallery-alt" v-model="itemForm.alt_text" placeholder="Describe the image for accessibility" /><InputError :message="itemForm.errors.alt_text" /></div>
                        <div class="grid grid-cols-2 gap-3"><div class="grid gap-2"><Label for="gallery-order">Sort order</Label><Input id="gallery-order" v-model.number="itemForm.sort_order" type="number" min="0" /></div><div class="grid gap-2"><Label for="gallery-date">Publish at</Label><Input id="gallery-date" v-model="itemForm.published_at" type="datetime-local" /></div></div>
                        <div class="flex flex-wrap gap-5 rounded-lg border p-3"><label class="flex items-center gap-2 text-sm"><input v-model="itemForm.is_published" type="checkbox" class="rounded" /><CheckCircle2 class="size-4 text-emerald-500" /> Published</label><label class="flex items-center gap-2 text-sm"><input v-model="itemForm.is_featured" type="checkbox" class="rounded" /><Star class="size-4 text-amber-500" /> Featured</label></div>
                        <Button class="w-full" :disabled="!canSubmitItem || itemForm.processing"><Upload /> {{ editingItem ? 'Save changes' : 'Upload image' }}</Button>
                    </form>
                </aside>
            </div>
        </div>
    </AppLayout>
</template>
