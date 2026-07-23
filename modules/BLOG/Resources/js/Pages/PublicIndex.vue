<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowRight, BookOpen, Search } from 'lucide-vue-next';
import { ref } from 'vue';

import FrontendLayout from '../../../../FRONTEND/Resources/js/Layout/FrontendLayout.vue';

interface Blog { name: string; slug: string; excerpt: string; department: { id: number; name: string; slug: string } | null; og_image_url: string | null; created_at: string | null }
interface Department { id: number; name: string; blogs_count: number }
interface PageLink { url: string | null; label: string; active: boolean }
const props = defineProps<{ blogs: { data: Blog[]; total: number; links: PageLink[] }; departments: Department[]; filters: { search?: string; department?: string | number } }>();
const search = ref(props.filters.search ?? '');
const department = ref(String(props.filters.department ?? ''));
const filter = () => router.get('/blog', { search: search.value, department: department.value }, { preserveState: true, replace: true });
</script>

<template>
    <FrontendLayout>
    <Head title="Health Blog"><meta name="description" content="Expert health articles, medical advice, and hospital updates from our healthcare professionals." /></Head>
    <main class="min-h-screen bg-slate-50 text-slate-900">
        <header class="bg-gradient-to-br from-cyan-950 via-sky-900 to-blue-800 px-5 py-16 text-white"><div class="mx-auto max-w-5xl text-center"><div class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm text-cyan-100"><BookOpen class="size-4" /> Health knowledge centre</div><h1 class="text-4xl font-bold md:text-5xl">Insights for healthier living</h1><p class="mx-auto mt-4 max-w-2xl text-sky-100">Reliable information from our medical departments and healthcare professionals.</p></div></header>
        <div class="mx-auto max-w-7xl px-5 py-10">
            <form class="mx-auto mb-10 grid max-w-3xl gap-3 rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-200 sm:grid-cols-[1fr_200px_auto]" @submit.prevent="filter"><div class="relative"><Search class="absolute left-3 top-3 size-4 text-slate-400" /><input v-model="search" class="h-10 w-full rounded-lg border border-slate-200 pl-10 pr-3 text-sm outline-none focus:border-sky-500" placeholder="Search health articles…" /></div><select v-model="department" class="h-10 rounded-lg border border-slate-200 px-3 text-sm"><option value="">All departments</option><option v-for="item in departments" :key="item.id" :value="String(item.id)">{{ item.name }} ({{ item.blogs_count }})</option></select><button class="rounded-lg bg-sky-700 px-5 text-sm font-semibold text-white hover:bg-sky-800">Search</button></form>
            <section class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <article v-for="blog in blogs.data" :key="blog.slug" class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-xl"><div class="aspect-[16/9] overflow-hidden bg-gradient-to-br from-sky-100 to-cyan-50"><img v-if="blog.og_image_url" :src="blog.og_image_url" :alt="blog.name" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" /><div v-else class="flex h-full items-center justify-center"><BookOpen class="size-12 text-sky-300" /></div></div><div class="p-6"><div class="flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-sky-700"><span>{{ blog.department?.name ?? 'Health' }}</span><time class="font-normal normal-case text-slate-400">{{ blog.created_at }}</time></div><h2 class="mt-3 text-xl font-bold leading-snug">{{ blog.name }}</h2><p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">{{ blog.excerpt }}</p><Link :href="route('blog.show', blog.slug)" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-900">Read article <ArrowRight class="size-4" /></Link></div></article>
            </section>
            <div v-if="blogs.data.length === 0" class="rounded-2xl border border-dashed bg-white py-20 text-center"><BookOpen class="mx-auto mb-3 size-12 text-slate-300" /><h2 class="font-semibold">No articles found</h2><p class="mt-1 text-sm text-slate-500">Try another search or department.</p></div>
            <nav class="mt-10 flex flex-wrap justify-center gap-1"><template v-for="link in blogs.links" :key="link.label"><Link v-if="link.url" :href="link.url" preserve-scroll :class="['rounded-lg border px-3 py-2 text-sm', link.active ? 'border-sky-700 bg-sky-700 text-white' : 'border-slate-200 bg-white hover:bg-slate-100']" v-html="link.label" /><span v-else class="rounded-lg border px-3 py-2 text-sm opacity-40" v-html="link.label" /></template></nav>
        </div>
    </main>
    </FrontendLayout>
</template>
