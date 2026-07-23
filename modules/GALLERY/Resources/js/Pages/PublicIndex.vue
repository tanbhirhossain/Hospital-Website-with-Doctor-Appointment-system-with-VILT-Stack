<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, ImageIcon, X } from 'lucide-vue-next';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import FrontendLayout from '../../../../FRONTEND/Resources/js/Layout/FrontendLayout.vue';

interface Item { id: number; title: string; description: string | null; alt_text: string; category: { name: string; slug: string }; image_url: string | null; thumbnail_url: string | null }
interface Category { name: string; slug: string; description: string | null; items_count: number }
interface LinkItem { url: string | null; label: string; active: boolean }

const props = defineProps<{ items: { data: Item[]; total: number; links: LinkItem[] }; categories: Category[]; selectedCategory: string }>();
const selected = ref<Item | null>(null);

const selectedIndex = () => props.items.data.findIndex((item) => item.id === selected.value?.id);
const close = () => selected.value = null;
const previous = () => { const index = selectedIndex(); if (index > 0) selected.value = props.items.data[index - 1]; };
const next = () => { const index = selectedIndex(); if (index < props.items.data.length - 1) selected.value = props.items.data[index + 1]; };
const keyboard = (event: KeyboardEvent) => { if (event.key === 'Escape') close(); if (event.key === 'ArrowLeft') previous(); if (event.key === 'ArrowRight') next(); };

onMounted(() => window.addEventListener('keydown', keyboard));
onBeforeUnmount(() => window.removeEventListener('keydown', keyboard));
</script>

<template>
    <FrontendLayout>
        <Head title="Photo Gallery" />
        <main class="min-h-screen bg-slate-50 text-slate-900">
            <!-- Header -->
            <header class="bg-gradient-to-br from-sky-950 via-sky-900 to-cyan-800 px-5 py-20 text-center text-white">
                <div class="mx-auto max-w-3xl">
                    <div class="mb-3 text-sm font-semibold uppercase tracking-[0.25em] text-cyan-200">Our moments</div>
                    <h1 class="text-4xl font-bold md:text-6xl">Photo Gallery</h1>
                    <p class="mx-auto mt-6 max-w-2xl text-sky-100/80 text-lg">Explore our events, facilities, and the heart of our community.</p>
                </div>
            </header>

            <div class="mx-auto max-w-7xl px-5 py-12">
                <!-- Navigation -->
                <nav class="mb-12 flex flex-wrap justify-center gap-2" aria-label="Gallery categories">
                    <Link href="/photo-gallery" :class="['rounded-full border px-6 py-2.5 text-sm font-medium transition-all duration-300', !selectedCategory ? 'border-sky-700 bg-sky-700 text-white shadow-lg shadow-sky-900/20' : 'border-slate-200 bg-white hover:border-sky-400']">All <span class="ml-1 opacity-70">{{ items.total }}</span></Link>
                    <Link v-for="category in categories" :key="category.slug" :href="`/photo-gallery?category=${category.slug}`" :class="['rounded-full border px-6 py-2.5 text-sm font-medium transition-all duration-300', selectedCategory === category.slug ? 'border-sky-700 bg-sky-700 text-white shadow-lg shadow-sky-900/20' : 'border-slate-200 bg-white hover:border-sky-400']">{{ category.name }} <span class="ml-1 opacity-70">{{ category.items_count }}</span></Link>
                </nav>

                <!-- Grid -->
                <section class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 reveal-gallery scroll-reveal animate-in">
                    <button v-for="item in items.data" :key="item.id" type="button" class="group gallery-card overflow-hidden rounded-3xl bg-white text-left ring-1 ring-slate-200" @click="selected = item">
                        <div class="aspect-[4/3] overflow-hidden bg-slate-100 relative">
                            <img v-if="item.thumbnail_url" :src="item.thumbnail_url" :alt="item.alt_text" loading="lazy" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 transition-opacity group-hover:opacity-100" />
                        </div>
                        <div class="p-6">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-sky-700">{{ item.category.name }}</div>
                            <h2 class="mt-2 font-semibold text-slate-800">{{ item.title }}</h2>
                        </div>
                    </button>
                </section>

                <div v-if="items.data.length === 0" class="rounded-3xl border border-dashed border-slate-300 bg-white py-20 text-center text-slate-500">
                    <ImageIcon class="mx-auto mb-3 size-12 text-slate-300" />
                    <h2 class="font-medium">No images found in this category.</h2>
                </div>

                <!-- Pagination -->
                <nav class="mt-16 flex flex-wrap justify-center gap-1">
                    <template v-for="link in items.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url" preserve-scroll :class="['rounded-xl border px-4 py-2.5 text-sm transition', link.active ? 'border-sky-700 bg-sky-700 text-white' : 'border-slate-200 bg-white hover:bg-slate-50']" v-html="link.label" />
                        <span v-else class="rounded-xl border border-slate-100 px-4 py-2.5 text-sm text-slate-300" v-html="link.label" />
                    </template>
                </nav>
            </div>
        </main>

        <!-- Fullscreen Modal -->
        <div v-if="selected" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-in fade-in" @click.self="close">
            <button class="absolute top-8 right-8 text-white/70 hover:text-white transition-colors z-50" @click="close"><X class="size-8" /></button>
            <button v-if="selectedIndex() > 0" class="absolute left-8 text-white hover:scale-110 transition-transform hidden md:block" @click="previous"><ChevronLeft class="size-12" /></button>
            
            <figure class="max-h-[85vh] max-w-5xl relative animate-in zoom-in-95 duration-300">
                <img :src="selected.image_url" class="max-h-[75vh] w-full rounded-2xl shadow-2xl border border-white/10 object-contain" />
                <figcaption class="mt-6 text-center text-white px-4">
                    <h3 class="text-2xl font-semibold tracking-wide">{{ selected.title }}</h3>
                    <p v-if="selected.description" class="mt-2 text-slate-300">{{ selected.description }}</p>
                </figcaption>
            </figure>

            <button v-if="selectedIndex() < items.data.length - 1" class="absolute right-8 text-white hover:scale-110 transition-transform hidden md:block" @click="next"><ChevronRight class="size-12" /></button>
        </div>
    </FrontendLayout>
</template>

<style scoped>
.gallery-card {
    transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.gallery-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
.modal-backdrop {
    backdrop-filter: blur(16px);
    background: rgba(15, 23, 42, 0.92);
}
/* Reusing your existing global animations */
.animate-in { animation: premiumBloomIn 0.6s ease-out forwards; }
</style>