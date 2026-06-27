<script setup>
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    columns: Array,   // [{ key, label, class }]
    rows: Array,
    pagination: Object,
    loading: Boolean,
    emptyMessage: { type: String, default: 'No records found.' },
})
</script>

<template>
    <div class="bg-slate-800/40 border border-slate-700/50 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-700/50">
                        <th v-for="col in columns" :key="col.key"
                            :class="['px-5 py-3.5 text-left text-[11px] font-semibold text-slate-400 uppercase tracking-widest', col.class]">
                            {{ col.label }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/30">
                    <template v-if="loading">
                        <tr v-for="n in 5" :key="n">
                            <td v-for="col in columns" :key="col.key" class="px-5 py-4">
                                <div class="h-4 bg-slate-700/50 rounded animate-pulse" :style="{ width: Math.random() * 40 + 40 + '%' }" />
                            </td>
                        </tr>
                    </template>
                    <template v-else-if="rows?.length">
                        <tr v-for="(row, idx) in rows" :key="row.id ?? idx"
                            class="hover:bg-slate-700/20 transition-colors group">
                            <slot :row="row" :idx="idx" />
                        </tr>
                    </template>
                    <tr v-else>
                        <td :colspan="columns.length" class="px-5 py-16 text-center text-slate-500 text-sm">
                            <slot name="empty">{{ emptyMessage }}</slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination?.last_page > 1"
            class="flex items-center justify-between gap-4 px-5 py-4 border-t border-slate-700/50">
            <p class="text-xs text-slate-400">
                Showing <span class="text-white font-medium">{{ pagination.from }}</span>–<span class="text-white font-medium">{{ pagination.to }}</span>
                of <span class="text-white font-medium">{{ pagination.total }}</span>
            </p>
            <div class="flex items-center gap-1.5">
                <template v-for="link in pagination.links" :key="link.label">
                    <component :is="link.url ? Link : 'span'" :href="link.url ?? undefined"
                        :class="[
                            'min-w-[32px] h-8 flex items-center justify-center px-2 rounded-lg text-xs font-medium transition-colors',
                            link.active
                                ? 'bg-violet-600 text-white'
                                : link.url
                                    ? 'text-slate-400 hover:text-white hover:bg-slate-700/60 cursor-pointer'
                                    : 'text-slate-600 cursor-not-allowed',
                        ]"
                        preserve-scroll
                        v-html="link.label.replace('&laquo;', '‹').replace('&raquo;', '›')" />
                </template>
            </div>
        </div>
    </div>
</template>