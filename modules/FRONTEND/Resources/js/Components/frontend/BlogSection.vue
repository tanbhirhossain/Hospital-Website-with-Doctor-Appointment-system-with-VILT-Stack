<script setup lang="ts">
import { ref, onMounted } from 'vue'

// Define Props for the Blog Component
defineProps<{
  posts: Array<{
    id: number
    name: string
    og_image_url: string
    slug: string
    descriptions: string
    created_at: string
    department: { name: string }
    creator: { name: string }
  }>
}>()

// --- YouTube Feed Setup (No API Key Required) ---
const CHANNEL_ID = 'UCRu97CG2fmPbW9cNAhn4Vog' 

interface YouTubeVideo {
  id: string
  title: string
  thumbnail: string
  publishedAt: string
  url: string
}

const videos = ref<YouTubeVideo[]>([])
const ytLoading = ref(true)
const ytError = ref<string | null>(null)

// Extract Video ID from URL
const getVideoId = (url: string): string | null => {
  const match = url.match(/(?:v=|\/embed\/|\/100\/|\.be\/)([^"&?\/ ]{11})/)
  return match ? match[1] : null
}

// Relative time calculation
const formatRelativeTime = (dateString: string): string => {
  const date = new Date(dateString)
  const now = new Date()
  const seconds = Math.floor((now.getTime() - date.getTime()) / 1000)
  
  let interval = Math.floor(seconds / 31536000)
  if (interval >= 1) return `${interval} yr ago`
  interval = Math.floor(seconds / 2592000)
  if (interval >= 1) return `${interval} mo ago`
  interval = Math.floor(seconds / 86400)
  if (interval >= 1) return `${interval} days ago`
  interval = Math.floor(seconds / 3600)
  if (interval >= 1) return `${interval} hrs ago`
  return 'Recently'
}

const fetchLatestVideosNoApi = async () => {
  ytLoading.value = true
  ytError.value = null

  try {
    const rssUrl = `https://www.youtube.com/feeds/videos.xml?channel_id=${CHANNEL_ID}`
    const jsonFeedUrl = `https://api.rss2json.com/v1/api.json?rss_url=${encodeURIComponent(rssUrl)}`

    const response = await fetch(jsonFeedUrl)
    const data = await response.json()

    if (data.status !== 'ok') {
      throw new Error('Could not fetch channel RSS feed.')
    }

    // Slice to 3 items only for 1 single row
    videos.value = data.items.slice(0, 3).map((item: any) => {
      const id = getVideoId(item.link) || ''
      return {
        id,
        title: item.title,
        thumbnail: `https://img.youtube.com/vi/${id}/maxresdefault.jpg`,
        publishedAt: formatRelativeTime(item.pubDate),
        url: item.link
      }
    })
  } catch (err) {
    console.error('RSS Feed Fetch Error:', err)
    ytError.value = 'Unable to load channel videos. Please check Channel ID.'
  } finally {
    ytLoading.value = false
  }
}

const openVideo = (url: string) => {
  window.open(url, '_blank')
}

onMounted(() => {
  fetchLatestVideosNoApi()
})
</script>

<template>
  <div class="space-y-16">
    <!-- SECTION 1: Blog & Health Insights -->
    <section id="blog" role="region" aria-label="Latest blog posts" class="py-12 bg-white scroll-reveal reveal-blog fade-in-0 slide-in-from-bottom-8 duration-700">
      <div class="container mx-auto px-4">
        <div class="mb-14 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
          <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-sky-600">Health Journal</p>
            <h2 class="mt-2 text-4xl md:text-5xl font-bold text-gray-900">Latest Blog & Health Insights</h2>
            <p class="mt-3 max-w-3xl text-lg text-gray-600">Practical medical guidance, prevention tips, and wellness updates from AMZ specialists.</p>
          </div>
          <a href="/blog" class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-5 py-3 text-sm font-semibold text-blue-800 transition hover:bg-blue-100">
            View All Posts <i class="fas fa-arrow-right text-xs"></i>
          </a>
        </div>

        <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-3 stagger-grid">
          <article v-for="post in posts" :key="post.id" class="premium-sheen overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg transition hover:-translate-y-1 hover:shadow-2xl flex flex-col justify-between">
            <div>
              <div class="relative h-56 overflow-hidden">
                <img :src="post.og_image_url" :alt="post.name" class="h-full w-full object-cover transition duration-500 hover:scale-105" loading="lazy" />
                <span class="absolute left-4 top-4 rounded-full bg-blue-800 px-3 py-1 text-xs font-semibold text-white">
                  {{ post.department.name }}
                </span>
              </div>
              <div class="p-6">
                <div class="mb-3 flex items-center gap-3 text-xs font-medium text-slate-500">
                  <span><i class="fas fa-calendar-alt mr-1"></i> {{ post.created_at ? post.created_at.slice(0, 10) : '' }}</span>
                  <span><i class="fas fa-clock mr-1"></i>5 min read</span>
                </div>
                <h3 class="mb-2 text-xl font-bold text-gray-900 line-clamp-2">{{ post.name }}</h3>
                <p class="mb-5 text-sm leading-6 text-gray-600 line-clamp-3" v-html="post.descriptions"></p>
              </div>
            </div>

            <div class="px-6 pb-6 pt-0 border-t border-slate-100 mt-auto pt-4 flex items-center justify-between">
              <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">By {{ post.creator.name }}</p>
              <a :href="route('front.blog.details', post.slug)" class="text-sm font-semibold text-blue-800 transition hover:text-sky-500">
                Read Article <i class="fas fa-arrow-right ml-1 text-xs"></i>
              </a>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- SECTION 2: YouTube Channel Stream (Single Row / 3 Videos) -->
    <section id="youtube-stream" role="region" aria-label="Official YouTube Channel Stream" class="pb-20 bg-white">
      <div class="container mx-auto px-4 space-y-8">
        
        <!-- Header Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-slate-900 p-6 sm:p-8 text-white shadow-xl">
          <div class="absolute -top-24 -right-24 w-72 h-72 bg-red-500/20 rounded-full blur-3xl pointer-events-none"></div>
          <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-rose-500/20 rounded-full blur-3xl pointer-events-none"></div>

          <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl">
              <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800/80 border border-slate-700/80 text-red-400 text-xs font-semibold uppercase tracking-wider mb-4 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                Live Channel Stream
              </div>
              <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white leading-tight">
                Latest Hospital Videos
              </h2>
              <p class="mt-2 text-slate-300 text-sm sm:text-base leading-relaxed">
                Watch virtual tours, medical guidance, specialist talks, and health tips updated live from our YouTube channel.
              </p>
            </div>

            <a 
              :href="`https://www.youtube.com/channel/${CHANNEL_ID}`" 
              target="_blank" 
              class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-2xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-lg shadow-red-600/30 hover:shadow-red-600/50 transition-all duration-300 shrink-0 self-start md:self-center"
            >
              <i class="fab fa-youtube text-lg"></i>
              Visit Channel
            </a>
          </div>
        </div>

        <!-- Skeleton Loading State (3 items) -->
        <div v-if="ytLoading" class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div v-for="i in 3" :key="i" class="bg-white rounded-3xl border border-slate-200/80 p-4 space-y-4 animate-pulse">
            <div class="aspect-video bg-slate-200 rounded-2xl w-full"></div>
            <div class="h-4 bg-slate-200 rounded-md w-5/6"></div>
            <div class="h-4 bg-slate-200 rounded-md w-2/3"></div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="ytError" class="p-8 text-center bg-red-50 border border-red-200 rounded-3xl text-red-600 font-medium text-sm">
          {{ ytError }}
        </div>

        <!-- Single Row Video Grid (3 Videos max) -->
        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <article
            v-for="video in videos"
            :key="video.id"
            @click="openVideo(video.url)"
            class="group relative bg-white rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-xl hover:border-red-200 transition-all duration-300 overflow-hidden cursor-pointer flex flex-col justify-between"
          >
            <div>
              <!-- Video Thumbnail -->
              <div class="relative aspect-video w-full overflow-hidden bg-slate-900">
                <img 
                  :src="video.thumbnail" 
                  :alt="video.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90 group-hover:opacity-100"
                  @error="(e: any) => e.target.src = `https://img.youtube.com/vi/${video.id}/hqdefault.jpg`"
                />
                
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/20"></div>

                <!-- Play Overlay Icon -->
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-12 h-12 rounded-full bg-red-600/90 text-white flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:bg-red-600 transition-all duration-300">
                    <i class="fas fa-play translate-x-0.5 text-sm"></i>
                  </div>
                </div>
              </div>

              <!-- Title -->
              <div class="p-5">
                <h3 class="text-base font-bold text-slate-900 line-clamp-2 group-hover:text-red-600 transition-colors leading-snug">
                  {{ video.title }}
                </h3>
              </div>
            </div>

            <!-- Card Footer -->
            <div class="px-5 pb-5 flex items-center justify-between text-xs font-medium text-slate-500 border-t border-slate-100/80 mt-auto pt-3">
              <span class="flex items-center gap-1.5 text-red-600 font-semibold">
                Watch on YouTube
              </span>

              <span class="flex items-center gap-1.5">
                <i class="far fa-clock text-slate-400"></i>
                {{ video.publishedAt }}
              </span>
            </div>
          </article>
        </div>

      </div>
    </section>
  </div>
</template>