<script setup lang="ts">
import { computed, ref } from 'vue'

const props = defineProps({
    availableWeekdays: {
        type: Array<number>,
        default: () => [0, 1, 2, 3, 4, 6],
    },
    blockedDates: {
        type: Array<string>,
        default: () => [],
    },
})

type Doctor = { id: string; name: string; title: string; exp: string }

const steps = [
    { key: 'department', label: 'Department' },
    { key: 'doctor', label: 'Doctor' },
    { key: 'date', label: 'Available Date' },
    { key: 'time', label: 'Time Slot' },
    { key: 'patient', label: 'Patient Info' },
    { key: 'confirm', label: 'Confirm' },
]

const departments = [
    { id: 'cardiology', name: 'Cardiology', icon: 'fa-heart-pulse', tone: 'from-blue-700 to-sky-600' },
    { id: 'medicine', name: 'Medicine', icon: 'fa-stethoscope', tone: 'from-emerald-700 to-teal-600' },
    { id: 'gynecology', name: 'Gynecology', icon: 'fa-venus', tone: 'from-rose-600 to-pink-600' },
    { id: 'pediatrics', name: 'Pediatrics', icon: 'fa-baby', tone: 'from-amber-600 to-orange-600' },
    { id: 'surgery', name: 'Surgery', icon: 'fa-user-doctor', tone: 'from-indigo-700 to-violet-700' },
    { id: 'gynecology', name: 'Gynecology', icon: 'fa-venus', tone: 'from-rose-600 to-pink-600' },
    { id: 'pediatrics', name: 'Pediatrics', icon: 'fa-baby', tone: 'from-amber-600 to-orange-600' },
    { id: 'surgery', name: 'Surgery', icon: 'fa-user-doctor', tone: 'from-indigo-700 to-violet-700' },
    { id: 'cardiology', name: 'Cardiology', icon: 'fa-heart-pulse', tone: 'from-blue-700 to-sky-600' },
    { id: 'medicine', name: 'Medicine', icon: 'fa-stethoscope', tone: 'from-emerald-700 to-teal-600' },
    { id: 'gynecology', name: 'Gynecology', icon: 'fa-venus', tone: 'from-rose-600 to-pink-600' },
    { id: 'pediatrics', name: 'Pediatrics', icon: 'fa-baby', tone: 'from-amber-600 to-orange-600' },
    { id: 'surgery', name: 'Surgery', icon: 'fa-user-doctor', tone: 'from-indigo-700 to-violet-700' },
    { id: 'gynecology', name: 'Gynecology', icon: 'fa-venus', tone: 'from-rose-600 to-pink-600' },
    { id: 'pediatrics', name: 'Pediatrics', icon: 'fa-baby', tone: 'from-amber-600 to-orange-600' },
    { id: 'surgery', name: 'Surgery', icon: 'fa-user-doctor', tone: 'from-indigo-700 to-violet-700' },
]

const doctorsByDepartment: Record<string, Doctor[]> = {
    cardiology: [
        { id: 'doc-1', name: 'Dr. Farhan Rahman', title: 'Senior Cardiologist', exp: '15+ Years' },
        { id: 'doc-2', name: 'Dr. Sadia Karim', title: 'Interventional Cardiologist', exp: '11+ Years' },
    ],
    medicine: [
        { id: 'doc-3', name: 'Dr. Imran Chowdhury', title: 'Consultant Medicine', exp: '12+ Years' },
        { id: 'doc-4', name: 'Dr. Nusrat Jahan', title: 'Internal Medicine Specialist', exp: '9+ Years' },
    ],
    gynecology: [
        { id: 'doc-5', name: 'Dr. Ruba Ahmed', title: 'Obstetrics & Gynecology', exp: '14+ Years' },
        { id: 'doc-6', name: 'Dr. Sabina Noor', title: 'Women Wellness Consultant', exp: '10+ Years' },
    ],
    pediatrics: [
        { id: 'doc-7', name: 'Dr. Ayaan Hasan', title: 'Child Specialist', exp: '13+ Years' },
        { id: 'doc-8', name: 'Dr. Nabila Sultana', title: 'Pediatric Consultant', exp: '8+ Years' },
    ],
    surgery: [
        { id: 'doc-9', name: 'Dr. Tanvir Islam', title: 'General & Laparoscopic Surgeon', exp: '16+ Years' },
        { id: 'doc-10', name: 'Dr. Shafiq Mamun', title: 'Surgical Specialist', exp: '12+ Years' },
    ],
}

