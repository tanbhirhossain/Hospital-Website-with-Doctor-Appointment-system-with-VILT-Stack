<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    ArrowLeft, Edit, CheckCircle, XCircle, Clock, User, Stethoscope,
    Calendar, Phone, Mail, FileText, AlertTriangle, CalendarOff, Sparkles
} from 'lucide-vue-next';

interface Doctor { id: number; name: string; specialty?: string }
interface Appointment {
    id: number; doctor_id: number; patient_name: string; patient_email: string | null;
    patient_phone: string; appointment_date: string; start_time: string; end_time: string;
    slot_duration: number; status: string; notes: string | null; cancellation_reason: string | null;
    doctor: Doctor; created_at: string; updated_at: string;
}

const props = defineProps<{ appointment: Appointment; statuses: Record<string, string> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Appointments', href: '/admin/appointments' },
    { title: `#${props.appointment.id}`, href: `/admin/appointments/${props.appointment.id}` },
];

const showModal = ref(false);
const cancelReason = ref('');

const sCfg: Record<string, { dot: string; bg: string; text: string; border: string }> = {
    pending:   { dot: 'bg-amber-500',   bg: 'bg-amber-50',   text: 'text-amber-700', border: 'border-amber-200' },
    confirmed: { dot: 'bg-blue-500',    bg: 'bg-blue-50',    text: 'text-blue-700', border: 'border-blue-200' },
    completed: { dot: 'bg-emerald-500', bg: 'bg-emerald-50', text: 'text-emerald-700', border: 'border-emerald-200' },
    cancelled: { dot: 'bg-red-500',     bg: 'bg-red-50',     text: 'text-red-700', border: 'border-red-200' },
    no_show:   { dot: 'bg-slate-400',   bg: 'bg-slate-50',   text: 'text-slate-600', border: 'border-slate-200' },
};

function fmt(t: string) { if (!t) return ''; const [h, m] = t.split(':').map(Number); return `${h % 12 || 12}:${String(m).padStart(2, '0')} ${h >= 12 ? 'PM' : 'AM'}`; }
function fmtDate(d: string) { return new Date(d + 'T00:00:00').toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }); }
function initials(n: string) { return n.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2); }

function doCancel() {
    router.post(`/admin/appointments/${props.appointment.id}/cancel`, { cancellation_reason: cancelReason.value }, { preserveScroll: true, onSuccess: () => { showModal.value = false; } });
}

const st = sCfg[props.appointment.status] || sCfg.pending;
</script>

