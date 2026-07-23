import { ref, onMounted, onUnmounted } from 'vue'

export function useCounter(targets: number[]) {
  const values = ref(targets.map(() => 0))
  let animated = false
  let observer: IntersectionObserver | null = null

  const startCounting = (el: Element) => {
    if (animated) return
    animated = true
    targets.forEach((target, i) => {
      let current = 0
      const increment = target / 120
      const tick = () => {
        current += increment
        if (current < target) {
          values.value[i] = Math.ceil(current)
          requestAnimationFrame(tick)
        } else {
          values.value[i] = target
        }
      }
      tick()
    })
  }

  onMounted(() => {
    const statsEl = document.getElementById('stats-section')
    if (statsEl) {
      observer = new IntersectionObserver(
        (entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              startCounting(entry.target)
              observer?.unobserve(entry.target)
            }
          })
        },
        { threshold: 0.5 }
      )
      observer.observe(statsEl)
    }
  })

  onUnmounted(() => {
    observer?.disconnect()
  })

  return { values }
}