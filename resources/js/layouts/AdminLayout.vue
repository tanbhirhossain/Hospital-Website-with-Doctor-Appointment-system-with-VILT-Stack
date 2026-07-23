<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import {
    LayoutDashboard, Users, Stethoscope, Star, BedDouble,
    CalendarCheck, BarChart3, ChevronRight, Menu, X,
    Bell, Search, LogOut, Settings, ChevronDown,
    Building2, Activity, Sparkles, CheckCircle2,
    AlertCircle, Info, RefreshCw, Moon, Sun,
    Shield, Zap, TrendingUp, Clock
} from 'lucide-vue-next'

// --- Page state ---------------------------------------------------------------
const page = usePage()
const user = computed(() => page.props.auth?.user)
const flash = computed(() => page.props.flash)

// --- Sidebar state ------------------------------------------------------------
const sidebarOpen    = ref(true)
const mobileSidebar  = ref(false)
const sidebarHovered = ref(false)

// Tooltip tracking for collapsed sidebar
const hoveredRoute = ref(null)

// Auto-collapse on small screens
const handleResize = () => {
    if (window.innerWidth < 1024) {
        sidebarOpen.value = false
    }
}
onMounted(() => {
    handleResize()
    window.addEventListener('resize', handleResize)
})
onUnmounted(() => window.removeEventListener('resize', handleResize))

// Close mobile sidebar on navigation
router.on('start', () => { mobileSidebar.value = false })

// --- Dark mode ----------------------------------------------------------------
const darkMode = ref(true) // admin defaults dark
watch(darkMode, (val) => {
    document.documentElement.classList.toggle('dark', val)
})

// --- Search -------------------------------------------------------------------
const searchOpen  = ref(false)
const searchQuery = ref('')

// --- Notifications ------------------------------------------------------------
const notifOpen = ref(false)
const notifCount = ref(4)
const notifications = ref([
    { id: 1, type: 'appointment', icon: CalendarCheck, color: 'text-indigo-400', bg: 'bg-indigo-950/60 ring-indigo-500/30', title: 'New appointment booked', meta: 'Dr. Rahman – 2 min ago', unread: true },
    { id: 2, type: 'system',      icon: Shield,        color: 'text-emerald-400', bg: 'bg-emerald-950/60 ring-emerald-500/30', title: 'System backup completed', meta: 'Auto-backup – 1 h ago', unread: true },
    { id: 3, type: 'alert',       icon: AlertCircle,   color: 'text-amber-400',   bg: 'bg-amber-950/60 ring-amber-500/30',   title: 'Doctor schedule conflict', meta: 'Dept. Cardiology – 3 h ago', unread: false },
    { id: 4, type: 'report',      icon: TrendingUp,    color: 'text-sky-400',     bg: 'bg-sky-950/60 ring-sky-500/30',       title: 'Monthly report ready', meta: 'Reports module – 5 h ago', unread: false },
])
const markAllRead = () => {
    notifications.value.forEach(n => n.unread = false)
    notifCount.value = 0
}

// --- User menu ----------------------------------------------------------------
const userMenuOpen = ref(false)

// Close dropdowns on outside click
const onDocClick = (e) => {
    if (!e.target.closest('[data-notif]'))     notifOpen.value    = false
    if (!e.target.closest('[data-usermenu]'))  userMenuOpen.value = false
    if (!e.target.closest('[data-search]'))    searchOpen.value   = false
}
onMounted(() => document.addEventListener('click', onDocClick))
onUnmounted(() => document.removeEventListener('click', onDocClick))

// --- Flash auto-dismiss -------------------------------------------------------
const flashVisible = ref(false)
watch(flash, (val) => {
    if (val?.success || val?.error || val?.warning || val?.info) {
        flashVisible.value = true
        setTimeout(() => { flashVisible.value = false }, 5000)
    }
}, { deep: true, immediate: true })

