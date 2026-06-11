<script setup>
import { computed, ref, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Plus,Search,CalendarDays,Clock,Pencil,Trash2,X,User,Mail,Phone,Stethoscope,Timer, ChevronLeft, ChevronRight, Loader2, Check, Sparkles,} from 'lucide-vue-next'

const props = defineProps({
    appointments: {
        type: Object,
        required: true,
    },
    doctors: {
        type: [Array, Object],
        default: () => [],
    },
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
    filters: {
        type: Object,
        default: () => ({}),
    },
})

const search = ref(props.filters.search || '')
const statusFilter = ref(props.filters.status || '')
const doctorFilter = ref(props.filters.doctor_id || '')
const dateFilter = ref(props.filters.date || '')
const perPage = ref(props.appointments?.per_page || 15)

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

const form = useForm({
    doctor_id: '',
    patient_name: '',
    patient_email: '',
    patient_phone: '',
    appointment_date: '',
    start_time: '',
    end_time: '',
    slot_duration: 30,
    status: 'pending',
    notes: '',
})

const rows = computed(() => props.appointments?.data || [])

const doctorsList = computed(() => {
    if (Array.isArray(props.doctors)) return props.doctors
    return Object.values(props.doctors || {})
})

const selectedDoctor = computed(() => {
    return doctorsList.value.find((doctor) => Number(doctor.id) === Number(form.doctor_id))
})

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
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        )
    }, 350)
})

watch(
    () => form.doctor_id,
    () => {
        resetScheduleSelection()
    },
)

watch(
    () => form.appointment_date,
    () => {
        if (form.doctor_id && form.appointment_date) {
            fetchAvailableSlots()
        }
    },
)

const resetScheduleSelection = () => {
    form.appointment_date = ''
    form.start_time = ''
    form.end_time = ''
    form.slot_duration = 30
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
    form.slot_duration = 30
    selectedSlotKey.value = ''
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
    form.slot_duration = 30
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
    form.appointment_date = appointment.appointment_date || ''
    form.start_time = normalizeTime(appointment.start_time)
    form.end_time = normalizeTime(appointment.end_time)
    form.slot_duration = appointment.slot_duration || 30
    form.status = appointment.status || 'pending'
    form.notes = appointment.notes || ''

    selectedSlotKey.value = `${form.start_time}-${form.end_time}`

    if (appointment.appointment_date) {
        calendarMonth.value = new Date(`${appointment.appointment_date}T00:00:00`)
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

    router.delete(route('appointments.destroy', appointment.id), {
        preserveScroll: true,
    })
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

    if (!keepSelected) {
        clearSlotOnly()
    }

    try {
        const url = routeAvailableSlots()

        const query = new URLSearchParams({
            doctor_id: form.doctor_id,
            date: form.appointment_date,
        })

        const response = await fetch(`${url}?${query.toString()}`, {
            headers: {
                Accept: 'application/json',
            },
        })

        const data = await response.json()

        if (data.success) {
            slots.value = data.slots || []

            if (!slots.value.length) {
                slotMessage.value = 'No slots found for this date. The doctor may be on rest or unavailable.'
            }

            return
        }

        slots.value = []
        slotMessage.value = data.message || 'Could not load slots.'
    } catch (error) {
        slots.value = []
        slotMessage.value = 'Unable to fetch time slots. Please check the available-slots route.'
    } finally {
        loadingSlots.value = false
    }
}

const routeAvailableSlots = () => {
    try {
        return route('booking.available-slots')
    } catch {
        return '/booking/available-slots'
    }
}

const selectCalendarDate = (day) => {
    if (!day.selectable) return

    form.appointment_date = day.date
}

const selectSlot = (slot) => {
    if (!slot.available) return

    form.start_time = normalizeTime(slot.start_time)
    form.end_time = normalizeTime(slot.end_time)
    form.slot_duration = calculateDuration(slot.start_time, slot.end_time)
    selectedSlotKey.value = `${form.start_time}-${form.end_time}`
    slotMessage.value = ''
}

const calculateDuration = (start, end) => {
    const [startHour, startMinute] = normalizeTime(start).split(':').map(Number)
    const [endHour, endMinute] = normalizeTime(end).split(':').map(Number)

    return endHour * 60 + endMinute - (startHour * 60 + startMinute)
}

const calendarDays = computed(() => {
    const year = calendarMonth.value.getFullYear()
    const month = calendarMonth.value.getMonth()

    const firstDay = new Date(year, month, 1)
    const lastDay = new Date(year, month + 1, 0)
    const startPadding = firstDay.getDay()
    const days = []

    for (let i = 0; i < startPadding; i++) {
        days.push({
            label: '',
            date: '',
            muted: true,
            selectable: false,
        })
    }

    for (let day = 1; day <= lastDay.getDate(); day++) {
        const date = new Date(year, month, day)
        const dateString = toDateString(date)
        const dayOfWeek = date.getDay()
        const isPast = isPastDate(date)
        const hasSchedule = selectedDoctorWorkingDays.value.includes(dayOfWeek)

        days.push({
            label: day,
            date: dateString,
            dayOfWeek,
            muted: false,
            today: dateString === toDateString(new Date()),
            selected: form.appointment_date === dateString,
            hasSchedule,
            selectable: Boolean(form.doctor_id) && hasSchedule && !isPast,
        })
    }

    return days
})

const previousMonth = () => {
    const date = new Date(calendarMonth.value)
    date.setMonth(date.getMonth() - 1)
    calendarMonth.value = date
}

const nextMonth = () => {
    const date = new Date(calendarMonth.value)
    date.setMonth(date.getMonth() + 1)
    calendarMonth.value = date
}

const currentMonthLabel = computed(() => {
    return calendarMonth.value.toLocaleDateString('en-US', {
        month: 'long',
        year: 'numeric',
    })
})

const toDateString = (date) => {
    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')

    return `${year}-${month}-${day}`
}

const isPastDate = (date) => {
    const today = new Date()
    today.setHours(0, 0, 0, 0)

    const target = new Date(date)
    target.setHours(0, 0, 0, 0)

    return target < today
}

const normalizeTime = (time) => {
    if (!time) return ''
    return String(time).slice(0, 5)
}

const formatTime = (time) => {
    const normalized = normalizeTime(time)
    if (!normalized) return '—'

    const [hour, minute] = normalized.split(':').map(Number)
    const suffix = hour >= 12 ? 'PM' : 'AM'
    const displayHour = hour % 12 || 12

    return `${displayHour}:${String(minute).padStart(2, '0')} ${suffix}`
}

const formatDate = (date) => {
    if (!date) return '—'

    return new Date(`${date}T00:00:00`).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    })
}

