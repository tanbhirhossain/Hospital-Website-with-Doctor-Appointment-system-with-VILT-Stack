<template>
  <div class="relative w-full overflow-hidden rounded-[2rem] border border-slate-200/80 bg-white/95 p-4 shadow-[0_34px_80px_-42px_rgba(15,23,42,.6)] backdrop-blur-sm md:h-[680px] md:p-6">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_0%_0%,rgba(14,165,233,.12),transparent_36%),radial-gradient(circle_at_100%_100%,rgba(59,130,246,.1),transparent_38%)]"></div>
    
    <!-- Loading Overlay -->
    <div v-if="loading" class="absolute inset-0 z-10 flex items-center justify-center bg-white/80 backdrop-blur-sm">
      <div class="flex flex-col items-center gap-2">
        <div class="h-8 w-8 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
        <p class="text-sm font-medium text-slate-600">Loading...</p>
      </div>
    </div>

    <div class="relative flex min-h-0 flex-col md:h-full">
      <header class="mb-5 rounded-2xl border border-slate-200 bg-gradient-to-r from-slate-50 to-white p-4">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500">Appointment Desk</p>
            <h3 class="text-xl font-black tracking-tight text-slate-900 md:text-2xl">Book Your Consultation</h3>
            <p class="text-xs text-slate-500 md:text-sm">Choose a department, select doctor, and confirm in 6 quick steps.</p>
          </div>
          <div class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-right shadow-sm sm:w-auto">
            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Progress</p>
            <p class="text-sm font-bold text-slate-900">{{ currentStep }}/{{ steps.length }}</p>
          </div>
        </div>
        <div class="h-2 rounded-full bg-slate-200/80">
          <div class="h-full rounded-full bg-gradient-to-r from-cyan-500 via-sky-500 to-blue-700 transition-all duration-500" :style="{ width: progressWidth }"></div>
        </div>
      </header>

      <!-- Success Message -->
      <div v-if="success" class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
        <i class="fas fa-check-circle mr-2"></i>Appointment request confirmed. We'll contact you shortly.
      </div>

      <!-- Error Message -->
      <div v-if="errorMsg" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ errorMsg }}
      </div>

      <div class="grid min-h-0 flex-1 gap-3 lg:grid-cols-12">
        <!-- Sidebar Steps -->
        <aside class="hidden rounded-2xl border border-slate-200 bg-slate-50/85 p-3 lg:col-span-3 lg:block lg:h-full lg:overflow-y-auto">
          <p class="mb-3 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-500">Journey</p>
          <div class="space-y-2">
            <div
              v-for="(step, index) in steps"
              :key="step.key"
              :class="[
                'flex items-center gap-2 rounded-xl border px-2.5 py-2 text-sm transition',
                currentStep === index + 1
                  ? 'border-blue-200 bg-blue-50 text-blue-800'
                  : currentStep > index + 1
                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                    : 'border-slate-200 bg-white text-slate-600',
              ]"
            >
              <span class="grid h-6 w-6 place-items-center rounded-lg border border-slate-200 bg-white text-[11px] font-bold">
                <i v-if="currentStep > index + 1" class="fas fa-check"></i>
                <span v-else>{{ index + 1 }}</span>
              </span>
              <span class="font-semibold">{{ step.label }}</span>
            </div>
          </div>
        </aside>

        <!-- Main Content -->
        <section class="rounded-2xl border border-slate-200 bg-white p-4 md:p-5 lg:col-span-6 lg:flex lg:min-h-0 lg:flex-col">
          <div class="mb-4 flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Current Step</p>
            <p class="text-sm font-semibold text-slate-800">{{ activeStep.label }}</p>
          </div>
          <div class="min-h-0 flex-1 overflow-y-auto pr-1">
            <!-- Step 1: Department -->
            <div v-if="currentStep === 1" class="grid gap-2.5 sm:grid-cols-2">
              <button
                v-for="dept in departments"
                :key="dept.id"
                type="button"
                @click="chooseDepartment(dept.id)"
                :class="[
                  'group rounded-2xl border p-3 text-left transition',
                  booking.department === dept.id
                    ? 'border-blue-400 bg-blue-50 shadow-[0_12px_24px_-20px_rgba(59,130,246,.9)]'
                    : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50',
                ]"
              >
                <div :class="`mb-2 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br ${dept.color || 'from-blue-600 to-cyan-600'} text-white shadow round-3xl`">
                  <i :class="`fas ${dept.text_icon || 'fa-hospital'}`"></i>
                </div>
                <p class="text-sm font-semibold text-slate-900">{{ dept.name }}</p>
              </button>
            </div>

            <!-- Step 2: Doctor -->
            <div v-else-if="currentStep === 2" class="space-y-2.5">
              <button
                v-for="doc in doctors"
                :key="doc.id"
                type="button"
                @click="chooseDoctor(doc.id)"
                :class="[
                  'w-full rounded-2xl border p-3 text-left transition',
                  booking.doctor === doc.id
                    ? 'border-blue-400 bg-blue-50 shadow-[0_12px_24px_-20px_rgba(59,130,246,.9)]'
                    : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50',
                ]"
              >
                <p class="text-sm font-semibold text-slate-900">{{ doc.name }}</p>
                <p class="text-xs text-slate-600">{{ doc.specialty || doc.title || 'Specialist' }}</p>
              </button>
              <div v-if="!doctors.length" class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-700">
                No doctors available for this department.
              </div>
            </div>

            <!-- Step 3: Calendar -->
            <div v-else-if="currentStep === 3" class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
              <div class="mb-3 flex items-center justify-between">
                <button type="button" @click="prevMonth" class="grid h-8 w-8 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600"><i class="fas fa-chevron-left text-xs"></i></button>
                <p class="text-sm font-bold text-slate-900">{{ monthLabel }}</p>
                <button type="button" @click="nextMonth" class="grid h-8 w-8 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600"><i class="fas fa-chevron-right text-xs"></i></button>
              </div>
              <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-slate-400">
                <span v-for="d in ['S', 'M', 'T', 'W', 'T', 'F', 'S']" :key="d">{{ d }}</span>
              </div>
              <div class="mt-1 grid grid-cols-7 gap-1">
                <div v-for="(day, idx) in calendarDays" :key="day ? day.toISOString() : `blank-${idx}`" class="aspect-square">
                  <button
                    v-if="day"
                    type="button"
                    @click="chooseDate(day)"
                    :disabled="!isAvailableDate(day)"
                    :class="[
                      'h-full w-full rounded-md border text-[11px] font-semibold',
                      booking.date === toIsoDate(day)
                        ? 'border-blue-600 bg-blue-600 text-white'
                        : isAvailableDate(day)
                          ? 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:border-emerald-400'
                          : 'cursor-not-allowed border-slate-100 bg-slate-100 text-slate-400',
                    ]"
                  >
                    {{ day.getDate() }}
                  </button>
                  <div v-else class="h-full w-full"></div>
                </div>
              </div>
              <p v-if="!hasAvailableDatesInMonth" class="mt-2 text-center text-xs text-slate-500">No available dates this month.</p>
            </div>

            <!-- Step 4: Time Slots -->
            <div v-else-if="currentStep === 4">
              <div v-if="!availableSlotsForSelectedDate.length" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                No slots available for selected date.
              </div>
              <div v-else class="grid grid-cols-2 gap-2.5">
                <button
                v-for="slot in availableSlotsForSelectedDate"
                :key="slot.start_time"
                type="button"
                @click="selectTime(slot)"
                :class="[
                    'rounded-xl border px-3 py-2 text-sm font-semibold',
                    booking.time === slot.start_time ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300',
                ]"
                >
                {{ slot.start_time }} - {{ slot.end_time }}
                </button>
              </div>
            </div>

            <!-- Step 5: Patient Info -->
            <div v-else-if="currentStep === 5" class="space-y-2.5">
              <div>
                <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Patient Name *</label>
                <input v-model="booking.name" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 caret-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" />
              </div>
              <div>
                <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Phone *</label>
                <input v-model="booking.phone" type="tel" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 caret-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" />
              <!-- ইনপুট ফিল্ডের ঠিক নিচে এটি যোগ করো -->
                <p v-if="booking.phone && !isValidBdPhone(booking.phone)" class="mt-1 text-xs text-rose-500">
                  আপনার ফোন নম্বরটি সঠিক নয় (০১৭XXXXXXXX ফরম্যাটে ১১ ডিজিট দিন)
                </p>
              </div>
              <div>
                <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Email</label>
                <input v-model="booking.email" type="email" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 caret-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" />
              </div>
              <div>
                <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Problems</label>
                <textarea v-model="booking.problem" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 caret-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
              </div>
            </div>

            <!-- Step 6: Confirm -->
            <div v-else class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-3">
              <p class="text-sm font-semibold text-emerald-800">Ready to confirm this booking. Check the summary on the right.</p>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="mt-4 flex flex-col gap-2 border-t border-slate-100 pt-3 sm:flex-row sm:items-center sm:justify-between">
            <button type="button" @click="goBack" :disabled="currentStep === 1" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:opacity-40">Back</button>
            <button
              v-if="currentStep < steps.length"
              type="button"
              @click="goNext"
              :disabled="!canContinue"
              class="rounded-xl bg-gradient-to-r from-cyan-600 to-blue-700 px-5 py-2 text-sm font-semibold text-white shadow-[0_12px_22px_-14px_rgba(3,105,161,.95)] transition hover:brightness-105 disabled:opacity-50"
            >
              Continue
            </button>
            <button
              v-else
              type="button"
              @click="confirmBooking"
              :disabled="loading"
              class="rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-5 py-2 text-sm font-semibold text-white shadow-[0_12px_22px_-14px_rgba(5,150,105,.95)] transition hover:brightness-105 disabled:opacity-50"
            >
              {{ loading ? 'Booking...' : 'Confirm Booking' }}
            </button>
          </div>
        </section>

        <!-- Live Review Sidebar -->
        <aside class="rounded-2xl border border-slate-200 bg-slate-50/90 p-3 lg:col-span-3 lg:h-full w-ful lg:overflow-y-auto">
          <div class="mb-3 flex items-center justify-between">
            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-500">Live Review</p>
            <span class="rounded-lg bg-blue-100 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-blue-700">Realtime</span>
          </div>
          <div class="space-y-2 text-xs">
            <div class="rounded-xl border border-slate-200 bg-white px-3 py-2"><span class="text-slate-500">Department:</span> <span class="font-semibold text-slate-900">{{ selectedDepartment?.name || '-' }}</span></div>
            <div class="rounded-xl border border-slate-200 bg-white px-3 py-2"><span class="text-slate-500">Doctor:</span> <span class="font-semibold text-slate-900">{{ selectedDoctor?.name || '-' }}</span></div>
            <div class="rounded-xl border border-slate-200 bg-white px-3 py-2"><span class="text-slate-500">Date:</span> <span class="font-semibold text-slate-900">{{ formatDate(booking.date) }}</span></div>
            <div class="rounded-xl border border-slate-200 bg-white px-3 py-2"><span class="text-slate-500">Time:</span> <span class="font-semibold text-slate-900">{{ booking.time || '-' }}</span></div>
            <div class="rounded-xl border border-slate-200 bg-white px-3 py-2">
              <p><span class="text-slate-500">Patient:</span> <span class="font-semibold text-slate-900">{{ booking.name || '-' }}</span></p>
              <p class="text-xs text-slate-600">{{ booking.phone || '-' }}</p>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, watch } from 'vue'
