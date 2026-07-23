import { ref, onMounted, onUnmounted } from 'vue'

export function useCarousel(items: any[], autoplayInterval = 5000) {
  const currentSlide = ref(0)
  let interval: ReturnType<typeof setInterval> | null = null

  const next = () => {
    currentSlide.value = (currentSlide.value + 1) % items.length
  }
  const prev = () => {
    currentSlide.value = (currentSlide.value - 1 + items.length) % items.length
  }
  const goTo = (index: number) => {
    currentSlide.value = index
  }

  const startAutoplay = () => {
    stopAutoplay()
    interval = setInterval(next, autoplayInterval)
  }
  const stopAutoplay = () => {
    if (interval) clearInterval(interval)
    interval = null
  }

  onMounted(() => startAutoplay())
  onUnmounted(() => stopAutoplay())

  return {
    currentSlide,
    next,
    prev,
    goTo,
    startAutoplay,
    stopAutoplay,
  }
}