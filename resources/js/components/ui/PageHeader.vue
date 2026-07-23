<script setup>
import { Link } from '@inertiajs/vue3'
import { ChevronRight } from 'lucide-vue-next'

defineProps({
    title: String,
    subtitle: String,
    breadcrumbs: { type: Array, default: () => [] }, // [{ label, href }]
})
</script>

<template>
    <div class="mb-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm shadow-slate-200/60">
        <div class="relative px-6 py-6">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-600 via-cyan-500 to-teal-500" />
            <nav v-if="breadcrumbs.length" class="mb-2 flex flex-wrap items-center gap-1.5">
                <template v-for="(crumb, i) in breadcrumbs" :key="i">
                    <ChevronRight v-if="i > 0" class="size-3 text-slate-300" />
                    <Link v-if="crumb.href" :href="crumb.href" class="text-xs font-bold text-slate-500 transition-colors hover:text-blue-700">
                        {{ crumb.label }}
                    </Link>
                    <span v-else class="text-xs font-bold text-slate-400">{{ crumb.label }}</span>
                </template>
            </nav>
            <h1 class="text-2xl font-black tracking-tight text-slate-950 md:text-3xl">{{ title }}</h1>
            <p v-if="subtitle" class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">{{ subtitle }}</p>
            <div v-if="$slots.actions" class="mt-4 flex flex-wrap gap-2">
                <slot name="actions" />
            </div>
        </div>
    </div>
</template>
