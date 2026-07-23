
<script setup lang="ts">
defineProps<{
  doctors: Array<{
    slug: string
    color: string
    name: string
    specialty: string
    qualifications: string
    designation: string
    institute: string
    profile_image_url: string
  }>
}>()
</script>

<template>
  <section id="doctors" role="region" aria-label="Our doctors" class="py-20 bg-gradient-to-br from-slate-50 via-blue-50/30 to-slate-100 scroll-reveal reveal-doctors fade-in-0 slide-in-from-right-10 duration-700">
      <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-200 to-transparent"></div>

    <div class="container mx-auto px-4 max-w-7xl">
      
      <!-- Section Header -->
      <div class="text-center mb-16">
        <!-- <span class="inline-block py-1 px-4 rounded-full bg-blue-100 text-blue-800 text-xs font-bold tracking-wider uppercase mb-3">
          Expert Care Team
        </span> -->
        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">Meet Our Specialist Doctors</h2>
        <p class="text-base md:text-lg text-slate-600 max-w-2xl mx-auto">
          Highly qualified and experienced medical professionals dedicated to providing exceptional patient-centered care.
        </p>
      </div>

      <!-- Doctors Grid -->
      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 stagger-grid">
        <div 
          v-for="doc in doctors" 
          :key="doc.slug" 
          class="group bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between hover:-translate-y-1.5"
        >
          <!-- Doctor Image Container -->
          <div class="relative h-72 w-full bg-slate-100 overflow-hidden flex items-center justify-center">
            <img 
              v-if="doc.profile_image_url" 
              :src="doc.profile_image_url" 
              :alt="doc.name" 
              class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" 
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-slate-200 text-slate-400">
              <i class="fas fa-user-md text-6xl"></i>
            </div>
            <!-- Specialty Badge Overlay -->
            <div class="absolute bottom-3 left-3 right-3 flex justify-center">
              <span class="bg-white/90 backdrop-blur-md text-blue-900 font-semibold text-xs py-1 px-3 rounded-full shadow-sm border border-slate-200/50 truncate max-w-full">
                {{ doc.specialty }}
              </span>
            </div>
          </div>

          <!-- Doctor Information -->
          <div class="p-6 flex-1 flex flex-col justify-between">
            <div>
              <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-1 mb-1">
                {{ doc.name }}
              </h3>
              
              <p class="text-xs font-medium text-slate-500 mb-4 line-clamp-1">
                {{ doc.qualifications }}
              </p>

              <hr class="border-slate-100 mb-4" />

              <div class="space-y-1.5 mb-6 text-xs text-slate-600">
                <p class="font-medium text-slate-800 line-clamp-1 flex items-center gap-1.5">
                  <i class="fas fa-stethoscope text-blue-600 w-4"></i>
                  <span>{{ doc.designation }}</span>
                </p>
                <p class="text-slate-500 line-clamp-1 flex items-center gap-1.5">
                  <i class="fas fa-hospital text-slate-400 w-4"></i>
                  <span>{{ doc.institute }}</span>
                </p>
              </div>
            </div>

            <!-- Action Button -->
            <a 
              :href="`/doctors/${doc.slug}`" 
              class="w-full bg-slate-900 hover:bg-blue-700 text-white text-center py-2.5 px-4 rounded-xl text-xs font-semibold shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 group/btn"
            >
              <span>Book Appointment</span>
              <i class="fas fa-arrow-right text-[10px] transition-transform group-hover/btn:translate-x-0.5"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- View All Doctors Footer Action -->
      <div class="text-center mt-14">
        <a 
          href="/doctors" 
          class="inline-flex items-center gap-2 bg-white text-slate-800 border border-slate-200/80 px-8 py-3.5 rounded-xl font-semibold text-sm shadow-sm hover:bg-slate-50 hover:shadow-md transition-all transform hover:scale-[1.02]"
        >
          <i class="fas fa-users text-blue-600"></i>
          <span>View All Doctors</span>
        </a>
      </div>

    </div>
  </section>
</template>

