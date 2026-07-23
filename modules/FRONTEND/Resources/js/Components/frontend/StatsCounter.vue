<script setup lang="ts">
import { useCounter } from '../../composables/useCounter'

const props = defineProps<{
  stats: Array<{ target: number; label: string }>
}>()

const { values } = useCounter(props.stats.map(s => s.target))
</script>

<template>
  <section id="stats-section" class="py-20 bg-gradient-to-r from-blue-800 to-sky-500 text-white premium-shine-section scroll-reveal fade-in-0 slide-in-from-bottom-8 duration-700">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        <div v-for="(stat, i) in stats" :key="stat.label">
          <div class="text-5xl font-bold mb-2" style="font-feature-settings: 'tnum'; font-variant-numeric: tabular-nums;">
            {{ values[i].toLocaleString() }}{{ values[i] >= stat.target ? '+' : '' }}
          </div>
          <p class="text-lg text-white/90">{{ stat.label }}</p>
        </div>
      </div>
    </div>
  </section>
</template>