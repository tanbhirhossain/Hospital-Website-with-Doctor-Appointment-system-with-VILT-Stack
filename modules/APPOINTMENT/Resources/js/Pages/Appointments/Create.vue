<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import InputError from '@/components/InputError.vue';
import {
    ArrowLeft, Save, Calendar, Clock, User, Stethoscope, Check,
    AlertCircle, Phone, Mail, Loader2
} from 'lucide-vue-next';

interface Doctor { id: number; name: string; specialty?: string; image?: string }
interface TimeSlot { start_time: string; end_time: string; available: boolean }

const props = defineProps<{ doctors: Doctor[]; statuses: Record<string, string> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Appointments', href: '/admin/appointments' },
    { title: 'New Appointment', href: '/admin/appointments/create' },
];

const form = useForm({
    doctor_id: '' as string, patient_name: '', patient_email: '', patient_phone: '',
    appointment_date: '', start_time: '', end_time: '', slot_duration: 0, notes: '',
});

const timeSlots = ref<TimeSlot[]>([]);
const loadingSlots = ref(false);
const selectedSlot = ref('');
const today = new Date().toISOString().split('T')[0];
const maxDate = new Date(Date.now() + 90 * 86400000).toISOString().split('T')[0];

const activeStep = computed(() => {
    if (form.doctor_id && form.appointment_date && selectedSlot.value && form.patient_name && form.patient_phone) return 4;
    if (form.doctor_id && form.appointment_date && selectedSlot.value) return 3;
    if (form.doctor_id && form.appointment_date) return 2;
    if (form.doctor_id) return 1;
    return 0;
});

const availableCount = computed(() => timeSlots.value.filter(s => s.available).length);
const selectedDoctor = computed(() => props.doctors.find(d => String(d.id) === form.doctor_id));

watch(() => form.doctor_id, () => { timeSlots.value = []; selectedSlot.value = ''; form.start_time = ''; form.end_time = ''; form.appointment_date = ''; });
watch(() => form.appointment_date, v => { if (v && form.doctor_id) fetchSlots(); else { timeSlots.value = []; selectedSlot.value = ''; } });

async function fetchSlots() {
    loadingSlots.value = true; timeSlots.value = []; selectedSlot.value = ''; form.start_time = ''; form.end_time = '';
    try {
        const r = await fetch(`/booking/available-slots?doctor_id=${form.doctor_id}&date=${form.appointment_date}`, { headers: { Accept: 'application/json' } });
        const d = await r.json();
        if (d.success) timeSlots.value = d.slots;
    } catch (e) { console.error(e); }
    finally { loadingSlots.value = false; }
}

function pickSlot(s: TimeSlot) {
    if (!s.available) return;
    selectedSlot.value = s.start_time; form.start_time = s.start_time; form.end_time = s.end_time;
    const [sh, sm] = s.start_time.split(':').map(Number); const [eh, em] = s.end_time.split(':').map(Number);
    form.slot_duration = (eh * 60 + em) - (sh * 60 + sm);
}

function fmt(t: string) { const [h, m] = t.split(':').map(Number); return `${h % 12 || 12}:${String(m).padStart(2, '0')} ${h >= 12 ? 'PM' : 'AM'}`; }

function submit() { form.post('/admin/appointments'); }
</script>

