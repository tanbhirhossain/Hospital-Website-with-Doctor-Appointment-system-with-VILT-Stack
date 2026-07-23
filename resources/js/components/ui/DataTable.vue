<script setup>
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
    <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm shadow-slate-200/60">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50/90">
                        <th v-for="col in columns" :key="col.key"
                            :class="['border-b border-slate-200 px-5 py-3.5 text-left text-[11px] font-black uppercase tracking-[0.16em] text-slate-500', col.class]">
                            {{ col.label }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template v-if="loading">
                        <tr v-for="n in 5" :key="n">
                            <td v-for="col in columns" :key="col.key" class="px-5 py-4">
                                <div class="h-4 rounded-full bg-slate-100 animate-pulse" :style="{ width: Math.random() * 40 + 40 + '%' }" />
                            </td>
                        </tr>
                    </template>
                    <template v-else-if="rows?.length">
                        <tr v-for="(row, idx) in rows" :key="row.id ?? idx"
                            class="group transition hover:bg-blue-50/40">
                            <slot :row="row" :idx="idx" />
                        </tr>
                    </template>
                    <tr v-else>
                        <td :colspan="columns.length" class="px-5 py-16 text-center text-sm text-slate-500">
                            <slot name="empty">{{ emptyMessage }}</slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="pagination?.last_page > 1"
            class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs font-medium text-slate-500">
                Showing <span class="font-black text-slate-900">{{ pagination.from }}</span>–<span class="font-black text-slate-900">{{ pagination.to }}</span>
                of <span class="font-black text-slate-900">{{ pagination.total }}</span>
            </p>
            <div class="flex flex-wrap items-center gap-1.5">
                <template v-for="link in pagination.links" :key="link.label">
                    <component :is="link.url ? Link : 'span'" :href="link.url ?? undefined"
                        :class="[
                            'flex h-9 min-w-9 items-center justify-center rounded-xl px-3 text-xs font-black transition-colors',
                            link.active
                                ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20'
                                : link.url
                                    ? 'border border-slate-200 bg-white text-slate-600 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700'
                                    : 'border border-slate-100 text-slate-300 cursor-not-allowed',
                        ]"
                        preserve-scroll
                        v-html="link.label.replace('&laquo;', '‹').replace('&raquo;', '›')" />
                </template>
            </div>
        </div>
    </div>
</template>
