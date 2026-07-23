<script setup>
defineProps({ status: String })

const map = {
    pending: 'bg-amber-50 text-amber-700 ring-amber-200',
    confirmed: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    cancelled: 'bg-rose-50 text-rose-700 ring-rose-200',
    completed: 'bg-sky-50 text-sky-700 ring-sky-200',
    no_show: 'bg-slate-100 text-slate-600 ring-slate-200',
    active: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    inactive: 'bg-slate-100 text-slate-600 ring-slate-200',
    draft: 'bg-slate-100 text-slate-600 ring-slate-200',
    scheduled: 'bg-indigo-50 text-indigo-700 ring-indigo-200',
    sent: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    failed: 'bg-rose-50 text-rose-700 ring-rose-200',
}
const label = {
    pending: 'Pending', confirmed: 'Confirmed', cancelled: 'Cancelled',
    completed: 'Completed', no_show: 'No Show',
    active: 'Active', inactive: 'Inactive', draft: 'Draft', scheduled: 'Scheduled', sent: 'Sent', failed: 'Failed',
}
</script>

<template>
    <span :class="['inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-black uppercase tracking-[0.12em] ring-1', map[status] ?? 'bg-slate-100 text-slate-600 ring-slate-200']">
        {{ label[status] ?? status }}
    </span>
</template>
