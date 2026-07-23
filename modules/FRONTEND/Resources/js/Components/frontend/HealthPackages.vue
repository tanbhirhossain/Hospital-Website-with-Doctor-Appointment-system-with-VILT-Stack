<script setup lang="ts">
defineProps<{
  packages: Array<{
    bg: string
    color: string
    icon: string
    title: string
    desc: string
    price: string
  }>
}>()
</script>

<template>
  <section id="health-packages" role="region" aria-label="Health packages" class="py-20 bg-gradient-to-b from-slate-50 to-white scroll-reveal reveal-packages fade-in-0 slide-in-from-bottom-8 duration-700">
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-200 to-transparent"></div>

    <div class="container mx-auto px-4">
      <div class="text-center mb-14">
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Health Packages</h2>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">Affordable preventive checkup plans designed for individuals, women, seniors, and families.</p>
      </div>
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 stagger-grid">
        <article v-for="pkg in packages" :key="pkg.title" class="premium-sheen bg-white rounded-2xl p-6 border border-slate-200 shadow-md hover:shadow-xl transition">
          <div :class="`w-12 h-12 rounded-lg ${pkg.bg} ${pkg.color} flex items-center justify-center mb-4`">
            <i :class="`fas ${pkg.icon}`"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">{{ pkg.title }}</h3>
          <p class="text-sm text-gray-600 mb-4">{{ pkg.desc }}</p>
          <p class="text-2xl font-bold text-blue-800 mb-4">{{ pkg.price }}</p>
          <a href="#appointment" class="block text-center bg-blue-800 text-white py-2.5 rounded-lg font-semibold hover:bg-sky-500 transition-colors">Book Package</a>
        </article>
      </div>
    </div>
  </section>
</template>