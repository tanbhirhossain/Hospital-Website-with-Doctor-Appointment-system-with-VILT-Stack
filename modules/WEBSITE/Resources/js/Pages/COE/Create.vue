<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between py-2">
        <div>
          <h2 class="font-bold text-2xl text-gray-900 tracking-tight">Create Center of Excellence</h2>
          <p class="text-sm text-gray-500 mt-1">Define the core identity, media assets, and SEO strategy for your new CoE.</p>
        </div>
      </div>
    </template>

    <Head title="Create Center of Excellence" />

    <div class="py-10 bg-gray-50 min-h-screen">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <form @submit.prevent="submit" class="space-y-8 pb-24">
          <!-- ============================================================ -->
          <!-- BASIC INFORMATION                                            -->
          <!-- ============================================================ -->
          <div class="bg-white rounded-xl border border-gray-300 shadow-sm p-6 sm:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
              <div class="lg:col-span-4">
                <h3 class="text-base font-bold text-gray-900">Basic Information</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-xs leading-relaxed">
                  Primary identity, slug, icon, and descriptive content.
                </p>
              </div>

              <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Name -->
                <div class="sm:col-span-2">
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">
                    Name <span class="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    v-model="form.name"
                    placeholder="e.g., Artificial Intelligence"
                    class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                    :class="{ 'border-red-500': form.errors.name }"
                    @input="autoSlug"
                  />
                  <InputError :message="form.errors.name" class="mt-1" />
                </div>

                <!-- Slug -->
                <div class="sm:col-span-2">
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Slug URL</label>
                  <div class="flex h-11 rounded-lg shadow-sm overflow-hidden border border-gray-300">
                    <span class="inline-flex items-center px-4 bg-gray-100 border-r border-gray-300 text-gray-500 text-xs font-mono">
                      /coe/
                    </span>
                    <input
                      type="text"
                      v-model="form.slug"
                      placeholder="artificial-intelligence"
                      class="flex-1 min-w-0 block w-full px-4 bg-gray-50 text-gray-500 text-sm font-mono outline-none"
                      readonly
                    />
                  </div>
                  <p class="text-xs text-gray-400 mt-1">Auto-generated from name – you can edit it below</p>
                  <div class="mt-2">
                    <input
                      type="text"
                      v-model="form.slug_override"
                      placeholder="Override slug (optional)"
                      class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                      @input="form.slug = form.slug_override || generateSlug(form.name)"
                    />
                  </div>
                  <InputError :message="form.errors.slug" class="mt-1" />
                </div>

                <!-- Icon -->
                <div class="sm:col-span-2">
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Icon</label>
                  <IconPicker v-model="form.icon" placeholder="Search or choose an icon…" />
                  <InputError :message="form.errors.icon" class="mt-1" />
                </div>

                <!-- Custom Style (JSON) -->
                <div class="sm:col-span-2">
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Custom Style (JSON)</label>
                  <input
                    type="text"
                    v-model="form.style"
                    placeholder='{"bg": "blue-50", "text": "dark"}'
                    class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none font-mono"
                  />
                  <InputError :message="form.errors.style" class="mt-1" />
                </div>

                <!-- Description -->
                <div class="sm:col-span-2">
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Description</label>
                  <textarea
                    v-model="form.description"
                    rows="4"
                    placeholder="A brief overview of this Center of Excellence…"
                    class="w-full block rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none resize-y"
                    :class="{ 'border-red-500': form.errors.description }"
                  ></textarea>
                  <InputError :message="form.errors.description" class="mt-1" />
                </div>
              </div>
            </div>
          </div>

          <!-- ============================================================ -->
          <!-- MEDIA & DOCUMENTS                                             -->
          <!-- ============================================================ -->
          <div class="bg-white rounded-xl border border-gray-300 shadow-sm p-6 sm:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
              <div class="lg:col-span-4">
                <h3 class="text-base font-bold text-gray-900">Media & Documents</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-xs leading-relaxed">
                  Banner, thumbnail, and gallery images for the CoE.
                </p>
              </div>

              <div class="lg:col-span-8 space-y-8">
                <!-- Banner Image (single) -->
                <div class="border border-dashed border-gray-300 rounded-lg p-6 bg-gray-50 transition-all hover:bg-gray-100"
                     :class="{ 'border-indigo-400 bg-indigo-50': isDragOverBanner }"
                     @dragover.prevent="isDragOverBanner = true"
                     @dragleave.prevent="isDragOverBanner = false"
                     @drop.prevent="handleBannerDrop">
                  <h4 class="text-sm font-bold text-gray-900 mb-2">Banner Image</h4>
                  <p class="text-xs text-gray-600 mb-4">Upload a hero banner for the CoE page (JPG, PNG, WEBP – Max 5MB)</p>
                  <div class="flex items-center gap-4 flex-wrap">
                    <div v-if="bannerPreview" class="flex-shrink-0 relative group">
                      <img :src="bannerPreview" alt="Banner preview" class="w-32 h-20 rounded-lg object-cover border border-gray-200 shadow-sm" />
                      <button type="button" @click="removeBannerImage"
                              class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                      </button>
                    </div>
                    <div class="flex-1">
                      <label class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 cursor-pointer transition-all shadow-sm">
                        <input
                          type="file"
                          accept="image/jpeg,image/png,image/webp"
                          @change="setBannerImage"
                          class="hidden"
                        />
                        Choose Banner
                      </label>
                      <p class="text-xs text-gray-400 mt-2">or drag & drop</p>
                    </div>
                  </div>
                  <InputError :message="form.errors.banner_image" class="mt-2" />
                </div>

                <!-- Thumb Image (single) -->
                <div class="border border-dashed border-gray-300 rounded-lg p-6 bg-gray-50">
                  <h4 class="text-sm font-bold text-gray-900 mb-2">Thumbnail Image</h4>
                  <p class="text-xs text-gray-600 mb-4">Upload a square thumbnail for listing cards (JPG, PNG, WEBP – Max 2MB)</p>
                  <div class="flex items-center gap-4 flex-wrap">
                    <div v-if="thumbPreview" class="flex-shrink-0 relative group">
                      <img :src="thumbPreview" alt="Thumb preview" class="w-20 h-20 rounded-lg object-cover border border-gray-200 shadow-sm" />
                      <button type="button" @click="removeThumbImage"
                              class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                      </button>
                    </div>
                    <div class="flex-1">
                      <label class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 cursor-pointer transition-all shadow-sm">
                        <input
                          type="file"
                          accept="image/jpeg,image/png,image/webp"
                          @change="setThumbImage"
                          class="hidden"
                        />
                        Choose Thumbnail
                      </label>
                    </div>
                  </div>
                  <InputError :message="form.errors.thumb_image" class="mt-2" />
                </div>

                <!-- Gallery Images (multiple) -->
                <div class="border border-dashed border-gray-300 rounded-lg p-6 bg-gray-50">
                  <h4 class="text-sm font-bold text-gray-900 mb-2">Gallery Images</h4>
                  <p class="text-xs text-gray-600 mb-4">Upload multiple photos for the CoE gallery (JPG, PNG, WEBP – Max 5MB each, up to 10 images)</p>
                  <label class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 cursor-pointer transition-all shadow-sm mb-4">
                    <input
                      type="file"
                      multiple
                      accept="image/jpeg,image/png,image/webp"
                      @change="setGalleryFiles"
                      class="hidden"
                    />
                    Add Gallery Images
                  </label>
                  <div v-if="galleryPreviews.length" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <div v-for="(preview, idx) in galleryPreviews" :key="idx" class="relative group rounded-lg overflow-hidden border border-gray-200 bg-white shadow-sm">
                      <img :src="preview" class="w-full h-24 object-cover" />
                      <button type="button" @click="removeGalleryFile(idx)"
                              class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                      </button>
                      <span class="absolute bottom-0 left-0 right-0 text-xs bg-black bg-opacity-60 text-white truncate px-1">{{ galleryFiles[idx]?.name }}</span>
                    </div>
                  </div>
                  <InputError :message="form.errors.gallery" class="mt-2" />
                  <InputError :message="form.errors['gallery.*']" class="mt-1" />
                </div>
              </div>
            </div>
          </div>

          <!-- ============================================================ -->
          <!-- SEO & VISIBILITY                                              -->
          <!-- ============================================================ -->
          <div class="bg-white rounded-xl border border-gray-300 shadow-sm p-6 sm:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
              <div class="lg:col-span-4">
                <h3 class="text-base font-bold text-gray-900">SEO & Visibility</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-xs leading-relaxed">
                  Control search engine indexing, social sharing, and meta information.
                </p>
              </div>
              <div class="lg:col-span-8 space-y-4">
                <!-- Indexable checkbox -->
                <div class="flex items-center gap-6 flex-wrap">
                  <label class="inline-flex items-center">
                    <input type="checkbox" v-model="form.indexable" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <span class="ml-2 text-sm text-gray-700">Allow search indexing</span>
                  </label>
                </div>

                <!-- Meta Title -->
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Meta Title</label>
                  <input
                    type="text"
                    v-model="form.meta_title"
                    placeholder="SEO title (max 60 chars)"
                    class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                  />
                  <InputError :message="form.errors.meta_title" class="mt-1" />
                </div>

                <!-- Meta Description -->
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Meta Description</label>
                  <textarea
                    v-model="form.meta_description"
                    rows="2"
                    placeholder="Brief description for search results (max 160 chars)"
                    class="w-full block rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                  ></textarea>
                  <InputError :message="form.errors.meta_description" class="mt-1" />
                </div>

                <!-- Canonical URL -->
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Canonical URL</label>
                  <input
                    type="url"
                    v-model="form.canonical_url"
                    placeholder="https://example.com/coe/artificial-intelligence"
                    class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                  />
                  <InputError :message="form.errors.canonical_url" class="mt-1" />
                </div>

                <!-- OG Title & Description -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">OG Title</label>
                    <input
                      type="text"
                      v-model="form.og_title"
                      class="w-full h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                    />
                    <InputError :message="form.errors.og_title" class="mt-1" />
                  </div>
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">OG Description</label>
                    <input
                      type="text"
                      v-model="form.og_description"
                      class="w-full h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                    />
                    <InputError :message="form.errors.og_description" class="mt-1" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ============================================================ -->
          <!-- FORM ACTIONS                                                  -->
          <!-- ============================================================ -->
          <div class="flex justify-end items-center gap-3 bg-white border border-gray-300 rounded-xl p-4 shadow-sm mt-8">
            <Link :href="route('admin.coe.index')" class="h-11 px-6 border border-gray-300 rounded-lg text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 transition-all shadow-sm focus:ring-2 focus:ring-indigo-500/20 inline-flex items-center justify-center">
              Cancel
            </Link>
            <button type="submit" :disabled="form.processing" class="h-11 px-6 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-70 disabled:cursor-not-allowed inline-flex items-center gap-2">
              <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Save Center of Excellence
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import InputError from '../../Components/InputError.vue';
import IconPicker from '../../Components/IconPicker.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const form = useForm({
  name: '',
  slug: '',
  slug_override: '',
  description: '',
  icon: '',
  style: '',
  meta_title: '',
  meta_description: '',
  canonical_url: '',
  og_title: '',
  og_description: '',
  indexable: true,
  // Media files
  banner_image: null,   // File
  thumb_image: null,    // File
  gallery: [],          // Array of Files
});

