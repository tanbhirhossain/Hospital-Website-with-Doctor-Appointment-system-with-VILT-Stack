<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, NavigationGroup, SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Activity, ArrowRight, Boxes, LayoutDashboard, Route, ShieldCheck, Sparkles } from 'lucide-vue-next';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

const page = usePage<SharedData>();
const groups = computed<NavigationGroup[]>(() => page.props.navigation?.groups || []);
const moduleCount = computed(() => page.props.navigation?.moduleBoundaries?.length || 0);
const routeCount = computed(() => groups.value.reduce((total, group) => total + group.items.length, 0));
const primaryGroups = computed(() => groups.value.slice(0, 6));
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <section class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-950 p-6 text-white shadow-2xl shadow-slate-900/20 md:p-8">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(34,211,238,.35),transparent_26rem),radial-gradient(circle_at_bottom_right,rgba(37,99,235,.35),transparent_24rem)]" />
                <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-3xl">
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-black uppercase tracking-[0.18em] text-cyan-100 backdrop-blur">
                            <Sparkles class="size-3.5" /> Unified Admin Experience
                        </div>
                        <h1 class="text-3xl font-black tracking-tight md:text-5xl">Premium hospital command center</h1>
                        <p class="mt-3 text-sm leading-7 text-slate-200 md:text-base">
                            Navigate every module from one route-aware sidebar, manage daily operations, and keep CRUD pages visually consistent across the platform.
                        </p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3 lg:w-[520px]">
                        <div class="rounded-3xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                            <Boxes class="mb-3 size-5 text-cyan-200" />
                            <p class="text-3xl font-black">{{ moduleCount }}</p>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-300">Modules</p>
                        </div>
                        <div class="rounded-3xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                            <Route class="mb-3 size-5 text-cyan-200" />
                            <p class="text-3xl font-black">{{ routeCount }}</p>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-300">Nav routes</p>
                        </div>
                        <div class="rounded-3xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                            <ShieldCheck class="mb-3 size-5 text-cyan-200" />
                            <p class="text-3xl font-black">Live</p>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-300">Secure shell</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-3">
                <div class="admin-panel">
                    <div class="mb-4 flex size-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-700 ring-1 ring-blue-100">
                        <LayoutDashboard class="size-5" />
                    </div>
                    <h2 class="text-lg font-black text-slate-950">Unified layout</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">All backend modules now inherit one polished AppLayout shell with consistent spacing, header, and sidebar behavior.</p>
                </div>
                <div class="admin-panel">
                    <div class="mb-4 flex size-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                        <Activity class="size-5" />
                    </div>
                    <h2 class="text-lg font-black text-slate-950">Route-aware navigation</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Navigation groups are generated from registered backend route names and omitted safely when a route is unavailable.</p>
                </div>
                <div class="admin-panel">
                    <div class="mb-4 flex size-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-700 ring-1 ring-violet-100">
                        <Sparkles class="size-5" />
                    </div>
                    <h2 class="text-lg font-black text-slate-950">Premium CRUD polish</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Tables, forms, inputs, modals, panels, and shared UI components now follow one professional design language.</p>
                </div>
            </section>

            <section class="admin-panel">
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-black tracking-tight text-slate-950">Module directory</h2>
                        <p class="text-sm text-slate-500">Quick access to every backend module route exposed in the sidebar.</p>
                    </div>
                    <Link href="/email-marketing" class="admin-button-primary">
                        Open Email Marketing <ArrowRight class="size-4" />
                    </Link>
                </div>

                <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
                    <div v-for="group in primaryGroups" :key="group.id" class="rounded-3xl border border-slate-200 bg-slate-50/70 p-4">
                        <div class="mb-3">
                            <h3 class="font-black text-slate-950">{{ group.title }}</h3>
                            <p class="text-xs leading-5 text-slate-500">{{ group.description }}</p>
                        </div>
                        <div class="space-y-2">
                            <Link v-for="item in group.items" :key="item.href" :href="item.href" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:text-blue-700 hover:shadow-md">
                                <span>{{ item.title }}</span>
                                <ArrowRight class="size-4 text-slate-400" />
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