// --- Navigation ---------------------------------------------------------------
const navGroups = [
    {
        label: 'Core',
        items: [
            { label: 'Dashboard',           icon: LayoutDashboard, route: 'admin.dashboard',         color: 'text-violet-400',  accent: 'from-violet-500 to-indigo-600' },
            { label: 'Departments',          icon: Building2,       route: 'admin.departments.index',  color: 'text-sky-400',     accent: 'from-sky-500 to-blue-600' },
            { label: 'Doctors',              icon: Stethoscope,     route: 'admin.doctors.index',      color: 'text-emerald-400', accent: 'from-emerald-500 to-teal-600' },
        ]
    },
    {
        label: 'Clinical',
        items: [
            { label: 'Centers of Excellence', icon: Star,          route: 'admin.coe.index',           color: 'text-amber-400',   accent: 'from-amber-500 to-orange-600' },
            { label: 'Doctor Rests',           icon: BedDouble,    route: 'admin.doctor-rests.index',  color: 'text-rose-400',    accent: 'from-rose-500 to-pink-600' },
            { label: 'Appointments',           icon: CalendarCheck, route: 'appointments.index',        color: 'text-indigo-400',  accent: 'from-indigo-500 to-blue-600' },
        ]
    },
    {
        label: 'Analytics',
        items: [
            { label: 'Reports',              icon: BarChart3,       route: 'admin.reports.index',      color: 'text-teal-400',    accent: 'from-teal-500 to-cyan-600' },
        ]
    },
]

const isActive = (routeName) => {
    try {
        return route().current(routeName) || route().current(routeName + '.*')
    } catch {
        return false
    }
}

// --- User avatar initial ------------------------------------------------------
const userInitial = computed(() => user.value?.name?.charAt(0)?.toUpperCase() ?? 'A')
const userInitials = computed(() => {
    const parts = (user.value?.name ?? 'Admin').split(' ')
    return parts.length >= 2 ? parts[0][0] + parts[1][0] : parts[0][0]
})
</script>