// Slug helpers
const generateSlug = (text) => {
  if (!text) return '';
  return text
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-');
};

const autoSlug = () => {
  if (form.name && !form.slug_override) {
    form.slug = generateSlug(form.name);
  }
};

// Watch name to update slug when override is empty
watch(() => form.name, (newName) => {
  if (!form.slug_override) {
    form.slug = generateSlug(newName);
  }
});

// Media refs
const bannerPreview = ref('');
const thumbPreview = ref('');
const galleryFiles = ref([]);
const galleryPreviews = ref([]);
const isDragOverBanner = ref(false);

// Banner handlers
const setBannerImage = (event) => {
  const file = event.target.files?.[0];
  if (!file) return;
  if (!validateFile(file, 'image', 5)) return;
  form.banner_image = file;
  createPreview(file, bannerPreview);
};

const handleBannerDrop = (e) => {
  isDragOverBanner.value = false;
  const file = e.dataTransfer?.files[0];
  if (file && validateFile(file, 'image', 5)) {
    form.banner_image = file;
    createPreview(file, bannerPreview);
  }
};

const removeBannerImage = () => {
  form.banner_image = null;
  bannerPreview.value = '';
};

// Thumb handlers
const setThumbImage = (event) => {
  const file = event.target.files?.[0];
  if (!file) return;
  if (!validateFile(file, 'image', 2)) return;
  form.thumb_image = file;
  createPreview(file, thumbPreview);
};