import axios from 'axios'

// ----------------------------
// 1. Types
// ----------------------------
interface Doctor {
  id: string | number
  name: string
  specialty?: string
  title?: string
  exp?: string
}

interface Department {
  id: string | number
  name: string
  text_icon?: string
  color?: string
  doctors?: Doctor[]
}

interface Slot {
  start_time: string
  end_time: string
  available: boolean
}

type AvailabilityMap = Record<string, Slot[]>

// ----------------------------
// 2. API Service
// ----------------------------
const API_BASE = '/api'

async function fetchDepartments(): Promise<Department[]> {
  const res = await axios.get(`${API_BASE}/departments`)
  return res.data
}

async function fetchAvailability(
  doctorId: string | number,
  startDate: string,
  endDate: string
): Promise<AvailabilityMap> {
  const res = await axios.get(`${API_BASE}/doctors/${doctorId}/available-slots`, {
    params: {
      doctor_id: doctorId,
      start_date: startDate,
      end_date: endDate,
    },
  })
  
  console.log('Availability response:', res.data)

  // Response is directly the date-wise map
  const data = res.data || {}
  const normalized: AvailabilityMap = {}
  for (const [date, slots] of Object.entries(data)) {
    normalized[date] = (slots as any[]).map((slot: any) => ({
      start_time: slot.start_time?.slice(0, 5) || slot.start_time,
      end_time: slot.end_time?.slice(0, 5) || slot.end_time,
      available: slot.available ?? true,
    }))
  }
  return normalized
}

