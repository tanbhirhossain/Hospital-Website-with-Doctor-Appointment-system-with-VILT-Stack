<template>
  <FrontendLayout>
    <section id="blog" role="region" aria-label="Latest blog posts" class="py-24 bg-slate-50">
      <div class="container mx-auto px-4">
        
        <!-- Header Section -->
        <div class="mb-16 flex flex-col md:flex-row md:items-end md:justify-between gap-6">
          <div class="max-w-2xl">

            <h2 class="text-4xl md:text-5xl font-extrabold text-slate-950 tracking-tight">
              Latest Health Insights
            </h2>
            <p class="mt-4 text-lg text-slate-600">
              Evidence-based medical guidance and wellness updates from our specialists.
            </p>
          </div>

        </div>

        <!-- Blog Grid -->
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          <article 
            v-for="post in blogs" 
            :key="post.id" 
            class="group flex flex-col bg-white rounded-3xl border border-slate-200 shadow-sm transition-all duration-300 hover:shadow-2xl hover:border-blue-200 overflow-hidden"
          >
            <!-- Image Area -->
            <div class="relative h-64 overflow-hidden">
              <img 
                :src="post.og_image_url" 
                :alt="post.name" 
                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" 
                loading="lazy" 
              />
              <div class="absolute bottom-4 left-4">
                <span class="inline-flex items-center rounded-full bg-white/90 backdrop-blur-sm px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-blue-900">
                  {{ post.department?.name }}
                </span>
              </div>
            </div>

            <!-- Card Body -->
            <div class="flex flex-col flex-1 p-7">
              <div class="flex items-center gap-4 text-xs font-medium text-slate-500 mb-4">
                <span class="flex items-center gap-1.5">
                  <i class="fas fa-calendar-alt"></i> {{ post.created_at?.slice(0, 10) }}
                </span>
                <span class="flex items-center gap-1.5">
                  <i class="fas fa-clock"></i> 5 min read
                </span>
              </div>
              
              <h3 class="text-xl font-bold text-slate-950 mb-3 group-hover:text-blue-700 transition-colors">
                {{ post.name }}
              </h3>
              
              <div class="text-sm leading-relaxed text-slate-600 mb-6 line-clamp-3" v-html="post.descriptions"></div>
              
              <div class="mt-auto pt-6 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wide text-slate-400">
                  By {{ post.creator?.name }}
                </span>
                <a :href="post.href" class="text-sm font-semibold text-blue-700 inline-flex items-center group-hover:gap-2 transition-all">
                  Read <span class="ml-1 opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                </a>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>
  </FrontendLayout>
</template>

<script setup>
import FrontendLayout from '../../Layout/FrontendLayout.vue';

defineProps({
  blogs: Array
});
</script>