const doctorName = (appointment) => {
    return appointment?.doctor?.name ||
        doctorsList.value.find((doctor) => Number(doctor.id) === Number(appointment.doctor_id))?.name ||
        'Unknown Doctor'
}

const statusLabel = (status) => props.statuses?.[status] || status

const statusClass = (status) => {
    return {
        pending: 'status-pending',
        confirmed: 'status-confirmed',
        cancelled: 'status-cancelled',
        completed: 'status-completed',
        no_show: 'status-no-show',
    }[status] || 'status-default'
}

const visitPage = (url) => {
    if (!url) return

    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
    })
}

const openConfirmModal = (appointment) => {
    confirmingAppointment.value = appointment

    const sameDoctorDateConfirmed = rows.value.filter((item) => {
        return Number(item.doctor_id) === Number(appointment.doctor_id)
            && String(item.appointment_date).substring(0, 10) === String(appointment.appointment_date).substring(0, 10)
            && item.serial_number
    })

    const maxSerial = sameDoctorDateConfirmed.reduce((max, item) => {
        return Math.max(max, Number(item.serial_number || 0))
    }, 0)

    confirmSerialNumber.value = maxSerial + 1
    confirmModalOpen.value = true
}

const submitConfirmAppointment = () => {
    if (!confirmingAppointment.value) return

    router.post(
        route('appointments.confirm', confirmingAppointment.value.id),
        {
            serial_number: confirmSerialNumber.value || null,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                confirmModalOpen.value = false
                confirmingAppointment.value = null
                confirmSerialNumber.value = ''
            },
        },
    )
}

const closeConfirmModal = () => {
    confirmModalOpen.value = false
    confirmingAppointment.value = null
    confirmSerialNumber.value = ''
}
</script>

