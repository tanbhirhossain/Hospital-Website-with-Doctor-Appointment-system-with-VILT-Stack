<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import RichTextEditor from '../Components/RichTextEditor.vue';

import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
  ArchiveRestore,
  BookOpen,
  ChevronDown,
  Eye,
  FileText,
  Pencil,
  Plus,
  Search,
  Trash2,
  Upload,
  X,
  Maximize2,
  Minimize2,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, ref } from 'vue';

interface Department { id: number; name: string }
interface Blog { id: number; name: string; slug: string; department_id: number; department: { id: number; name: string; slug: string } | null; descriptions: string; meta_title: string | null; meta_description: string | null; canonical_url: string | null; og_image: string | null; og_image_url: string | null; is_indexable: boolean; creator: string | null; updater: string | null; created_at: string | null; updated_at: string | null; deleted_at: string | null; }
interface PaginationLink { url: string | null; label: string; active: boolean }
interface PaginatedBlogs { data: Blog[]; from: number | null; to: number | null; total: number; links: PaginationLink[] }

const props = defineProps<{
  blogs: PaginatedBlogs;
  departments: Department[];
  filters: { search?: string; department?: string; indexable?: string; trashed?: string };
  can: { create: boolean; edit: boolean; delete: boolean; restore: boolean };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Blogs', href: '/blogs' }];

// --- State ---
const editing = ref<Blog | null>(null);
const imagePreview = ref<string | null>(null);
const filters = ref({
  search: props.filters.search ?? '',
  department: String(props.filters.department ?? ''),
  indexable: props.filters.indexable ?? '',
  trashed: props.filters.trashed ?? '',
});
const isFilterOpen = ref(true);
const isFullscreen = ref(false);

// Simple toast placeholder – replace with your own
const toast = (message: string, type: 'success' | 'error' = 'success') => {
  alert(message);
};

// --- Form ---
const form = useForm({
  _method: 'post',
  name: '',
  slug: '',
  department_id: props.departments[0]?.id ?? null as number | null,
  descriptions: '',
  meta_title: '',
  meta_description: '',
  canonical_url: '',
  og_image: '',
  og_image_file: null as File | null,
  is_indexable: true,
});

// --- Computed ---
const isEditing = computed(() => editing.value !== null);
const canSubmit = computed(() => isEditing.value ? props.can.edit : props.can.create);
const titleCharacters = computed(() => form.meta_title.length);
const descriptionCharacters = computed(() => form.meta_description.length);

// --- Methods ---
const applyFilters = () => {
  router.get(route('blogs.index'), filters.value, { preserveState: true, replace: true });
};
const clearFilters = () => {
  filters.value = { search: '', department: '', indexable: '', trashed: '' };
  applyFilters();
};
const releasePreview = () => {
  if (imagePreview.value?.startsWith('blob:')) URL.revokeObjectURL(imagePreview.value);
  imagePreview.value = null;
};
const selectImage = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0] ?? null;
  releasePreview();
  form.og_image_file = file;
  if (file) imagePreview.value = URL.createObjectURL(file);
};
const resetForm = () => {
  editing.value = null;
  releasePreview();
  form.reset();
  form._method = 'post';
  form.department_id = props.departments[0]?.id ?? null;
  form.clearErrors();
};
const editBlog = (blog: Blog) => {
  editing.value = blog;
  releasePreview();
  form._method = 'put';
  form.name = blog.name;
  form.slug = blog.slug;
  form.department_id = blog.department_id;
  form.descriptions = blog.descriptions;
  form.meta_title = blog.meta_title ?? '';
  form.meta_description = blog.meta_description ?? '';
  form.canonical_url = blog.canonical_url ?? '';
  form.og_image = blog.og_image ?? '';
  form.og_image_file = null;
  form.is_indexable = blog.is_indexable;
  imagePreview.value = blog.og_image_url;
  form.clearErrors();
  window.scrollTo({ top: 0, behavior: 'smooth' });
};
const generateSlug = () => {
  if (form.name) {
    form.slug = form.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
  }
};
const submit = () => {
  if (!canSubmit.value) return;
  const url = editing.value ? route('blogs.update', editing.value.id) : route('blogs.store');
  form.post(url, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      toast(isEditing.value ? 'Blog updated!' : 'Blog published!', 'success');
      resetForm();
    },
    onError: () => toast('Something went wrong.', 'error'),
  });
};
const trash = (blog: Blog) => {
  if (props.can.delete && confirm(`Move “${blog.name}” to trash?`)) router.delete(route('blogs.destroy', blog.id), { preserveScroll: true });
};
const restore = (blog: Blog) => {
  if (props.can.restore) router.patch(route('blogs.restore', blog.id), {}, { preserveScroll: true });
};
const forceDelete = (blog: Blog) => {
  if (props.can.delete && confirm(`Permanently delete “${blog.name}”?`)) router.delete(route('blogs.force-delete', blog.id), { preserveScroll: true });
};
onBeforeUnmount(releasePreview);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Blogs" />
    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
      <!-- Header -->
      <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Blog Management</h1>
          <p class="text-sm text-muted-foreground">Write and manage your articles with a spacious editor.</p>
        </div>
        <div class="flex gap-2">
          <Button v-if="isEditing" variant="outline" @click="resetForm"><X class="mr-2 size-4" /> Cancel</Button>
          <Button v-else class="sm:hidden" @click="document.getElementById('blog-form')?.scrollIntoView({ behavior: 'smooth' })">
            <Plus class="mr-2 size-4" /> New
          </Button>
        </div>
      </header>

      <!-- Stats -->
      <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="flex items-center gap-4 rounded-lg border bg-card p-4 shadow-sm">
          <div class="rounded-full bg-primary/10 p-3 text-primary"><FileText class="size-5" /></div>
          <div><p class="text-sm text-muted-foreground">Total Blogs</p><p class="text-2xl font-bold">{{ blogs.total }}</p></div>
        </div>
        <div class="flex items-center gap-4 rounded-lg border bg-card p-4 shadow-sm">
          <div class="rounded-full bg-blue-500/10 p-3 text-blue-500"><BookOpen class="size-5" /></div>
          <div><p class="text-sm text-muted-foreground">Departments</p><p class="text-2xl font-bold">{{ departments.length }}</p></div>
        </div>
        <div class="flex items-center gap-4 rounded-lg border bg-card p-4 shadow-sm">
          <div class="rounded-full bg-amber-500/10 p-3 text-amber-500"><Eye class="size-5" /></div>
          <div><p class="text-sm text-muted-foreground">View</p><p class="text-lg font-medium">{{ filters.trashed === 'only' ? '🗑️ Trash' : filters.trashed === 'with' ? '📂 All' : '✅ Active' }}</p></div>
        </div>
      </section>

      <!-- Main Editor + Sidebar -->
      <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
        <!-- Left: Filters + Table + Editor (spacious) -->
        <div class="space-y-5">
          <!-- Filters (collapsible) -->
          <details class="rounded-xl border bg-card p-4" :open="isFilterOpen">
            <summary class="flex cursor-pointer items-center justify-between font-medium" @click="isFilterOpen = !isFilterOpen">
              <span>🔍 Filters</span>
              <ChevronDown class="size-4 transition-transform" :class="{ 'rotate-180': isFilterOpen }" />
            </summary>
            <form class="mt-4 grid gap-3 sm:grid-cols-[1fr_auto_auto_auto]" @submit.prevent="applyFilters">
              <div class="relative">
                <Search class="absolute left-3 top-2.5 size-4 text-muted-foreground" />
                <Input v-model="filters.search" class="pl-9" placeholder="Search by title or slug…" />
              </div>
              <select v-model="filters.department" class="h-9 rounded-md border border-input bg-background px-3 text-sm">
                <option value="">All departments</option>
                <option v-for="dept in departments" :key="dept.id" :value="String(dept.id)">{{ dept.name }}</option>
              </select>
              <select v-model="filters.indexable" class="h-9 rounded-md border border-input bg-background px-3 text-sm">
                <option value="">Any SEO</option><option value="1">Indexable</option><option value="0">No index</option>
              </select>
              <select v-model="filters.trashed" class="h-9 rounded-md border border-input bg-background px-3 text-sm">
                <option value="">Active</option><option value="only">Trash</option><option value="with">With trash</option>
              </select>
              <div class="flex gap-2 sm:col-span-1"><Button type="submit">Apply</Button><Button type="button" variant="ghost" @click="clearFilters">Clear</Button></div>
            </form>
          </details>

          <!-- Editor Card (full width) -->
          <div class="rounded-xl border bg-card p-4 shadow-sm">
            <!-- Editor Header with fullscreen toggle -->
            <div class="mb-3 flex items-center justify-between">
              <h2 class="text-lg font-semibold">{{ isEditing ? 'Edit Article' : 'Write New Article' }}</h2>
              <Button variant="ghost" size="sm" @click="isFullscreen = !isFullscreen">
                <Maximize2 v-if="!isFullscreen" class="size-4" />
                <Minimize2 v-else class="size-4" />
              </Button>
            </div>

            <!-- Core fields (compact row) -->
            <div class="grid gap-3 sm:grid-cols-2">
              <div class="grid gap-1">
                <Label for="blog-name">Title *</Label>
                <Input id="blog-name" v-model="form.name" placeholder="Catchy title" />
                <InputError :message="form.errors.name" />
              </div>
              <div class="grid gap-1">
                <Label for="blog-slug">Slug</Label>
                <div class="flex gap-2">
                  <Input id="blog-slug" v-model="form.slug" placeholder="auto-generated" />
                  <Button type="button" variant="outline" size="sm" @click="generateSlug">Generate</Button>
                </div>
                <p class="text-xs text-muted-foreground">Leave blank to auto‑generate.</p>
                <InputError :message="form.errors.slug" />
              </div>
            </div>

            <!-- Content Editor (large) -->
            <div class="mt-3 grid gap-1">
              <Label for="blog-content">Content</Label>
              <RichTextEditor
                id="blog-content"
                v-model="form.descriptions"
                :class="[
                  'rounded-md border border-input bg-background px-3 py-2 font-mono text-sm transition-all',
                  isFullscreen ? 'min-h-[70vh]' : 'min-h-[400px]',
                ]"
              />
              <InputError :message="form.errors.descriptions" />
            </div>

            <!-- Submit buttons (inline) -->
            <div class="mt-4 flex gap-2">
              <Button :disabled="!canSubmit || form.processing" @click="submit">
                <BookOpen class="mr-2 size-4" /> {{ isEditing ? 'Update' : 'Publish' }}
              </Button>
              <Button v-if="isEditing" variant="outline" @click="resetForm">Cancel</Button>
            </div>
          </div>

          <!-- Blog List (table) – now below editor -->
          <section class="overflow-hidden rounded-xl border bg-card">
            <div class="overflow-x-auto">
              <table class="w-full min-w-[820px] text-sm">
                <thead class="border-b bg-muted/50 text-left text-xs uppercase text-muted-foreground">
                  <tr><th class="px-4 py-3">Article</th><th class="px-4 py-3">Department</th><th class="px-4 py-3">SEO</th><th class="px-4 py-3">Audit</th><th class="px-4 py-3 text-right">Actions</th></tr>
                </thead>
                <tbody class="divide-y">
                  <tr v-for="blog in blogs.data" :key="blog.id" :class="['hover:bg-muted/30', blog.deleted_at ? 'opacity-65' : '']">
                    <td class="px-4 py-4">
                      <div class="flex items-center gap-3">
                        <img v-if="blog.og_image_url" :src="blog.og_image_url" :alt="blog.name" class="size-12 rounded-lg object-cover" />
                        <div v-else class="flex size-12 items-center justify-center rounded-lg bg-primary/10 text-primary"><FileText class="size-6" /></div>
                        <div class="min-w-0">
                          <div class="max-w-xs truncate font-medium" :title="blog.name">{{ blog.name }}</div>
                          <div class="max-w-xs truncate text-xs text-muted-foreground" :title="blog.slug">/{{ blog.slug }}</div>
                          <span v-if="blog.deleted_at" class="text-xs font-medium text-destructive">In trash</span>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-4 text-muted-foreground">{{ blog.department?.name ?? '—' }}</td>
                    <td class="px-4 py-4">
                      <div class="flex flex-col gap-1">
                        <span :class="['inline-flex w-fit rounded-full px-2.5 py-0.5 text-xs font-medium', blog.is_indexable ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700']">
                          {{ blog.is_indexable ? 'Index' : 'No index' }}
                        </span>
                        <span v-if="blog.meta_title" class="max-w-[120px] truncate text-xs text-muted-foreground" :title="blog.meta_title">{{ blog.meta_title }}</span>
                      </div>
                    </td>
                    <td class="px-4 py-4">
                      <div class="text-xs"><div>{{ blog.creator ?? '—' }}</div><div class="text-muted-foreground">{{ blog.updated_at }}</div></div>
                    </td>
                    <td class="px-4 py-4">
                      <div class="flex justify-end gap-2">
                        <a v-if="!blog.deleted_at" :href="route('blog.show', blog.slug)" target="_blank"><Button size="sm" variant="ghost"><Eye class="size-4" /></Button></a>
                        <Button v-if="!blog.deleted_at" size="sm" variant="outline" :disabled="!can.edit" @click="editBlog(blog)"><Pencil class="size-4" /></Button>
                        <Button v-if="!blog.deleted_at" size="sm" variant="destructive" :disabled="!can.delete" @click="trash(blog)"><Trash2 class="size-4" /></Button>
                        <Button v-if="blog.deleted_at" size="sm" variant="outline" :disabled="!can.restore" @click="restore(blog)"><ArchiveRestore class="size-4" /></Button>
                        <Button v-if="blog.deleted_at" size="sm" variant="destructive" :disabled="!can.delete" @click="forceDelete(blog)"><Trash2 class="size-4" /></Button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="blogs.data.length === 0"><td colspan="5" class="px-4 py-14 text-center text-muted-foreground">No articles found.</td></tr>
                </tbody>
              </table>
            </div>
            <div class="flex flex-col gap-3 border-t px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
              <p class="text-sm text-muted-foreground">Showing {{ blogs.from ?? 0 }}–{{ blogs.to ?? 0 }} of {{ blogs.total }}</p>
              <div class="flex flex-wrap gap-1">
                <template v-for="link in blogs.links" :key="link.label">
                  <Link v-if="link.url" :href="link.url" preserve-scroll :class="['rounded-md border px-3 py-1.5 text-sm', link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted']" v-html="link.label" />
                  <span v-else class="rounded-md border px-3 py-1.5 text-sm opacity-40" v-html="link.label" />
                </template>
              </div>
            </div>
          </section>
        </div>

        <!-- Right Sidebar: Metadata (SEO, Image, Department, etc.) -->
        <aside class="space-y-4 xl:sticky xl:top-6 xl:self-start">
          <!-- Department -->
          <div class="rounded-xl border bg-card p-4 shadow-sm">
            <Label for="blog-department">Department</Label>
            <select id="blog-department" v-model.number="form.department_id" class="mt-1 h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
              <option :value="null" disabled>Select a department</option>
              <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
            </select>
            <InputError :message="form.errors.department_id" />
          </div>

          <!-- SEO Settings (collapsible) -->
          <details class="rounded-xl border bg-card p-4 shadow-sm" open>
            <summary class="cursor-pointer font-medium">🔍 SEO Settings</summary>
            <div class="mt-3 space-y-3">
              <div class="grid gap-1">
                <div class="flex justify-between"><Label for="meta-title">Meta Title</Label><span :class="['text-xs', titleCharacters > 60 ? 'text-destructive' : 'text-muted-foreground']">{{ titleCharacters }}/60</span></div>
                <Input id="meta-title" v-model="form.meta_title" placeholder="SEO title" />
                <InputError :message="form.errors.meta_title" />
              </div>
              <div class="grid gap-1">
                <div class="flex justify-between"><Label for="meta-description">Meta Description</Label><span :class="['text-xs', descriptionCharacters > 160 ? 'text-destructive' : 'text-muted-foreground']">{{ descriptionCharacters }}/160</span></div>
                <textarea id="meta-description" v-model="form.meta_description" rows="2" class="rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Brief summary" />
                <InputError :message="form.errors.meta_description" />
              </div>
              <div class="grid gap-1">
                <Label for="canonical">Canonical URL</Label>
                <Input id="canonical" v-model="form.canonical_url" type="url" placeholder="https://..." />
                <InputError :message="form.errors.canonical_url" />
              </div>
              <div class="flex items-center gap-2">
                <input id="is-indexable" v-model="form.is_indexable" type="checkbox" class="rounded border-input" />
                <Label for="is-indexable" class="cursor-pointer text-sm">Allow indexing</Label>
              </div>
            </div>
          </details>

          <!-- Social Image (collapsible) -->
          <details class="rounded-xl border bg-card p-4 shadow-sm" open>
            <summary class="cursor-pointer font-medium">🖼️ Social Image</summary>
            <div class="mt-3 space-y-3">
              <div class="grid gap-1">
                <Label>Upload image</Label>
                <label for="og-image" class="relative flex aspect-[1.91/1] cursor-pointer items-center justify-center overflow-hidden rounded-lg border border-dashed bg-muted/40 transition hover:bg-muted/60">
                  <img v-if="imagePreview" :src="imagePreview" class="h-full w-full object-cover" />
                  <div v-else class="text-center text-sm text-muted-foreground">
                    <Upload class="mx-auto mb-2 size-6" />
                    <span>Click to upload</span>
                  </div>
                  <input id="og-image" class="sr-only" type="file" accept="image/*" @change="selectImage" />
                </label>
                <InputError :message="form.errors.og_image_file" />
              </div>
              <div class="grid gap-1">
                <Label for="og-url">Or image URL</Label>
                <Input id="og-url" v-model="form.og_image" type="url" placeholder="https://..." />
                <InputError :message="form.errors.og_image" />
              </div>
              <div v-if="imagePreview || form.og_image" class="rounded-lg border p-3">
                <p class="text-xs font-medium text-muted-foreground">Preview</p>
                <div class="mt-1 flex items-start gap-2">
                  <img :src="imagePreview || form.og_image" class="size-16 rounded object-cover" />
                  <div>
                    <p class="text-sm font-medium">{{ form.meta_title || form.name || 'Untitled' }}</p>
                    <p class="line-clamp-2 text-xs text-muted-foreground">{{ form.meta_description || 'No description' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </details>

          <!-- Quick action (publish) is now in the main editor, but we keep a duplicate here for convenience -->
          <Button class="w-full" :disabled="!canSubmit || form.processing" @click="submit">
            <BookOpen class="mr-2 size-4" /> {{ isEditing ? 'Update Article' : 'Publish Article' }}
          </Button>
        </aside>
      </div>
    </div>
  </AppLayout>
</template>