<script setup>
defineProps({
    label: String,
    value: [String, Number],
    sub: String,
    icon: Object,
    color: { type: String, default: 'blue' },
    trend: String,
    trendUp: Boolean,
})

const colorMap = {
    blue: { bg: 'from-blue-500 to-cyan-500', soft: 'bg-blue-50', icon: 'text-blue-700', ring: 'ring-blue-100' },
    violet: { bg: 'from-violet-500 to-fuchsia-500', soft: 'bg-violet-50', icon: 'text-violet-700', ring: 'ring-violet-100' },
    sky: { bg: 'from-sky-500 to-blue-500', soft: 'bg-sky-50', icon: 'text-sky-700', ring: 'ring-sky-100' },
    emerald: { bg: 'from-emerald-500 to-teal-500', soft: 'bg-emerald-50', icon: 'text-emerald-700', ring: 'ring-emerald-100' },
    amber: { bg: 'from-amber-500 to-orange-500', soft: 'bg-amber-50', icon: 'text-amber-700', ring: 'ring-amber-100' },
    rose: { bg: 'from-rose-500 to-pink-500', soft: 'bg-rose-50', icon: 'text-rose-700', ring: 'ring-rose-100' },
    indigo: { bg: 'from-indigo-500 to-blue-500', soft: 'bg-indigo-50', icon: 'text-indigo-700', ring: 'ring-indigo-100' },
    teal: { bg: 'from-teal-500 to-cyan-500', soft: 'bg-teal-50', icon: 'text-teal-700', ring: 'ring-teal-100' },
}
</script>

<template>
    <div class="group relative overflow-hidden rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-200/60 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-slate-200/70">
        <div :class="['absolute inset-x-0 top-0 h-1 bg-gradient-to-r', colorMap[color]?.bg ?? colorMap.blue.bg]" />
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0 flex-1">
                <p class="mb-2 text-xs font-black uppercase tracking-[0.16em] text-slate-500">{{ label }}</p>
                <p class="text-3xl font-black tabular-nums text-slate-950">{{ value }}</p>
                <p v-if="sub" class="mt-1.5 text-xs font-medium text-slate-500">{{ sub }}</p>
            </div>
            <div v-if="icon" :class="[
                'flex size-12 shrink-0 items-center justify-center rounded-2xl ring-1',
                colorMap[color]?.soft, colorMap[color]?.ring
            ]">
                <component :is="icon" :class="['size-5', colorMap[color]?.icon]" />
            </div>
        </div>
        <div v-if="trend" class="mt-4 flex items-center gap-1.5 border-t border-slate-100 pt-3">
            <span :class="trendUp ? 'text-emerald-600' : 'text-rose-600'" class="text-xs font-black">
                {{ trendUp ? '↑' : '↓' }} {{ trend }}
            </span>
            <span class="text-xs text-slate-500">vs last month</span>
        </div>
    </div>
</template>
