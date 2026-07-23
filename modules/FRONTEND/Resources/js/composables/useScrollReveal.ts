import { onMounted, onUnmounted } from 'vue'

export function useScrollReveal(selector = '.scroll-reveal') {
  let observer: IntersectionObserver | null = null

  onMounted(() => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches
    if (prefersReducedMotion) {
      document.querySelectorAll(selector).forEach(el => el.classList.add('animate-in'))
      return
    }

    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate-in')
            observer?.unobserve(entry.target)
          }
        })
      },
      { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
    )
    document.querySelectorAll(selector).forEach(el => observer?.observe(el))
  })

  onUnmounted(() => {
    observer?.disconnect()
  })
}