const slotsByWeekday: Record<number, string[]> = {
    0: ['09:00 AM', '10:30 AM', '12:00 PM'],
    1: ['09:00 AM', '10:00 AM', '11:30 AM', '02:00 PM', '04:00 PM'],
    2: ['09:30 AM', '11:00 AM', '01:30 PM', '03:30 PM'],
    3: ['09:00 AM', '10:30 AM', '12:30 PM', '03:00 PM'],
    4: ['09:00 AM', '10:00 AM', '11:00 AM', '02:30 PM'],
    5: [],
    6: ['10:00 AM', '11:30 AM', '01:00 PM'],
}

const currentStep = ref(1)
const monthCursor = ref(new Date(new Date().getFullYear(), new Date().getMonth(), 1))
const success = ref(false)

const booking = ref({
    department: '',
    doctor: '',
    date: '',
    time: '',
    name: '',
    phone: '',
    email: '',
    problem: '',
})

const activeStep = computed(() => steps[currentStep.value - 1])
const progressWidth = computed(() => `${Math.round((currentStep.value / steps.length) * 100)}%`)
const selectedDepartment = computed(() => departments.find((d) => d.id === booking.value.department))
const departmentDoctors = computed(() => doctorsByDepartment[booking.value.department] || [])
const selectedDoctor = computed(() => departmentDoctors.value.find((d) => d.id === booking.value.doctor))

const selectedWeekday = computed(() => {
    if (!booking.value.date) return null
    return new Date(`${booking.value.date}T00:00:00`).getDay()
})
const availableSlots = computed(() => (selectedWeekday.value === null ? [] : (slotsByWeekday[selectedWeekday.value] || [])))
const monthLabel = computed(() => monthCursor.value.toLocaleDateString(undefined, { month: 'long', year: 'numeric' }))

