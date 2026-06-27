<script setup>
defineProps({
    label: String,
    value: [String, Number],
    sub: String,
    icon: Object,
    color: { type: String, default: 'violet' },
    trend: String,
    trendUp: Boolean,
})

const colorMap = {
    violet: { bg: 'bg-violet-500/10', icon: 'text-violet-400', ring: 'ring-violet-500/20' },
    sky:    { bg: 'bg-sky-500/10',    icon: 'text-sky-400',    ring: 'ring-sky-500/20' },
    emerald:{ bg: 'bg-emerald-500/10',icon: 'text-emerald-400',ring: 'ring-emerald-500/20' },
    amber:  { bg: 'bg-amber-500/10',  icon: 'text-amber-400',  ring: 'ring-amber-500/20' },
    rose:   { bg: 'bg-rose-500/10',   icon: 'text-rose-400',   ring: 'ring-rose-500/20' },
    indigo: { bg: 'bg-indigo-500/10', icon: 'text-indigo-400', ring: 'ring-indigo-500/20' },
    teal:   { bg: 'bg-teal-500/10',   icon: 'text-teal-400',   ring: 'ring-teal-500/20' },
}
</script>

<template>
    <div class="bg-slate-800/40 border border-slate-700/50 rounded-2xl p-5 hover:border-slate-600/60 transition-colors group">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-slate-400 uppercase tracking-widest mb-2">{{ label }}</p>
                <p class="text-3xl font-bold text-white tabular-nums">{{ value }}</p>
                <p v-if="sub" class="text-xs text-slate-500 mt-1.5">{{ sub }}</p>
            </div>
            <div v-if="icon" :class="[
                'w-11 h-11 rounded-xl flex items-center justify-center ring-1 shrink-0',
                colorMap[color]?.bg, colorMap[color]?.ring
            ]">
                <component :is="icon" :class="['w-5 h-5', colorMap[color]?.icon]" />
            </div>
        </div>
        <div v-if="trend" class="mt-3 pt-3 border-t border-slate-700/50 flex items-center gap-1.5">
            <span :class="trendUp ? 'text-emerald-400' : 'text-rose-400'" class="text-xs font-medium">
                {{ trendUp ? '↑' : '↓' }} {{ trend }}
            </span>
            <span class="text-xs text-slate-500">vs last month</span>
        </div>
    </div>
</template>