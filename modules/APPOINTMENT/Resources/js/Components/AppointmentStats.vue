<script setup lang="ts">
import { CalendarDays, Clock, CheckCircle, XCircle, UserX, Calendar } from 'lucide-vue-next';

interface Stats { total: number; pending: number; confirmed: number; completed: number; cancelled: number; no_show: number }
defineProps<{ stats: Stats }>();

const items = [
    { key: 'total',     label: 'Total',     icon: CalendarDays, color: 'text-blue-600',    bg: 'bg-blue-50' },
    { key: 'pending',   label: 'Pending',   icon: Clock,        color: 'text-amber-600',   bg: 'bg-amber-50' },
    { key: 'confirmed', label: 'Confirmed', icon: CheckCircle,  color: 'text-blue-600',    bg: 'bg-blue-50' },
    { key: 'completed', label: 'Completed', icon: Calendar,     color: 'text-emerald-600', bg: 'bg-emerald-50' },
    { key: 'cancelled', label: 'Cancelled', icon: XCircle,      color: 'text-red-600',     bg: 'bg-red-50' },
    { key: 'no_show',   label: 'No Show',   icon: UserX,        color: 'text-slate-500',   bg: 'bg-slate-50' },
];
</script>

<template>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        <div v-for="c in items" :key="c.key" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400">{{ c.label }}</span>
                <div :class="['flex h-7 w-7 items-center justify-center rounded-lg', c.bg]">
                    <component :is="c.icon" :class="['h-3.5 w-3.5', c.color]" />
                </div>
            </div>
            <p :class="['mt-2 text-2xl font-bold tabular-nums', c.color]">{{ stats[c.key as keyof Stats] ?? 0 }}</p>
        </div>
    </div>
</template>
