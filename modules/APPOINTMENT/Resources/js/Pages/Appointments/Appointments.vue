<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Plus, Search, Filter } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs = [{ title: 'Appointments', href: '/appointments' }];

const appointments = ref([
  { id: 1, patient: "Emma Thompson", doctor: "Dr. Sarah Patel", date: "2026-06-24", time: "09:30", type: "Follow-up", status: "confirmed", duration: "30m" },
  { id: 2, patient: "Liam Chen", doctor: "Dr. Michael Torres", date: "2026-06-24", time: "10:00", type: "Consultation", status: "confirmed", duration: "45m" },
  { id: 3, patient: "Sophia Rivera", doctor: "Dr. James Kim", date: "2026-06-24", time: "10:45", type: "New Patient", status: "pending", duration: "60m" },
  { id: 4, patient: "Noah Patel", doctor: "Dr. Emily Wong", date: "2026-06-24", time: "11:30", type: "Follow-up", status: "confirmed", duration: "30m" },
]);

const statusColors: Record<string, string> = {
  confirmed: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400',
  pending: 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400',
  cancelled: 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-400',
};
</script>

<template>
  <Head title="Appointments" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-4xl font-semibold tracking-tighter">Appointments</h1>
        <p class="text-slate-600 dark:text-slate-400">Manage all scheduled appointments</p>
      </div>
      <button class="flex items-center gap-2 px-5 py-3 bg-emerald-600 text-white rounded-2xl text-sm font-medium">
        <Plus class="w-4 h-4" /> New Appointment
      </button>
    </div>

    <div class="flex flex-col md:flex-row gap-3 mb-6">
      <div class="relative flex-1 max-w-sm">
        <Search class="absolute left-4 top-3.5 w-4 h-4 text-slate-400" />
        <input type="text" placeholder="Search patients or doctors..." class="w-full pl-11 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm" />
      </div>
      <button class="flex items-center justify-center gap-2 px-5 py-3 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-medium">
        <Filter class="w-4 h-4" /> Filter
      </button>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden">
      <table class="w-full">
        <thead>
          <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 text-left text-xs font-medium text-slate-500">
            <th class="px-6 py-4">Patient</th>
            <th class="px-6 py-4">Doctor</th>
            <th class="px-6 py-4">Date &amp; Time</th>
            <th class="px-6 py-4">Type</th>
            <th class="px-6 py-4">Duration</th>
            <th class="px-6 py-4">Status</th>
            <th class="px-6 py-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
          <tr v-for="appt in appointments" :key="appt.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
            <td class="px-6 py-5 font-medium">{{ appt.patient }}</td>
            <td class="px-6 py-5 text-slate-600 dark:text-slate-400">{{ appt.doctor }}</td>
            <td class="px-6 py-5">
              <div class="font-mono">{{ appt.date }}</div>
              <div class="text-xs text-emerald-600">{{ appt.time }}</div>
            </td>
            <td class="px-6 py-5">{{ appt.type }}</td>
            <td class="px-6 py-5 text-slate-500">{{ appt.duration }}</td>
            <td class="px-6 py-5">
              <span class="inline-block px-3 py-px text-xs font-semibold rounded-full" :class="statusColors[appt.status]">
                {{ appt.status }}
              </span>
            </td>
            <td class="px-6 py-5 text-right">
              <button class="text-emerald-600 hover:underline text-sm">View details</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>