<template>
    <Head title="New Appointment" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6 p-4 sm:p-6 lg:p-8">

            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/admin/appointments" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900" style="font-family: 'Fraunces', serif">New Appointment</h1>
                    <p class="text-sm text-slate-500">Schedule a doctor appointment</p>
                </div>
            </div>

            <!-- Steps -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <template v-for="(label, i) in ['Doctor', 'Date & Time', 'Details', 'Confirm']" :key="i">
                        <div class="flex items-center gap-2.5">
                            <div :class="['flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold transition-all duration-300',
                                activeStep > i ? 'bg-blue-600 text-white' : activeStep === i ? 'border-2 border-blue-600 text-blue-600 bg-blue-50' : 'bg-slate-100 text-slate-400']">
                                <Check v-if="activeStep > i" class="h-3.5 w-3.5" /><span v-else>{{ i + 1 }}</span>
                            </div>
                            <span :class="['hidden text-sm font-medium sm:block', activeStep >= i ? 'text-slate-900' : 'text-slate-400']">{{ label }}</span>
                        </div>
                        <div v-if="i < 3" :class="['mx-2 h-px flex-1 transition-all duration-300', activeStep > i ? 'bg-blue-600' : 'bg-slate-200']"></div>
                    </template>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <!-- Doctor Selection -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50"><Stethoscope class="h-4 w-4 text-blue-600" /></div>
                        <div><h2 class="text-sm font-semibold text-slate-900">Select Doctor</h2><p class="text-xs text-slate-400">Choose a specialist</p></div>
                    </div>
                    <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                        <button v-for="doc in doctors" :key="doc.id" type="button" @click="form.doctor_id = String(doc.id)"
                            :class="['flex items-center gap-3 rounded-xl border-2 p-4 text-left transition-all duration-200',
                                form.doctor_id === String(doc.id) ? 'border-blue-600 bg-blue-50/60 shadow-sm' : 'border-slate-200 hover:border-slate-300 hover:shadow-sm']">
                            <div :class="['flex h-11 w-11 shrink-0 items-center justify-center rounded-full transition-colors',
                                form.doctor_id === String(doc.id) ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500']">
                                <Stethoscope class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p :class="['text-sm font-semibold truncate', form.doctor_id === String(doc.id) ? 'text-blue-700' : 'text-slate-900']">{{ doc.name }}</p>
                                <p v-if="doc.specialty" class="text-xs text-slate-400 truncate">{{ doc.specialty }}</p>
                            </div>
                            <div v-if="form.doctor_id === String(doc.id)" class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white"><Check class="h-3.5 w-3.5" /></div>
                        </button>
                    </div>
                    <InputError :message="form.errors.doctor_id" />
                </div>

                <!-- Date -->
                <Transition name="slide">
                    <div v-if="form.doctor_id" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-4 flex items-center gap-2.5">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50"><Calendar class="h-4 w-4 text-blue-600" /></div>
                            <div><h2 class="text-sm font-semibold text-slate-900">Pick a Date</h2><p class="text-xs text-slate-400">{{ selectedDoctor?.name }}</p></div>
                        </div>
                        <input v-model="form.appointment_date" type="date" :min="today" :max="maxDate"
                            class="w-full max-w-xs rounded-xl border border-slate-200 py-2.5 px-4 text-sm text-slate-900 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10" />
                        <InputError :message="form.errors.appointment_date" />
                    </div>
                </Transition>

                <!-- Time Slots -->
                <Transition name="slide">
                    <div v-if="form.doctor_id && form.appointment_date" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50"><Clock class="h-4 w-4 text-blue-600" /></div>
                                <div><h2 class="text-sm font-semibold text-slate-900">Choose Time</h2><p class="text-xs text-slate-400">{{ availableCount }} slots available</p></div>
                            </div>
                            <div v-if="timeSlots.length" class="hidden items-center gap-4 text-[11px] text-slate-400 sm:flex">
                                <span class="flex items-center gap-1"><span class="h-2.5 w-4 rounded bg-emerald-100 border border-emerald-300"></span> Open</span>
                                <span class="flex items-center gap-1"><span class="h-2.5 w-4 rounded bg-slate-100 border border-slate-200"></span> Taken</span>
                            </div>
                        </div>

                        <div v-if="loadingSlots" class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-5">
                            <div v-for="n in 10" :key="n" class="h-14 animate-pulse rounded-xl bg-slate-100"></div>
                        </div>

                        <div v-else-if="timeSlots.length === 0" class="py-10 text-center">
                            <AlertCircle class="mx-auto mb-2 h-10 w-10 text-amber-400" />
                            <p class="font-medium text-slate-600">No slots available</p>
                            <p class="text-sm text-slate-400">Try a different date</p>
                        </div>

                        <div v-else class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6">
                            <button v-for="slot in timeSlots" :key="slot.start_time" type="button" :disabled="!slot.available" @click="pickSlot(slot)"
                                :class="['relative flex flex-col items-center rounded-xl border-2 px-1 py-2.5 text-center transition-all duration-150 sm:px-2',
                                    selectedSlot === slot.start_time
                                        ? 'border-blue-600 bg-blue-600 text-white shadow-lg shadow-blue-600/20'
                                        : slot.available
                                            ? 'border-emerald-200 bg-white text-emerald-700 hover:border-emerald-400 hover:shadow-md cursor-pointer'
                                            : 'border-slate-100 bg-slate-50/50 text-slate-300 cursor-not-allowed']">
                                <span class="text-xs font-bold sm:text-sm">{{ fmt(slot.start_time) }}</span>
                                <span :class="['text-[10px]', selectedSlot === slot.start_time ? 'text-blue-200' : 'opacity-40']">{{ fmt(slot.end_time) }}</span>
                                <div v-if="selectedSlot === slot.start_time" class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-white text-blue-600 shadow"><Check class="h-3 w-3" /></div>
                            </button>
                        </div>
                        <InputError :message="form.errors.start_time || form.errors.end_time" />
                    </div>
                </Transition>

                <!-- Patient Info -->
                <Transition name="slide">
                    <div v-if="selectedSlot" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-5 flex items-center gap-2.5">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50"><User class="h-4 w-4 text-blue-600" /></div>
                            <div><h2 class="text-sm font-semibold text-slate-900">Patient Details</h2><p class="text-xs text-slate-400">Who is this appointment for?</p></div>
                        </div>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Full Name <span class="text-red-500">*</span></label>
                                    <div class="relative"><User class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                        <input v-model="form.patient_name" type="text" placeholder="John Doe" class="w-full rounded-xl border border-slate-200 py-2.5 pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10" /></div>
                                    <InputError :message="form.errors.patient_name" />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Phone <span class="text-red-500">*</span></label>
                                    <div class="relative"><Phone class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                        <input v-model="form.patient_phone" type="text" placeholder="+880 1XXX-XXXXXX" class="w-full rounded-xl border border-slate-200 py-2.5 pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10" /></div>
                                    <InputError :message="form.errors.patient_phone" />
                                </div>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Email</label>
                                <div class="relative"><Mail class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                    <input v-model="form.patient_email" type="email" placeholder="patient@email.com" class="w-full rounded-xl border border-slate-200 py-2.5 pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10" /></div>
                                <InputError :message="form.errors.patient_email" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Notes</label>
                                <textarea v-model="form.notes" rows="3" placeholder="Symptoms, special requirements..."
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10"></textarea>
                                <InputError :message="form.errors.notes" />
                            </div>
                        </div>
                    </div>
                </Transition>

                <!-- Summary -->
                <Transition name="slide">
                    <div v-if="selectedSlot" class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-5">
                        <div class="mb-4 flex items-center gap-2"><Clock class="h-4 w-4 text-blue-600" /><span class="text-[11px] font-bold uppercase tracking-widest text-blue-600">Summary</span></div>
                        <div class="grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                            <div><p class="text-[11px] font-medium text-blue-500">Doctor</p><p class="font-semibold text-slate-900">{{ selectedDoctor?.name }}</p></div>
                            <div><p class="text-[11px] font-medium text-blue-500">Date</p><p class="font-semibold text-slate-900">{{ form.appointment_date }}</p></div>
                            <div><p class="text-[11px] font-medium text-blue-500">Time</p><p class="font-semibold text-slate-900">{{ fmt(form.start_time) }} – {{ fmt(form.end_time) }}</p></div>
                            <div><p class="text-[11px] font-medium text-blue-500">Duration</p><p class="font-semibold text-slate-900">{{ form.slot_duration }} min</p></div>
                        </div>
                        <div class="mt-5 flex items-center justify-end gap-3">
                            <Link href="/admin/appointments" class="rounded-xl px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</Link>
                            <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 active:scale-[0.98] disabled:opacity-50">
                                <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" /><Save v-else class="h-4 w-4" />
                                {{ form.processing ? 'Booking...' : 'Book Appointment' }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.slide-enter-active { transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-leave-active { transition: all 0.15s ease; }
.slide-enter-from { opacity: 0; transform: translateY(12px); }
.slide-leave-to { opacity: 0; transform: translateY(-8px); }
</style>
