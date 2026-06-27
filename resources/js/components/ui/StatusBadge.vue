<script setup>
defineProps({ status: String })

const map = {
    pending:   'bg-amber-500/10 text-amber-300 ring-amber-500/20',
    confirmed: 'bg-emerald-500/10 text-emerald-300 ring-emerald-500/20',
    cancelled: 'bg-rose-500/10 text-rose-300 ring-rose-500/20',
    completed: 'bg-sky-500/10 text-sky-300 ring-sky-500/20',
    no_show:   'bg-slate-500/10 text-slate-400 ring-slate-500/20',
    active:    'bg-emerald-500/10 text-emerald-300 ring-emerald-500/20',
    inactive:  'bg-slate-500/10 text-slate-400 ring-slate-500/20',
}
const label = {
    pending: 'Pending', confirmed: 'Confirmed', cancelled: 'Cancelled',
    completed: 'Completed', no_show: 'No Show',
    active: 'Active', inactive: 'Inactive',
}
</script>

<template>
    <span :class="['inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold ring-1 uppercase tracking-wider', map[status] ?? 'bg-slate-700 text-slate-300 ring-slate-600']">
        {{ label[status] ?? status }}
    </span>
</template>