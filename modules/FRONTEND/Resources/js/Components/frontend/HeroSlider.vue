<script setup lang="ts">
import { computed } from 'vue' // Ensure this is imported
import { useCarousel } from '../../composables/useCarousel'

const props = defineProps<{
  slides: Array<any>
}>()

const { currentSlide, next, prev, goTo, startAutoplay, stopAutoplay } = useCarousel(props.slides)

const processedButtons = computed(() => {
  const current = props.slides[currentSlide.value];
  if (!current || !current.buttons) return [];
  
  // Safely parse if it's a string, or return if it's already an array
  return typeof current.buttons === 'string' 
    ? JSON.parse(current.buttons) 
    : current.buttons;
})

// console.log(slides)
</script>

<template>
  <section id="home" role="region" aria-label="Hero section" class="relative scroll-reveal reveal-home fade-in-0 zoom-in-95 duration-700">
    <div class="relative h-[500px] md:h-[600px] lg:h-[700px] overflow-hidden">
      <div
        v-for="(slide, i) in slides"
        :key="i"
        class="hero-slide absolute inset-0"
        :class="i === currentSlide ? 'opacity-100 z-20 pointer-events-auto' : 'opacity-0 z-0 pointer-events-none'"
      >
        <div :class="`absolute inset-0 bg-linear-to-r ${slide.badge_text} to-transparent z-10 transition-opacity duration-1000`"></div>
        <img
          :src="slide.image_url"
          :alt="slide.alt"
          class="hero-slide-image w-full h-full object-cover"
          :class="i === currentSlide ? 'hero-image-active' : 'hero-image-inactive'"
          loading="eager"
        />
        <div class="absolute inset-0 z-20 flex items-center">
          <div class="container mx-auto px-4">
            <div
              class="hero-slide-content max-w-3xl text-white"
              :class="i === currentSlide ? 'hero-content-active' : 'hero-content-inactive'"
            >
              <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight">{{ slide.title }}</h2>
              <p class="text-lg md:text-xl mb-8 text-gray-100">{{ slide.subtitle }}</p>
              <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex flex-col sm:flex-row gap-4">
                <a
                    v-for="(cta, idx) in processedButtons"
                    :key="idx"
                    :href="cta.href"
                    :class="[
                    'px-8 py-4 rounded-lg font-semibold transition-all duration-300 ease-in-out transform hover:scale-105 text-center cursor-pointer',
                    cta.style === 'white' 
                        ? `bg-white ${cta.color} hover:bg-gray-100 hover:shadow-xl` 
                        : 'border-2 border-white text-white hover:bg-white hover:text-blue-800'
                    ]"
                >
                    <i :class="`fas ${cta.icon} mr-2`"></i>{{ cta.label }}
                </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Controls -->
      <button
        @click="() => { stopAutoplay(); prev(); startAutoplay() }"
        aria-label="Previous slide"
        class="absolute left-4 top-1/2 -translate-y-1/2 z-30 bg-white/30 hover:bg-white/50 backdrop-blur-sm text-white p-3 rounded-full transition"
      >
        <i class="fas fa-chevron-left text-xl"></i>
      </button>
      <button
        @click="() => { stopAutoplay(); next(); startAutoplay() }"
        aria-label="Next slide"
        class="absolute right-4 top-1/2 -translate-y-1/2 z-30 bg-white/30 hover:bg-white/50 backdrop-blur-sm text-white p-3 rounded-full transition"
      >
        <i class="fas fa-chevron-right text-xl"></i>
      </button>

      <!-- Dots -->
      <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 flex space-x-2">
        <button
          v-for="(_, i) in slides"
          :key="i"
          @click="() => { stopAutoplay(); goTo(i); startAutoplay() }"
          :class="['w-3 h-3 rounded-full transition-all', i === currentSlide ? 'bg-white' : 'bg-white/50']"
        />
      </div>
    </div>
  </section>
</template>