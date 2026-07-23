<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { ArrowLeft, Save, Calendar, Clock, User, Phone, Mail, Loader2 } from 'lucide-vue-next';

interface Doctor { id: number; name: string; specialty?: string }
interface Appointment {
    id: number; doctor_id: number; patient_name: string; patient_email: string | null;
    patient_phone: string; appointment_date: string; start_time: string; end_time: string;
    slot_duration: number; status: string; notes: string | null; cancellation_reason: string | null; doctor: Doctor;
}

const props = defineProps<{ appointment: Appointment; doctors: Doctor[]; statuses: Record<string, string> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Appointments', href: '/admin/appointments' },
    { title: `Edit #${props.appointment.id}`, href: `/admin/appointments/${props.appointment.id}/edit` },
];

const form = useForm({
    patient_name: props.appointment.patient_name, patient_email: props.appointment.patient_email || '',
    patient_phone: props.appointment.patient_phone, appointment_date: props.appointment.appointment_date,
    start_time: props.appointment.start_time, end_time: props.appointment.end_time,
    slot_duration: props.appointment.slot_duration, status: props.appointment.status,
    notes: props.appointment.notes || '', cancellation_reason: props.appointment.cancellation_reason || '',
});

const sCfg: Record<string, { dot: string; bg: string; text: string; border: string }> = {
    pending:   { dot: 'bg-amber-500',   bg: 'bg-amber-50',   text: 'text-amber-700', border: 'border-amber-200' },
    confirmed: { dot: 'bg-blue-500',    bg: 'bg-blue-50',    text: 'text-blue-700', border: 'border-blue-200' },
    completed: { dot: 'bg-emerald-500', bg: 'bg-emerald-50', text: 'text-emerald-700', border: 'border-emerald-200' },
    cancelled: { dot: 'bg-red-500',     bg: 'bg-red-50',     text: 'text-red-700', border: 'border-red-200' },
    no_show:   { dot: 'bg-slate-400',   bg: 'bg-slate-50',   text: 'text-slate-600', border: 'border-slate-200' },
};

function submit() { form.put(`/admin/appointments/${props.appointment.id}`); }
</script>

<template>
    <Head title="Edit Appointment" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6 p-4 sm:p-6 lg:p-8">
            <div class="flex items-center gap-4">
                <Link href="/admin/appointments" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-50">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900" style="font-family: 'Fraunces', serif">Edit #{{ appointment.id }}</h1>
                    <span :class="['inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-[11px] font-semibold', sCfg[appointment.status]?.bg, sCfg[appointment.status]?.text, sCfg[appointment.status]?.border]">
                        <span :class="['h-1.5 w-1.5 rounded-full', sCfg[appointment.status]?.dot]"></span>
                        {{ statuses[appointment.status] }}
                    </span>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-5 flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50"><Calendar class="h-4 w-4 text-blue-600" /></div>
                        <h2 class="text-sm font-semibold text-slate-900">Schedule</h2>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Date</label>
                            <input v-model="form.appointment_date" type="date" class="w-full rounded-xl border border-slate-200 py-2.5 px-3 text-sm text-slate-900 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10" />
                            <InputError :message="form.errors.appointment_date" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Start</label>
                            <input v-model="form.start_time" type="time" class="w-full rounded-xl border border-slate-200 py-2.5 px-3 text-sm text-slate-900 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10" />
                            <InputError :message="form.errors.start_time" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">End</label>
                            <input v-model="form.end_time" type="time" class="w-full rounded-xl border border-slate-200 py-2.5 px-3 text-sm text-slate-900 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10" />
                            <InputError :message="form.errors.end_time" />
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Status</label>
                            <select v-model="form.status" class="w-full rounded-xl border border-slate-200 py-2.5 px-3 text-sm text-slate-900 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10">
                                <option v-for="(label, key) in statuses" :key="key" :value="key">{{ label }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Duration</label>
                            <input v-model="form.slot_duration" type="number" min="5" max="480" class="w-full rounded-xl border border-slate-200 py-2.5 px-3 text-sm text-slate-900 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10" />
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-5 flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50"><User class="h-4 w-4 text-blue-600" /></div>
                        <h2 class="text-sm font-semibold text-slate-900">Patient</h2>
                    </div>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Name <span class="text-red-500">*</span></label>
                                <div class="relative"><User class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                    <input v-model="form.patient_name" type="text" class="w-full rounded-xl border border-slate-200 py-2.5 pl-11 pr-4 text-sm text-slate-900 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10" /></div>
                                <InputError :message="form.errors.patient_name" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Phone <span class="text-red-500">*</span></label>
                                <div class="relative"><Phone class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                    <input v-model="form.patient_phone" type="text" class="w-full rounded-xl border border-slate-200 py-2.5 pl-11 pr-4 text-sm text-slate-900 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10" /></div>
                                <InputError :message="form.errors.patient_phone" />
                            </div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Email</label>
                            <div class="relative"><Mail class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                <input v-model="form.patient_email" type="email" class="w-full rounded-xl border border-slate-200 py-2.5 pl-11 pr-4 text-sm text-slate-900 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10" /></div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Notes</label>
                            <textarea v-model="form.notes" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 focus:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10"></textarea>
                        </div>
                        <div v-if="form.status === 'cancelled'">
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-red-500">Cancellation Reason</label>
                            <textarea v-model="form.cancellation_reason" rows="2" class="w-full rounded-xl border border-red-200 bg-red-50/50 px-4 py-3 text-sm text-slate-900 focus:border-red-400 focus:outline-none focus:ring-4 focus:ring-red-500/10"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <Link href="/admin/appointments" class="rounded-xl px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</Link>
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 active:scale-[0.98] disabled:opacity-50">
                        <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" /><Save v-else class="h-4 w-4" />
                        {{ form.processing ? 'Saving...' : 'Update' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