async function submitBooking(payload: any): Promise<any> {
  const doctorId = payload.doctor_id
  const res = await axios.post(`${API_BASE}/doctors/${doctorId}/book-appointment`, payload)
  return res.data
}

// ----------------------------
// 3. Component State
// ----------------------------
const steps = [
  { key: 'department', label: 'Department' },
  { key: 'doctor', label: 'Doctor' },
  { key: 'date', label: 'Available Date' },
  { key: 'time', label: 'Time Slot' },
  { key: 'patient', label: 'Patient Info' },
  { key: 'confirm', label: 'Confirm' },
]

const currentStep = ref(1)
const monthCursor = ref(new Date(new Date().getFullYear(), new Date().getMonth(), 1))
const success = ref(false)
const loading = ref(false)
const errorMsg = ref('')

const departments = ref<Department[]>([])
const doctors = ref<Doctor[]>([])
const availability = ref<AvailabilityMap>({})
const selectedDoctorId = ref<string | number>('')

// Selected slot (full object)
const selectedSlot = ref<Slot | null>(null)

// Booking form
const booking = ref({
  department: '',
  doctor: '',
  date: '',
  time: '',
  end_time: '',
  name: '',
  phone: '',
  email: '',
  problem: '',
})

// ----------------------------
// 4. Computed Helpers
// ----------------------------
const activeStep = computed(() => steps[currentStep.value - 1])
const progressWidth = computed(() => `${Math.round((currentStep.value / steps.length) * 100)}%`)

