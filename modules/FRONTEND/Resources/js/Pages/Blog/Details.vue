<template>
  <FrontendLayout>
    <article class="py-16 bg-white">
      <!-- Article Header -->
      <header class="mx-auto max-w-3xl px-4 text-center mb-12">
        <span class="inline-block py-1 px-3 mb-4 text-xs font-bold uppercase tracking-widest text-blue-700 bg-blue-50 rounded-full">
          {{ blog.department?.name }}
        </span>
        <h1 class="text-4xl md:text-6xl font-extrabold text-slate-950 leading-tight mb-6 tracking-tight">
          {{ blog.name }}
        </h1>
        <div class="flex items-center justify-center gap-6 text-sm text-slate-500">
          <div class="flex items-center gap-2">
            <span class="font-medium text-slate-900">By {{ blog.creator?.name }}</span>
          </div>
          <span>•</span>
          <time>{{ blog.created_at?.slice(0, 10) }}</time>
          <span>•</span>
          <span>5 min read</span>
        </div>
      </header>

      <!-- Featured Image -->
      <div class="mx-auto max-w-5xl px-4 mb-16">
        <img 
          :src="blog.og_image_url" 
          :alt="blog.name" 
          class="w-full h-auto rounded-3xl shadow-lg"
        />
      </div>

      <!-- Main Content -->
      <div class="mx-auto max-w-3xl px-4">
        <div 
          class="prose prose-lg prose-slate prose-blue max-w-none 
                 prose-headings:font-bold prose-a:text-blue-700 
                 prose-img:rounded-2xl"
          v-html="blog.content"
        ></div>

        <!-- Call to Action / Footer -->
        <div class="mt-16 pt-10 border-t border-slate-100">
          <div class="bg-slate-50 rounded-3xl p-8 flex items-center justify-between">
            <div>
              <h4 class="font-bold text-lg text-slate-950">Enjoyed this article?</h4>
              <p class="text-slate-600 text-sm mt-1">Join our newsletter for more health insights.</p>
            </div>
            <button class="bg-blue-700 text-white px-6 py-2.5 rounded-full font-semibold hover:bg-blue-800 transition">
              Subscribe
            </button>
          </div>
        </div>
      </div>
    </article>
  </FrontendLayout>
</template>

<script setup>
import FrontendLayout from '../../Layout/FrontendLayout.vue';
defineProps({
  blog: Object
});
</script>