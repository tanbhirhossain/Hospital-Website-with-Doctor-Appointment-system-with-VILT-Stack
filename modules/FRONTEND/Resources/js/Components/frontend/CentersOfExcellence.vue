<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

defineProps<{
  centers: Array<{
    id: string
    slug: string
    bg_color: string
    text_color: string
    icon: string
    name: string
    striped_desc: string
    banner_url: string
    color: string
  }>
}>()

const getSlicedDescription = (desc: string) => {
  if (!desc) return ''
  
  const match = desc.match(/Hospital([\s\S]*?)What\s+we\s+offer/i)
  
  let text = match && match[1] ? match[1].trim() : desc
  
  if (text.length > 50) {
    return text.substring(0, 160) + '...'
  }
  
  return text
}
</script>

<template>
  <section id="center-of-excellence" role="region" aria-label="Center Of Excellence" class="py-20 bg-gradient-to-br from-slate-50 to-blue-50 relative overflow-hidden scroll-reveal reveal-departments fade-in-0 slide-in-from-bottom-8 duration-700">
  <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-200 to-transparent"></div>

    <div class="container mx-auto px-4">
      <div class="text-center mb-14">
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Center Of Excellence</h2>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">Advanced multidisciplinary centers delivering focused, high-quality care.</p>
      </div>

      <!-- <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 stagger-grid">
        <Link 
          v-for="center in centers" 
          :key="center.id" 
          :href="`/center-of-excellence/${center.slug}`"
          class="block group"
        >
          <article :id="center.id" class="h-full premium-sheen bg-white rounded-2xl p-6 border border-slate-100 shadow-md hover:shadow-xl transition-all">
            <div :class="`w-12 h-12 rounded-lg ${center.bg_color} ${center.text_color} flex items-center justify-center mb-4`">
              <i :class="`fas ${center.icon}`"></i>
            </div>
            <h3 class="font-bold text-gray-900">{{ center.name }}</h3>
            <p class="text-sm text-gray-600 mt-2" v-html="getSlicedDescription(center.striped_desc)"></p>
          </article>
        </Link>
      </div> -->


                 <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <div v-for="dept in centers" :key="dept.id"
                        class="premium-sheen group relative flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200/70 bg-white shadow-lg transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-200/50">

                        <div class="relative overflow-hidden p-8">
                            <img
                                :src="dept?.banner_url"
                                alt="Department background"
                                class="absolute inset-0 h-full w-full object-cover object-right"
                            />
                            <div :class="`absolute inset-0 bg-gradient-to-br ${dept.color} opacity-70`"></div>
                            <div class="absolute -right-14 -top-10 h-40 w-40 rounded-full bg-white/10 blur-3xl transition-all duration-500 group-hover:bg-white/25"></div>
                            <div class="absolute bottom-0 left-0 h-px w-full bg-white/25"></div>

                            <div class="relative z-10 mb-6 flex h-16 w-16 items-center justify-center rounded-2xl border border-white/25 bg-white/20 shadow-inner backdrop-blur-md transition-transform duration-500 group-hover:rotate-6">
                                <i :class="`fas ${dept['icon']} text-3xl text-white transition-transform duration-500 group-hover:scale-110`"></i>
                            </div>

                            <h3 class="relative z-10 mb-2 text-2xl font-black tracking-tight text-white">{{ dept.name }}</h3>
                            <!-- <p :class="`${dept['text-color']} relative z-10 text-sm leading-snug opacity-90 line-clamp-2`">
                                {{ dept.tagline }}
                            </p> -->
                        </div>

                        <div class="flex flex-grow flex-col p-8">
                            <p class="mb-8 min-h-[4.5rem] line-clamp-3 text-base leading-relaxed text-slate-600" v-html="getSlicedDescription(dept.striped_desc)">
                              
                            </p>

                            <!-- <div class="mb-7 grid grid-cols-1 gap-3">
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">Specialists</p>
                                    <p class="mt-1 text-sm font-extrabold text-slate-800">{{ dept.specialists }}+</p>
                                </div>
                            </div> -->

                            <a :href="`/center-of-excellence/${dept.slug}`"
                                class="group/btn mt-auto inline-flex items-center justify-between rounded-2xl bg-gradient-to-r from-slate-900 to-blue-900 px-5 py-3.5 text-white shadow-lg shadow-blue-900/20 transition-all duration-300 hover:from-blue-800 hover:to-sky-600 hover:shadow-xl hover:shadow-sky-300/40 active:scale-[0.98]">
                                <span class="text-xs font-bold uppercase tracking-[0.16em]">Read More</span>
                                <span class="ml-3 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20 transition-all duration-300 group-hover/btn:translate-x-1 group-hover/btn:bg-white/30">
                                    <i class="fas fa-arrow-right text-[11px]"></i>
                                </span>
                            </a>
                        </div>

                        <!-- <div v-if="dept.available247"
                            class="absolute right-5 top-5 rounded-full border border-white/30 bg-white/20 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-white backdrop-blur-md">
                            Emergency 24/7
                        </div> -->
                    </div> 
                </div>
                     

      <div class="text-center mt-10">
        <Link href="/center-of-excellence" class="inline-flex items-center bg-gradient-to-r from-blue-800 to-sky-500 text-white px-8 py-3 rounded-lg font-semibold hover:shadow-xl transition-all">
          View All Center Of Excellence <i class="fas fa-arrow-right ml-2"></i>
        </Link>
      </div>
    </div>
  </section>
</template>