const selectedDepartment = computed(() =>
  departments.value.find((d) => d.id === booking.value.department)
)

const selectedDoctor = computed(() =>
  doctors.value.find((d) => d.id === booking.value.doctor)
)

const availableSlotsForSelectedDate = computed(() => {
  if (!booking.value.date || !availability.value[booking.value.date]) return []
  return availability.value[booking.value.date].filter((slot) => slot.available)
})

const monthLabel = computed(() =>
  monthCursor.value.toLocaleDateString(undefined, {
    month: 'long',
    year: 'numeric',
  })
)

const isValidBdPhone = (phone: string) => {
  const banglaToEnglish = (str: string) => {
    // Record<string, string> ব্যবহার করা হয়েছে
    const numbers: Record<string, string> = {
      '০': '0', '১': '1', '২': '2', '৩': '3', '৪': '4',
      '৫': '5', '৬': '6', '৭': '7', '৮': '8', '৯': '9'
    };
    return str.replace(/[০-৯]/g, d => numbers[d]);
  };

  const convertedPhone = banglaToEnglish(phone).trim();
  const phoneRegex = /^01[3-9]\d{8}$/;
  return phoneRegex.test(convertedPhone);
};

// Calendar
const today = () => {
  const now = new Date()
  return new Date(now.getFullYear(), now.getMonth(), now.getDate())
}

const toIsoDate = (date: Date) => {
  const y = date.getFullYear()
  const m = String(date.getMonth() + 1).padStart(2, '0')
  const d = String(date.getDate()).padStart(2, '0')
  return `${y}-${m}-${d}`
}

const isAvailableDate = (date: Date) => {
  const iso = toIsoDate(date)
  if (date < today()) return false
  return !!availability.value[iso] && availability.value[iso].some((s) => s.available)
}

const calendarDays = computed(() => {
  const y = monthCursor.value.getFullYear()
  const m = monthCursor.value.getMonth()
  const first = new Date(y, m, 1).getDay()
  const total = new Date(y, m + 1, 0).getDate()
  const cells: Array<Date | null> = []
  for (let i = 0; i < first; i += 1) cells.push(null)
  for (let d = 1; d <= total; d += 1) cells.push(new Date(y, m, d))
  return cells
})

const hasAvailableDatesInMonth = computed(() => {
  const start = new Date(monthCursor.value.getFullYear(), monthCursor.value.getMonth(), 1)
  const end = new Date(monthCursor.value.getFullYear(), monthCursor.value.getMonth() + 1, 0)
  for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
    if (isAvailableDate(d)) return true
  }
  return false
})

const canContinue = computed(() => {
  if (currentStep.value === 1) return Boolean(booking.value.department)
  if (currentStep.value === 2) return Boolean(booking.value.doctor)
  if (currentStep.value === 3) return Boolean(booking.value.date)
  if (currentStep.value === 4) return Boolean(booking.value.time && booking.value.end_time)
  if (currentStep.value === 5) return Boolean(booking.value.name && booking.value.phone)
  return true
})