<template>
    <Head :title="`Appointment #${appointment.id}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6 p-4 sm:p-6 lg:p-8">

            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-4">
                    <Link href="/admin/appointments" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-50">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900" style="font-family: 'Fraunces', serif">Appointment #{{ appointment.id }}</h1>
                            <span :class="['inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-[11px] font-semibold', st.bg, st.text, st.border]">
                                <span :class="['h-1.5 w-1.5 rounded-full', st.dot]"></span>
                                {{ statuses[appointment.status] }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-slate-500">Created {{ appointment.created_at }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link :href="`/admin/appointments/${appointment.id}/edit`" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <Edit class="h-3.5 w-3.5" /> Edit
                    </Link>
                    <button v-if="appointment.status === 'pending'" @click="router.post(`/admin/appointments/${appointment.id}/confirm`, {}, { preserveScroll: true })"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 active:scale-[0.98]">
                        <CheckCircle class="h-3.5 w-3.5" /> Confirm
                    </button>
                    <button v-if="appointment.status === 'confirmed'" @click="router.post(`/admin/appointments/${appointment.id}/complete`, {}, { preserveScroll: true })"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 active:scale-[0.98]">
                        <CheckCircle class="h-3.5 w-3.5" /> Complete
                    </button>
                    <button v-if="appointment.status === 'confirmed'" @click="router.post(`/admin/appointments/${appointment.id}/no-show`, {}, { preserveScroll: true })"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        <CalendarOff class="h-3.5 w-3.5" /> No Show
                    </button>
                    <button v-if="appointment.status === 'pending' || appointment.status === 'confirmed'" @click="showModal = true"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                        <XCircle class="h-3.5 w-3.5" /> Cancel
                    </button>
                </div>
            </div>

            <!-- Doctor & Schedule -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-5"><Sparkles class="h-4 w-4 text-blue-600" /><span class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Appointment</span></div>
                <div class="flex items-center gap-4 mb-5">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-600/20">
                        <Stethoscope class="h-6 w-6 text-white" />
                    </div>
                    <div><h3 class="text-lg font-bold text-slate-900" style="font-family: 'Fraunces', serif">{{ appointment.doctor?.name }}</h3><p v-if="appointment.doctor?.specialty" class="text-sm text-slate-500">{{ appointment.doctor.specialty }}</p></div>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div class="rounded-xl bg-slate-50 px-4 py-3"><p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Date</p><p class="mt-1 text-sm font-semibold text-slate-900">{{ fmtDate(appointment.appointment_date) }}</p></div>
                    <div class="rounded-xl bg-slate-50 px-4 py-3"><p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Time</p><p class="mt-1 text-sm font-semibold text-slate-900">{{ fmt(appointment.start_time) }} – {{ fmt(appointment.end_time) }}</p></div>
                    <div class="rounded-xl bg-slate-50 px-4 py-3"><p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Duration</p><p class="mt-1 text-sm font-semibold text-slate-900">{{ appointment.slot_duration }} min</p></div>
                </div>
            </div>

            <!-- Patient -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-5"><User class="h-4 w-4 text-blue-600" /><span class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Patient</span></div>
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-base font-bold text-white">{{ initials(appointment.patient_name) }}</div>
                    <div>
                        <h3 class="font-semibold text-slate-900">{{ appointment.patient_name }}</h3>
                        <div class="mt-1 flex flex-wrap items-center gap-3 text-sm text-slate-500">
                            <span class="flex items-center gap-1"><Phone class="h-3 w-3" />{{ appointment.patient_phone }}</span>
                            <span v-if="appointment.patient_email" class="flex items-center gap-1"><Mail class="h-3 w-3" />{{ appointment.patient_email }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div v-if="appointment.notes || appointment.cancellation_reason" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4"><FileText class="h-4 w-4 text-blue-600" /><span class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Notes</span></div>
                <p v-if="appointment.notes" class="text-sm leading-relaxed text-slate-700">{{ appointment.notes }}</p>
                <div v-if="appointment.cancellation_reason" class="mt-3 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4">
                    <AlertTriangle class="mt-0.5 h-4 w-4 shrink-0 text-red-500" />
                    <div><p class="text-[11px] font-bold uppercase tracking-widest text-red-500">Cancellation Reason</p><p class="mt-1 text-sm text-red-700">{{ appointment.cancellation_reason }}</p></div>
                </div>
            </div>
        </div>

        <!-- Cancel Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showModal = false"></div>
                    <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl">
                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-100"><AlertTriangle class="h-5 w-5 text-red-600" /></div>
                                <div><h3 class="text-base font-semibold text-slate-900" style="font-family: 'Fraunces', serif">Cancel Appointment</h3><p class="mt-1 text-sm text-slate-500">Are you sure?</p></div>
                            </div>
                            <div class="mt-5"><textarea v-model="cancelReason" rows="3" placeholder="Reason (optional)..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10"></textarea></div>
                        </div>
                        <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-4">
                            <button @click="showModal = false" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900">Keep</button>
                            <button @click="doCancel" class="rounded-xl bg-red-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 active:scale-[0.98]">Cancel Appointment</button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>

<style scoped>
.modal-enter-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.modal-leave-active { transition: all 0.15s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-from > :last-child, .modal-leave-to > :last-child { transform: scale(0.95) translateY(10px); }
</style>
