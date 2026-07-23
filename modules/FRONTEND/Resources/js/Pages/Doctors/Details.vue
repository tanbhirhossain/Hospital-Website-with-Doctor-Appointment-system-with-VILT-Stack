<template>
  <FrontendLayout>
    <div class="min-h-screen text-slate-800 relative overflow-hidden font-sans antialiased pb-24 selection:bg-blue-600/10 selection:text-blue-800 bg-[#F7F9FB]">

      <!-- Background Decorative Elements -->
      <div class="absolute top-[-10%] left-[-10%] w-[600px] h-[600px] bg-gradient-to-br from-blue-200/40 via-sky-200/20 to-transparent rounded-full blur-[120px] mix-blend-multiply pointer-events-none"></div>
      <div class="absolute top-[20%] right-[-15%] w-[500px] h-[500px] bg-gradient-to-bl from-amber-100/30 via-transparent to-transparent rounded-full blur-[120px] pointer-events-none"></div>

      <!-- Breadcrumb -->
      <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 pt-10 relative z-10">
        <Link :href="route('front.doctor.index')" class="inline-flex items-center gap-2 text-[11px] font-bold tracking-[0.13em] text-slate-400 hover:text-blue-600 uppercase transition-colors duration-300 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 rounded-md">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 transform group-hover:-translate-x-1 transition-transform">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
          </svg>
          Medical Directory
        </Link>
      </div>

      <!-- Inline status banner -->
      <Transition name="banner">
        <div
          v-if="notice.message"
          class="fixed top-6 left-1/2 -translate-x-1/2 z-50 max-w-md w-[calc(100%-2.5rem)]"
          role="status"
          aria-live="polite"
        >
          <div
            class="rounded-2xl px-5 py-4 shadow-2xl border flex items-start gap-3 backdrop-blur-xl"
            :class="notice.type === 'error' ? 'bg-rose-50/95 border-rose-100 text-rose-700' : 'bg-emerald-50/95 border-emerald-100 text-emerald-700'"
          >
            <i :class="notice.type === 'error' ? 'fas fa-circle-exclamation' : 'fas fa-circle-check'" class="mt-0.5 text-sm"></i>
            <p class="text-sm font-bold leading-snug">{{ notice.message }}</p>
            <button @click="notice.message = ''" class="ml-auto text-current/60 hover:text-current focus-visible:outline-none">
              <i class="fas fa-xmark text-xs"></i>
            </button>
          </div>
        </div>
      </Transition>

      <main class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 pt-6 relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">

        <!-- Left Column: Doctor Profile + Live Booking Summary -->
        <div class="lg:col-span-4 lg:sticky lg:top-10 space-y-5">
          <div class="rounded-[2.5rem] border border-white bg-white/80 backdrop-blur-2xl shadow-xl shadow-slate-200/50 p-6">
            <div class="relative w-full aspect-[4/5] rounded-[2rem] overflow-hidden bg-slate-100 mb-6">
              <img v-if="doctor.profile_image_url" :src="doctor.profile_image_url" :alt="doctor.name" class="w-full h-full object-cover object-top" />
              <div v-else class="w-full h-full flex items-center justify-center text-slate-300">
                <i class="fas fa-user-md text-6xl"></i>
              </div>

              <div class="absolute bottom-0 inset-x-0 h-14 bg-gradient-to-t from-black/50 to-transparent flex items-end">
                <svg viewBox="0 0 200 24" class="w-full h-6 px-4 pb-2 opacity-90" preserveAspectRatio="none">
                  <polyline points="0,12 40,12 50,4 60,20 70,12 200,12" fill="none" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>

              <span v-if="doctor.is_available !== false" class="absolute top-4 right-4 inline-flex items-center gap-1.5 bg-white/90 backdrop-blur px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-[0.12em] text-emerald-600 shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Accepting Patients
              </span>
            </div>

            <div class="mt-5 px-1">
              <span v-if="doctor.specialty" class="inline-flex items-center text-[10px] font-bold tracking-[0.13em] uppercase text-blue-700 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-md mb-2.5">
                {{ doctor.specialty }}
              </span>

              <h1 class="font-serif text-[26px] font-bold text-slate-900 tracking-tight leading-[1.15] mb-4">
                {{ doctor.name }}
              </h1>

              <div class="grid grid-cols-2 gap-2.5">
                <div class="bg-slate-50/90 border border-slate-100 px-4 py-3 rounded-2xl flex flex-col gap-0.5 shadow-sm">
                  <span class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.12em]">Consultation</span>
                  <span class="text-sm font-bold text-slate-900">?{{ doctor.visit_fee }}</span>
                </div>
                <div v-if="doctor.report_fee" class="bg-amber-50/70 border border-amber-100 px-4 py-3 rounded-2xl flex flex-col gap-0.5 shadow-sm">
                  <span class="text-[9px] font-bold text-amber-600/80 uppercase tracking-[0.12em]">Report Review</span>
                  <span class="text-sm font-bold text-amber-700">?{{ doctor.report_fee }}</span>
                </div>
              </div>

              <div v-if="doctor.phone" class="flex flex-col gap-2 mt-4">
                <a :href="'tel:' + doctor.phone" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-slate-900 text-white hover:bg-blue-700 text-xs font-bold tracking-wider shadow-md hover:shadow-lg transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                  <i class="fas fa-phone-alt text-[10px]"></i> Contact Helpline
                </a>
              </div>
            </div>
          </div>

          <!-- Live Booking Summary -->
          <div class="rounded-[2.5rem] border border-white bg-white/80 backdrop-blur-2xl shadow-xl shadow-slate-200/50 p-6">
            <h2 class="text-[11px] font-bold text-slate-400 tracking-[0.14em] uppercase mb-5 flex items-center gap-2">
              <i class="fas fa-clipboard-list text-blue-500 text-xs"></i> Your Appointment
            </h2>

            <dl class="space-y-3.5">
              <div class="flex items-start justify-between gap-3">
                <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em] pt-0.5">Date</dt>
                <dd class="text-sm font-bold text-right" :class="selectedDate ? 'text-slate-900' : 'text-slate-300'">
                  {{ selectedDate ? formattedSelectedDate : 'Not selected yet' }}
                </dd>
              </div>
              <div class="flex items-start justify-between gap-3 pt-3.5 border-t border-slate-100">
                <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em] pt-0.5">Time</dt>
                <dd class="text-sm font-bold text-right" :class="selectedSlot ? 'text-blue-700' : 'text-slate-300'">
                  {{ selectedSlot ? selectedSlot.start_time : '—' }}
                </dd>
              </div>
              <div class="flex items-start justify-between gap-3 pt-3.5 border-t border-slate-100">
                <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em] pt-0.5">Patient</dt>
                <dd class="text-sm font-bold text-right" :class="form.patient_name ? 'text-slate-900' : 'text-slate-300'">
                  {{ form.patient_name || '—' }}
                </dd>
              </div>
              <div class="flex items-start justify-between gap-3 pt-3.5 border-t border-slate-100">
                <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em] pt-0.5">Fee Due</dt>
                <dd class="text-base font-bold text-slate-900">?{{ doctor.visit_fee }}</dd>
              </div>
            </dl>
          </div>
        </div>

        <!-- Right Column: Profile & Booking System -->
        <div class="lg:col-span-8 space-y-6">

          <!-- Personal Profile Section -->
          <section class="rounded-[2.5rem] border border-white bg-white/75 backdrop-blur-xl p-6 md:p-8 shadow-xl shadow-slate-200/50">
            <h2 class="text-xs font-bold text-slate-400 tracking-[0.13em] uppercase mb-6 flex items-center gap-2">
              <span class="w-1.5 h-3 bg-blue-600 rounded-full"></span> Personal Profile
            </h2>

            <div v-if="hasValidBiography"
                 class="text-slate-600 text-sm md:text-[15px] leading-relaxed font-medium space-y-4 mb-6 prose prose-slate max-w-none border-b border-slate-100 pb-6"
                 v-html="doctor.biography">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
              <div v-if="doctor.specialty" class="flex flex-col gap-1.5 pb-2 border-b border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Primary Specialty</span>
                <span class="text-xs font-bold text-slate-800 tracking-wide">{{ doctor.specialty }}</span>
              </div>

              <div v-if="doctor.designation" class="flex flex-col gap-1.5 pb-2 border-b border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Academic Designation</span>
                <span class="text-xs font-bold text-slate-800 tracking-wide">{{ doctor.designation }}</span>
              </div>

              <div v-if="doctor.institute" class="flex flex-col gap-1.5 pb-2 border-b border-slate-100 md:col-span-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Institutional Affiliation</span>
                <span class="text-xs font-bold text-slate-800 leading-relaxed">{{ doctor.institute }}</span>
              </div>

              <div v-if="parsedQualifications.length" class="flex flex-col gap-2.5 pt-1 md:col-span-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Professional Credentials</span>
                <div class="flex flex-wrap gap-2">
                  <span v-for="(qual, index) in parsedQualifications" :key="index" class="inline-flex items-center text-xs font-bold text-slate-700 bg-blue-50/40 border border-blue-100/70 px-3.5 py-1.5 rounded-xl shadow-[0_2px_4px_rgba(0,0,0,0.01)] hover:bg-blue-50 transition-colors duration-200">
                    <i class="fas fa-graduation-cap text-blue-500 mr-2 text-[11px]"></i> {{ qual }}
                  </span>
                </div>
              </div>
            </div>
          </section>

          <!-- Booking Card -->
          <div ref="bookingCardRef" class="rounded-[2.5rem] border border-white bg-white shadow-xl shadow-slate-200/50 overflow-hidden scroll-mt-10">
            <!-- Stepper Header -->
            <div class="px-6 md:px-8 py-6 border-b border-slate-50 bg-slate-50/50">
              <div class="flex items-center justify-between mb-1">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-[0.12em] flex items-center gap-3">
                  <span class="w-2 h-4 bg-blue-600 rounded-full"></span>
                  Book Appointment
                </h2>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.12em]">Step {{ currentStep }} of 3</span>
              </div>

              <div class="flex items-center gap-2 mt-5">
                <div v-for="(label, idx) in stepLabels" :key="label" class="flex items-center flex-1 last:flex-none">
                  <div class="flex flex-col items-center gap-1.5" :class="idx + 1 < 3 ? 'w-full' : ''">
                    <div class="flex items-center w-full">
                      <div
                        class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300"
                        :class="[
                          currentStep === idx + 1 ? 'bg-blue-600 text-white ring-4 ring-blue-100' :
                          currentStep > idx + 1 ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500'
                        ]"
                      >
                        <i v-if="currentStep > idx + 1" class="fas fa-check"></i>
                        <span v-else>{{ idx + 1 }}</span>
                      </div>
                      <div v-if="idx < 2" class="h-[2px] flex-1 mx-2" :class="currentStep > idx + 1 ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                    </div>
                    <span class="hidden sm:block text-[9px] font-bold uppercase tracking-[0.12em]" :class="currentStep === idx + 1 ? 'text-blue-600' : 'text-slate-400'">{{ label }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Stage 1: Date & Time Selection -->
            <div v-if="currentStep === 1" class="p-6 md:p-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Custom Calendar Picker -->
                <div>
                  <h3 class="text-base font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <i class="far fa-calendar-alt text-blue-500 text-sm"></i> Select Preferred Date
                  </h3>

                  <div class="bg-slate-50 rounded-3xl p-5 border border-slate-100">
                    <div class="flex items-center justify-between mb-4 px-1">
                      <div class="flex items-baseline gap-2">
                        <span class="font-serif font-bold text-slate-900 text-[15px]">{{ currentMonthName }}</span>
                      </div>
                      <div class="flex items-center gap-1.5">
                        <button @click="jumpToToday" type="button" class="px-3 h-8 flex items-center justify-center rounded-full bg-white hover:bg-blue-50 hover:text-blue-600 transition text-[10px] font-bold uppercase tracking-wider text-slate-500 shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">Today</button>
                        <button @click="prevMonth" type="button" aria-label="Previous month" class="w-8 h-8 flex items-center justify-center rounded-full bg-white hover:bg-blue-50 hover:text-blue-600 transition shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"><i class="fas fa-chevron-left text-[10px]"></i></button>
                        <button @click="nextMonth" type="button" aria-label="Next month" class="w-8 h-8 flex items-center justify-center rounded-full bg-white hover:bg-blue-50 hover:text-blue-600 transition shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"><i class="fas fa-chevron-right text-[10px]"></i></button>
                      </div>
                    </div>

                    <div class="grid grid-cols-7 gap-1 text-center mb-2">
                      <span v-for="day in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']" :key="day" class="text-[9px] font-bold text-slate-400 tracking-wider">{{ day }}</span>
                    </div>

                    <Transition name="calendar-fade" mode="out-in">
                      <div class="grid grid-cols-7 gap-1.5" :key="currentMonthName">
                        <button
                          v-for="date in calendarDays"
                          :key="date.full ?? `${date.day}-${date.isCurrentMonth}`"
                          type="button"
                          @click="selectDate(date)"
                          :disabled="!date.isAvailable"
                          class="aspect-square flex flex-col items-center justify-center text-xs font-bold rounded-2xl transition-all duration-200 relative focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                          :class="[
                            date.isCurrentMonth ? '' : 'opacity-0 pointer-events-none',
                            selectedDate === date.full ? 'bg-gradient-to-br from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-200 scale-[1.08] z-10' :
                            date.isAvailable ? 'bg-white text-slate-700 hover:border-blue-400 hover:-translate-y-0.5 border border-slate-100 shadow-sm' : 'text-slate-300 bg-transparent cursor-not-allowed'
                          ]"
                        >
                          <span>{{ date.day }}</span>
                          <span
                            v-if="date.isAvailable && selectedDate !== date.full"
                            class="w-1 h-1 rounded-full bg-blue-400 mt-0.5"
                          ></span>
                          <span v-if="date.isToday && selectedDate !== date.full" class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-amber-400 ring-2 ring-slate-50"></span>
                        </button>
                      </div>
                    </Transition>
                  </div>
                </div>

                <!-- Time Slot Picker -->
                <div>
                  <h3 class="text-base font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <i class="far fa-clock text-blue-500 text-sm"></i> Available Time Slots
                  </h3>

                  <div v-if="loadingSlots" class="space-y-3">
                    <div v-for="i in 4" :key="i" class="h-12 bg-slate-50 animate-pulse rounded-2xl"></div>
                  </div>

                  <div v-else-if="timeSlots.length > 0" class="space-y-5 max-h-[360px] overflow-y-auto pr-1 -mr-1">
                    <div v-for="group in groupedSlots" :key="group.label" v-show="group.slots.length">
                      <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-[0.12em] mb-2.5">{{ group.label }}</span>
                      <div class="grid grid-cols-2 gap-3">
                        <!-- 🆕 Updated slot button with expiration check -->
                        <button
                          v-for="slot in group.slots"
                          :key="slot.id"
                          type="button"
                          @click="chooseSlot(slot)"
                          :disabled="!slot.available || isSlotExpired(slot)"
                          class="relative p-4 rounded-2xl border-2 text-sm font-semibold tracking-tight transition-all duration-200 text-center focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                          :class="[
                            selectedSlot?.id === slot.id
                              ? 'bg-slate-800 border-slate-800 text-white shadow-lg shadow-slate-200'
                              : (slot.available && !isSlotExpired(slot))
                                ? 'bg-slate-50 border-slate-200 text-slate-700 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700'
                                : 'bg-slate-100/60 border-transparent text-slate-400 cursor-not-allowed'
                          ]"
                        >
                          <span>{{ slot.start_time }}</span>
                          <!-- 🆕 Expired label -->
                          <span
                            v-if="!slot.available"
                            class="absolute -top-1.5 -right-1.5 bg-rose-500 text-white text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full shadow-sm"
                          >
                            Booked
                          </span>
                          <span
                            v-else-if="isSlotExpired(slot)"
                            class="absolute -top-1.5 -right-1.5 bg-amber-500 text-white text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full shadow-sm"
                          >
                            Expired
                          </span>
                        </button>
                      </div>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.12em] text-center pt-1 flex items-center justify-center gap-1.5">
                      <i class="fas fa-hand-pointer text-blue-300"></i> Tap a time to continue automatically
                    </p>
                  </div>

                  <div v-else class="h-full flex flex-col items-center justify-center text-center p-8 bg-slate-50 rounded-3xl border border-dashed border-slate-200 min-h-[220px]">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm">
                      <i class="fas fa-calendar-day text-slate-300"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.12em]">{{ selectedDate ? 'No open slots on this date' : 'Select a date first' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Stage 2: Patient Information Form -->
            <div v-if="currentStep === 2" class="p-6 md:p-8 animate-in fade-in slide-in-from-right-4 duration-500">
              <div class="max-w-2xl mx-auto">
                <h3 class="text-xl font-bold text-slate-900 mb-1">Patient Information</h3>
                <p class="text-slate-500 text-sm mb-8">Please provide the details of the person visiting the doctor.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label for="patient_name" class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.12em]">Full Name</label>
                    <input id="patient_name" v-model="form.patient_name" type="text" placeholder="e.g. John Doe" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all text-sm font-bold">
                  </div>

                  <div class="space-y-2">
                    <label for="patient_phone" class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.12em]">Phone Number</label>
                    <input id="patient_phone" v-model="form.patient_phone" type="tel" placeholder="017XXXXXXXX" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all text-sm font-bold">
                  </div>

                  <div class="space-y-2">
                    <label for="patient_age" class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.12em]">Age</label>
                    <input id="patient_age" v-model="form.patient_age" type="number" min="0" placeholder="Years" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all text-sm font-bold">
                  </div>

                  <div class="space-y-2">
                    <label for="patient_email" class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.12em]">Email <span class="text-slate-300 normal-case font-bold">(Optional)</span></label>
                    <input id="patient_email" v-model="form.patient_email" type="email" placeholder="you@example.com" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all text-sm font-bold">
                  </div>

                  <div class="space-y-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.12em]">Gender</span>
                    <div class="grid grid-cols-2 gap-3" role="radiogroup" aria-label="Patient gender">
                      <button
                        v-for="g in ['Male', 'Female']" :key="g"
                        type="button"
                        role="radio"
                        :aria-checked="form.patient_gender === g"
                        @click="form.patient_gender = g"
                        class="py-4 rounded-2xl border-2 text-sm font-bold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        :class="form.patient_gender === g ? 'bg-blue-600 border-blue-600 text-white' : 'bg-slate-50 border-slate-50 text-slate-500 hover:border-slate-200'"
                      >{{ g }}</button>
                    </div>
                  </div>

                  <div class="md:col-span-2 space-y-2">
                    <label for="reason" class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.12em]">Reason for Visit (Optional)</label>
                    <textarea id="reason" v-model="form.reason" rows="3" placeholder="Briefly describe the symptoms..." class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all text-sm font-bold"></textarea>
                  </div>
                </div>
              </div>
            </div>

            <!-- Stage 3: Confirmation -->
            <div v-if="currentStep === 3" class="p-6 md:p-8 animate-in fade-in zoom-in-95 duration-500 text-center">
              <div class="max-w-md mx-auto">
                <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl shadow-inner">
                  <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-2">Review Appointment</h3>
                <p class="text-slate-500 text-sm mb-8">Please verify the appointment details before confirming.</p>

                <div class="bg-slate-50 rounded-[2rem] border border-slate-100 p-6 text-left space-y-4 mb-8">
                  <div class="flex justify-between border-b border-slate-200/50 pb-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.12em]">Doctor</span>
                    <span class="text-sm font-bold text-slate-900">{{ doctor.name }}</span>
                  </div>
                  <div class="flex justify-between border-b border-slate-200/50 pb-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.12em]">Schedule</span>
                    <span class="text-sm font-bold text-blue-600">{{ formattedSelectedDate }} at {{ selectedSlot?.start_time }}</span>
                  </div>
                  <div class="flex justify-between border-b border-slate-200/50 pb-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.12em]">Patient</span>
                    <span class="text-sm font-bold text-slate-900">{{ form.patient_name }}</span>
                  </div>
                  <div v-if="form.patient_email" class="flex justify-between border-b border-slate-200/50 pb-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.12em]">Email</span>
                    <span class="text-sm font-bold text-slate-900">{{ form.patient_email }}</span>
                  </div>
                  <div class="flex justify-between pt-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.12em]">Total Fee</span>
                    <span class="text-lg font-bold text-slate-900">?{{ doctor.visit_fee }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer Navigation -->
            <div v-if="currentStep > 1" class="px-6 md:px-8 py-6 border-t border-slate-50 bg-slate-50/30 flex items-center justify-between">
              <button
                v-if="currentStep > 1"
                type="button"
                @click="currentStep--; scrollToBooking()"
                class="px-6 py-4 rounded-2xl text-xs font-bold text-slate-500 hover:text-slate-900 hover:bg-white transition-all border border-transparent hover:border-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
              >
                Go Back
              </button>
              <div v-else></div>

              <button
                type="button"
                @click="handleNext"
                :disabled="!canProceed || isSubmitting"
                class="px-10 py-4 bg-blue-600 text-white rounded-2xl text-xs font-bold tracking-[0.12em] uppercase shadow-xl shadow-blue-200 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-95 flex items-center gap-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
              >
                <span>{{ currentStep === 3 ? 'Confirm & Book' : 'Continue' }}</span>
                <i v-if="!isSubmitting" class="fas fa-arrow-right"></i>
                <i v-else class="fas fa-spinner fa-spin"></i>
              </button>
            </div>
          </div>
        </div>
      </main>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'; // 🆕 added onMounted, nextTick (already present)
import { Link } from '@inertiajs/vue3';
import FrontendLayout from '../../Layout/FrontendLayout.vue';
import axios from 'axios';

const props = defineProps({
    doctor: { type: Object, required: true }
});

// Profile Helpers
const hasValidBiography = computed(() => {
    if (!props.doctor.biography) return false;
    const cleanBio = props.doctor.biography.trim();
    if (cleanBio.startsWith("Here’s a situation") || cleanBio.includes("Here’s a situation that comes up")) {
        return false;
    }
    return cleanBio.length > 0;
});

const parseJsonField = (field) => {
    if (!field) return [];
    if (Array.isArray(field)) return field;
    try {
        return typeof field === 'string' ? JSON.parse(field) : [];
    } catch (e) {
        return field.split(',').map(item => item.trim());
    }
};

const parsedQualifications = computed(() => parseJsonField(props.doctor.qualifications));

// State
const stepLabels = ['Schedule', 'Details', 'Confirm'];
const currentStep = ref(1);
const loadingSlots = ref(false);
const isSubmitting = ref(false);
const availableDates = ref([]);
const timeSlots = ref([]);
const selectedDate = ref(null);
const selectedSlot = ref(null);
const notice = ref({ message: '', type: 'success' });
let noticeTimeout = null;
const bookingCardRef = ref(null);

const showNotice = (message, type = 'success') => {
  notice.value = { message, type };
  clearTimeout(noticeTimeout);
  noticeTimeout = setTimeout(() => { notice.value.message = ''; }, 5000);
};

// Form handling
const form = ref({
  patient_name: '',
  patient_phone: '',
  patient_age: '',
  patient_email: '',
  patient_gender: 'Male',
  reason: ''
});

// Calendar Logic
const viewDate = ref(new Date());
const currentMonthName = computed(() => {
  return viewDate.value.toLocaleString('default', { month: 'long', year: 'numeric' });
});

const calendarDays = computed(() => {
  const year = viewDate.value.getFullYear();
  const month = viewDate.value.getMonth();

  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const daysInPrevMonth = new Date(year, month, 0).getDate();

  const days = [];

  for (let i = firstDay - 1; i >= 0; i--) {
    days.push({ day: daysInPrevMonth - i, isCurrentMonth: false, full: null, isAvailable: false });
  }

  const today = new Date();
  today.setHours(0, 0, 0, 0);

  for (let i = 1; i <= daysInMonth; i++) {
    const fullDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
    const d = new Date(year, month, i);

    const isAvailable = availableDates.value.some(ad => ad.date === fullDate);

    days.push({
      day: i,
      isCurrentMonth: true,
      full: fullDate,
      isAvailable: isAvailable,
      isToday: d.getTime() === today.getTime()
    });
  }

  const remaining = 42 - days.length;
  for (let i = 1; i <= remaining; i++) {
    days.push({ day: i, isCurrentMonth: false, full: null, isAvailable: false });
  }

  return days;
});

const prevMonth = () => { viewDate.value = new Date(viewDate.value.getFullYear(), viewDate.value.getMonth() - 1, 1); };
const nextMonth = () => { viewDate.value = new Date(viewDate.value.getFullYear(), viewDate.value.getMonth() + 1, 1); };
const jumpToToday = () => { viewDate.value = new Date(); };

// Group time slots by period of day
const groupedSlots = computed(() => {
  const groups = {
    Morning: [],
    Afternoon: [],
    Evening: []
  };

  for (const slot of timeSlots.value) {
    const hour = parseInt(String(slot.start_time).split(':')[0], 10);
    if (hour < 12) groups.Morning.push(slot);
    else if (hour < 17) groups.Afternoon.push(slot);
    else groups.Evening.push(slot);
  }

  return Object.entries(groups).map(([label, slots]) => ({ label, slots }));
});

// 🆕 Helper: check if a date string (YYYY-MM-DD) is today
const isToday = (dateStr) => {
  if (!dateStr) return false;
  const today = new Date();
  const d = new Date(dateStr);
  return d.getFullYear() === today.getFullYear() &&
         d.getMonth() === today.getMonth() &&
         d.getDate() === today.getDate();
};

// 🆕 Check if a slot is expired (only for today's date)
const isSlotExpired = (slot) => {
  if (!selectedDate.value) return false;
  if (!isToday(selectedDate.value)) return false;
  if (!slot.start_time) return false;

  const now = new Date();
  const [hours, minutes] = slot.start_time.split(':').map(Number);
  const slotTime = new Date();
  slotTime.setHours(hours, minutes, 0, 0);
  return slotTime < now;
};

// Computed
const canProceed = computed(() => {
  if (currentStep.value === 1) return selectedDate.value && selectedSlot.value;
  if (currentStep.value === 2) return form.value.patient_name && form.value.patient_phone && form.value.patient_age;
  return true;
});

const formattedSelectedDate = computed(() => {
  if (!selectedDate.value) return '';
  return new Date(selectedDate.value).toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
});

// Actions
const fetchAvailability = async () => {
  try {
    const start = new Date();
    const end = new Date();
    end.setDate(start.getDate() + 60);

    const response = await axios.get(route('api.doctors.availability', {
      doctor_id: props.doctor.id,
      start_date: start.toISOString().split('T')[0],
      end_date: end.toISOString().split('T')[0]
    }));
    availableDates.value = response.data;
  } catch (error) {
    console.error("Error fetching availability:", error);
    showNotice('Could not load available dates. Please refresh the page.', 'error');
  }
};

const selectDate = async (date) => {
  if (!date.isAvailable) return;
  selectedDate.value = date.full;
  selectedSlot.value = null;
  loadingSlots.value = true;

  try {
    const response = await axios.get(route('api.doctors.available-slots', {
      doctor_id: props.doctor.id,
      doctorId: props.doctor.id,
      date: date.full
    }));
    timeSlots.value = response.data;
  } catch (error) {
    console.error("Error fetching time slots:", error);
    showNotice('Could not load time slots for that date.', 'error');
  } finally {
    loadingSlots.value = false;
  }
};

const scrollToBooking = async () => {
  await nextTick();
  bookingCardRef.value?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
};

// 🆕 Updated chooseSlot to prevent expired selections
const chooseSlot = async (slot) => {
  if (!slot.available) return;
  if (isSlotExpired(slot)) {
    showNotice('This time slot has already expired.', 'error');
    return;
  }
  selectedSlot.value = slot;
  await new Promise((resolve) => setTimeout(resolve, 260));
  currentStep.value = 2;
  scrollToBooking();
};

const handleNext = async () => {
  if (!canProceed.value) return;

  if (currentStep.value < 3) {
    currentStep.value++;
    scrollToBooking();
  } else {
    submitAppointment();
  }
};

// Reset the entire booking form to initial state
const resetBooking = async () => {
  currentStep.value = 1;
  selectedDate.value = null;
  selectedSlot.value = null;
  timeSlots.value = [];
  // Reset form fields
  form.value = {
    patient_name: '',
    patient_phone: '',
    patient_age: '',
    patient_email: '',
    patient_gender: 'Male',
    reason: ''
  };
  // Re-fetch availability (to refresh booked slots)
  await fetchAvailability();
  // Scroll to top of booking card
  await scrollToBooking();
};

// Normalize time to HH:MM
const normalizeTime = (value) => {
  const str = String(value ?? '').trim();
  if (!str) return '';

  const match = str.match(/^(\d{1,2}):(\d{2})(?::\d{2})?\s*([AaPp][Mm])?$/);
  if (!match) return str;

  let [, hh, mm, meridiem] = match;
  let hours = parseInt(hh, 10);

  if (meridiem) {
    const isPM = meridiem.toLowerCase() === 'pm';
    if (hours === 12) hours = isPM ? 12 : 0;
    else if (isPM) hours += 12;
  }

  return `${String(hours).padStart(2, '0')}:${mm}`;
};

const submitAppointment = async () => {
  if (!selectedSlot.value || !selectedDate.value) {
    showNotice('Please select a valid date and time slot.', 'error');
    return;
  }

  isSubmitting.value = true;

  try {
    let formattedStart = normalizeTime(
      selectedSlot.value.start_time ?? selectedSlot.value.start ?? selectedSlot.value.time ?? selectedSlot.value.value
    );
    let formattedEnd = normalizeTime(selectedSlot.value.end_time ?? selectedSlot.value.end);

    if (!formattedEnd && formattedStart) {
      const [hours, minutes] = formattedStart.split(':').map(Number);
      const timeObject = new Date();
      timeObject.setHours(hours, minutes, 0, 0);
      timeObject.setMinutes(timeObject.getMinutes() + 20);
      formattedEnd = timeObject.toTimeString().slice(0, 5);
    }

    const formattedDate = selectedDate.value.includes('T') ? selectedDate.value.split('T')[0] : selectedDate.value;

    await axios.post(route('api.doctors.book-appointment', { doctorId: props.doctor.id }), {
      doctor_id: props.doctor.id,
      appointment_date: formattedDate,
      start_time: formattedStart,
      end_time: formattedEnd,
      patient_name: form.value.patient_name,
      patient_phone: form.value.patient_phone,
      patient_age: form.value.patient_age,
      patient_email: form.value.patient_email,
      patient_gender: form.value.patient_gender.toLowerCase(),
      reason: form.value.reason
    });

    showNotice('Appointment booked successfully!', 'success');
    // Reset the form after successful booking
    await resetBooking();
  } catch (error) {
    console.error("Booking failed:", error.response?.data || error.message);
    showNotice(error.response?.data?.message || 'An error occurred while saving the appointment.', 'error');
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(() => {
  fetchAvailability();
});
</script>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
@import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&display=swap');

.font-serif {
  font-family: 'Fraunces', ui-serif, Georgia, serif;
}

.animate-in {
  animation-duration: 0.4s;
  animation-timing-function: cubic-bezier(0.16, 1, 0.3, 1);
  animation-fill-mode: forwards;
}

@keyframes slide-in-from-bottom-4 {
  from { transform: translateY(10px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

@keyframes slide-in-from-right-4 {
  from { transform: translateX(10px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.calendar-fade-enter-active,
.calendar-fade-leave-active {
  transition: opacity 0.18s ease, transform 0.18s ease;
}
.calendar-fade-enter-from {
  opacity: 0;
  transform: translateX(6px);
}
.calendar-fade-leave-to {
  opacity: 0;
  transform: translateX(-6px);
}

.banner-enter-active,
.banner-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.banner-enter-from,
.banner-leave-to {
  opacity: 0;
  transform: translate(-50%, -12px);
}

input, textarea {
  outline: none;
}

@media (prefers-reduced-motion: reduce) {
  .animate-in,
  .animate-pulse {
    animation: none !important;
  }
}
</style>