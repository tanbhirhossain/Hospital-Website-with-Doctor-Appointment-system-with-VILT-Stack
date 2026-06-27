<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
  Calendar, Users, UserCheck, Clock, TrendingUp,
  Bell, Search, Menu, X, User, LogOut, Settings, Home
} from 'lucide-vue-next';

interface Props {
  breadcrumbs?: Array<{ title: string; href?: string }>;
}

const props = withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});

const sidebarOpen = ref(false);
const userMenuOpen = ref(false);
const notificationsOpen = ref(false);

const user = ref({
  name: 'Dr. Sarah Chen',
  role: 'Hospital Administrator',
  avatar: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=100&h=100&fit=crop&crop=face'
});

const navItems = [
  { name: 'Dashboard', href: '/dashboard', icon: Home, active: true },
  { name: 'Appointments', href: '/admin/appointments', icon: Calendar },
  { name: 'Doctors', href: '/admin/doctors', icon: UserCheck },
  { name: 'Role', href: '/admin/role', icon: Users },
  { name: 'Departments', href: '/admin/departments', icon: Clock },
  { name: 'Reports', href: '/reports', icon: TrendingUp },
];

const notifications = ref([
  { id: 1, message: 'New appointment request from John Doe', time: '2 min ago', type: 'appointment' },
  { id: 2, message: 'Dr. Emily Patel updated availability', time: '15 min ago', type: 'doctor' },
  { id: 3, message: 'Payment received: $245.00', time: '1 hour ago', type: 'payment' },
]);

