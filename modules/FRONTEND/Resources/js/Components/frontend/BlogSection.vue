<script setup lang="ts">
defineProps<{
  posts: Array<{
    id: number
    name: string
    og_image_url: string
    slug: string
    descriptions: string
    created_at: string
    department: { name: string }
    creator: { name: string }
  }>
}>()
</script>

<template>
  <section id="blog" role="region" aria-label="Latest blog posts" class="py-20 bg-white scroll-reveal reveal-blog fade-in-0 slide-in-from-bottom-8 duration-700">
    <div class="container mx-auto px-4">
      <div class="mb-14 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
        <div>
          <p class="text-xs font-bold uppercase tracking-[0.2em] text-sky-600">Health Journal</p>
          <h2 class="mt-2 text-4xl md:text-5xl font-bold text-gray-900">Latest Blog & Health Insights</h2>
          <p class="mt-3 max-w-3xl text-lg text-gray-600">Practical medical guidance, prevention tips, and wellness updates from AMZ specialists.</p>
        </div>
        <a href="/blog" class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-5 py-3 text-sm font-semibold text-blue-800 transition hover:bg-blue-100">
          View All Posts <i class="fas fa-arrow-right text-xs"></i>
        </a>
      </div>

      <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-3 stagger-grid">
        <article v-for="post in posts" :key="post.id" class="premium-sheen overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg transition hover:-translate-y-1 hover:shadow-2xl">
          <div class="relative h-56 overflow-hidden">
            <img :src="post.og_image_url" :alt="post.name" class="h-full w-full object-cover transition duration-500 hover:scale-105" loading="lazy" />
            <span class="absolute left-4 top-4 rounded-full bg-blue-800 px-3 py-1 text-xs font-semibold text-white">
              {{ post.department.name }}
            </span>
          </div>
          <div class="p-6">
            <div class="mb-3 flex items-center gap-3 text-xs font-medium text-slate-500">
              <span><i class="fas fa-calendar-alt mr-1"></i> {{ post.created_at ? post.created_at.slice(0, 10) : '' }}</span>
              <span><i class="fas fa-clock mr-1"></i>5 min read</span>
            </div>
            <h3 class="mb-2 text-xl font-bold text-gray-900">{{ post.name }}</h3>
            <p class="mb-5 text-sm leading-6 text-gray-600" v-html="post.descriptions"></p>
            <div class="flex items-center justify-between border-t border-slate-100 pt-4">
              <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">By {{ post.creator.name }}</p>
              <a :href="route('front.blog.details', post.slug)" class="text-sm font-semibold text-blue-800 transition hover:text-sky-500">
                Read Article <i class="fas fa-arrow-right ml-1 text-xs"></i>
              </a>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>