<template>
    <div class="flex h-screen bg-slate-950 overflow-hidden antialiased" style="font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif;">

        <!-- ¦¦ MOBILE OVERLAY ¦¦ -->
        <Transition
            enter-from-class="opacity-0" enter-active-class="transition duration-200"
            leave-to-class="opacity-0"   leave-active-class="transition duration-200">
            <div v-if="mobileSidebar" @click="mobileSidebar = false"
                class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden" />
        </Transition>

        <!-- ¦¦¦¦¦¦¦¦¦¦¦¦ SIDEBAR ¦¦¦¦¦¦¦¦¦¦¦¦ -->
        <aside
            :class="[
                'fixed lg:relative z-50 flex flex-col h-full transition-all duration-300 ease-in-out shrink-0',
                'border-r border-slate-800/70',
                sidebarOpen ? 'w-64' : 'w-[68px]',
                mobileSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            ]"
            style="background: linear-gradient(180deg, #0f172a 0%, #0c1220 100%);">

            <!-- -- LOGO HEADER ------------------------------------ -->
            <div class="relative flex items-center h-16 px-4 border-b border-slate-800/70 shrink-0 overflow-hidden">
                <!-- Subtle ambient glow behind logo -->
                <div class="absolute -top-6 -left-4 w-24 h-24 bg-violet-600/10 rounded-full blur-2xl pointer-events-none" />

                <div class="flex items-center gap-3 min-w-0 relative z-10">
                    <!-- Logo mark -->
                    <div class="relative shrink-0">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/25"
                            style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);">
                            <Activity class="w-4.5 h-4.5 text-white" style="width:18px;height:18px;" />
                        </div>
                        <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-400 rounded-full border-2 border-slate-900" />
                    </div>

                    <Transition
                        enter-from-class="opacity-0 -translate-x-3" enter-active-class="transition duration-200"
                        leave-to-class="opacity-0 -translate-x-3"   leave-active-class="transition duration-150">
                        <div v-if="sidebarOpen" class="min-w-0">
                            <p class="text-sm font-extrabold text-white tracking-tight leading-none">AMZ Hospital</p>
                            <p class="text-[10px] text-slate-400 mt-0.5 font-medium tracking-wide">Management Portal</p>
                        </div>
                    </Transition>
                </div>

                <!-- Collapse toggle (desktop) -->
                <button @click="sidebarOpen = !sidebarOpen"
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-lg items-center justify-center text-slate-500 hover:text-slate-300 hover:bg-slate-800 transition-all hidden lg:flex">
                    <ChevronRight :class="['w-3.5 h-3.5 transition-transform duration-300', sidebarOpen ? 'rotate-180' : '']" />
                </button>

                <!-- Mobile close -->
                <button @click="mobileSidebar = false" class="ml-auto lg:hidden text-slate-500 hover:text-white">
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- -- NAVIGATION ------------------------------------- -->
            <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-5" style="scrollbar-width:none;">
                <div v-for="group in navGroups" :key="group.label">

                    <!-- Group label -->
                    <Transition
                        enter-from-class="opacity-0" enter-active-class="transition duration-200"
                        leave-to-class="opacity-0"   leave-active-class="transition duration-100">
                        <p v-if="sidebarOpen"
                            class="mb-1.5 px-3 text-[10px] font-bold uppercase tracking-widest text-slate-600 select-none">
                            {{ group.label }}
                        </p>
                    </Transition>
                    <div v-if="!sidebarOpen" class="mb-1.5 px-3">
                        <div class="h-px bg-slate-800" />
                    </div>

                    <!-- Nav items -->
                    <div class="space-y-0.5">
                        <div v-for="item in group.items" :key="item.route" class="relative"
                            @mouseenter="hoveredRoute = item.route"
                            @mouseleave="hoveredRoute = null">

                            <Link :href="route(item.route)"
                                :class="[
                                    'group relative flex items-center gap-3 rounded-xl text-sm font-medium transition-all duration-150 overflow-hidden',
                                    sidebarOpen ? 'px-3 py-2.5' : 'px-[14px] py-2.5 justify-center',
                                    isActive(item.route)
                                        ? 'text-white bg-slate-800/80 shadow-sm'
                                        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40',
                                ]">

                                <!-- Active left accent bar -->
                                <div v-if="isActive(item.route)"
                                    class="absolute left-0 inset-y-2 w-0.5 rounded-full"
                                    :style="`background: linear-gradient(to bottom, var(--tw-gradient-stops)); background: linear-gradient(180deg, #7c3aed, #4f46e5);`"
                                    style="background: linear-gradient(180deg, #a78bfa, #818cf8);" />

                                <!-- Icon -->
                                <component :is="item.icon"
                                    :class="['shrink-0 transition-colors', item.color, 'w-[18px] h-[18px]']"
                                    style="width:18px;height:18px;" />

                                <!-- Label -->
                                <Transition
                                    enter-from-class="opacity-0 translate-x-1" enter-active-class="transition duration-150"
                                    leave-to-class="opacity-0"                  leave-active-class="transition duration-100">
                                    <span v-if="sidebarOpen" class="truncate leading-none font-semibold">{{ item.label }}</span>
                                </Transition>

                                <!-- Active dot -->
                                <div v-if="isActive(item.route) && sidebarOpen"
                                    class="ml-auto w-1.5 h-1.5 rounded-full bg-violet-400 shrink-0" />
                            </Link>

                            <!-- Tooltip when collapsed -->
                            <Transition
                                enter-from-class="opacity-0 translate-x-1" enter-active-class="transition duration-150"
                                leave-to-class="opacity-0 translate-x-1"   leave-active-class="transition duration-100">
                                <div v-if="!sidebarOpen && hoveredRoute === item.route"
                                    class="absolute left-full top-1/2 -translate-y-1/2 ml-3 z-50 pointer-events-none">
                                    <div class="bg-slate-800 text-white text-xs font-semibold px-2.5 py-1.5 rounded-lg shadow-xl whitespace-nowrap border border-slate-700">
                                        {{ item.label }}
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- -- SIDEBAR FOOTER --------------------------------- -->
            <div class="border-t border-slate-800/70 p-3 shrink-0">
                <!-- System status pill -->
                <Transition
                    enter-from-class="opacity-0" enter-active-class="transition duration-200"
                    leave-to-class="opacity-0"   leave-active-class="transition duration-100">
                    <div v-if="sidebarOpen"
                        class="mb-3 flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-950/40 border border-emerald-800/40">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse shrink-0" />
                        <span class="text-[10px] font-semibold text-emerald-400 tracking-wide">All Systems Operational</span>
                    </div>
                </Transition>

                <!-- User row -->
                <div :class="['flex items-center gap-2.5 rounded-xl p-2 hover:bg-slate-800/40 transition-colors cursor-pointer', !sidebarOpen && 'justify-center']">
                    <!-- Avatar -->
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold shrink-0 shadow-md"
                        style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);">
                        {{ userInitials }}
                    </div>

                    <Transition
                        enter-from-class="opacity-0" enter-active-class="transition duration-200"
                        leave-to-class="opacity-0"   leave-active-class="transition duration-100">
                        <div v-if="sidebarOpen" class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-white truncate leading-none">{{ user?.name ?? 'Admin' }}</p>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5">{{ user?.email ?? 'admin@amz.com' }}</p>
                        </div>
                    </Transition>

                    <Transition
                        enter-from-class="opacity-0" enter-active-class="transition duration-200"
                        leave-to-class="opacity-0"   leave-active-class="transition duration-100">
                        <Link v-if="sidebarOpen" :href="route('logout')" method="post" as="button"
                            class="shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-slate-600 hover:text-rose-400 hover:bg-slate-800 transition-all"
                            title="Sign out">
                            <LogOut class="w-3.5 h-3.5" />
                        </Link>
                    </Transition>
                </div>
            </div>
        </aside>

        <!-- ¦¦¦¦¦¦¦¦¦¦¦¦ MAIN PANEL ¦¦¦¦¦¦¦¦¦¦¦¦ -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- -- TOPBAR ------------------------------------------ -->
            <header class="h-16 flex items-center gap-3 px-4 lg:px-5 shrink-0 border-b border-slate-800/70 z-30"
                style="background: rgba(15,23,42,0.95); backdrop-filter: blur(20px);">

                <!-- Mobile hamburger -->
                <button @click="mobileSidebar = !mobileSidebar"
                    class="lg:hidden w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all">
                    <Menu class="w-5 h-5" />
                </button>

                <!-- Breadcrumb / page title slot -->
                <div class="flex-1 min-w-0">
                    <slot name="header" />
                </div>

                <!-- -- TOP-RIGHT ACTIONS -- -->
                <div class="flex items-center gap-1.5">

                    <!-- Search trigger -->
                    <div class="relative" data-search>
                        <button @click.stop="searchOpen = !searchOpen"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all">
                            <Search class="w-4 h-4" />
                        </button>

                        <!-- Search dropdown -->
                        <Transition
                            enter-from-class="opacity-0 scale-95 -translate-y-1" enter-active-class="transition duration-150 origin-top-right"
                            leave-to-class="opacity-0 scale-95 -translate-y-1"   leave-active-class="transition duration-100 origin-top-right">
                            <div v-if="searchOpen" @click.stop
                                class="absolute right-0 top-full mt-2 w-72 rounded-2xl border border-slate-700/60 shadow-2xl shadow-black/40 overflow-hidden z-50"
                                style="background: #0f1929;">
                                <div class="flex items-center gap-2 px-3 py-2.5 border-b border-slate-800">
                                    <Search class="w-3.5 h-3.5 text-slate-500 shrink-0" />
                                    <input v-model="searchQuery" type="text" placeholder="Search modules, patients…"
                                        class="flex-1 text-xs bg-transparent text-white placeholder-slate-500 outline-none"
                                        autofocus />
                                    <kbd class="text-[9px] px-1.5 py-0.5 rounded bg-slate-800 text-slate-500 font-mono">ESC</kbd>
                                </div>
                                <div class="py-1.5">
                                    <p class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-600">Quick Links</p>
                                    <Link v-for="item in navGroups.flatMap(g => g.items).slice(0,5)" :key="item.route"
                                        :href="route(item.route)"
                                        class="flex items-center gap-2.5 px-3 py-2 hover:bg-slate-800/60 transition-colors group">
                                        <component :is="item.icon" class="w-3.5 h-3.5 shrink-0" :class="item.color" />
                                        <span class="text-xs text-slate-300 group-hover:text-white transition-colors font-medium">{{ item.label }}</span>
                                    </Link>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Notifications -->
                    <div class="relative" data-notif>
                        <button @click.stop="notifOpen = !notifOpen; notifCount = 0"
                            class="relative w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all">
                            <Bell class="w-4 h-4" />
                            <span v-if="notifCount > 0"
                                class="absolute top-1 right-1 min-w-[16px] h-4 flex items-center justify-center rounded-full bg-rose-500 text-[9px] font-bold text-white leading-none px-1">
                                {{ notifCount }}
                            </span>
                        </button>

                        <!-- Notifications panel -->
                        <Transition
                            enter-from-class="opacity-0 scale-95 -translate-y-1" enter-active-class="transition duration-150 origin-top-right"
                            leave-to-class="opacity-0 scale-95 -translate-y-1"   leave-active-class="transition duration-100 origin-top-right">
                            <div v-if="notifOpen" @click.stop
                                class="absolute right-0 top-full mt-2 w-80 rounded-2xl border border-slate-700/60 shadow-2xl shadow-black/40 overflow-hidden z-50"
                                style="background: #0f1929;">
                                <!-- Panel header -->
                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800">
                                    <span class="text-xs font-bold text-white">Notifications</span>
                                    <button @click="markAllRead" class="text-[10px] text-slate-500 hover:text-violet-400 font-semibold transition-colors">
                                        Mark all read
                                    </button>
                                </div>
                                <!-- List -->
                                <div class="max-h-72 overflow-y-auto" style="scrollbar-width:none;">
                                    <div v-for="n in notifications" :key="n.id"
                                        :class="['flex gap-3 px-4 py-3 border-b border-slate-800/60 last:border-b-0 transition-colors hover:bg-slate-800/30 cursor-pointer', n.unread && 'bg-slate-800/20']">
                                        <div :class="['w-8 h-8 rounded-lg flex items-center justify-center shrink-0 ring-1', n.bg]">
                                            <component :is="n.icon" class="w-4 h-4" :class="n.color" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-slate-200 leading-snug">{{ n.title }}</p>
                                            <p class="text-[10px] text-slate-500 mt-0.5 flex items-center gap-1">
                                                <Clock class="w-3 h-3 inline" />
                                                {{ n.meta }}
                                            </p>
                                        </div>
                                        <div v-if="n.unread" class="mt-1 w-1.5 h-1.5 rounded-full bg-violet-400 shrink-0" />
                                    </div>
                                </div>
                                <!-- Footer -->
                                <div class="px-4 py-2.5 border-t border-slate-800">
                                    <button class="text-[10px] text-slate-500 hover:text-slate-300 font-semibold transition-colors">
                                        View all notifications ?
                                    </button>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Settings -->
                    <Link :href="route('profile.edit')"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all">
                        <Settings class="w-4 h-4" />
                    </Link>

                    <!-- Divider -->
                    <div class="h-5 w-px bg-slate-800 mx-1" />

                    <!-- User menu -->
                    <div class="relative" data-usermenu>
                        <button @click.stop="userMenuOpen = !userMenuOpen"
                            class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-xl hover:bg-slate-800 transition-all">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-[11px] font-bold"
                                style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);">
                                {{ userInitials }}
                            </div>
                            <span class="text-xs font-semibold text-slate-300 hidden sm:block max-w-[100px] truncate">
                                {{ user?.name?.split(' ')[0] ?? 'Admin' }}
                            </span>
                            <ChevronDown :class="['w-3 h-3 text-slate-500 transition-transform duration-200', userMenuOpen ? 'rotate-180' : '']" />
                        </button>

                        <Transition
                            enter-from-class="opacity-0 scale-95 -translate-y-1" enter-active-class="transition duration-150 origin-top-right"
                            leave-to-class="opacity-0 scale-95 -translate-y-1"   leave-active-class="transition duration-100 origin-top-right">
                            <div v-if="userMenuOpen" @click.stop
                                class="absolute right-0 top-full mt-2 w-52 rounded-2xl border border-slate-700/60 shadow-2xl shadow-black/40 overflow-hidden z-50 py-1.5"
                                style="background: #0f1929;">
                                <!-- User info row -->
                                <div class="px-4 py-3 border-b border-slate-800 mb-1">
                                    <p class="text-xs font-bold text-white truncate">{{ user?.name ?? 'Admin' }}</p>
                                    <p class="text-[10px] text-slate-500 truncate mt-0.5">{{ user?.email ?? '' }}</p>
                                    <span class="mt-1.5 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-violet-950/60 text-violet-400 text-[10px] font-bold ring-1 ring-violet-500/30">
                                        <Shield class="w-2.5 h-2.5" /> Administrator
                                    </span>
                                </div>

                                <Link :href="route('profile.edit')"
                                    class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800/50 transition-colors">
                                    <Settings class="w-3.5 h-3.5" />
                                    Profile & Settings
                                </Link>
                                <Link :href="route('admin.dashboard')"
                                    class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800/50 transition-colors">
                                    <Activity class="w-3.5 h-3.5" />
                                    Activity Log
                                </Link>

                                <div class="my-1.5 mx-3 h-px bg-slate-800" />

                                <Link :href="route('logout')" method="post" as="button"
                                    class="flex items-center gap-2.5 w-full px-4 py-2 text-xs text-rose-400 hover:text-rose-300 hover:bg-rose-950/30 transition-colors">
                                    <LogOut class="w-3.5 h-3.5" />
                                    Sign out
                                </Link>
                            </div>
                        </Transition>
                    </div>
                </div>
            </header>

            <!-- -- FLASH MESSAGES ----------------------------------- -->
            <Transition
                enter-from-class="opacity-0 -translate-y-2" enter-active-class="transition duration-300"
                leave-to-class="opacity-0 -translate-y-2"   leave-active-class="transition duration-200">
                <div v-if="flashVisible && (flash?.success || flash?.error || flash?.warning || flash?.info)"
                    class="mx-4 lg:mx-5 mt-4 shrink-0">

                    <!-- Success -->
                    <div v-if="flash?.success"
                        class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm font-medium ring-1"
                        style="background: rgba(6,78,59,0.25); border-color: rgba(52,211,153,0.25); color: #6ee7b7; ring-color: rgba(52,211,153,0.2);">
                        <CheckCircle2 class="w-4 h-4 mt-0.5 shrink-0 text-emerald-400" />
                        <span>{{ flash.success }}</span>
                        <button @click="flashVisible = false" class="ml-auto text-emerald-600 hover:text-emerald-300 transition-colors">
                            <X class="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <!-- Error -->
                    <div v-if="flash?.error"
                        class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
                        style="background: rgba(127,29,29,0.25); border-color: rgba(248,113,113,0.25); color: #fca5a5;">
                        <AlertCircle class="w-4 h-4 mt-0.5 shrink-0 text-rose-400" />
                        <span>{{ flash.error }}</span>
                        <button @click="flashVisible = false" class="ml-auto text-rose-600 hover:text-rose-300 transition-colors">
                            <X class="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <!-- Warning -->
                    <div v-if="flash?.warning"
                        class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
                        style="background: rgba(120,53,15,0.25); border-color: rgba(251,191,36,0.25); color: #fde68a;">
                        <AlertCircle class="w-4 h-4 mt-0.5 shrink-0 text-amber-400" />
                        <span>{{ flash.warning }}</span>
                        <button @click="flashVisible = false" class="ml-auto text-amber-600 hover:text-amber-300 transition-colors">
                            <X class="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <!-- Info -->
                    <div v-if="flash?.info"
                        class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
                        style="background: rgba(23,37,84,0.25); border-color: rgba(96,165,250,0.25); color: #bfdbfe;">
                        <Info class="w-4 h-4 mt-0.5 shrink-0 text-blue-400" />
                        <span>{{ flash.info }}</span>
                        <button @click="flashVisible = false" class="ml-auto text-blue-600 hover:text-blue-300 transition-colors">
                            <X class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </Transition>

            <!-- -- PAGE CONTENT ------------------------------------- -->
            <main class="flex-1 overflow-y-auto" style="scrollbar-width:thin; scrollbar-color: #1e293b transparent;">
                <div class="p-4 lg:p-6 max-w-screen-2xl mx-auto w-full">
                    <slot />
                </div>
            </main>

            <!-- -- FOOTER BAR --------------------------------------- -->
            <footer class="h-9 border-t border-slate-800/50 flex items-center justify-between px-5 shrink-0"
                style="background: rgba(15,23,42,0.8);">
                <span class="text-[10px] text-slate-600 font-medium">
                    AMZ Hospital Ltd. &copy; {{ new Date().getFullYear() }} · Management Portal
                </span>
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1.5 text-[10px] text-emerald-500 font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                        Production
                    </span>
                    <span class="text-[10px] text-slate-700">v2.0.0</span>
                </div>
            </footer>
        </div>
    </div>
</template>