<script setup lang="ts">
defineProps<{
  services: Array<{
    bg: string
    icon: string
    title: string
    desc: string
    items: string[]
  }>
}>()
</script>

<template>
  <section id="services" role="region" aria-label="Healthcare services" class="py-20 bg-white scroll-reveal reveal-services fade-in-0 slide-in-from-bottom-8 duration-700">
      <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-200 to-transparent"></div>

    <div class="container mx-auto px-4">
      <div class="text-center mb-16">
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Comprehensive Healthcare Services</h2>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">From emergency response to diagnostics, surgery, maternity, pharmacy, and recovery support in one integrated system.</p>
      </div>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 stagger-grid">
        <div v-for="svc in services" :key="svc.title" :class="`premium-sheen bg-gradient-to-br ${svc.bg} to-white border-2 border-gray-100 rounded-2xl p-8 hover:border-blue-800 hover:shadow-xl transition`">
          <i :class="`fas ${svc.icon} text-5xl text-blue-800 mb-4`"></i>
          <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ svc.title }}</h3>
          <p class="text-gray-600 mb-4">{{ svc.desc }}</p>
          <ul class="space-y-2 text-sm text-gray-600">
            <li v-for="item in svc.items" :key="item" class="flex items-center">
              <i class="fas fa-check-circle text-emerald-500 mr-2"></i>{{ item }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
</template>