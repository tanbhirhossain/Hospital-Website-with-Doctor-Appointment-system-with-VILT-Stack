<script setup>
import { ref } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
const profileDropdownOpen = ref(false);

const page = usePage();
const user = page.props.auth?.user;

const logout = () => {
  router.post(route('logout'));
};
</script>

<template>
  <div class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased flex">
    
    <!-- Mobile Sidebar Backdrop -->
    <div 
      v-if="sidebarOpen" 
      @click="sidebarOpen = false" 
      class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs lg:hidden transition-opacity"
    ></div>

    <!-- Sidebar Navigation -->
    <aside 
      :class="[
        'fixed inset-y-0 left-0 z-50 bg-slate-900 text-slate-300 transition-all duration-300 ease-in-out flex flex-col border-r border-slate-800 lg:static lg:translate-x-0',
        sidebarCollapsed ? 'lg:w-20' : 'lg:w-72',
        'w-72',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <!-- Brand Logo / Header -->
      <div class="h-16 flex items-center justify-between px-6 bg-slate-950 border-b border-slate-800 overflow-hidden">
        <Link :href="route('dashboard')" class="flex items-center gap-3 group min-w-max">
          <div class="w-9 h-9 rounded-xl bg-teal-600 flex items-center justify-center text-white font-bold shadow-lg shadow-teal-600/30 group-hover:bg-teal-500 transition-colors shrink-0">
            A
          </div>
          <span v-show="!sidebarCollapsed" class="text-lg font-bold tracking-wide text-white transition-opacity duration-300">AMZ Health Admin</span>
        </Link>
        
        <!-- Desktop Collapse Toggle Button -->
        <button 
          @click="sidebarCollapsed = !sidebarCollapsed" 
          class="hidden lg:flex text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 transition-colors"
          :title="sidebarCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
        >
          <svg class="w-5 h-5 transition-transform duration-300" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
          </svg>
        </button>
      </div>

      <!-- Navigation Links -->
      <nav class="flex-1 px-4 py-6 space-y-6 overflow-y-auto custom-scrollbar overflow-x-hidden">
        
        <!-- Section: Core -->
        <div>
          <p v-show="!sidebarCollapsed" class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Core</p>
          <div class="space-y-1">
            <Link 
              :href="route('dashboard')" 
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group relative"
              :class="route().current('dashboard') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'hover:bg-slate-800/60 text-slate-400 hover:text-white'"
              :title="sidebarCollapsed ? 'Dashboard' : ''"
            >
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
              <span v-show="!sidebarCollapsed" class="truncate">Dashboard</span>
            </Link>
          </div>
        </div>

        <!-- Section: Clinical & Appointments -->
        <div>
          <p v-show="!sidebarCollapsed" class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Appointments</p>
          <div class="space-y-1">
            <Link 
              :href="route('appointments.index')" 
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group relative"
              :class="route().current('appointments.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'hover:bg-slate-800/60 text-slate-400 hover:text-white'"
              :title="sidebarCollapsed ? 'Manage Appointments' : ''"
            >
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <span v-show="!sidebarCollapsed" class="truncate">Manage Appointments</span>
            </Link>
            <Link 
              :href="route('admin.patient-reviews.index')" 
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group relative"
              :class="route().current('admin.patient-reviews.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'hover:bg-slate-800/60 text-slate-400 hover:text-white'"
              :title="sidebarCollapsed ? 'Patient Reviews' : ''"
            >
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
              <span v-show="!sidebarCollapsed" class="truncate">Patient Reviews</span>
            </Link>
            <Link 
              :href="route('admin.client-reviews.index')" 
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group relative"
              :class="route().current('admin.client-reviews.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'hover:bg-slate-800/60 text-slate-400 hover:text-white'"
              :title="sidebarCollapsed ? 'Client Reviews' : ''"
            >
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
              <span v-show="!sidebarCollapsed" class="truncate">Client Reviews</span>
            </Link>
          </div>
        </div>

        <!-- Section: Hospital Modules -->
        <div>
          <p v-show="!sidebarCollapsed" class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Hospital Modules</p>
          <div class="space-y-1">
            <Link 
              :href="route('admin.doctors.index')" 
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group relative"
              :class="route().current('admin.doctors.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'hover:bg-slate-800/60 text-slate-400 hover:text-white'"
              :title="sidebarCollapsed ? 'Doctors Directory' : ''"
            >
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              <span v-show="!sidebarCollapsed" class="truncate">Doctors Directory</span>
            </Link>
            <Link 
              :href="route('admin.departments.index')" 
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group relative"
              :class="route().current('admin.departments.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'hover:bg-slate-800/60 text-slate-400 hover:text-white'"
              :title="sidebarCollapsed ? 'Departments' : ''"
            >
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
              <span v-show="!sidebarCollapsed" class="truncate">Departments</span>
            </Link>
            <Link 
              :href="route('admin.services.index')" 
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group relative"
              :class="route().current('admin.services.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'hover:bg-slate-800/60 text-slate-400 hover:text-white'"
              :title="sidebarCollapsed ? 'Services' : ''"
            >
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
              <span v-show="!sidebarCollapsed" class="truncate">Services</span>
            </Link>
            <Link 
              :href="route('admin.health-packages.index')" 
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group relative"
              :class="route().current('admin.health-packages.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'hover:bg-slate-800/60 text-slate-400 hover:text-white'"
              :title="sidebarCollapsed ? 'Health Packages' : ''"
            >
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
              <span v-show="!sidebarCollapsed" class="truncate">Health Packages</span>
            </Link>
            <Link 
              :href="route('admin.coe.index')" 
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group relative"
              :class="route().current('admin.coe.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'hover:bg-slate-800/60 text-slate-400 hover:text-white'"
              :title="sidebarCollapsed ? 'Centers of Excellence' : ''"
            >
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              <span v-show="!sidebarCollapsed" class="truncate">Centers of Excellence</span>
            </Link>
          </div>
        </div>

        <!-- Section: Content & Media -->
        <div>
          <p v-show="!sidebarCollapsed" class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Content Management</p>
          <div class="space-y-1">
            <Link 
              :href="route('blogs.index')" 
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group relative"
              :class="route().current('blogs.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'hover:bg-slate-800/60 text-slate-400 hover:text-white'"
              :title="sidebarCollapsed ? 'Blogs' : ''"
            >
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
              <span v-show="!sidebarCollapsed" class="truncate">Blogs</span>
            </Link>
            <Link 
              :href="route('gallery.index')" 
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group relative"
              :class="route().current('gallery.*') || route().current('gallery-categories.*') || route().current('gallery-items.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'hover:bg-slate-800/60 text-slate-400 hover:text-white'"
              :title="sidebarCollapsed ? 'Gallery' : ''"
            >
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <span v-show="!sidebarCollapsed" class="truncate">Gallery</span>
            </Link>
            <Link 
              :href="route('admin.hero-sliders.index')" 
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group relative"
              :class="route().current('admin.hero-sliders.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'hover:bg-slate-800/60 text-slate-400 hover:text-white'"
              :title="sidebarCollapsed ? 'Hero Sliders' : ''"
            >
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
              <span v-show="!sidebarCollapsed" class="truncate">Hero Sliders</span>
            </Link>
            <Link 
              :href="route('admin.contact-messages.index')" 
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group relative"
              :class="route().current('admin.contact-messages.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'hover:bg-slate-800/60 text-slate-400 hover:text-white'"
              :title="sidebarCollapsed ? 'Contact Messages' : ''"
            >
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              <span v-show="!sidebarCollapsed" class="truncate">Contact Messages</span>
            </Link>
          </div>
        </div>

        <!-- Section: System & Tools -->
        <div>
          <p v-show="!sidebarCollapsed" class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">System & Marketing</p>
          <div class="space-y-1">
            <Link 
              :href="route('emailmarketing.index')" 
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors group relative"
              :class="route().current('emailmarketing.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'hover:bg-slate-800/60 text-slate-400 hover:text-white'"
              :title="sidebarCollapsed ? 'Email Marketing' : ''"
            >
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              <span v-show="!sidebarCollapsed" class="truncate">Email Marketing</span>
            </Link>
          </div>
        </div>

      </nav>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0">
      
      <!-- Top Navbar -->
      <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 sticky top-0 z-30 shadow-xs">
        <div class="flex items-center gap-4">
          <button @click="sidebarOpen = true" class="text-slate-500 hover:text-slate-700 lg:hidden focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
          </button>
          <h1 class="text-lg font-semibold text-slate-800">
            <slot name="header">Dashboard Overview</slot>
          </h1>
        </div>

        <!-- User Profile Dropdown Menu -->
        <div class="flex items-center gap-4 relative">
          
          <!-- Click Outside Overlay -->
          <div v-if="profileDropdownOpen" @click="profileDropdownOpen = false" class="fixed inset-0 z-40"></div>

          <!-- Profile Trigger Button -->
          <button 
            @click="profileDropdownOpen = !profileDropdownOpen" 
            class="flex items-center gap-3 pl-4 border-l border-slate-200 focus:outline-none group py-1"
          >
            <div class="w-9 h-9 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-sm shadow-inner group-hover:bg-teal-200 transition-colors">
              {{ user?.name ? user.name.charAt(0) : 'A' }}
            </div>
            <div class="hidden md:block text-left">
              <p class="text-sm font-medium text-slate-800 leading-tight group-hover:text-teal-600 transition-colors">{{ user?.name || 'Administrator' }}</p>
              <p class="text-xs text-slate-500 leading-tight">System Admin</p>
            </div>
            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-transform" :class="{ 'rotate-180': profileDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- Dropdown Popup -->
          <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
          >
            <div v-if="profileDropdownOpen" class="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 z-50">
              <div class="px-4 py-2.5 border-b border-slate-100 md:hidden">
                <p class="text-sm font-medium text-slate-800">{{ user?.name || 'Administrator' }}</p>
                <p class="text-xs text-slate-500 truncate">{{ user?.email || 'admin@amzhealth.com' }}</p>
              </div>

              <!-- Profile Options -->
              <Link 
                :href="route('profile.edit')" 
                @click="profileDropdownOpen = false"
                class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-teal-600 transition-colors"
              >
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profile Settings
              </Link>

              <div class="h-px bg-slate-100 my-1"></div>

              <!-- Logout Option -->
              <button 
                @click="logout; profileDropdownOpen = false" 
                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition-colors text-left"
              >
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Log Out
              </button>
            </div>
          </transition>

        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-6 lg:p-8 overflow-y-auto">
        <slot />
      </main>

    </div>
  </div>
</template>