<template>
    <AppLayout>
        <div class="appointment-page">
            <div class="appointment-container">
                <section class="hero-card">
                    <div class="hero-copy">
                        <span class="hero-badge">
                            <Sparkles size="14" />
                            Appointment Command Center
                        </span>

                        <h1>Premium Appointment Management</h1>

                        <p>
                            Manage patients, doctors, schedules and real-time slot booking from one beautiful dashboard.
                        </p>
                    </div>

                    <button class="primary-btn hero-action" type="button" @click="openCreateDrawer">
                        <Plus size="18" />
                        Create Appointment
                    </button>
                </section>

                <section class="stats-grid">
                    <div class="stat-card">
                        <span>Total</span>
                        <strong>{{ pageStats.total }}</strong>
                        <p>All records</p>
                    </div>

                    <div class="stat-card amber">
                        <span>Pending</span>
                        <strong>{{ pageStats.pending }}</strong>
                        <p>This page</p>
                    </div>

                    <div class="stat-card green">
                        <span>Confirmed</span>
                        <strong>{{ pageStats.confirmed }}</strong>
                        <p>This page</p>
                    </div>

                    <div class="stat-card blue">
                        <span>Completed</span>
                        <strong>{{ pageStats.completed }}</strong>
                        <p>This page</p>
                    </div>

                    <div class="stat-card rose">
                        <span>Cancelled</span>
                        <strong>{{ pageStats.cancelled }}</strong>
                        <p>This page</p>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-top">
                        <div>
                            <h2>Appointments</h2>
                            <p>Live search, smart filters, schedule-based booking and server pagination.</p>
                        </div>

                        <button class="clear-btn" type="button" @click="clearFilters">
                            Clear Filters
                        </button>
                    </div>

                    <div class="filters">
                        <div class="field-icon search-box">
                            <Search size="17" />
                            <input v-model="search" type="text" placeholder="Search patient name, phone or email..." />
                        </div>

                        <select v-model="statusFilter">
                            <option value="">All Status</option>
                            <option v-for="(label, value) in statuses" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>

                        <select v-model="doctorFilter">
                            <option value="">All Doctors</option>
                            <option v-for="doctor in doctorsList" :key="doctor.id" :value="doctor.id">
                                {{ doctor.name }}
                            </option>
                        </select>

                        <input v-model="dateFilter" type="date" />

                        <select v-model="perPage">
                            <option value="10">10 per page</option>
                            <option value="15">15 per page</option>
                            <option value="25">25 per page</option>
                            <option value="50">50 per page</option>
                        </select>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Serial</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Schedule</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="appointment in rows" :key="appointment.id">
                                    <td>
                                        <div class="serial-cell">
                                                {{ appointment.serial_number ?? '—' }}

                                        </div>
                                    </td>
                                    <td>
                                        <div class="patient-cell">

                                            <div class="avatar">
                                                {{ appointment.patient_name?.charAt(0) || 'P' }}
                                            </div>

                                            <div>
                                                <strong>{{ appointment.patient_name }}</strong>
                                                <span>{{ appointment.patient_email || 'No email' }}</span>
                                                <small>{{ appointment.patient_phone }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="doctor-cell">
                                            <Stethoscope size="16" />
                                            {{ doctorName(appointment) }}
                                        </div>
                                    </td>

                                    <td>
                                        <div class="schedule-cell">
                                            <span>
                                                <CalendarDays size="15" />
                                                {{ formatDate(String(appointment.appointment_date).substring(0, 10)) }}

                                                
                                            </span>

                                            <span>
                                                <Clock size="15" />
                                                {{ formatTime(appointment.start_time) }} - {{ formatTime(appointment.end_time) }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="duration-pill">
                                            <Timer size="14" />
                                            {{ appointment.slot_duration }} min
                                        </span>
                                    </td>

                                    <td>
                                        <span class="status-pill" :class="statusClass(appointment.status)">
                                            {{ statusLabel(appointment.status) }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="actions">
                                            <button
                                                v-if="appointment.status === 'pending'"
                                                type="button"
                                                class="success"
                                                title="Confirm appointment"
                                                @click="openConfirmModal(appointment)"
                                            >
                                                <Check size="16" />
                                            </button>

                                            <button type="button" @click="openEditDrawer(appointment)">
                                                <Pencil size="16" />
                                            </button>

                                            <button type="button" class="danger" @click="deleteAppointment(appointment)">
                                                <Trash2 size="16" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="rows.length === 0">
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <Search size="30" />
                                            <h3>No appointments found</h3>
                                            <p>Try changing your search or filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination">
                        <p>
                            Showing
                            <strong>{{ appointments.from || 0 }}</strong>
                            to
                            <strong>{{ appointments.to || 0 }}</strong>
                            of
                            <strong>{{ appointments.total || 0 }}</strong>
                            appointments
                        </p>

                        <div class="pagination-links">
                            <button
                                v-for="link in appointments.links"
                                :key="link.label"
                                type="button"
                                :disabled="!link.url"
                                :class="{ active: link.active }"
                                v-html="link.label"
                                @click="visitPage(link.url)"
                            ></button>
                        </div>
                    </div>
                </section>
            </div>

            <div v-if="drawerOpen" class="drawer-overlay" @click="closeDrawer"></div>

            <aside :class="['drawer', { open: drawerOpen }]">
                <div class="drawer-header">
                    <div>
                        <span>{{ isEditing ? 'Update Booking' : 'New Booking' }}</span>
                        <h2>{{ isEditing ? 'Edit Appointment' : 'Create Appointment' }}</h2>
                        <p>Select doctor, valid schedule date and available time slot.</p>
                    </div>

                    <button type="button" @click="closeDrawer">
                        <X size="22" />
                    </button>
                </div>

                <div class="drawer-body">
                    <div class="form-section">
                        <div class="section-title">
                            <div>
                                <Stethoscope size="18" />
                            </div>
                            <div>
                                <h3>Doctor</h3>
                                <p>Calendar will unlock based on doctor schedule.</p>
                            </div>
                        </div>

                        <select v-model="form.doctor_id" :class="{ invalid: form.errors.doctor_id }">
                            <option value="">Select doctor</option>
                            <option v-for="doctor in doctorsList" :key="doctor.id" :value="doctor.id">
                                {{ doctor.name }}
                            </option>
                        </select>

                        <p v-if="form.errors.doctor_id" class="error-text">
                            {{ form.errors.doctor_id }}
                        </p>

                        <div v-if="form.doctor_id && !hasDoctorSchedule" class="warning-box">
                            This doctor has no active timetable. Calendar dates are disabled.
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">
                            <div>
                                <CalendarDays size="18" />
                            </div>
                            <div>
                                <h3>Schedule Date</h3>
                                <p>Only working schedule dates are selectable.</p>
                            </div>
                        </div>

                        <div class="calendar-card">
                            <div class="calendar-head">
                                <button type="button" @click="previousMonth">
                                    <ChevronLeft size="18" />
                                </button>

                                <strong>{{ currentMonthLabel }}</strong>

                                <button type="button" @click="nextMonth">
                                    <ChevronRight size="18" />
                                </button>
                            </div>

                            <div class="weekdays">
                                <span>Sun</span>
                                <span>Mon</span>
                                <span>Tue</span>
                                <span>Wed</span>
                                <span>Thu</span>
                                <span>Fri</span>
                                <span>Sat</span>
                            </div>

                            <div class="calendar-grid">
                                <button
                                    v-for="(day, index) in calendarDays"
                                    :key="index"
                                    type="button"
                                    :disabled="!day.selectable"
                                    :class="[
                                        'calendar-day',
                                        {
                                            muted: day.muted,
                                            today: day.today,
                                            selected: day.selected,
                                            disabled: !day.selectable && !day.muted,
                                            scheduled: day.hasSchedule && !day.muted,
                                        },
                                    ]"
                                    @click="selectCalendarDate(day)"
                                >
                                    <span>{{ day.label }}</span>
                                </button>
                            </div>

                            <div class="calendar-legend">
                                <span><i class="legend-dot scheduled"></i> Scheduled</span>
                                <span><i class="legend-dot disabled"></i> Not available</span>
                                <span><i class="legend-dot selected"></i> Selected</span>
                            </div>
                        </div>

                        <p v-if="form.errors.appointment_date" class="error-text">
                            {{ form.errors.appointment_date }}
                        </p>
                    </div>

                    <div class="form-section">
                        <div class="section-title">
                            <div>
                                <Clock size="18" />
                            </div>
                            <div>
                                <h3>Available Time Slots</h3>
                                <p>Generated from doctor timetable, rest days and booked appointments.</p>
                            </div>
                        </div>

                        <div v-if="!form.doctor_id" class="soft-box">
                            Select a doctor first.
                        </div>

                        <div v-else-if="!form.appointment_date" class="soft-box">
                            Select a schedule date from the calendar.
                        </div>

                        <div v-else-if="loadingSlots" class="loading-box">
                            <Loader2 size="18" class="spin" />
                            Loading available slots...
                        </div>

                        <div v-else>
                            <div v-if="slots.length" class="slots-grid">
                                <button
                                    v-for="slot in slots"
                                    :key="`${slot.start_time}-${slot.end_time}`"
                                    type="button"
                                    :disabled="!slot.available"
                                    :class="[
                                        'slot-btn',
                                        {
                                            selected: selectedSlotKey === `${normalizeTime(slot.start_time)}-${normalizeTime(slot.end_time)}`,
                                            unavailable: !slot.available,
                                        },
                                    ]"
                                    @click="selectSlot(slot)"
                                >
                                    <strong>{{ formatTime(slot.start_time) }}</strong>
                                    <span>{{ formatTime(slot.end_time) }}</span>
                                    <Check v-if="selectedSlotKey === `${normalizeTime(slot.start_time)}-${normalizeTime(slot.end_time)}`" size="15" />
                                </button>
                            </div>

                            <div v-else class="soft-box">
                                {{ slotMessage || 'No slots loaded.' }}
                            </div>
                        </div>

                        <p v-if="slotMessage" class="hint-text">
                            {{ slotMessage }}
                        </p>

                        <p v-if="form.errors.start_time || form.errors.end_time" class="error-text">
                            {{ form.errors.start_time || form.errors.end_time }}
                        </p>
                    </div>

                    <div class="form-section">
                        <div class="section-title">
                            <div>
                                <User size="18" />
                            </div>
                            <div>
                                <h3>Patient Details</h3>
                                <p>Basic patient information for appointment record.</p>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Patient Name *</label>
                                <div class="field-icon">
                                    <User size="16" />
                                    <input
                                        v-model="form.patient_name"
                                        type="text"
                                        placeholder="Enter patient name"
                                        :class="{ invalid: form.errors.patient_name }"
                                    />
                                </div>
                                <p v-if="form.errors.patient_name" class="error-text">
                                    {{ form.errors.patient_name }}
                                </p>
                            </div>

                            <div class="form-group">
                                <label>Phone *</label>
                                <div class="field-icon">
                                    <Phone size="16" />
                                    <input
                                        v-model="form.patient_phone"
                                        type="text"
                                        placeholder="Enter phone number"
                                        :class="{ invalid: form.errors.patient_phone }"
                                    />
                                </div>
                                <p v-if="form.errors.patient_phone" class="error-text">
                                    {{ form.errors.patient_phone }}
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <div class="field-icon">
                                <Mail size="16" />
                                <input
                                    v-model="form.patient_email"
                                    type="email"
                                    placeholder="Enter email address"
                                    :class="{ invalid: form.errors.patient_email }"
                                />
                            </div>
                            <p v-if="form.errors.patient_email" class="error-text">
                                {{ form.errors.patient_email }}
                            </p>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select v-model="form.status">
                                <option v-for="(label, value) in statuses" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea
                                v-model="form.notes"
                                rows="4"
                                placeholder="Write appointment note..."
                            ></textarea>
                        </div>
                    </div>

                    <div v-if="form.doctor_id && form.appointment_date && form.start_time" class="summary-card">
                        <span>Booking Summary</span>
                        <div>
                            <strong>{{ selectedDoctor?.name }}</strong>
                            <p>
                                {{ formatDate(form.appointment_date) }}
                                ·
                                {{ formatTime(form.start_time) }} - {{ formatTime(form.end_time) }}
                                ·
                                {{ form.slot_duration }} min
                            </p>
                        </div>
                    </div>
                </div>

                <div class="drawer-footer">
                    <button class="secondary-btn" type="button" @click="closeDrawer">
                        Cancel
                    </button>

                    <button
                        class="primary-btn submit-btn"
                        type="button"
                        :disabled="form.processing"
                        @click="submitAppointment"
                    >
                        {{ form.processing ? 'Saving...' : isEditing ? 'Update Appointment' : 'Save Appointment' }}
                    </button>
                </div>
            </aside>

            <div v-if="confirmModalOpen" class="modal-backdrop">
                <div class="confirm-modal">
                    <div class="modal-header">
                        <div>
                            <h3>Confirm Appointment</h3>
                            <p>
                                Assign auto or manual serial before confirming.
                            </p>
                        </div>

                        <button type="button" @click="closeConfirmModal">
                            <X size="18" />
                        </button>
                    </div>

                    <div v-if="confirmingAppointment" class="confirm-summary">
                        <strong>{{ confirmingAppointment.patient_name }}</strong>
                        <span>{{ doctorName(confirmingAppointment) }}</span>
                        <span>
                            {{ formatDate(String(confirmingAppointment.appointment_date).substring(0, 10)) }}
                            —
                            {{ formatTime(confirmingAppointment.start_time) }}
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Serial Number</label>
                        <input
                            v-model="confirmSerialNumber"
                            type="number"
                            min="1"
                            placeholder="Leave as suggested or change manually"
                        />
                        <p class="help-text">
                            Suggested serial is auto-generated. You can change it before confirming.
                        </p>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" @click="closeConfirmModal">
                            Cancel
                        </button>

                        <button type="button" class="btn-primary" @click="submitConfirmAppointment">
                            Confirm Appointment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.serial-cell {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.serial-cell strong {
    font-size: 13px;
    color: #111827;
}

.serial-cell small {
    font-size: 11px;
    color: #6b7280;
    text-transform: capitalize;
}

.actions button.success {
    color: #047857;
    background: #ecfdf5;
}

.actions button.success:hover {
    background: #d1fae5;
}

.modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 80;
    background: rgba(15, 23, 42, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.confirm-modal {
    width: 100%;
    max-width: 460px;
    background: #ffffff;
    border-radius: 18px;
    padding: 22px;
    box-shadow: 0 24px 80px rgba(15, 23, 42, 0.25);
}

.modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
}

.modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #111827;
}

.modal-header p {
    margin: 5px 0 0;
    font-size: 13px;
    color: #6b7280;
}

.modal-header button {
    border: none;
    background: #f3f4f6;
    border-radius: 10px;
    padding: 8px;
    cursor: pointer;
}

.confirm-summary {
    display: flex;
    flex-direction: column;
    gap: 4px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 14px;
    margin-bottom: 16px;
}

.confirm-summary strong {
    color: #111827;
    font-size: 15px;
}

.confirm-summary span {
    color: #6b7280;
    font-size: 13px;
}

.help-text {
    margin-top: 6px;
    font-size: 12px;
    color: #6b7280;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.btn-secondary,
.btn-primary {
    border: none;
    border-radius: 10px;
    padding: 10px 14px;
    font-weight: 600;
    cursor: pointer;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-primary {
    background: #2563eb;
    color: #ffffff;
}

.appointment-page {
    min-height: 100vh;
    padding: 32px;
    color: #0f172a;
    background:
        radial-gradient(circle at top left, rgba(99, 102, 241, 0.18), transparent 30%),
        radial-gradient(circle at top right, rgba(14, 165, 233, 0.16), transparent 28%),
        linear-gradient(180deg, #f8fafc 0%, #edf2f7 100%);
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

.appointment-container {
    max-width: 1440px;
    margin: 0 auto;
}

.hero-card {
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 28px;
    padding: 38px;
    border-radius: 36px;
    color: white;
    background: linear-gradient(135deg, #020617 0%, #0f172a 45%, #312e81 100%);
    box-shadow: 0 28px 90px rgba(15, 23, 42, 0.26);
}

.hero-card::before {
    content: "";
    position: absolute;
    right: -90px;
    top: -100px;
    width: 320px;
    height: 320px;
    border-radius: 999px;
    background: rgba(99, 102, 241, 0.42);
    filter: blur(34px);
}

.hero-card::after {
    content: "";
    position: absolute;
    left: 30%;
    bottom: -160px;
    width: 300px;
    height: 300px;
    border-radius: 999px;
    background: rgba(56, 189, 248, 0.16);
    filter: blur(32px);
}

.hero-copy,
.hero-action {
    position: relative;
    z-index: 1;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 850;
    color: #c7d2fe;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.14);
}

.hero-card h1 {
    margin: 0;
    max-width: 720px;
    font-size: 42px;
    line-height: 1.07;
    font-weight: 950;
    letter-spacing: -0.05em;
}

.hero-card p {
    max-width: 720px;
    margin: 14px 0 0;
    color: #cbd5e1;
    font-size: 15px;
    line-height: 1.7;
}

.primary-btn,
.secondary-btn,
.clear-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    border: 0;
    cursor: pointer;
    border-radius: 18px;
    padding: 14px 20px;
    font-size: 14px;
    font-weight: 850;
    transition: 0.2s ease;
    white-space: nowrap;
}

.primary-btn {
    background: #ffffff;
    color: #020617;
    box-shadow: 0 18px 45px rgba(0, 0, 0, 0.2);
}

.primary-btn:hover {
    transform: translateY(-2px);
    background: #eef2ff;
}

.submit-btn {
    width: 100%;
    background: #111827;
    color: #ffffff;
}

.submit-btn:hover {
    background: #4f46e5;
}

.submit-btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.secondary-btn {
    width: 100%;
    border: 1px solid #e2e8f0;
    color: #334155;
    background: #ffffff;
}

.clear-btn {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 16px;
    margin-top: 22px;
}

.stat-card {
    padding: 22px;
    border-radius: 28px;
    border: 1px solid #e2e8f0;
    background: rgba(255, 255, 255, 0.92);
    box-shadow: 0 16px 45px rgba(15, 23, 42, 0.06);
}

.stat-card span {
    color: #64748b;
    font-size: 13px;
    font-weight: 850;
}

.stat-card strong {
    display: block;
    margin-top: 10px;
    font-size: 34px;
    line-height: 1;
    color: #0f172a;
}

.stat-card p {
    margin: 10px 0 0;
    color: #94a3b8;
    font-size: 12px;
}

.stat-card.green strong {
    color: #059669;
}

.stat-card.amber strong {
    color: #d97706;
}

.stat-card.rose strong {
    color: #e11d48;
}

.stat-card.blue strong {
    color: #2563eb;
}

.panel {
    margin-top: 22px;
    overflow: hidden;
    border-radius: 32px;
    border: 1px solid #e2e8f0;
    background: rgba(255, 255, 255, 0.96);
    box-shadow: 0 22px 70px rgba(15, 23, 42, 0.08);
}

.panel-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 18px;
    padding: 24px;
    border-bottom: 1px solid #e2e8f0;
}

.panel-top h2 {
    margin: 0;
    font-size: 23px;
    font-weight: 950;
    letter-spacing: -0.035em;
}

.panel-top p {
    margin: 6px 0 0;
    color: #64748b;
    font-size: 14px;
}

.filters {
    display: grid;
    grid-template-columns: 1.7fr repeat(4, minmax(140px, 1fr));
    gap: 12px;
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
}

.field-icon {
    position: relative;
}

.field-icon svg {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}

input,
select,
textarea {
    width: 100%;
    border: 1px solid #dbe3ef;
    outline: none;
    background: #f8fafc;
    color: #0f172a;
    border-radius: 18px;
    font-size: 14px;
    transition: 0.2s ease;
}

input,
select {
    height: 48px;
    padding: 0 16px;
}

textarea {
    padding: 14px 16px;
    resize: none;
}

.field-icon input {
    padding-left: 46px;
}

input:focus,
select:focus,
textarea:focus {
    border-color: #6366f1;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.13);
}

.invalid {
    border-color: #fb7185 !important;
    background: #fff1f2 !important;
}

.error-text {
    margin: 7px 0 0;
    color: #e11d48;
    font-size: 12px;
    font-weight: 750;
}

.hint-text {
    margin: 10px 0 0;
    color: #64748b;
    font-size: 13px;
    font-weight: 650;
}

.table-wrap {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 1080px;
    border-collapse: collapse;
}

thead {
    background: #f8fafc;
}

th {
    padding: 16px 24px;
    text-align: left;
    color: #64748b;
    font-size: 12px;
    font-weight: 950;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

td {
    padding: 18px 24px;
    border-top: 1px solid #edf2f7;
    vertical-align: middle;
}

tbody tr:hover {
    background: #f8fafc;
}

.text-right {
    text-align: right;
}

.patient-cell {
    display: flex;
    align-items: center;
    gap: 14px;
}

.avatar {
    width: 48px;
    height: 48px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 17px;
    color: #4338ca;
    font-weight: 950;
    background: linear-gradient(135deg, #e0e7ff, #cffafe);
}

.patient-cell strong {
    display: block;
    color: #0f172a;
    font-size: 15px;
    font-weight: 900;
}

.patient-cell span,
.patient-cell small {
    display: block;
    margin-top: 4px;
    color: #64748b;
    font-size: 13px;
}

.doctor-cell,
.schedule-cell span,
.duration-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.doctor-cell {
    color: #334155;
    font-weight: 800;
}

.doctor-cell svg {
    color: #6366f1;
}

.schedule-cell {
    display: grid;
    gap: 6px;
}

.schedule-cell span {
    color: #475569;
    font-size: 14px;
    font-weight: 650;
}

.schedule-cell svg {
    color: #94a3b8;
}

.duration-pill {
    padding: 8px 12px;
    border-radius: 999px;
    color: #475569;
    background: #f1f5f9;
    font-size: 12px;
    font-weight: 900;
}

.status-pill {
    display: inline-flex;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid;
    font-size: 12px;
    font-weight: 950;
}

.status-pending {
    color: #b45309;
    background: #fffbeb;
    border-color: #fde68a;
}

.status-confirmed {
    color: #047857;
    background: #ecfdf5;
    border-color: #a7f3d0;
}

.status-cancelled {
    color: #be123c;
    background: #fff1f2;
    border-color: #fecdd3;
}

.status-completed {
    color: #1d4ed8;
    background: #eff6ff;
    border-color: #bfdbfe;
}

.status-no-show {
    color: #475569;
    background: #f8fafc;
    border-color: #cbd5e1;
}

.actions {
    display: flex;
    justify-content: flex-end;
    gap: 9px;
}

.actions button {
    width: 40px;
    height: 40px;
    display: inline-grid;
    place-items: center;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    border-radius: 14px;
    color: #64748b;
    cursor: pointer;
    transition: 0.2s ease;
}

.actions button:hover {
    background: #eef2ff;
    border-color: #c7d2fe;
    color: #4f46e5;
}

.actions button.danger:hover {
    background: #fff1f2;
    border-color: #fecdd3;
    color: #e11d48;
}

.empty-state {
    padding: 46px 20px;
    text-align: center;
    color: #64748b;
}

.empty-state svg {
    color: #94a3b8;
}

.empty-state h3 {
    margin: 14px 0 4px;
    color: #0f172a;
}

.pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 18px 24px;
    border-top: 1px solid #e2e8f0;
}

.pagination p {
    margin: 0;
    color: #64748b;
    font-size: 14px;
}

.pagination-links {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 8px;
}

.pagination-links button {
    min-width: 40px;
    height: 40px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    border-radius: 13px;
    padding: 0 12px;
    color: #334155;
    font-weight: 900;
    cursor: pointer;
}

.pagination-links button.active {
    background: #111827;
    color: #ffffff;
    border-color: #111827;
}

.pagination-links button:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

.drawer-overlay {
    position: fixed;
    inset: 0;
    z-index: 40;
    background: rgba(15, 23, 42, 0.58);
    backdrop-filter: blur(7px);
}

.drawer {
    position: fixed;
    top: 0;
    right: 0;
    z-index: 50;
    width: min(100%, 620px);
    height: 100vh;
    display: flex;
    flex-direction: column;
    background: #ffffff;
    box-shadow: -28px 0 80px rgba(15, 23, 42, 0.26);
    transform: translateX(100%);
    transition: transform 0.28s ease;
}

.drawer.open {
    transform: translateX(0);
}

.drawer-header {
    display: flex;
    justify-content: space-between;
    gap: 18px;
    padding: 24px;
    border-bottom: 1px solid #e2e8f0;
    background:
        radial-gradient(circle at top right, rgba(99, 102, 241, 0.12), transparent 32%),
        #ffffff;
}

.drawer-header span {
    display: inline-flex;
    margin-bottom: 7px;
    color: #6366f1;
    font-size: 12px;
    font-weight: 950;
    text-transform: uppercase;
    letter-spacing: 0.12em;
}

.drawer-header h2 {
    margin: 0;
    color: #0f172a;
    font-size: 24px;
    font-weight: 950;
    letter-spacing: -0.04em;
}

.drawer-header p {
    margin: 6px 0 0;
    color: #64748b;
    font-size: 14px;
}

.drawer-header button {
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    border: 0;
    border-radius: 14px;
    background: #f1f5f9;
    color: #475569;
    cursor: pointer;
}

.drawer-body {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
    background: #f8fafc;
}

.form-section {
    margin-bottom: 18px;
    padding: 18px;
    border: 1px solid #e2e8f0;
    border-radius: 24px;
    background: #ffffff;
    box-shadow: 0 12px 35px rgba(15, 23, 42, 0.045);
}

.section-title {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
}

.section-title > div:first-child {
    width: 40px;
    height: 40px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 14px;
    color: #4f46e5;
    background: #eef2ff;
}

.section-title h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 950;
    color: #0f172a;
}

.section-title p {
    margin: 4px 0 0;
    color: #64748b;
    font-size: 13px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.form-group {
    margin-bottom: 16px;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #334155;
    font-size: 13px;
    font-weight: 900;
}

.calendar-card {
    padding: 14px;
    border-radius: 22px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
}

.calendar-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}

.calendar-head strong {
    font-size: 15px;
    font-weight: 950;
}

.calendar-head button {
    width: 38px;
    height: 38px;
    display: grid;
    place-items: center;
    border: 1px solid #e2e8f0;
    border-radius: 13px;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
}

.weekdays,
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 8px;
}

.weekdays {
    margin-bottom: 8px;
}

.weekdays span {
    text-align: center;
    color: #94a3b8;
    font-size: 11px;
    font-weight: 950;
    text-transform: uppercase;
}

.calendar-day {
    height: 44px;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    background: #ffffff;
    color: #334155;
    font-weight: 900;
    cursor: pointer;
    transition: 0.18s ease;
}

.calendar-day.scheduled {
    border-color: #c7d2fe;
    background: #eef2ff;
    color: #4338ca;
}

.calendar-day.today {
    box-shadow: inset 0 0 0 2px #38bdf8;
}

.calendar-day.selected {
    border-color: #4f46e5;
    background: #4f46e5;
    color: #ffffff;
    box-shadow: 0 12px 30px rgba(79, 70, 229, 0.26);
}

.calendar-day.disabled {
    background: #f1f5f9;
    color: #cbd5e1;
    cursor: not-allowed;
    opacity: 0.75;
}

.calendar-day.muted {
    border-color: transparent;
    background: transparent;
    cursor: default;
}

.calendar-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 14px;
    color: #64748b;
    font-size: 12px;
    font-weight: 700;
}

.calendar-legend span {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.legend-dot {
    width: 9px;
    height: 9px;
    border-radius: 999px;
}

.legend-dot.scheduled {
    background: #6366f1;
}

.legend-dot.disabled {
    background: #cbd5e1;
}

.legend-dot.selected {
    background: #4f46e5;
}

.slots-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}

.slot-btn {
    position: relative;
    min-height: 72px;
    display: grid;
    gap: 3px;
    place-items: center;
    border: 1px solid #bbf7d0;
    border-radius: 18px;
    background: #ffffff;
    color: #047857;
    cursor: pointer;
    transition: 0.18s ease;
}

.slot-btn strong {
    font-size: 13px;
    font-weight: 950;
}

.slot-btn span {
    color: #94a3b8;
    font-size: 11px;
    font-weight: 800;
}

.slot-btn:hover {
    transform: translateY(-1px);
    border-color: #10b981;
    box-shadow: 0 12px 30px rgba(16, 185, 129, 0.12);
}

.slot-btn.selected {
    border-color: #4f46e5;
    background: #4f46e5;
    color: #ffffff;
    box-shadow: 0 16px 36px rgba(79, 70, 229, 0.24);
}

.slot-btn.selected span {
    color: #c7d2fe;
}

.slot-btn.selected svg {
    position: absolute;
    right: 8px;
    top: 8px;
}

.slot-btn.unavailable {
    border-color: #e2e8f0;
    background: #f1f5f9;
    color: #cbd5e1;
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
}

.soft-box,
.loading-box,
.warning-box {
    padding: 14px 16px;
    border-radius: 18px;
    font-size: 13px;
    font-weight: 750;
}

.soft-box {
    color: #64748b;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
}

.loading-box {
    display: flex;
    align-items: center;
    gap: 9px;
    color: #4f46e5;
    background: #eef2ff;
    border: 1px solid #c7d2fe;
}

.warning-box {
    margin-top: 12px;
    color: #b45309;
    background: #fffbeb;
    border: 1px solid #fde68a;
}

.spin {
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.summary-card {
    display: flex;
    gap: 14px;
    align-items: center;
    padding: 18px;
    border-radius: 24px;
    border: 1px solid #c7d2fe;
    background: linear-gradient(135deg, #eef2ff, #f0f9ff);
}

.summary-card span {
    padding: 8px 12px;
    border-radius: 999px;
    color: #4338ca;
    background: #ffffff;
    font-size: 11px;
    font-weight: 950;
    text-transform: uppercase;
    white-space: nowrap;
}

.summary-card strong {
    color: #0f172a;
    font-size: 15px;
}

.summary-card p {
    margin: 4px 0 0;
    color: #475569;
    font-size: 13px;
    font-weight: 750;
}

.drawer-footer {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    padding: 20px 24px;
    border-top: 1px solid #e2e8f0;
    background: #ffffff;
}

@media (max-width: 1280px) {
    .stats-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .filters {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .search-box {
        grid-column: span 2;
    }
}

@media (max-width: 760px) {
    .appointment-page {
        padding: 18px;
    }

    .hero-card,
    .panel-top {
        flex-direction: column;
        align-items: flex-start;
    }

    .hero-card {
        padding: 26px;
        border-radius: 26px;
    }

    .hero-card h1 {
        font-size: 30px;
    }

    .stats-grid,
    .filters,
    .form-grid,
    .drawer-footer,
    .slots-grid {
        grid-template-columns: 1fr;
    }

    .search-box {
        grid-column: span 1;
    }

    .pagination {
        flex-direction: column;
        align-items: flex-start;
    }

    .pagination-links {
        justify-content: flex-start;
    }

    .calendar-day {
        height: 38px;
        border-radius: 12px;
    }
}
</style>