const removeThumbImage = () => {
  form.thumb_image = null;
  thumbPreview.value = '';
};

// Gallery handlers
const setGalleryFiles = (event) => {
  const files = event.target.files;
  if (!files) return;
  const validFiles = [];
  const validPreviews = [];
  for (let i = 0; i < files.length && galleryFiles.value.length + validFiles.length < 10; i++) {
    const file = files[i];
    if (validateFile(file, 'image', 5)) {
      validFiles.push(file);
      const reader = new FileReader();
      reader.onload = (e) => validPreviews.push(e.target?.result);
      reader.readAsDataURL(file);
    }
  }
  galleryFiles.value.push(...validFiles);
  galleryPreviews.value.push(...validPreviews);
  form.gallery = [...galleryFiles.value];
};

const removeGalleryFile = (index) => {
  galleryFiles.value.splice(index, 1);
  galleryPreviews.value.splice(index, 1);
  form.gallery = [...galleryFiles.value];
};

// Helpers
const validateFile = (file, type, maxSizeMB) => {
  if (type === 'image' && !file.type.startsWith('image/')) return false;
  return file.size <= maxSizeMB * 1024 * 1024;
};

const createPreview = (file, target) => {
  const reader = new FileReader();
  reader.onload = (e) => target.value = e.target?.result;
  reader.readAsDataURL(file);
};

// Submit
const submit = () => {
  // For file uploads, we need to use FormData – Inertia's useForm handles that automatically when we pass files.
  // But we must ensure the files are in the form data.
  // Since we use Inertia, we can just call form.post with the files.
  // However, Inertia's form will automatically convert to FormData if any file is present.
  // So we can simply:
  form.post(route('admin.coe.store'), {
    preserveScroll: true,
    onError: (errors) => {
      console.error('Validation errors:', errors);
    },
  });
};
</script>