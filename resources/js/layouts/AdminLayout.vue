<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
    LayoutDashboard, Users, Stethoscope, Star, BedDouble,
    CalendarCheck, BarChart3, ChevronRight, Menu, X,
    Bell, Search, LogOut, Settings, ChevronDown,
    Building2, Activity
} from 'lucide-vue-next'

const page = usePage()
const sidebarOpen = ref(true)
const mobileSidebarOpen = ref(false)

const user = computed(() => page.props.auth?.user)
const flash = computed(() => page.props.flash)

const navItems = [
    {
        label: 'Dashboard',
        icon: LayoutDashboard,
        route: 'admin.dashboard',
        color: 'text-violet-400',
    },
    {
        label: 'Departments',
        icon: Building2,
        route: 'admin.departments.index',
        color: 'text-sky-400',
    },
    {
        label: 'Doctors',
        icon: Stethoscope,
        route: 'admin.doctors.index',
        color: 'text-emerald-400',
    },
    {
        label: 'Centers of Excellence',
        icon: Star,
        route: 'admin.coe.index',
        color: 'text-amber-400',
    },
    {
        label: 'Doctor Rests',
        icon: BedDouble,
        route: 'admin.doctor-rests.index',
        color: 'text-rose-400',
    },
    {
        label: 'Appointments',
        icon: CalendarCheck,
        route: 'appointments.index',
        color: 'text-indigo-400',
    },
    {
        label: 'Reports',
        icon: BarChart3,
        route: 'admin.reports.index',
        color: 'text-teal-400',
    },
]

const isActive = (routeName) => {
    return route().current(routeName) || route().current(routeName + '.*')
}
</script>

<template>
    <div class="flex h-screen bg-slate-950 overflow-hidden font-sans antialiased">

        <!-- Mobile overlay -->
        <Transition enter-from-class="opacity-0" enter-active-class="transition-opacity duration-200"
            leave-to-class="opacity-0" leave-active-class="transition-opacity duration-200">
            <div v-if="mobileSidebarOpen" @click="mobileSidebarOpen = false"
                class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden" />
        </Transition>

        <!-- Sidebar -->
        <aside :class="[
            'fixed lg:relative z-50 flex flex-col h-full bg-slate-900 border-r border-slate-800 transition-all duration-300 ease-in-out',
            sidebarOpen ? 'w-64' : 'w-16',
            mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
        ]">

            <!-- Logo -->
            <div class="flex items-center h-16 px-4 border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center shrink-0 shadow-lg shadow-violet-500/20">
                        <Activity class="w-4 h-4 text-white" />
                    </div>
                    <Transition enter-from-class="opacity-0 -translate-x-2" enter-active-class="transition duration-200"
                        leave-to-class="opacity-0 -translate-x-2" leave-active-class="transition duration-150">
                        <div v-if="sidebarOpen" class="min-w-0">
                            <p class="text-sm font-bold text-white truncate leading-none">Hospital Admin</p>
                            <p class="text-[11px] text-slate-400 mt-0.5 truncate">Management Portal</p>
                        </div>
                    </Transition>
                </div>

                <button @click="sidebarOpen = !sidebarOpen"
                    class="ml-auto shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:text-slate-300 hover:bg-slate-800 transition-colors hidden lg:flex">
                    <ChevronRight :class="['w-4 h-4 transition-transform duration-300', sidebarOpen ? 'rotate-180' : '']" />
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-0.5 scrollbar-hide">
                <template v-for="item in navItems" :key="item.route">
                    <Link :href="route(item.route)" :class="[
                        'group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150',
                        isActive(item.route)
                            ? 'bg-slate-800 text-white shadow-sm'
                            : 'text-slate-400 hover:text-white hover:bg-slate-800/60'
                    ]">
                        <component :is="item.icon" :class="['w-5 h-5 shrink-0', item.color]" />
                        <Transition enter-from-class="opacity-0" enter-active-class="transition duration-200"
                            leave-to-class="opacity-0" leave-active-class="transition duration-100">
                            <span v-if="sidebarOpen" class="truncate leading-none">{{ item.label }}</span>
                        </Transition>
                        <div v-if="isActive(item.route) && sidebarOpen"
                            class="ml-auto w-1.5 h-1.5 rounded-full bg-violet-400 shrink-0" />
                    </Link>
                </template>
            </nav>

            <!-- User -->
            <div class="border-t border-slate-800 p-3">
                <div :class="['flex items-center gap-3 px-2 py-2 rounded-xl', sidebarOpen ? '' : 'justify-center']">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-400 to-indigo-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ user?.name?.charAt(0)?.toUpperCase() ?? 'A' }}
                    </div>
                    <Transition enter-from-class="opacity-0" enter-active-class="transition duration-200"
                        leave-to-class="opacity-0" leave-active-class="transition duration-100">
                        <div v-if="sidebarOpen" class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-white truncate">{{ user?.name ?? 'Admin' }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ user?.email ?? '' }}</p>
                        </div>
                    </Transition>
                    <Transition enter-from-class="opacity-0" enter-active-class="transition duration-200"
                        leave-to-class="opacity-0" leave-active-class="transition duration-100">
                        <Link v-if="sidebarOpen" :href="route('logout')" method="post" as="button"
                            class="shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:text-rose-400 hover:bg-slate-800 transition-colors">
                            <LogOut class="w-3.5 h-3.5" />
                        </Link>
                    </Transition>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Topbar -->
            <header class="h-16 bg-slate-900/80 backdrop-blur-xl border-b border-slate-800 flex items-center gap-4 px-4 lg:px-6 shrink-0">
                <button @click="mobileSidebarOpen = !mobileSidebarOpen"
                    class="lg:hidden w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                    <Menu class="w-5 h-5" />
                </button>

                <!-- Breadcrumb slot -->
                <div class="flex-1 min-w-0">
                    <slot name="header" />
                </div>

                <div class="flex items-center gap-2">
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                        <Bell class="w-4 h-4" />
                    </button>
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                        <Settings class="w-4 h-4" />
                    </button>
                </div>
            </header>

            <!-- Flash messages -->
            <div v-if="flash?.success || flash?.error" class="px-4 lg:px-6 pt-4">
                <div v-if="flash?.success"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-950/60 border border-emerald-500/30 text-emerald-300 text-sm">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0" />
                    {{ flash.success }}
                </div>
                <div v-if="flash?.error"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl bg-rose-950/60 border border-rose-500/30 text-rose-300 text-sm">
                    <div class="w-1.5 h-1.5 rounded-full bg-rose-400 shrink-0" />
                    {{ flash.error }}
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
