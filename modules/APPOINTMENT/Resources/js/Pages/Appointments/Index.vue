<script setup>
import { computed, ref, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {
    Plus, Search, CalendarDays, Clock, Pencil, Trash2, X, User, Mail, Phone,
    Stethoscope, Timer, ChevronLeft, ChevronRight, Loader2, Check, Sparkles,
} from 'lucide-vue-next'

const props = defineProps({
    appointments: { type: Object, required: true },
    doctors: { type: [Array, Object], default: () => [] },
    statuses: {
        type: Object,
        default: () => ({
            pending: 'Pending',
            confirmed: 'Confirmed',
            cancelled: 'Cancelled',
            completed: 'Completed',
            no_show: 'No Show',
        }),
    },
    filters: { type: Object, default: () => ({}) },
})

/* ---------------------------------------------------------
 * Filter state
 * --------------------------------------------------------- */
const search = ref(props.filters.search || '')
const statusFilter = ref(props.filters.status || '')
const doctorFilter = ref(props.filters.doctor_id || '')
const dateFilter = ref(props.filters.date || '')
const perPage = ref(props.appointments?.per_page || 15)

/* ---------------------------------------------------------
 * Drawer / modal state
 * --------------------------------------------------------- */
const drawerOpen = ref(false)
const isEditing = ref(false)
const selectedAppointment = ref(null)

const slots = ref([])
const loadingSlots = ref(false)
const slotMessage = ref('')
const selectedSlotKey = ref('')

const confirmModalOpen = ref(false)
const confirmingAppointment = ref(null)
const confirmSerialNumber = ref('')

const calendarMonth = ref(new Date())
const todayISO = new Date().toISOString().split('T')[0]

/* ---------------------------------------------------------
 * Inertia form
 * --------------------------------------------------------- */
const form = useForm({
    doctor_id: '',
    patient_name: '',
    patient_email: '',
    patient_phone: '',
    appointment_date: '',
    start_time: '',
    end_time: '',
    slot_duration: 15,
    status: 'pending',
    notes: '',
})

/* ---------------------------------------------------------
 * Helpers
 * --------------------------------------------------------- */
const normalizeTime = (timeStr) => {
    if (!timeStr) return ''
    const parts = timeStr.split(':')
    return parts.length >= 2 ? `${parts[0]}:${parts[1]}` : timeStr
}

const formatDate = (dateStr) => {
    if (!dateStr) return '—'
    const d = new Date(`${dateStr}T00:00:00`)
    if (Number.isNaN(d.getTime())) return dateStr
    return d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' })
}

const formatTime = (timeStr) => {
    if (!timeStr) return '—'
    const [h, m] = timeStr.split(':')
    const hour = Number(h)
    const period = hour >= 12 ? 'PM' : 'AM'
    const displayHour = ((hour + 11) % 12) + 1
    return `${displayHour}:${m} ${period}`
}

const doctorName = (appointment) => {
    if (appointment?.doctor?.name) return appointment.doctor.name
    const match = doctorsList.value.find((d) => Number(d.id) === Number(appointment?.doctor_id))
    return match?.name || 'Unassigned'
}

const STATUS_STYLES = {
    pending: 'bg-amber-50 text-amber-700 border border-amber-200',
    confirmed: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    completed: 'bg-blue-50 text-blue-700 border border-blue-200',
    cancelled: 'bg-rose-50 text-rose-700 border border-rose-200',
    no_show: 'bg-slate-50 text-slate-600 border border-slate-300',
}

const statusClass = (status) => STATUS_STYLES[status] || STATUS_STYLES.no_show
const statusLabel = (status) => props.statuses[status] || status

const visitPage = (url) => {
    if (!url) return
    router.get(url, {}, { preserveState: true, preserveScroll: true, replace: true })
}

/* ---------------------------------------------------------
 * Computed
 * --------------------------------------------------------- */
const rows = computed(() => props.appointments?.data || [])

const doctorsList = computed(() => {
    if (Array.isArray(props.doctors)) return props.doctors
    return Object.values(props.doctors || {})
})

const selectedDoctor = computed(() =>
    doctorsList.value.find((doctor) => Number(doctor.id) === Number(form.doctor_id)),
)

const selectedDoctorWorkingDays = computed(() => {
    const timetables = selectedDoctor.value?.timetables || []
    return timetables
        .filter((item) => item.is_active === true || item.is_active === 1 || item.is_active === '1')
        .map((item) => Number(item.day_of_week))
})

const hasDoctorSchedule = computed(() => selectedDoctorWorkingDays.value.length > 0)

const pageStats = computed(() => ({
    total: props.appointments?.total || 0,
    pending: rows.value.filter((item) => item.status === 'pending').length,
    confirmed: rows.value.filter((item) => item.status === 'confirmed').length,
    completed: rows.value.filter((item) => item.status === 'completed').length,
    cancelled: rows.value.filter((item) => item.status === 'cancelled').length,
    no_show: rows.value.filter((item) => item.status === 'no_show').length,
}))

const currentMonthLabel = computed(() =>
    calendarMonth.value.toLocaleDateString('default', { month: 'long', year: 'numeric' }),
)

// FIX: this previously emitted { date, dayNumber, isCurrentMonth, isSelectable }
// while the template read { date, label, muted, today, selected, selectable, hasSchedule }.
// The mismatch meant every calendar cell rendered blank and permanently disabled —
// which is why no date ever appeared as available after picking a doctor.
const calendarDays = computed(() => {
    const year = calendarMonth.value.getFullYear()
    const month = calendarMonth.value.getMonth()

    const firstDayIndex = new Date(year, month, 1).getDay()
    const totalDaysInMonth = new Date(year, month + 1, 0).getDate()

    const days = []

    for (let i = 0; i < firstDayIndex; i++) {
        days.push({ date: null, label: '', muted: true, selectable: false, today: false, selected: false, hasSchedule: false })
    }

    for (let day = 1; day <= totalDaysInMonth; day++) {
        const dateObj = new Date(year, month, day)
        const formattedDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`
        const dayOfWeek = dateObj.getDay()

        const isWorkingDay = selectedDoctorWorkingDays.value.includes(dayOfWeek)
        const isPast = formattedDate < todayISO
        const isSelectable = !isPast && (!hasDoctorSchedule.value || isWorkingDay)

        days.push({
            date: formattedDate,
            label: day,
            muted: false,
            today: formattedDate === todayISO,
            selected: formattedDate === form.appointment_date,
            selectable: isSelectable,
            hasSchedule: hasDoctorSchedule.value ? isWorkingDay : false,
        })
    }

    return days
})

/* ---------------------------------------------------------
 * Watchers
 * --------------------------------------------------------- */
let filterTimeout = null
watch([search, statusFilter, doctorFilter, dateFilter, perPage], () => {
    clearTimeout(filterTimeout)
    filterTimeout = setTimeout(() => {
        router.get(
            route('appointments.index'),
            {
                search: search.value || undefined,
                status: statusFilter.value || undefined,
                doctor_id: doctorFilter.value || undefined,
                date: dateFilter.value || undefined,
                per_page: perPage.value || 15,
            },
            { preserveState: true, preserveScroll: true, replace: true },
        )
    }, 350)
})

watch(() => form.doctor_id, () => {
    resetScheduleSelection()
})

watch(() => form.appointment_date, () => {
    if (form.doctor_id && form.appointment_date) fetchAvailableSlots()
})

/* ---------------------------------------------------------
 * Methods
 * --------------------------------------------------------- */
const resetScheduleSelection = () => {
    form.appointment_date = ''
    form.start_time = ''
    form.end_time = ''
    form.slot_duration = 15
    slots.value = []
    selectedSlotKey.value = ''
    slotMessage.value = ''

    if (selectedDoctor.value?.timetables?.length) {
        calendarMonth.value = new Date()
    }
}

const clearSlotOnly = () => {
    form.start_time = ''
    form.end_time = ''
    form.slot_duration = 15
    selectedSlotKey.value = ''
}

const selectSlot = (slot) => {
    if (!slot.available) return
    form.start_time = slot.start_time
    form.end_time = slot.end_time
    selectedSlotKey.value = `${normalizeTime(slot.start_time)}-${normalizeTime(slot.end_time)}`
}

const selectCalendarDate = (day) => {
    if (!day.date || !day.selectable) return
    form.appointment_date = day.date
}

const previousMonth = () => {
    calendarMonth.value = new Date(calendarMonth.value.getFullYear(), calendarMonth.value.getMonth() - 1, 1)
}

const nextMonth = () => {
    calendarMonth.value = new Date(calendarMonth.value.getFullYear(), calendarMonth.value.getMonth() + 1, 1)
}

const openCreateDrawer = () => {
    isEditing.value = false
    selectedAppointment.value = null

    form.reset()
    form.clearErrors()

    form.doctor_id = ''
    form.patient_name = ''
    form.patient_email = ''
    form.patient_phone = ''
    form.appointment_date = ''
    form.start_time = ''
    form.end_time = ''
    form.slot_duration = 15
    form.status = 'pending'
    form.notes = ''

    slots.value = []
    selectedSlotKey.value = ''
    slotMessage.value = ''
    calendarMonth.value = new Date()

    drawerOpen.value = true
}

const openEditDrawer = async (appointment) => {
    isEditing.value = true
    selectedAppointment.value = appointment

    form.clearErrors()

    form.doctor_id = appointment.doctor_id || ''
    form.patient_name = appointment.patient_name || ''
    form.patient_email = appointment.patient_email || ''
    form.patient_phone = appointment.patient_phone || ''
    form.appointment_date = String(appointment.appointment_date || '').substring(0, 10)
    form.start_time = normalizeTime(appointment.start_time)
    form.end_time = normalizeTime(appointment.end_time)
    form.slot_duration = appointment.slot_duration || 15
    form.status = appointment.status || 'pending'
    form.notes = appointment.notes || ''

    selectedSlotKey.value = `${form.start_time}-${form.end_time}`

    if (form.appointment_date) {
        calendarMonth.value = new Date(`${form.appointment_date}T00:00:00`)
    }

    drawerOpen.value = true

    if (form.doctor_id && form.appointment_date) {
        await fetchAvailableSlots(true)
    }
}

const closeDrawer = () => {
    drawerOpen.value = false
    selectedAppointment.value = null
    form.clearErrors()
}

const submitAppointment = () => {
    if (!form.doctor_id || !form.appointment_date || !form.start_time || !form.end_time) {
        slotMessage.value = 'Please select doctor, valid schedule date and available time slot.'
        return
    }

    if (isEditing.value && selectedAppointment.value) {
        form.put(route('appointments.update', selectedAppointment.value.id), {
            preserveScroll: true,
            onSuccess: () => closeDrawer(),
        })
        return
    }

    form.post(route('appointments.store'), {
        preserveScroll: true,
        onSuccess: () => closeDrawer(),
    })
}

const deleteAppointment = (appointment) => {
    if (!confirm(`Delete appointment for ${appointment.patient_name}?`)) return
    router.delete(route('appointments.destroy', appointment.id), { preserveScroll: true })
}

const clearFilters = () => {
    search.value = ''
    statusFilter.value = ''
    doctorFilter.value = ''
    dateFilter.value = ''
    perPage.value = 15
}

const fetchAvailableSlots = async (keepSelected = false) => {
    if (!form.doctor_id || !form.appointment_date) return

    loadingSlots.value = true
    slotMessage.value = ''

    if (!keepSelected) clearSlotOnly()

    try {
        const baseUrl = route('test-slot')
        const query = new URLSearchParams({
            doctor_id: form.doctor_id,
            start_date: form.appointment_date,
            end_date: form.appointment_date,
        })

        const response = await fetch(`${baseUrl}?${query.toString()}`, {
            headers: { Accept: 'application/json' },
        })

        const data = await response.json()
        const selectedDate = form.appointment_date

        if (data && data[selectedDate]) {
            slots.value = data[selectedDate]
            if (!slots.value.length) {
                slotMessage.value = 'No slots found for this date. The doctor may be on rest or unavailable.'
            }
            return
        }

        slots.value = []
        slotMessage.value = 'No available slots found for this date.'
    } catch (error) {
        slots.value = []
        slotMessage.value = 'Unable to fetch time slots. Please check your network connection.'
    } finally {
        loadingSlots.value = false
    }
}

/* ---------------------------------------------------------
 * Confirm modal
 * --------------------------------------------------------- */
const openConfirmModal = (appointment) => {
    confirmingAppointment.value = appointment
    confirmSerialNumber.value = appointment.serial_number ?? ''
    confirmModalOpen.value = true
}

const closeConfirmModal = () => {
    confirmModalOpen.value = false
    confirmingAppointment.value = null
    confirmSerialNumber.value = ''
}

const submitConfirmAppointment = () => {
    if (!confirmingAppointment.value) return

    router.put(
        route('appointments.confirm', confirmingAppointment.value.id),
        { serial_number: confirmSerialNumber.value || null },
        { preserveScroll: true, onSuccess: () => closeConfirmModal() },
    )
}
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto flex max-w-7xl flex-col gap-6">

                <!-- Hero -->
                <section class="relative flex flex-col gap-6 overflow-hidden rounded-[36px] bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-900 p-9 text-white shadow-[0_28px_90px_rgba(15,23,42,0.26)] sm:flex-row sm:items-center sm:justify-between">
                    <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-indigo-500/40 blur-[34px]"></div>
                    <div class="pointer-events-none absolute -bottom-40 left-[30%] h-72 w-72 rounded-full bg-sky-400/15 blur-[32px]"></div>

                    <div class="relative flex flex-col gap-3">
                        <span class="inline-flex w-fit items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3.5 py-2 text-[13px] font-extrabold uppercase tracking-wide text-indigo-200">
                            <Sparkles :size="14" />
                            Appointment Command Center
                        </span>
                        <h1 class="max-w-xl text-[32px] font-black leading-tight tracking-tight sm:text-[42px]">Premium Appointment Management</h1>
                        <p class="max-w-xl text-[15px] leading-relaxed text-slate-300">
                            Manage patients, doctors, schedules and real-time slot booking from one beautiful dashboard.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="relative inline-flex items-center justify-center gap-2.5 rounded-2xl bg-white px-5 py-3.5 text-sm font-extrabold text-slate-950 shadow-[0_18px_45px_rgba(0,0,0,0.2)] transition hover:-translate-y-0.5 hover:bg-indigo-50"
                        @click="openCreateDrawer"
                    >
                        <Plus :size="18" />
                        Create Appointment
                    </button>
                </section>

                <!-- Stats -->
                <section class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total</span>
                        <strong class="mt-2 block text-2xl font-bold text-slate-900">{{ pageStats.total }}</strong>
                        <p class="mt-1 text-xs text-slate-500">All records</p>
                    </div>

                    <div class="rounded-2xl border border-amber-100 bg-amber-50 p-5 shadow-sm">
                        <span class="text-xs font-semibold uppercase tracking-wide text-amber-600">Pending</span>
                        <strong class="mt-2 block text-2xl font-bold text-amber-900">{{ pageStats.pending }}</strong>
                        <p class="mt-1 text-xs text-amber-600/80">This page</p>
                    </div>

                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm">
                        <span class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Confirmed</span>
                        <strong class="mt-2 block text-2xl font-bold text-emerald-900">{{ pageStats.confirmed }}</strong>
                        <p class="mt-1 text-xs text-emerald-600/80">This page</p>
                    </div>

                    <div class="rounded-2xl border border-sky-100 bg-sky-50 p-5 shadow-sm">
                        <span class="text-xs font-semibold uppercase tracking-wide text-sky-600">Completed</span>
                        <strong class="mt-2 block text-2xl font-bold text-sky-900">{{ pageStats.completed }}</strong>
                        <p class="mt-1 text-xs text-sky-600/80">This page</p>
                    </div>

                    <div class="rounded-2xl border border-rose-100 bg-rose-50 p-5 shadow-sm">
                        <span class="text-xs font-semibold uppercase tracking-wide text-rose-600">Cancelled</span>
                        <strong class="mt-2 block text-2xl font-bold text-rose-900">{{ pageStats.cancelled }}</strong>
                        <p class="mt-1 text-xs text-rose-600/80">This page</p>
                    </div>
                </section>

                <!-- Panel: filters + table -->
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Appointments</h2>
                            <p class="text-sm text-slate-500">Live search, smart filters, schedule-based booking and server pagination.</p>
                        </div>

                        <button
                            type="button"
                            class="mt-3 inline-flex w-fit items-center rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 sm:mt-0"
                            @click="clearFilters"
                        >
                            Clear Filters
                        </button>
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                        <div class="relative sm:col-span-2">
                            <Search :size="17" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search patient name, phone or email..."
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-10 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                            />
                        </div>

                        <select v-model="statusFilter" class="rounded-lg border border-slate-200 py-2.5 px-3 text-sm text-slate-700 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                            <option value="">All Status</option>
                            <option v-for="(label, value) in statuses" :key="value" :value="value">{{ label }}</option>
                        </select>

                        <select v-model="doctorFilter" class="rounded-lg border border-slate-200 py-2.5 px-3 text-sm text-slate-700 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                            <option value="">All Doctors</option>
                            <option v-for="doctor in doctorsList" :key="doctor.id" :value="doctor.id">{{ doctor.name }}</option>
                        </select>

                        <input
                            v-model="dateFilter"
                            type="date"
                            class="rounded-lg border border-slate-200 py-2.5 px-3 text-sm text-slate-700 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                        />
                    </div>

                    <div class="mt-3 flex justify-end">
                        <select v-model="perPage" class="rounded-lg border border-slate-200 py-2 px-3 text-sm text-slate-600 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                            <option value="10">10 per page</option>
                            <option value="15">15 per page</option>
                            <option value="25">25 per page</option>
                            <option value="50">50 per page</option>
                        </select>
                    </div>

                    <!-- Table -->
                    <div class="mt-5 overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-100 text-sm">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <th class="px-4 py-3">Serial</th>
                                    <th class="px-4 py-3">Patient</th>
                                    <th class="px-4 py-3">Doctor</th>
                                    <th class="px-4 py-3">Schedule</th>
                                    <th class="px-4 py-3">Duration</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 bg-white">
                                <tr v-for="appointment in rows" :key="appointment.id" class="transition hover:bg-slate-50">
                                    <td class="px-4 py-3 font-medium text-slate-700">
                                        {{ appointment.serial_number ?? '—' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                                                {{ appointment.patient_name?.charAt(0) || 'P' }}
                                            </div>
                                            <div class="min-w-0">
                                                <strong class="block truncate text-slate-900">{{ appointment.patient_name }}</strong>
                                                <span class="block truncate text-xs text-slate-500">{{ appointment.patient_email || 'No email' }}</span>
                                                <small class="block text-xs text-slate-400">{{ appointment.patient_phone }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="inline-flex items-center gap-1.5 text-slate-700">
                                            <Stethoscope :size="16" class="text-indigo-500" />
                                            {{ doctorName(appointment) }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex flex-col gap-1 text-xs text-slate-600">
                                            <span class="inline-flex items-center gap-1.5">
                                                <CalendarDays :size="14" class="text-slate-400" />
                                                {{ formatDate(String(appointment.appointment_date).substring(0, 10)) }}
                                            </span>
                                            <span class="inline-flex items-center gap-1.5">
                                                <Clock :size="14" class="text-slate-400" />
                                                {{ formatTime(appointment.start_time) }} - {{ formatTime(appointment.end_time) }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                            <Timer :size="13" />
                                            {{ appointment.slot_duration }} min
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(appointment.status)">
                                            {{ statusLabel(appointment.status) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button
                                                v-if="appointment.status === 'pending'"
                                                type="button"
                                                title="Confirm appointment"
                                                class="rounded-lg p-2 text-emerald-600 transition hover:bg-emerald-50"
                                                @click="openConfirmModal(appointment)"
                                            >
                                                <Check :size="16" />
                                            </button>

                                            <button
                                                type="button"
                                                title="Edit"
                                                class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100"
                                                @click="openEditDrawer(appointment)"
                                            >
                                                <Pencil :size="16" />
                                            </button>

                                            <button
                                                type="button"
                                                title="Delete"
                                                class="rounded-lg p-2 text-rose-500 transition hover:bg-rose-50"
                                                @click="deleteAppointment(appointment)"
                                            >
                                                <Trash2 :size="16" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="rows.length === 0">
                                    <td colspan="7" class="px-4 py-16">
                                        <div class="flex flex-col items-center gap-2 text-center text-slate-400">
                                            <Search :size="30" />
                                            <h3 class="text-sm font-semibold text-slate-600">No appointments found</h3>
                                            <p class="text-xs">Try changing your search or filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-slate-500">
                            Showing <strong class="text-slate-700">{{ appointments.from || 0 }}</strong>
                            to <strong class="text-slate-700">{{ appointments.to || 0 }}</strong>
                            of <strong class="text-slate-700">{{ appointments.total || 0 }}</strong> appointments
                        </p>

                        <div class="flex flex-wrap gap-1.5">
                            <button
                                v-for="link in appointments.links"
                                :key="link.label"
                                type="button"
                                :disabled="!link.url"
                                :class="[
                                    'min-w-[40px] rounded-[13px] px-3 py-2 text-xs font-black transition disabled:cursor-not-allowed disabled:opacity-45',
                                    link.active ? 'border border-slate-900 bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50',
                                ]"
                                v-html="link.label"
                                @click="visitPage(link.url)"
                            ></button>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Drawer overlay -->
            <div
                v-if="drawerOpen"
                class="fixed inset-0 z-40 bg-slate-900/[.58] backdrop-blur-[7px] transition-opacity"
                @click="closeDrawer"
            ></div>

            <!-- Drawer -->
            <aside
                :class="[
                    'fixed inset-y-0 right-0 z-50 flex w-full max-w-[620px] flex-col bg-white shadow-[-28px_0_80px_rgba(15,23,42,0.26)] transition-transform duration-300',
                    drawerOpen ? 'translate-x-0' : 'translate-x-full',
                ]"
            >
                <div class="relative flex items-start justify-between gap-4 overflow-hidden border-b border-slate-200 bg-white px-6 py-6">
                    <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-indigo-500/10 blur-2xl"></div>
                    <div class="relative">
                        <span class="mb-1.5 inline-flex text-xs font-extrabold uppercase tracking-[0.12em] text-indigo-500">
                            {{ isEditing ? 'Update Booking' : 'New Booking' }}
                        </span>
                        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">{{ isEditing ? 'Edit Appointment' : 'Create Appointment' }}</h2>
                        <p class="mt-1.5 text-sm text-slate-500">Select doctor, valid schedule date and available time slot.</p>
                    </div>

                    <button type="button" class="relative flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 transition hover:bg-slate-200" @click="closeDrawer">
                        <X :size="20" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto bg-slate-50 px-6 py-6">
                    <!-- Doctor -->
                    <div class="mb-4 rounded-[24px] border border-slate-200 bg-white p-[18px] shadow-[0_12px_35px_rgba(15,23,42,0.045)]">
                        <div class="mb-4 flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                                <Stethoscope :size="18" />
                            </div>
                            <div>
                                <h3 class="text-[15px] font-extrabold text-slate-900">Doctor</h3>
                                <p class="mt-0.5 text-[13px] text-slate-500">Calendar will unlock based on doctor schedule.</p>
                            </div>
                        </div>

                        <select
                            v-model="form.doctor_id"
                            :class="[
                                'h-12 w-full rounded-2xl border bg-slate-50 px-4 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4',
                                form.errors.doctor_id ? 'border-rose-400 bg-rose-50 focus:ring-rose-100' : 'border-slate-200 focus:ring-indigo-100',
                            ]"
                        >
                            <option value="">Select doctor</option>
                            <option v-for="doctor in doctorsList" :key="doctor.id" :value="doctor.id">{{ doctor.name }}</option>
                        </select>

                        <p v-if="form.errors.doctor_id" class="mt-1.5 text-xs font-bold text-rose-600">{{ form.errors.doctor_id }}</p>

                        <div v-if="form.doctor_id && !hasDoctorSchedule" class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-2.5 text-xs font-semibold text-amber-700">
                            This doctor has no active timetable. Calendar dates are disabled.
                        </div>
                    </div>

                    <!-- Calendar -->
                    <div class="mb-4 rounded-[24px] border border-slate-200 bg-white p-[18px] shadow-[0_12px_35px_rgba(15,23,42,0.045)]">
                        <div class="mb-4 flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                                <CalendarDays :size="18" />
                            </div>
                            <div>
                                <h3 class="text-[15px] font-extrabold text-slate-900">Schedule Date</h3>
                                <p class="mt-0.5 text-[13px] text-slate-500">Only working schedule dates are selectable.</p>
                            </div>
                        </div>

                        <div class="rounded-[22px] border border-slate-200 bg-slate-50 p-3.5">
                            <div class="mb-3.5 flex items-center justify-between">
                                <button type="button" class="flex h-[38px] w-[38px] items-center justify-center rounded-[13px] border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-100" @click="previousMonth">
                                    <ChevronLeft :size="18" />
                                </button>

                                <strong class="text-[15px] font-extrabold text-slate-900">{{ currentMonthLabel }}</strong>

                                <button type="button" class="flex h-[38px] w-[38px] items-center justify-center rounded-[13px] border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-100" @click="nextMonth">
                                    <ChevronRight :size="18" />
                                </button>
                            </div>

                            <div class="mb-2 grid grid-cols-7 text-center text-[11px] font-extrabold uppercase text-slate-400">
                                <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                            </div>

                            <div class="grid grid-cols-7 gap-2">
                                <button
                                    v-for="(day, index) in calendarDays"
                                    :key="index"
                                    type="button"
                                    :disabled="!day.selectable"
                                    :class="[
                                        'h-11 rounded-[14px] border text-sm font-extrabold transition',
                                        day.muted ? 'invisible border-transparent' : '',
                                        day.selected
                                            ? 'border-indigo-600 bg-indigo-600 text-white shadow-[0_12px_30px_rgba(79,70,229,0.26)]'
                                            : day.selectable
                                                ? day.hasSchedule
                                                    ? 'border-indigo-200 bg-indigo-50 text-indigo-700 hover:border-indigo-400'
                                                    : 'border-slate-200 bg-white text-slate-700 hover:border-indigo-300'
                                                : 'cursor-not-allowed border-transparent bg-slate-100 text-slate-300 opacity-75',
                                        day.today && !day.selected ? 'shadow-[inset_0_0_0_2px_#38bdf8]' : '',
                                    ]"
                                    @click="selectCalendarDate(day)"
                                >
                                    {{ day.label }}
                                </button>
                            </div>

                            <div class="mt-3.5 flex flex-wrap gap-3 text-xs font-bold text-slate-500">
                                <span class="inline-flex items-center gap-1.5"><i class="h-[9px] w-[9px] rounded-full bg-indigo-500"></i> Scheduled</span>
                                <span class="inline-flex items-center gap-1.5"><i class="h-[9px] w-[9px] rounded-full bg-slate-300"></i> Not available</span>
                                <span class="inline-flex items-center gap-1.5"><i class="h-[9px] w-[9px] rounded-full bg-indigo-600"></i> Selected</span>
                            </div>
                        </div>

                        <p v-if="form.errors.appointment_date" class="mt-1.5 text-xs font-bold text-rose-600">{{ form.errors.appointment_date }}</p>
                    </div>

                    <!-- Slots -->
                    <div class="mb-4 rounded-[24px] border border-slate-200 bg-white p-[18px] shadow-[0_12px_35px_rgba(15,23,42,0.045)]">
                        <div class="mb-4 flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                                <Clock :size="18" />
                            </div>
                            <div>
                                <h3 class="text-[15px] font-extrabold text-slate-900">Available Time Slots</h3>
                                <p class="mt-0.5 text-[13px] text-slate-500">Generated from doctor timetable, rest days and booked appointments.</p>
                            </div>
                        </div>

                        <div v-if="!form.doctor_id" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-500">
                            Select a doctor first.
                        </div>

                        <div v-else-if="!form.appointment_date" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-500">
                            Select a schedule date from the calendar.
                        </div>

                        <div v-else-if="loadingSlots" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-500">
                            <Loader2 :size="18" class="animate-spin" />
                            Loading available slots...
                        </div>

                        <div v-else>
                            <div v-if="slots.length" class="grid grid-cols-3 gap-2.5">
                                <button
                                    v-for="slot in slots"
                                    :key="`${slot.start_time}-${slot.end_time}`"
                                    type="button"
                                    :disabled="!slot.available"
                                    :class="[
                                        'relative grid min-h-[72px] place-items-center gap-0.5 rounded-[18px] border transition',
                                        selectedSlotKey === `${normalizeTime(slot.start_time)}-${normalizeTime(slot.end_time)}`
                                            ? 'border-indigo-600 bg-indigo-600 text-white shadow-[0_16px_36px_rgba(79,70,229,0.24)]'
                                            : slot.available
                                                ? 'border-emerald-200 bg-white text-emerald-700 hover:-translate-y-0.5 hover:border-emerald-500 hover:shadow-[0_12px_30px_rgba(16,185,129,0.12)]'
                                                : 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-300',
                                    ]"
                                    @click="selectSlot(slot)"
                                >
                                    <Check
                                        v-if="selectedSlotKey === `${normalizeTime(slot.start_time)}-${normalizeTime(slot.end_time)}`"
                                        :size="14"
                                        class="absolute right-2 top-2"
                                    />
                                    <strong class="text-[13px] font-extrabold">{{ formatTime(slot.start_time) }}</strong>
                                    <span :class="['text-[11px] font-bold', selectedSlotKey === `${normalizeTime(slot.start_time)}-${normalizeTime(slot.end_time)}` ? 'text-indigo-200' : 'text-slate-400']">
                                        {{ formatTime(slot.end_time) }}
                                    </span>
                                </button>
                            </div>

                            <div v-else class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-500">
                                {{ slotMessage || 'No slots loaded.' }}
                            </div>
                        </div>

                        <p v-if="slotMessage && slots.length" class="mt-2.5 text-[13px] font-semibold text-slate-500">{{ slotMessage }}</p>
                        <p v-if="form.errors.start_time || form.errors.end_time" class="mt-2.5 text-xs font-bold text-rose-600">
                            {{ form.errors.start_time || form.errors.end_time }}
                        </p>
                    </div>

                    <!-- Patient details -->
                    <div class="mb-4 rounded-[24px] border border-slate-200 bg-white p-[18px] shadow-[0_12px_35px_rgba(15,23,42,0.045)]">
                        <div class="mb-4 flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                                <User :size="18" />
                            </div>
                            <div>
                                <h3 class="text-[15px] font-extrabold text-slate-900">Patient Details</h3>
                                <p class="mt-0.5 text-[13px] text-slate-500">Basic patient information for appointment record.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3.5">
                            <div>
                                <label class="mb-2 block text-[13px] font-extrabold text-slate-700">Patient Name *</label>
                                <div class="relative">
                                    <User :size="16" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
                                    <input
                                        v-model="form.patient_name"
                                        type="text"
                                        placeholder="Enter patient name"
                                        :class="[
                                            'h-12 w-full rounded-2xl border bg-slate-50 pl-[46px] pr-4 text-sm text-slate-900 transition focus:bg-white focus:outline-none focus:ring-4',
                                            form.errors.patient_name ? 'border-rose-400 bg-rose-50 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-100',
                                        ]"
                                    />
                                </div>
                                <p v-if="form.errors.patient_name" class="mt-1.5 text-xs font-bold text-rose-600">{{ form.errors.patient_name }}</p>
                            </div>

                            <div>
                                <label class="mb-2 block text-[13px] font-extrabold text-slate-700">Phone *</label>
                                <div class="relative">
                                    <Phone :size="16" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
                                    <input
                                        v-model="form.patient_phone"
                                        type="text"
                                        placeholder="Enter phone number"
                                        :class="[
                                            'h-12 w-full rounded-2xl border bg-slate-50 pl-[46px] pr-4 text-sm text-slate-900 transition focus:bg-white focus:outline-none focus:ring-4',
                                            form.errors.patient_phone ? 'border-rose-400 bg-rose-50 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-100',
                                        ]"
                                    />
                                </div>
                                <p v-if="form.errors.patient_phone" class="mt-1.5 text-xs font-bold text-rose-600">{{ form.errors.patient_phone }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="mb-2 block text-[13px] font-extrabold text-slate-700">Email</label>
                            <div class="relative">
                                <Mail :size="16" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
                                <input
                                    v-model="form.patient_email"
                                    type="email"
                                    placeholder="Enter email address"
                                    :class="[
                                        'h-12 w-full rounded-2xl border bg-slate-50 pl-[46px] pr-4 text-sm text-slate-900 transition focus:bg-white focus:outline-none focus:ring-4',
                                        form.errors.patient_email ? 'border-rose-400 bg-rose-50 focus:ring-rose-100' : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-100',
                                    ]"
                                />
                            </div>
                            <p v-if="form.errors.patient_email" class="mt-1.5 text-xs font-bold text-rose-600">{{ form.errors.patient_email }}</p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-2 block text-[13px] font-extrabold text-slate-700">Status</label>
                            <select v-model="form.status" class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100">
                                <option v-for="(label, value) in statuses" :key="value" :value="value">{{ label }}</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <label class="mb-2 block text-[13px] font-extrabold text-slate-700">Notes</label>
                            <textarea
                                v-model="form.notes"
                                rows="4"
                                placeholder="Write appointment note..."
                                class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div v-if="form.doctor_id && form.appointment_date && form.start_time" class="flex items-center justify-between gap-3 rounded-2xl bg-gradient-to-br from-indigo-50 to-sky-50 p-4">
                        <span class="rounded-full bg-white px-3 py-2 text-[11px] font-black uppercase tracking-wide text-indigo-700">Booking Summary</span>
                        <div class="text-right">
                            <strong class="block text-[15px] font-black text-slate-900">{{ selectedDoctor?.name }}</strong>
                            <p class="mt-1 text-[13px] font-bold text-slate-600">
                                {{ formatDate(form.appointment_date) }} · {{ formatTime(form.start_time) }} - {{ formatTime(form.end_time) }} · {{ form.slot_duration }} min
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 border-t border-slate-200 bg-white px-6 py-5">
                    <button
                        type="button"
                        class="rounded-2xl border border-slate-200 py-3.5 text-sm font-extrabold text-slate-700 transition hover:bg-slate-50"
                        @click="closeDrawer"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        :disabled="form.processing"
                        class="rounded-2xl bg-slate-900 py-3.5 text-sm font-extrabold text-white transition hover:bg-indigo-600 disabled:cursor-not-allowed disabled:opacity-65"
                        @click="submitAppointment"
                    >
                        {{ form.processing ? 'Saving...' : isEditing ? 'Update Appointment' : 'Save Appointment' }}
                    </button>
                </div>
            </aside>

            <!-- Confirm modal -->
            <div v-if="confirmModalOpen" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-900/45 p-5">
                <div class="w-full max-w-[460px] rounded-[18px] bg-white p-[22px] shadow-[0_24px_80px_rgba(15,23,42,0.25)]">
                    <div class="mb-[18px] flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">Confirm Appointment</h3>
                            <p class="mt-[5px] text-[13px] text-slate-500">Assign auto or manual serial before confirming.</p>
                        </div>
                        <button type="button" class="rounded-[10px] bg-slate-100 p-2 text-slate-500 transition hover:bg-slate-200" @click="closeConfirmModal">
                            <X :size="18" />
                        </button>
                    </div>

                    <div v-if="confirmingAppointment" class="mb-4 flex flex-col gap-1 rounded-[14px] border border-slate-200 bg-slate-50 p-3.5">
                        <strong class="text-[15px] text-slate-900">{{ confirmingAppointment.patient_name }}</strong>
                        <span class="text-[13px] text-slate-500">{{ doctorName(confirmingAppointment) }}</span>
                        <span class="text-[13px] text-slate-500">
                            {{ formatDate(String(confirmingAppointment.appointment_date).substring(0, 10)) }} — {{ formatTime(confirmingAppointment.start_time) }}
                        </span>
                    </div>

                    <div class="mb-5">
                        <label class="mb-2 block text-[13px] font-extrabold text-slate-700">Serial Number</label>
                        <input
                            v-model="confirmSerialNumber"
                            type="number"
                            min="1"
                            placeholder="Leave as suggested or change manually"
                            class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-100"
                        />
                        <p class="mt-1.5 text-xs text-slate-500">Suggested serial is auto-generated. You can change it before confirming.</p>
                    </div>

                    <div class="flex justify-end gap-2.5">
                        <button
                            type="button"
                            class="rounded-[10px] bg-slate-100 px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200"
                            @click="closeConfirmModal"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            class="rounded-[10px] bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                            @click="submitConfirmAppointment"
                        >
                            Confirm Appointment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
