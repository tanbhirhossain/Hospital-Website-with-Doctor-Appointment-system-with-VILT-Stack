<script setup lang="ts">
defineProps<{
  departments: Array<{
    id: number
    name: string
    banner_url: string
    color: string
    text_icon: string
    'text-color': string
    tagline: string
    striped_desc: string
    specialists: number
    slug: string
    available247?: boolean
  }>
}>()
</script>

<template>
  <section id="departments" class="py-24 bg-slate-50 relative overflow-hidden scroll-reveal reveal-departments fade-in-0 slide-in-from-bottom-8 duration-700">
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-200 to-transparent"></div>
    <div class="container mx-auto px-4 relative z-10">
      <div class="text-center mb-16 scroll-reveal">
        <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-6 tracking-tight">Specialized Departments</h2>
        <div class="w-20 h-1.5 bg-blue-600 mx-auto rounded-full"></div>
        <p class="mt-6 text-slate-500 max-w-2xl mx-auto text-lg leading-relaxed">
          World-class medical expertise paired with state-of-the-art technology to provide the highest standard of care.
        </p>
      </div>

      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
        <div
          v-for="dept in departments"
          :key="dept.id"
          class="premium-sheen group relative flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200/70 bg-white shadow-lg transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-200/50 scroll-reveal"
        >
          <div class="relative overflow-hidden p-8">
            <img :src="dept.banner_url" alt="Department background" class="absolute inset-0 h-full w-full object-cover object-right" />
            <div :class="`absolute inset-0 bg-gradient-to-br ${dept.color} opacity-70`"></div>
            <div class="absolute -right-14 -top-10 h-40 w-40 rounded-full bg-white/10 blur-3xl transition-all duration-500 group-hover:bg-white/25"></div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-white/25"></div>
            <div class="relative z-10 mb-6 flex h-16 w-16 items-center justify-center rounded-2xl border border-white/25 bg-white/20 shadow-inner backdrop-blur-md transition-transform duration-500 group-hover:rotate-6">
              <i :class="`fas ${dept.text_icon} text-3xl text-white transition-transform duration-500 group-hover:scale-110`"></i>
            </div>
            <h3 class="relative z-10 mb-2 text-2xl font-black tracking-tight text-white">{{ dept.name }}</h3>
            <p :class="`${dept['text-color']} relative z-10 text-sm leading-snug opacity-90 line-clamp-2`">
              {{ dept.tagline }}
            </p>
          </div>
          <div class="flex flex-grow flex-col p-8">
            <p class="mb-8 min-h-[4.5rem] line-clamp-3 text-base leading-relaxed text-slate-600">
              {{ dept.striped_desc }}
            </p>
            <div class="mb-7 grid grid-cols-1 gap-3">
              <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">Specialists</p>
                <p class="mt-1 text-sm font-extrabold text-slate-800">{{ dept.specialists }}+</p>
              </div>
            </div>
            <a :href="'/departments/' + dept.slug" class="group/btn mt-auto inline-flex items-center justify-between rounded-2xl bg-gradient-to-r from-slate-900 to-blue-900 px-5 py-3.5 text-white shadow-lg shadow-blue-900/20 transition-all duration-300 hover:from-blue-800 hover:to-sky-600 hover:shadow-xl hover:shadow-sky-300/40 active:scale-[0.98]">
              <span class="text-xs font-bold uppercase tracking-[0.16em]">Read More</span>
              <span class="ml-3 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20 transition-all duration-300 group-hover/btn:translate-x-1 group-hover/btn:bg-white/30">
                <i class="fas fa-arrow-right text-[11px]"></i>
              </span>
            </a>
          </div>
          <div v-if="dept.available247" class="absolute right-5 top-5 rounded-full border border-white/30 bg-white/20 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-white backdrop-blur-md">
            Emergency 24/7
          </div>
        </div>
      </div>
      <div class="mt-20 text-center">
        <a href="/departments" class="inline-flex items-center group text-blue-800 font-bold tracking-widest uppercase text-sm">
          View All Departments
          <span class="ml-4 w-12 h-px bg-blue-800 transition-all group-hover:w-20"></span>
        </a>
      </div>
    </div>
  </section>
</template>