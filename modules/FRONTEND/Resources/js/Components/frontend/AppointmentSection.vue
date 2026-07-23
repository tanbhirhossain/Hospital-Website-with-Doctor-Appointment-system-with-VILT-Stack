<script setup lang="ts">
import AppointmentBookingWizard from '../frontend/AppointmentBookingWizard.vue'

defineProps<{
  availableWeekdays: number[]
  blockedDates: string[]
}>()
</script>

<template>
  <section id="appointment" role="region" aria-label="Book appointment" class="py-20 bg-gradient-to-br from-blue-800 to-sky-500 text-white relative overflow-hidden premium-shine-section scroll-reveal reveal-appointment fade-in-0 zoom-in-95 duration-700">
    <div class="absolute inset-0 opacity-10">
      <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
    </div>
    <div class="container mx-auto max-w-[1500px] px-4 relative z-10">
      <div class="grid lg:grid-cols-12 gap-10 items-start">
        <div class="lg:col-span-5">
          <div class="inline-block bg-white/20 px-4 py-2 rounded-full text-sm font-semibold mb-4">
            <i class="fas fa-calendar-check mr-2"></i>Book Your Appointment
          </div>
          <h2 class="text-4xl md:text-5xl font-bold mb-6">Schedule Your Visit Today</h2>
          <p class="text-xl mb-8 text-white/90">Get expert medical care at your convenience. Book an appointment with our specialists.</p>
          <div class="space-y-4">
            <div v-for="feat in [
              { icon: 'fa-clock', title: 'Quick & Easy', desc: 'Book in just a few steps' },
              { icon: 'fa-user-md', title: 'Choose Your Doctor', desc: 'Select from our expert specialists' },
              { icon: 'fa-calendar-alt', title: 'Flexible Scheduling', desc: 'Pick your preferred time slot' },
            ]" :key="feat.title" class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <i :class="`fas ${feat.icon} text-2xl`"></i>
              </div>
              <div>
                <h4 class="font-semibold text-lg">{{ feat.title }}</h4>
                <p class="text-white/80">{{ feat.desc }}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="lg:col-span-7">
          <AppointmentBookingWizard
            :available-weekdays="availableWeekdays"
            :blocked-dates="blockedDates"
          />
        </div>
      </div>
    </div>
  </section>
</template>