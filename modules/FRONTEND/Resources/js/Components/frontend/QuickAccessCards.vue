<script setup lang="ts">
defineProps<{
  cards: Array<{
    gradient: string
    icon: string
    title: string
    link: string
  }>
}>()
</script>

<template>
  <section class="py-0 relative -mt-16 z-40 scroll-reveal reveal-quick fade-in-0 slide-in-from-bottom-8 duration-700">
    <div class="container mx-auto px-4">
      <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-6 stagger-grid">
        <a
          v-for="card in cards"
          :key="card.title"
          :href="card.link"
          :aria-label="card.title"
          class="group premium-sheen relative overflow-hidden rounded-[28px] border border-white/80 bg-gradient-to-br from-white/95 via-white/85 to-slate-50/90 p-6 shadow-[0_30px_70px_-40px_rgba(2,8,23,0.9)] backdrop-blur transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_36px_80px_-45px_rgba(2,8,23,0.95)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/60 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
        >
          <div :class="`absolute inset-0 bg-gradient-to-br ${card.gradient} opacity-[0.12]`"></div>
          <div class="absolute inset-0 border border-white/60 rounded-[26px] pointer-events-none"></div>
          <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.7),transparent_55%)] opacity-60"></div>
          <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-gradient-to-br from-white/70 to-white/5 blur-3xl transition-opacity duration-300 group-hover:opacity-90"></div>
          <div :class="`absolute -left-14 -bottom-14 h-32 w-32 rounded-full bg-gradient-to-br ${card.gradient} opacity-30 blur-3xl`"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between">
              <div :class="`h-12 w-12 rounded-2xl bg-gradient-to-br ${card.gradient} text-white flex items-center justify-center shadow-[0_12px_28px_-12px_rgba(2,6,23,0.7)] ring-1 ring-white/50`">
                <i :class="`fas ${card.icon} text-lg`"></i>
              </div>
              <div class="h-9 w-9 rounded-full border border-slate-200/70 text-slate-500 flex items-center justify-center transition-all duration-300 group-hover:border-slate-300 group-hover:text-slate-700 group-hover:translate-x-0.5 bg-white/80 shadow-inner">
                <i class="fas fa-arrow-right text-[11px]"></i>
              </div>
            </div>
            <div class="mt-6">
              <h3 class="text-[18px] leading-tight font-extrabold text-slate-900">{{ card.title }}</h3>
              <p class="text-[11px] uppercase tracking-[0.26em] text-slate-400 mt-2">&nbsp;</p>
            </div>
            <div :class="`mt-6 h-1.5 w-16 rounded-full bg-gradient-to-r ${card.gradient} shadow-[0_8px_20px_-12px_rgba(15,23,42,0.8)]`"></div>
          </div>
        </a>
      </div>
    </div>
  </section>
</template>