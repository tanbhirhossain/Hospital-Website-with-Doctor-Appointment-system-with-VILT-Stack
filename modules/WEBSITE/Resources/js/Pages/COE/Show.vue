<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    item: Object
});
</script>

<template>
    <div class="max-w-5xl mx-auto p-6 mt-10">
        <div class="mb-4">
            <Link :href="route('admin.coe.index')" class="text-xs text-indigo-600 font-semibold uppercase tracking-wider hover:text-indigo-800 flex items-center gap-1 transition">
                &larr; Return to Overview
            </Link>
        </div>

        <div class="bg-gradient-to-r from-slate-900 to-indigo-950 text-white rounded-t-xl p-8 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white/10 backdrop-blur-md rounded-lg text-2xl font-mono border border-white/10">
                    💡
                </div>
                <div>
                    <h1 class="text-3xl font-black tracking-tight">{{ item.name }}</h1>
                    <p class="text-xs text-indigo-200/80 font-mono mt-1">Slug Reference Path: {{ item.slug }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border-x border-b border-gray-200 rounded-b-xl shadow-sm p-8 space-y-8">
            <div>
                <h3 class="text-xs font-bold uppercase text-gray-400 tracking-wider mb-2">Description Statement</h3>
                <div class="text-gray-700 bg-gray-50 border border-gray-100 rounded-lg p-4 leading-relaxed whitespace-pre-line text-sm">
                    {{ item.description || 'No formal operational description has been designated for this entity.' }}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border border-gray-100 rounded-lg p-4 bg-white shadow-xs">
                    <h4 class="text-xs font-bold uppercase text-gray-400 tracking-wider mb-3">System Attributes</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <dt class="text-gray-500">CSS Class / Icon Key:</dt>
                            <dd class="font-mono text-gray-800 font-medium">{{ item.icon || '—' }}</dd>
                        </div>
                        <div class="flex justify-between py-1">
                            <dt class="text-gray-500">Inline Style Directive:</dt>
                            <dd class="font-mono text-gray-800 font-medium max-w-[200px] truncate" :title="item.style">{{ item.style || '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="border border-gray-100 rounded-lg p-4 bg-white shadow-xs">
                    <h4 class="text-xs font-bold uppercase text-gray-400 tracking-wider mb-3">Search Engine Directives</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <dt class="text-gray-500">Robot Status Directive:</dt>
                            <dd>
                                <span :class="[item.indexable ? 'text-emerald-700 bg-emerald-50 border border-emerald-100' : 'text-rose-700 bg-rose-50 border border-rose-100', 'px-2 py-0.5 rounded text-xs font-semibold']">
                                    {{ item.indexable ? 'ALLOW_INDEX' : 'NO_INDEX' }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between py-1">
                            <dt class="text-gray-500">Canonical Resource Link:</dt>
                            <dd class="text-gray-800 font-medium max-w-[200px] truncate" :title="item.canonical_url">{{ item.canonical_url || 'Default System Managed' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg bg-gray-50 overflow-hidden">
                <div class="bg-gray-100/80 px-4 py-3 border-b border-gray-200">
                    <h3 class="text-xs font-bold uppercase text-gray-500 tracking-wider">SEO Meta Target Configuration View</h3>
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="space-y-3">
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase">Standard Title Tag</span>
                            <span class="text-gray-900 font-medium">{{ item.meta_title }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase">Standard Meta Description</span>
                            <p class="text-gray-600 text-xs mt-0.5">{{ item.meta_description }}</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase">OpenGraph Object Title (og:title)</span>
                            <span class="text-gray-900 font-medium">{{ item.og_title }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase">OpenGraph Property Description (og:description)</span>
                            <p class="text-gray-600 text-xs mt-0.5">{{ item.og_description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <Link :href="route('admin.coe.edit', item.id)" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 transition rounded-md text-sm font-semibold text-white shadow-xs">
                    Edit Configuration
                </Link>
            </div>
        </div>
    </div>
</template>