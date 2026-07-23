<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import InputError from '@/components/InputError.vue';
import {
    Calendar, Clock, User, Stethoscope, Check, AlertCircle,
    Phone, Mail, HeartPulse, ArrowLeft, Shield, Sparkles, Loader2, X
} from 'lucide-vue-next';

interface Doctor { id: number; name: string; specialty?: string }
interface TimeSlot { start_time: string; end_time: string; available: boolean }

const props = defineProps<{ doctors: Doctor[]; success?: string; error?: string }>();

const form = useForm({
    doctor_id: '' as string, patient_name: '', patient_email: '', patient_phone: '',
    appointment_date: '', start_time: '', end_time: '', slot_duration: 0, notes: '',
});

const timeSlots = ref<TimeSlot[]>([]);
const loadingSlots = ref(false);
const selectedSlot = ref('');
const booked = ref(false);
const errorMsg = ref(props.error || '');

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
function fmtDate(d: string) { return new Date(d + 'T00:00:00').toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' }); }

function submit() {
    errorMsg.value = '';
    form.post('/booking', {
        onSuccess: () => { booked.value = true; },
        onError: (errors) => { const f = Object.values(errors)[0]; if (f) errorMsg.value = f as string; },
    });
}
function reset() { form.reset(); timeSlots.value = []; selectedSlot.value = ''; booked.value = false; errorMsg.value = ''; }
</script>

<template>
    <Head title="Book an Appointment" />

    <div class="min-h-screen bg-slate-50">
        <!-- Nav -->
        <nav class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/80 backdrop-blur-xl">
            <div class="mx-auto flex h-16 max-w-3xl items-center justify-between px-4 sm:px-6">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600">
                        <HeartPulse class="h-5 w-5 text-white" />
                    </div>
                    <span class="text-lg font-bold text-slate-900" style="font-family: 'Fraunces', serif">MediCare</span>
                </div>
                <Link href="/" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-900"><ArrowLeft class="h-4 w-4" />Home</Link>
            </div>
        </nav>

        <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 sm:py-12">

            <!-- Success -->
            <div v-if="booked || success" class="py-16 text-center">
                <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-xl shadow-emerald-200">
                    <Check class="h-10 w-10 text-white" />
                </div>
                <h2 class="mb-2 text-3xl font-bold text-slate-900" style="font-family: 'Fraunces', serif">You're All Set!</h2>
                <p class="mx-auto max-w-sm text-slate-500">Your appointment has been booked. We'll confirm it shortly.</p>
                <button @click="reset" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-slate-900 px-8 py-3 text-sm font-semibold text-white shadow-lg hover:bg-slate-800 active:scale-[0.98]">Book Another</button>
            </div>

            <template v-else>
                <!-- Hero -->
                <div class="mb-8 text-center">
                    <div class="mb-3 inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-blue-700">
                        <Sparkles class="h-3 w-3" /> Quick Booking
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900" style="font-family: 'Fraunces', serif">Book Your Appointment</h1>
                    <p class="mt-2 text-slate-500">Pick a doctor, choose a time, done.</p>
                </div>

                <!-- Progress -->
                <div class="mb-8 flex items-center justify-center">
                    <template v-for="(label, i) in ['Doctor', 'Schedule', 'Time', 'Confirm']" :key="i">
                        <div class="flex items-center gap-2">
                            <div :class="['flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold transition-all duration-300',
                                activeStep > i ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : activeStep === i ? 'border-2 border-blue-600 text-blue-600' : 'bg-slate-200 text-slate-400']">
                                <Check v-if="activeStep > i" class="h-4 w-4" /><span v-else>{{ i + 1 }}</span>
                            </div>
                            <span :class="['hidden text-xs font-medium sm:block', activeStep >= i ? 'text-slate-900' : 'text-slate-400']">{{ label }}</span>
                        </div>
                        <div v-if="i < 3" :class="['mx-2 h-px w-8 sm:w-12 transition-all duration-300', activeStep > i ? 'bg-blue-600' : 'bg-slate-200']"></div>
                    </template>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Error -->
                    <Transition name="slide">
                        <div v-if="errorMsg" class="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
                            <AlertCircle class="h-5 w-5 shrink-0 text-red-500" />
                            <p class="text-sm font-medium text-red-700">{{ errorMsg }}</p>
                            <button @click="errorMsg = ''" class="ml-auto text-red-400 hover:text-red-600"><X class="h-4 w-4" /></button>
                        </div>
                    </Transition>

                    <!-- Step 1: Doctor -->
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-4 flex items-center gap-2.5">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50"><Stethoscope class="h-4 w-4 text-blue-600" /></div>
                            <div><h2 class="text-sm font-semibold text-slate-900">Select Doctor</h2><p class="text-xs text-slate-400">Choose a specialist</p></div>
                        </div>
                        <div class="space-y-2">
                            <button v-for="doc in doctors" :key="doc.id" type="button" @click="form.doctor_id = String(doc.id)"
                                :class="['flex w-full items-center gap-3 rounded-xl border-2 p-4 text-left transition-all duration-200',
                                    form.doctor_id === String(doc.id) ? 'border-blue-600 bg-blue-50/60 shadow-sm' : 'border-slate-200 hover:border-slate-300']">
                                <div :class="['flex h-10 w-10 shrink-0 items-center justify-center rounded-full transition-colors',
                                    form.doctor_id === String(doc.id) ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500']">
                                    <Stethoscope class="h-4 w-4" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p :class="['text-sm font-semibold truncate', form.doctor_id === String(doc.id) ? 'text-blue-700' : 'text-slate-900']">{{ doc.name }}</p>
                                    <p v-if="doc.specialty" class="text-xs text-slate-400 truncate">{{ doc.specialty }}</p>
                                </div>
                                <div v-if="form.doctor_id === String(doc.id)" class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white"><Check class="h-3 w-3" /></div>
                            </button>
                        </div>
                        <InputError :message="form.errors.doctor_id" />
                    </div>

                    <!-- Step 2: Date -->
                    <Transition name="slide">
                        <div v-if="form.doctor_id" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="mb-4 flex items-center gap-2.5">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50"><Calendar class="h-4 w-4 text-blue-600" /></div>
                                <div><h2 class="text-sm font-semibold text-slate-900">Pick a Date</h2><p class="text-xs text-slate-400">{{ selectedDoctor?.name }}</p></div>
                            </div>
                            <input v-model="form.appointment_date" type="date" :min="today" :max="maxDate" class="w-full max-w-xs rounded-xl border border-slate-200 py-2.5 px-4 text-sm text-slate-900 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10" />
                            <InputError :message="form.errors.appointment_date" />
                        </div>
                    </Transition>

                    <!-- Step 3: Time -->
                    <Transition name="slide">
                        <div v-if="form.doctor_id && form.appointment_date" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="mb-4 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50"><Clock class="h-4 w-4 text-blue-600" /></div>
                                    <div><h2 class="text-sm font-semibold text-slate-900">Select Time</h2><p class="text-xs text-slate-400">{{ fmtDate(form.appointment_date) }}</p></div>
                                </div>
                                <span v-if="availableCount" class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">{{ availableCount }} open</span>
                            </div>

                            <div v-if="loadingSlots" class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-5">
                                <div v-for="n in 10" :key="n" class="h-14 animate-pulse rounded-xl bg-slate-100"></div>
                            </div>
                            <div v-else-if="timeSlots.length === 0" class="py-10 text-center">
                                <AlertCircle class="mx-auto mb-2 h-10 w-10 text-amber-400" />
                                <p class="font-medium text-slate-500">No slots available</p>
                                <p class="text-sm text-slate-400">Try another date</p>
                            </div>
                            <div v-else>
                                <div class="mb-3 flex items-center gap-4 text-[11px] text-slate-400">
                                    <span class="flex items-center gap-1"><span class="h-2.5 w-4 rounded bg-emerald-100 border border-emerald-300"></span> Open</span>
                                    <span class="flex items-center gap-1"><span class="h-2.5 w-4 rounded bg-slate-100 border border-slate-200"></span> Taken</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-5">
                                    <button v-for="slot in timeSlots" :key="slot.start_time" type="button" :disabled="!slot.available" @click="pickSlot(slot)"
                                        :class="['relative flex flex-col items-center rounded-xl border-2 px-1 py-2.5 transition-all duration-150 sm:px-2',
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
                            </div>
                            <InputError :message="form.errors.start_time || form.errors.end_time" />
                        </div>
                    </Transition>

                    <!-- Step 4: Patient -->
                    <Transition name="slide">
                        <div v-if="selectedSlot" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="mb-5 flex items-center gap-2.5">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50"><User class="h-4 w-4 text-blue-600" /></div>
                                <div><h2 class="text-sm font-semibold text-slate-900">Your Details</h2><p class="text-xs text-slate-400">So we can confirm your booking</p></div>
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
                                        <input v-model="form.patient_email" type="email" placeholder="you@email.com" class="w-full rounded-xl border border-slate-200 py-2.5 pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10" /></div>
                                    <InputError :message="form.errors.patient_email" />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Notes</label>
                                    <textarea v-model="form.notes" rows="3" placeholder="Symptoms or reason for visit..." class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10"></textarea>
                                </div>
                            </div>
                        </div>
                    </Transition>

                    <!-- Summary -->
                    <Transition name="slide">
                        <div v-if="selectedSlot" class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-5">
                            <div class="flex items-center gap-2 mb-4"><Shield class="h-4 w-4 text-blue-600" /><span class="text-[11px] font-bold uppercase tracking-widest text-blue-600">Summary</span></div>
                            <div class="grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                                <div><p class="text-[11px] font-medium text-blue-500">Doctor</p><p class="font-semibold text-slate-900">{{ selectedDoctor?.name }}</p></div>
                                <div><p class="text-[11px] font-medium text-blue-500">Date</p><p class="font-semibold text-slate-900">{{ fmtDate(form.appointment_date) }}</p></div>
                                <div><p class="text-[11px] font-medium text-blue-500">Time</p><p class="font-semibold text-slate-900">{{ fmt(form.start_time) }} – {{ fmt(form.end_time) }}</p></div>
                                <div><p class="text-[11px] font-medium text-blue-500">Duration</p><p class="font-semibold text-slate-900">{{ form.slot_duration }} min</p></div>
                            </div>
                            <div class="mt-5 flex justify-center">
                                <button type="submit" :disabled="form.processing"
                                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-10 py-3.5 text-sm font-bold text-white shadow-lg transition hover:bg-slate-800 active:scale-[0.98] disabled:opacity-50">
                                    <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" /><Check v-else class="h-4 w-4" />
                                    {{ form.processing ? 'Booking...' : 'Confirm Booking' }}
                                </button>
                            </div>
                        </div>
                    </Transition>
                </form>
            </template>
        </div>

        <footer class="border-t border-slate-200 py-6 text-center"><p class="text-xs text-slate-400">&copy; {{ new Date().getFullYear() }} MediCare</p></footer>
    </div>
</template>

<style scoped>
.slide-enter-active { transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-leave-active { transition: all 0.15s ease; }
.slide-enter-from { opacity: 0; transform: translateY(12px); }
.slide-leave-to { opacity: 0; transform: translateY(-8px); }
</style>
