<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  testimonials: Array<{
    initials: string
    bg: string
    cardBg: string
    name: string
    short: string
    full: string
  }>
}>()

const expanded = ref<Record<number, boolean>>({})
const toggle = (i: number) => { expanded.value[i] = !expanded.value[i] }

const doubled = [...props.testimonials, ...props.testimonials]
</script>

<template>
  <section id="testimonials" role="region" aria-label="Patient testimonials" class="py-20 bg-white scroll-reveal reveal-testimonials fade-in-0 slide-in-from-bottom-8 duration-700">
    <div class="container mx-auto px-4">
      <div class="text-center mb-16">
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">What Our Patients Say</h2>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">Real experiences from patients who trusted us with their healthcare</p>
      </div>
      <div class="overflow-hidden" style="mask-image: linear-gradient(to right, transparent, black 6%, black 94%, transparent);">
        <div class="flex items-stretch gap-6" style="width: max-content; animation: marquee 34s linear infinite;">
          <article
            v-for="(t, i) in doubled"
            :key="`${t.name}-${i}`"
            :class="`mb-4 premium-sheen premium-sheen-auto flex-shrink-0 w-[320px] sm:w-[360px] bg-gradient-to-br ${t.cardBg} to-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition`"
          >
            <div class="flex items-center mb-6">
              <div :class="`w-16 h-16 bg-gradient-to-br ${t.bg} rounded-full flex items-center justify-center text-white text-2xl font-bold mr-4`">
                {{ t.initials }}
              </div>
              <div>
                <h4 class="font-bold text-gray-900">{{ t.name }}</h4>
                <div class="flex text-yellow-400 text-sm">
                  <i v-for="s in 5" :key="s" class="fas fa-star"></i>
                </div>
              </div>
            </div>
            <p class="text-gray-600 italic">
              {{ t.short }}
              <span v-if="expanded[i]">{{ t.full }}</span>
            </p>
            <button @click="toggle(i)" class="mt-4 text-sm font-semibold text-blue-800 hover:text-sky-500">
              {{ expanded[i] ? 'Show less' : 'Read more' }}
            </button>
          </article>
        </div>
      </div>
    </div>
  </section>
</template>