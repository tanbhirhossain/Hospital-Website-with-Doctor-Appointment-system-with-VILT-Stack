<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CalendarDays, UserRound } from 'lucide-vue-next';
import FrontendLayout from '../../../../FRONTEND/Resources/js/Layout/FrontendLayout.vue';

defineProps<{ blog: { name: string; slug: string; descriptions: string; department: { name: string; slug: string } | null; meta_title: string; meta_description: string | null; canonical_url: string; og_image_url: string | null; is_indexable: boolean; author: string | null; created_at: string | null } }>();
</script>

<template>
    <FrontendLayout>
    <Head :title="blog.meta_title">
        <meta v-if="blog.meta_description" name="description" :content="blog.meta_description" />
        <meta name="robots" :content="blog.is_indexable ? 'index, follow' : 'noindex, nofollow'" />
        <link rel="canonical" :href="blog.canonical_url" />
        <meta property="og:type" content="article" />
        <meta property="og:title" :content="blog.meta_title" />
        <meta v-if="blog.meta_description" property="og:description" :content="blog.meta_description" />
        <meta property="og:url" :content="blog.canonical_url" />
        <meta v-if="blog.og_image_url" property="og:image" :content="blog.og_image_url" />
        <meta name="twitter:card" content="summary_large_image" />
    </Head>
    <main class="min-h-screen bg-white text-slate-900">
        <article>
            <header class="bg-gradient-to-br from-slate-950 via-sky-950 to-cyan-900 px-5 py-14 text-white md:py-20"><div class="mx-auto max-w-4xl"><Link href="/blog" class="mb-8 inline-flex items-center gap-2 text-sm text-sky-200 hover:text-white"><ArrowLeft class="size-4" /> Back to all articles</Link><div class="text-sm font-semibold uppercase tracking-[0.16em] text-cyan-300">{{ blog.department?.name ?? 'Health article' }}</div><h1 class="mt-4 text-4xl font-bold leading-tight md:text-6xl">{{ blog.name }}</h1><div class="mt-6 flex flex-wrap gap-5 text-sm text-sky-100"><span v-if="blog.author" class="inline-flex items-center gap-2"><UserRound class="size-4" /> {{ blog.author }}</span><time class="inline-flex items-center gap-2"><CalendarDays class="size-4" /> {{ blog.created_at }}</time></div></div></header>
            <div v-if="blog.og_image_url" class="mx-auto -mt-8 max-w-5xl px-5"><img :src="blog.og_image_url" :alt="blog.name" class="max-h-[560px] w-full rounded-2xl object-cover shadow-2xl" /></div>
            <div class="mx-auto max-w-4xl px-5 py-12"><div class="blog-content text-lg leading-8 text-slate-700 [&_a]:text-sky-700 [&_a]:underline [&_blockquote]:border-l-4 [&_blockquote]:border-sky-500 [&_blockquote]:pl-5 [&_h2]:mb-4 [&_h2]:mt-10 [&_h2]:text-3xl [&_h2]:font-bold [&_h2]:text-slate-900 [&_h3]:mb-3 [&_h3]:mt-8 [&_h3]:text-2xl [&_h3]:font-bold [&_h3]:text-slate-900 [&_img]:my-8 [&_img]:rounded-xl [&_li]:mb-2 [&_ol]:my-5 [&_ol]:list-decimal [&_ol]:pl-6 [&_p]:mb-6 [&_strong]:text-slate-900 [&_ul]:my-5 [&_ul]:list-disc [&_ul]:pl-6" v-html="blog.descriptions" /></div>
        </article>
    </main>

    </FrontendLayout>
</template>
