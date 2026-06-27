<script setup>
import { Link } from '@inertiajs/vue3'
import { ChevronRight } from 'lucide-vue-next'

defineProps({
    title: String,
    breadcrumbs: { type: Array, default: () => [] }, // [{ label, href }]
})
</script>

<template>
    <div>
        <nav v-if="breadcrumbs.length" class="flex items-center gap-1.5 mb-0.5">
            <template v-for="(crumb, i) in breadcrumbs" :key="i">
                <ChevronRight v-if="i > 0" class="w-3 h-3 text-slate-600" />
                <Link v-if="crumb.href" :href="crumb.href" class="text-xs text-slate-500 hover:text-slate-300 transition-colors">
                    {{ crumb.label }}
                </Link>
                <span v-else class="text-xs text-slate-400">{{ crumb.label }}</span>
            </template>
        </nav>
        <h1 class="text-base font-semibold text-white">{{ title }}</h1>
    </div>
</template>