<script setup lang="ts">
defineProps<{
  features: Array<{
    color: string
    icon: string
    title: string
    desc: string
  }>
}>()
</script>

<template>
  <section class="py-20 bg-gradient-to-br from-blue-50 to-indigo-50 scroll-reveal reveal-why fade-in-0 slide-in-from-bottom-10 duration-700">
      <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-200 to-transparent"></div>

    <div class="container mx-auto px-4">
      <div class="text-center mb-16">
        <div class="inline-block bg-white text-blue-800 px-4 py-2 rounded-full text-sm font-semibold mb-4 shadow-sm">
          <i class="fas fa-award mr-2"></i>Why Choose Us
        </div>
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Why AMZ Hospital is Your Best Choice</h2>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">Patients choose AMZ for reliability, transparency, faster access, and a safer care experience from admission to follow-up.</p>
      </div>
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 stagger-grid">
        <div
          v-for="(feat, i) in features"
          :key="feat.title"
          class="premium-sheen bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2"
        >
          <div :class="`w-16 h-16 bg-gradient-to-br ${feat.color} rounded-xl flex items-center justify-center mb-6`" style="animation: float 3s ease-in-out infinite" :style="`animation-delay: ${i * 0.2}s`">
            <i :class="`fas ${feat.icon} text-white text-3xl`"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-3">{{ feat.title }}</h3>
          <p class="text-gray-600">{{ feat.desc }}</p>
        </div>
      </div>
    </div>
  </section>
</template>