const toggleSidebar = () => sidebarOpen.value = !sidebarOpen.value;
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100">
    <!-- Top Navigation Bar -->
    <nav class="sticky top-0 z-50 border-b border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl">
      <div class="px-6 h-16 flex items-center justify-between">
        <!-- Left: Logo + Mobile Menu -->
        <div class="flex items-center gap-4">
          <button @click="toggleSidebar" class="lg:hidden p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
            <Menu v-if="!sidebarOpen" class="w-5 h-5" />
            <X v-else class="w-5 h-5" />
          </button>

          <Link :href="route('dashboard')" class="flex items-center gap-3">
            <div class="w-6 h-9 bg-gradient-to-br from-red-700 to-red-600 rounded-2xl flex items-center justify-center shadow-lg p-7">
              <span class="text-white font-bold text-xl tracking-tighter p-8"></span>
            </div>
            <div>
              <span class="font-semibold text-xl tracking-tight">MZ</span>
              <span class="text-emerald-600 dark:text-emerald-400 font-medium text-sm ml-0.5">Appointment</span>
            </div>
          </Link>
        </div>

        <!-- Search -->
        <div class="hidden md:flex flex-1 max-w-md mx-8">
          <div class="relative w-full">
            <Search class="absolute left-4 top-3.5 w-4 h-4 text-slate-400" />
            <input 
              type="text" 
              placeholder="Search appointments, doctors, patients..." 
              class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 pl-11 pr-4 py-2.5 text-sm rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all"
            />
          </div>
        </div>

        <!-- Right Section -->
        <div class="flex items-center gap-2">
          <!-- Live Status -->
          <div class="hidden xl:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/30 rounded-full text-xs">
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
            <span class="font-medium text-emerald-700 dark:text-emerald-400">47 appointments today</span>
          </div>

          <!-- Notifications -->
          <div class="relative">
            <button @click="notificationsOpen = !notificationsOpen; userMenuOpen = false" class="p-2.5 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all relative">
              <Bell class="w-5 h-5 text-slate-600 dark:text-slate-300" />
              <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white dark:ring-slate-900"></span>
            </button>

            <!-- Notifications Dropdown -->
            <div v-if="notificationsOpen" class="absolute right-0 mt-3 w-80 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl shadow-xl py-2 z-50">
              <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between">
                  <span class="font-semibold text-sm">Notifications</span>
                  <span class="text-xs px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full">3 new</span>
                </div>
              </div>
              <div class="max-h-[320px] overflow-y-auto">
                <div v-for="notif in notifications" :key="notif.id" class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 flex gap-3 text-sm cursor-pointer border-b border-slate-100 dark:border-slate-800 last:border-none">
                  <div class="mt-1">
                    <div class="w-2 h-2 rounded-full mt-1.5" :class="{
                      'bg-emerald-500': notif.type === 'appointment',
                      'bg-blue-500': notif.type === 'doctor',
                      'bg-amber-500': notif.type === 'payment'
                    }"></div>
                  </div>
                  <div class="flex-1">
                    <p class="text-slate-700 dark:text-slate-200">{{ notif.message }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ notif.time }}</p>
                  </div>
                </div>
              </div>
              <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-800">
                <button class="text-emerald-600 hover:text-emerald-700 text-sm font-medium w-full text-center">View all notifications</button>
              </div>
            </div>
          </div>

          <!-- User Menu -->
          <div class="relative">
            <button @click="userMenuOpen = !userMenuOpen; notificationsOpen = false" class="flex items-center gap-3 pl-2 pr-4 py-1.5 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
              <img :src="user.avatar" class="w-8 h-8 rounded-2xl object-cover ring-2 ring-white dark:ring-slate-800" alt="" />
              <div class="hidden md:block text-left">
                <div class="font-semibold text-sm leading-none">{{ user.name }}</div>
                <div class="text-xs text-slate-500">{{ user.role }}</div>
              </div>
            </button>

            <div v-if="userMenuOpen" class="absolute right-0 mt-3 w-64 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl shadow-xl py-2 z-50">
              <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                  <img :src="user.avatar" class="w-10 h-10 rounded-2xl" />
                  <div>
                    <div class="font-semibold">{{ user.name }}</div>
                    <div class="text-xs text-slate-500">{{ user.role }}</div>
                  </div>
                </div>
              </div>
              <div class="py-1">
                <Link href="/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-800/60"><User class="w-4 h-4" /> Profile</Link>
                <Link href="/settings" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-800/60"><Settings class="w-4 h-4" /> Settings</Link>
                <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                <Link :href="route('logout')" method="post" class="flex items-center gap-3 px-4 py-2.5 text-sm text-rose-600 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                  <LogOut class="w-4 h-4" /> Sign out
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <div class="flex">
      <!-- Sidebar -->
      <aside 
        :class="[
          'fixed lg:static inset-y-0 left-0 z-40 w-72 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transform transition-transform duration-300 flex flex-col',
          sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
        ]"
      >
        <div class="px-6 pt-8 pb-4">
          <div class="flex items-center justify-between lg:hidden mb-4">
            <span class="font-semibold text-lg">Menu</span>
            <button @click="sidebarOpen = false"><X class="w-5 h-5" /></button>
          </div>
        </div>

        <nav class="px-3 flex-1 space-y-1">
          <Link 
            v-for="item in navItems" 
            :key="item.name"
            :href="item.href"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-2xl transition-all"
            :class="item.active 
              ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' 
              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
          >
            <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
            <span>{{ item.name }}</span>
          </Link>
        </nav>

        <div class="p-6 mt-auto border-t border-slate-100 dark:border-slate-800">
          <div class="bg-emerald-50 dark:bg-emerald-950/40 rounded-3xl p-4 text-center">
            <div class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Need help?</div>
            <div class="text-sm text-emerald-700 dark:text-emerald-300 mt-1">Contact support</div>
            <button class="mt-3 text-xs bg-white dark:bg-emerald-900 text-emerald-700 px-4 py-1.5 rounded-2xl font-medium hover:bg-emerald-100">Open chat</button>
          </div>
        </div>
      </aside>

      <!-- Main Content -->
      <div class="flex-1 min-w-0">
        <!-- Breadcrumb -->
        <div v-if="props.breadcrumbs.length" class="px-8 pt-6 pb-2 flex items-center gap-2 text-sm text-slate-500">
          <Link href="/dashboard" class="hover:text-slate-700">Home</Link>
          <span>/</span>
          <span v-for="(crumb, index) in props.breadcrumbs" :key="index" class="text-slate-700 dark:text-slate-300">{{ crumb.title }}</span>
        </div>

        <div class="p-6 lg:p-8 pt-2">
          <slot />
        </div>
      </div>
    </div>

    <!-- Mobile Overlay -->
    <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/40 lg:hidden z-30" />
  </div>
</template>