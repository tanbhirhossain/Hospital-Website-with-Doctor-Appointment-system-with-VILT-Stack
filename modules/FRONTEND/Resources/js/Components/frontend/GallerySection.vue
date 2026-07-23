<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

interface GalleryItem {
  category: { name: string }
  image_url: string
  thumb_url: string
  title: string
  description: string
  slug: string
}

const props = defineProps<{
  items: GalleryItem[]
}>()

const badgeColors: Record<string, string> = {
  infrastructure: 'bg-blue-800',
  facilities: 'bg-red-500',
  equipment: 'bg-emerald-500',
  departments: 'bg-cyan-600'
}

const activeFilter = ref('all')

const filters = computed(() => {
  const categories = props.items.map(item => item.category.name)
  return ['all', ...new Set(categories)]
})

const filteredItems = computed(() => {
  if (activeFilter.value === 'all') return props.items
  return props.items.filter(item => item.category.name === activeFilter.value)
})

// Modal state
const selectedItem = ref<GalleryItem | null>(null)

const openModal = (item: GalleryItem) => {
  selectedItem.value = item
  document.body.style.overflow = 'hidden'
}

const closeModal = () => {
  selectedItem.value = null
  document.body.style.overflow = ''
}

const handleKeydown = (e: KeyboardEvent) => {
  if (e.key === 'Escape') closeModal()
}

onMounted(() => window.addEventListener('keydown', handleKeydown))
onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
  document.body.style.overflow = ''
})
</script>

<template>
  <section id="gallery" class="py-20 bg-white scroll-reveal reveal-gallery">
      <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-200 to-transparent"></div>

    <div class="container mx-auto px-4">
      <!-- Header -->
      <div class="text-center mb-16">
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Hospital Gallery</h2>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
          Explore our state-of-the-art facilities, modern infrastructure, and patient-centered environment
        </p>
      </div>

      <!-- Filter Tabs -->
      <div class="flex flex-wrap justify-center gap-3 mb-12">
        <button
          v-for="f in filters"
          :key="f"
          @click="activeFilter = f"
          :class="[
            'px-6 py-3 rounded-lg font-semibold transition capitalize',
            activeFilter === f
              ? 'bg-blue-800 text-white'
              : 'bg-white text-slate-800 border border-slate-200 hover:bg-sky-500 hover:text-white hover:border-sky-500'
          ]"
        >
          {{ f === 'all' ? 'All Photos' : f }}
        </button>
      </div>

      <!-- Gallery Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="item in filteredItems"
          :key="item.slug"
          class="group cursor-pointer"
          @click="openModal(item)"
        >
          <div class="relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300">
            <img
              :src="item.thumb_url"
              :alt="item.title"
              class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500"
              loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                <h3 class="text-xl font-bold mb-2">{{ item.title }}</h3>
                <p class="text-sm text-gray-200">{{ item.description }}</p>
              </div>
            </div>
            <div
              :class="`absolute top-4 left-4 ${badgeColors[item.category.name?.toLowerCase()] || 'bg-gray-600'} px-3 py-1 rounded-full text-white text-xs font-semibold`"
            >
              {{ item.category.name }}
            </div>
          </div>
        </div>
      </div>

      <!-- 🆕 View All Button -->
      <div class="flex justify-center mt-12">
        <a
          href="/photo-gallery"
          class="inline-flex items-center px-8 py-4 bg-blue-800 hover:bg-blue-900 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
          </svg>
         View All
        </a>
      </div>
    </div>

    <!-- ===== MODAL ===== -->
    <Teleport to="body">
      <div
        v-if="selectedItem"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
        @click.self="closeModal"
      >
        <div class="relative w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
          <!-- Close button -->
          <button
            @click="closeModal"
            class="absolute top-4 right-4 z-10 bg-black/50 hover:bg-black/70 text-white rounded-full p-2 transition"
            aria-label="Close modal"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- Image wrapper – fills remaining height, image covers it fully -->
          <div class="flex-1 flex items-center justify-center min-h-0 bg-gray-50 overflow-hidden">
            <img
              :src="selectedItem.image_url"
              :alt="selectedItem.title"
              class="w-full h-full object-cover"
            />
          </div>

          <!-- Caption -->
          <div class="flex-shrink-0 p-6 bg-white border-t border-gray-200">
            <h3 class="text-2xl font-bold text-gray-900">{{ selectedItem.title }}</h3>
            <p class="text-gray-600 mt-2">{{ selectedItem.description }}</p>
            <span
              class="inline-block mt-3 px-3 py-1 rounded-full text-white text-sm font-semibold"
              :class="badgeColors[selectedItem.category.name?.toLowerCase()] || 'bg-gray-600'"
            >
              {{ selectedItem.category.name }}
            </span>
          </div>
        </div>
      </div>
    </Teleport>
  </section>
</template>