// ----------------------------
// 5. Methods
// ----------------------------
const formatDate = (iso: string) => {
  if (!iso) return '-'
  return new Date(`${iso}T00:00:00`).toLocaleDateString(undefined, {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

const prevMonth = () => {
  const minMonth = new Date(today().getFullYear(), today().getMonth(), 1).getTime()
  if (monthCursor.value.getTime() <= minMonth) return
  monthCursor.value = new Date(monthCursor.value.getFullYear(), monthCursor.value.getMonth() - 1, 1)
  if (selectedDoctorId.value) fetchAvailabilityForMonth()
}

const nextMonth = () => {
  monthCursor.value = new Date(monthCursor.value.getFullYear(), monthCursor.value.getMonth() + 1, 1)
  if (selectedDoctorId.value) fetchAvailabilityForMonth()
}

const fetchAvailabilityForMonth = async () => {
  if (!selectedDoctorId.value) return
  loading.value = true
  errorMsg.value = ''
  try {
    const start = new Date(monthCursor.value.getFullYear(), monthCursor.value.getMonth(), 1)
    const end = new Date(monthCursor.value.getFullYear(), monthCursor.value.getMonth() + 1, 0)
    const startIso = toIsoDate(start)
    const endIso = toIsoDate(end)
    availability.value = await fetchAvailability(selectedDoctorId.value, startIso, endIso)
  } catch (err: any) {
    errorMsg.value = err.message || 'Failed to load availability'
    availability.value = {}
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  if (currentStep.value > 1) currentStep.value -= 1
}
const goNext = () => {
  if (canContinue.value && currentStep.value < steps.length) currentStep.value += 1
}

const chooseDepartment = (id: string | number) => {
  booking.value.department = id
  booking.value.doctor = ''
  const dept = departments.value.find((d) => d.id === id)
  doctors.value = dept?.doctors || []
  if (doctors.value.length === 0) {
    errorMsg.value = 'No doctors available for this department.'
  } else {
    errorMsg.value = ''
  }
  if (currentStep.value === 1) goNext()
}

const chooseDoctor = (id: string | number) => {
  booking.value.doctor = id
  selectedDoctorId.value = id
  booking.value.date = ''
  booking.value.time = ''
  booking.value.end_time = ''
  selectedSlot.value = null
  availability.value = {}
  if (currentStep.value === 2) {
    fetchAvailabilityForMonth()
    goNext()
  }
}

const chooseDate = (date: Date) => {
  if (isAvailableDate(date)) {
    booking.value.date = toIsoDate(date)
    booking.value.time = ''
    booking.value.end_time = ''
    selectedSlot.value = null
    if (currentStep.value === 3) goNext()
  }
}

// Select a time slot
const selectTime = (slot: Slot) => {
  selectedSlot.value = slot
  booking.value.time = slot.start_time
  booking.value.end_time = slot.end_time
  if (currentStep.value === 4) goNext()
}

const confirmBooking = async () => {
  loading.value = true
  errorMsg.value = ''
  try {
    const payload = {
      doctor_id: booking.value.doctor,
      appointment_date: booking.value.date,
      start_time: booking.value.time,
      end_time: booking.value.end_time,
      patient_name: booking.value.name,
      patient_phone: booking.value.phone,
      patient_email: booking.value.email,
      notes: booking.value.problem,
    }
    await submitBooking(payload)
    success.value = true
    resetWizard()
    setTimeout(() => { success.value = false }, 5000)
  } catch (err: any) {
    errorMsg.value = err.response?.data?.message || err.message || 'Booking failed'
  } finally {
    loading.value = false
  }
}

const resetWizard = () => {
  booking.value = {
    department: '',
    doctor: '',
    date: '',
    time: '',
    end_time: '',
    name: '',
    phone: '',
    email: '',
    problem: '',
  }
  currentStep.value = 1
  monthCursor.value = new Date(new Date().getFullYear(), new Date().getMonth(), 1)
  doctors.value = []
  availability.value = {}
  selectedDoctorId.value = ''
  selectedSlot.value = null
  errorMsg.value = ''
}

// ----------------------------
// 6. Lifecycle
// ----------------------------
onMounted(async () => {
  loading.value = true
  try {
    departments.value = await fetchDepartments()
  } catch (err: any) {
    errorMsg.value = 'Failed to load departments'
  } finally {
    loading.value = false
  }
})

// Reset date/time when doctor changes
watch(() => booking.value.doctor, () => {
  booking.value.date = ''
  booking.value.time = ''
  booking.value.end_time = ''
  selectedSlot.value = null
})
</script>