const formatDate = (iso: string) => {
    if (!iso) return '-'
    return new Date(`${iso}T00:00:00`).toLocaleDateString(undefined, {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
}

const toIsoDate = (date: Date) => {
    const y = date.getFullYear()
    const m = String(date.getMonth() + 1).padStart(2, '0')
    const d = String(date.getDate()).padStart(2, '0')
    return `${y}-${m}-${d}`
}
const today = () => {
    const now = new Date()
    return new Date(now.getFullYear(), now.getMonth(), now.getDate())
}
const isAvailableDate = (date: Date) => {
    if (date < today()) return false
    if (!props.availableWeekdays.includes(date.getDay())) return false
    return !props.blockedDates.includes(toIsoDate(date))
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

const canContinue = computed(() => {
    if (currentStep.value === 1) return Boolean(booking.value.department)
    if (currentStep.value === 2) return Boolean(booking.value.doctor)
    if (currentStep.value === 3) return Boolean(booking.value.date)
    if (currentStep.value === 4) return Boolean(booking.value.time)
    if (currentStep.value === 5) return Boolean(booking.value.name && booking.value.phone)
    return true
})

const prevMonth = () => {
    const minMonth = new Date(today().getFullYear(), today().getMonth(), 1).getTime()
    if (monthCursor.value.getTime() <= minMonth) return
    monthCursor.value = new Date(monthCursor.value.getFullYear(), monthCursor.value.getMonth() - 1, 1)
}
const nextMonth = () => { monthCursor.value = new Date(monthCursor.value.getFullYear(), monthCursor.value.getMonth() + 1, 1) }
const chooseDepartment = (id: string) => {
    booking.value.department = id
    booking.value.doctor = ''
    if (currentStep.value === 1) goNext()
}
const chooseDoctor = (id: string) => {
    booking.value.doctor = id
    if (currentStep.value === 2) goNext()
}
const chooseDate = (date: Date) => {
    if (isAvailableDate(date)) {
        booking.value.date = toIsoDate(date)
        booking.value.time = ''
        if (currentStep.value === 3) goNext()
    }
}
const goBack = () => { if (currentStep.value > 1) currentStep.value -= 1 }
const goNext = () => { if (canContinue.value && currentStep.value < steps.length) currentStep.value += 1 }

const resetWizard = () => {
    booking.value = { department: '', doctor: '', date: '', time: '', name: '', phone: '', email: '', problem: '' }
    currentStep.value = 1
    monthCursor.value = new Date(new Date().getFullYear(), new Date().getMonth(), 1)
}
const confirmBooking = () => {
    success.value = true
    resetWizard()
    setTimeout(() => { success.value = false }, 5000)
}
</script>

<template>
    <div class="relative w-full overflow-hidden rounded-[2rem] border border-slate-200/80 bg-white/95 p-4 shadow-[0_34px_80px_-42px_rgba(15,23,42,.6)] backdrop-blur-sm md:h-[680px] md:p-6">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_0%_0%,rgba(14,165,233,.12),transparent_36%),radial-gradient(circle_at_100%_100%,rgba(59,130,246,.1),transparent_38%)]"></div>
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

            <div v-if="success" class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                <i class="fas fa-check-circle mr-2"></i>Appointment request confirmed.
            </div>

            <div class="grid min-h-0 flex-1 gap-3 lg:grid-cols-12">
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

                <section class="rounded-2xl border border-slate-200 bg-white p-4 md:p-5 lg:col-span-6 lg:flex lg:min-h-0 lg:flex-col">
                    <div class="mb-4 flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Current Step</p>
                        <p class="text-sm font-semibold text-slate-800">{{ activeStep.label }}</p>
                    </div>
                    <div class="min-h-0 flex-1 overflow-y-auto pr-1">
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
                                <div :class="`mb-2 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br ${dept.tone} text-white shadow`">
                                    <i :class="`fas ${dept.icon} text-sm`"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-900">{{ dept.name }}</p>
                            </button>
                        </div>

                        <div v-else-if="currentStep === 2" class="space-y-2.5">
                            <button
                                v-for="doc in departmentDoctors"
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
                                <p class="text-xs text-slate-600">{{ doc.title }} - {{ doc.exp }}</p>
                            </button>
                            <div v-if="!departmentDoctors.length" class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-700">
                                Select department first.
                            </div>
                        </div>

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
                                                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                                    : 'cursor-not-allowed border-slate-100 bg-slate-100 text-slate-400',
                                        ]"
                                    >
                                        {{ day.getDate() }}
                                    </button>
                                    <div v-else class="h-full w-full"></div>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="currentStep === 4">
                            <div v-if="!availableSlots.length" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                                No slots available for selected date.
                            </div>
                            <div v-else class="grid grid-cols-2 gap-2.5">
                                <button
                                    v-for="slot in availableSlots"
                                    :key="slot"
                                    type="button"
                                    @click="booking.time = slot"
                                    :class="[
                                        'rounded-xl border px-3 py-2 text-sm font-semibold',
                                        booking.time === slot ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-slate-700',
                                    ]"
                                >
                                    {{ slot }}
                                </button>
                            </div>
                        </div>

                        <div v-else-if="currentStep === 5" class="space-y-2.5">
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Patient Name *</label>
                                <input v-model="booking.name" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 caret-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Phone *</label>
                                <input v-model="booking.phone" type="tel" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 caret-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100" />
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

                        <div v-else class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-3">
                            <p class="text-sm font-semibold text-emerald-800">Ready to confirm this booking.</p>
                        </div>
                    </div>

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
                            class="rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-5 py-2 text-sm font-semibold text-white shadow-[0_12px_22px_-14px_rgba(5,150,105,.95)] transition hover:brightness-105"
                        >
                            Confirm Booking
                        </button>
                    </div>
                </section>

                <aside class="rounded-2xl border border-slate-200 bg-slate-50/90 p-3 lg:col-span-3 lg:h-full lg:overflow-y-auto">
                    <div class="mb-3 flex items-center justify-between">
                        <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-500">Live Review</p>
                        <span class="rounded-lg bg-blue-100 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-blue-700">Realtime</span>
                    </div>
                    <div class="space-y-2 text-sm">
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
