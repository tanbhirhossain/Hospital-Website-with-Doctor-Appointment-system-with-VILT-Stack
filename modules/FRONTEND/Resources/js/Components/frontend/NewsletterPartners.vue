<script setup lang="ts">
import { ref } from 'vue'

defineProps<{
  partners: Array<{ name: string; logo: string }>
}>()

const email = ref('')
const success = ref(false)

const submit = () => {
  if (!email.value) return
  success.value = true
  email.value = ''
}
</script>

<template>
  <section id="newsletter-partners" class="relative py-24 bg-slate-900/5 overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-200 to-transparent"></div>

    <!-- Background Glow Accents -->
    <div class="absolute top-1/2 left-1/4 -translate-y-1/2 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="container mx-auto px-4 relative z-10">
      
      <!-- Newsletter Card -->
      <div class="max-w-4xl mx-auto mb-20">
        <div class="relative bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
          <div class="grid lg:grid-cols-12 items-stretch">
            
            <div class="lg:col-span-5 bg-gradient-to-br from-blue-900 via-blue-800 to-sky-600 text-white p-8 md:p-10 flex flex-col justify-between">
              <div>
                <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center mb-6 border border-white/20">
                  <i class="fas fa-paper-plane text-sky-200 text-sm"></i>
                </div>
                <span class="inline-block text-sky-200 text-xs font-bold uppercase tracking-widest mb-2">Stay Connected</span>
                <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight mb-3">Get Health Insights Delivered</h2>
                <p class="text-blue-100/80 text-sm leading-relaxed">Receive expert health tips, exclusive package offers, and critical hospital announcements.</p>
              </div>

              <div class="flex flex-wrap gap-2 pt-6 mt-6 border-t border-white/10 text-xs text-blue-100/90 font-medium">
                <span class="flex items-center gap-1.5"><i class="fas fa-check text-emerald-400"></i> Weekly tips</span>
                <span class="flex items-center gap-1.5"><i class="fas fa-check text-emerald-400"></i> No spam</span>
              </div>
            </div>

            <div class="lg:col-span-7 p-8 md:p-10 flex flex-col justify-center bg-white">
              <div v-if="success" class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-4 rounded-xl font-medium text-center text-sm flex items-center justify-center gap-2 mb-2">
                <i class="fas fa-check-circle text-emerald-600 text-base"></i> Subscribed successfully. Welcome aboard!
              </div>

              <form v-else @submit.prevent="submit" class="space-y-4">
                <div>
                  <label for="newsletter-email" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Email Address</label>
                  <input 
                    id="newsletter-email"
                    v-model="email" 
                    type="email" 
                    required 
                    placeholder="name@example.com" 
                    class="w-full px-4 py-3.5 rounded-xl border border-slate-200 text-slate-800 text-sm focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none bg-slate-50/50 transition-all" 
                  />
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3.5 rounded-xl text-sm transition-all shadow-md shadow-blue-500/20">
                  Subscribe Now
                </button>
              </form>

              <p class="text-xs text-slate-400 mt-4 leading-normal">
                By subscribing, you agree to receive healthcare updates from AMZ Hospital. You can unsubscribe at any time.
              </p>
            </div>

          </div>
        </div>
      </div>

      <!-- Corporate Partners Section -->
      <div>
        <div class="text-center mb-8">
          <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Trusted By Leading Organizations</p>
        </div>
        
        <!-- Seamless Marquee Track -->
        <div class="overflow-hidden relative w-full" style="mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);">
          <div class="flex w-max animate-marquee">
            <!-- First Group -->
            <div class="flex items-center gap-6 shrink-0 pr-6">
              <div 
                v-for="(partner, i) in partners" 
                :key="`${partner.name}-primary-${i}`" 
                class="h-20 w-44 bg-white border border-slate-100 rounded-2xl p-4 flex items-center justify-center shadow-sm hover:shadow-md transition-all group"
              >
                <img 
                  :src="partner.logo" 
                  :alt="partner.name" 
                  class="max-h-10 max-w-[120px] w-auto h-auto object-contain opacity-75 group-hover:opacity-100 transition-opacity" 
                />
              </div>
            </div>
            <!-- Duplicate Group for Infinite Effect -->
            <div class="flex items-center gap-6 shrink-0 pr-6" aria-hidden="true">
              <div 
                v-for="(partner, i) in partners" 
                :key="`${partner.name}-clone-${i}`" 
                class="h-20 w-44 bg-white border border-slate-100 rounded-2xl p-4 flex items-center justify-center shadow-sm hover:shadow-md transition-all group"
              >
                <img 
                  :src="partner.logo" 
                  :alt="partner.name" 
                  class="max-h-10 max-w-[120px] w-auto h-auto object-contain opacity-75 group-hover:opacity-100 transition-opacity" 
                />
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </section>
</template>

<style scoped>
@keyframes marquee {
  from {
    transform: translateX(0);
  }
  to {
    transform: translateX(-50%);
  }
}

.animate-marquee {
  animation: marquee 25s linear infinite;
}

.animate-marquee:hover {
  animation-play-state: paused;
}
</style>