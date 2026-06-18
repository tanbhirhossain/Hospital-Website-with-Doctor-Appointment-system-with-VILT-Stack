<script setup>

</script>

<template>
    <!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aura Dashboard - Next-Gen Admin UI</title>
  
  <!-- Tailwind CSS v4 Browser Compiler -->
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- Inter & Plus Jakarta Sans Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Vue 3 Global -->
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  
  <!-- Chart.js for Premium Visualizations -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    h1, h2, h3, h4, h5, h6, .font-heading {
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    /* Smooth Scrollbar design */
    ::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    ::-webkit-scrollbar-track {
      background: transparent;
    }
    ::-webkit-scrollbar-thumb {
      background: rgba(156, 163, 175, 0.3);
      border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: rgba(156, 163, 175, 0.5);
    }
  </style>
</head>
<div class="h-full bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-300">

  <div id="app" class="flex h-full overflow-hidden" v-cloak>
    
    <!-- Sidebar Left: Navigation & Branding -->
    <aside 
      :class="[
        'fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-slate-900 text-slate-200 transition-transform duration-300 transform border-r border-slate-800 lg:translate-x-0 lg:static lg:inset-auto',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <!-- Brand Logo Header -->
      <div class="flex items-center justify-between h-16 px-6 border-b border-slate-800">
        <div class="flex items-center gap-3">
          <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/20">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <span class="text-lg font-bold tracking-tight text-white font-heading">Aura <span class="text-indigo-400">OS</span></span>
        </div>
        <!-- Mobile close button -->
        <button @click="sidebarOpen = false" class="p-1 rounded-lg hover:bg-slate-800 lg:hidden focus:outline-none">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Navigation Links -->
      <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
        <div class="px-3 mb-2 text-xs font-semibold tracking-wider text-slate-500 uppercase">Core Management</div>
        
        <button 
          v-for="item in navItems" 
          :key="item.id"
          @click="setActiveTab(item.id)"
          :class="[
            'flex items-center justify-between w-full px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group',
            activeTab === item.id 
              ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' 
              : 'hover:bg-slate-800/60 text-slate-400 hover:text-slate-100'
          ]"
        >
          <div class="flex items-center gap-3">
            <i :data-lucide="item.icon" class="w-4.5 h-4.5"></i>
            <span>{{ item.name }}</span>
          </div>
          <span 
            v-if="item.badge" 
            :class="[
              'px-2 py-0.5 text-2xs font-bold rounded-full', 
              activeTab === item.id ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-800 text-slate-300'
            ]"
          >
            {{ item.badge }}
          </span>
        </button>

        <div class="pt-6 px-3 mb-2 text-xs font-semibold tracking-wider text-slate-500 uppercase">Support & Intelligence</div>

        <button 
          @click="setActiveTab('ai-copilot')"
          :class="[
            'flex items-center justify-between w-full px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group relative overflow-hidden',
            activeTab === 'ai-copilot' 
              ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md' 
              : 'hover:bg-slate-800/60 text-slate-400 hover:text-slate-100'
          ]"
        >
          <div class="flex items-center gap-3 z-10">
            <i data-lucide="sparkles" class="w-4.5 h-4.5 text-amber-400"></i>
            <span>AI Copilot</span>
          </div>
          <span class="px-1.5 py-0.5 text-[10px] font-semibold bg-amber-400 text-slate-900 rounded-md uppercase tracking-wider scale-90 z-10">Beta</span>
          <!-- Soft ambient glow on hover -->
          <span class="absolute inset-0 bg-gradient-to-r from-indigo-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity"></span>
        </button>
      </nav>

      <!-- User Context Bar in Sidebar (Bottom) -->
      <div class="p-4 border-t border-slate-800 bg-slate-900/50">
        <div class="flex items-center gap-3">
          <div class="relative">
            <img class="w-10 h-10 rounded-xl object-cover ring-2 ring-slate-800" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=256" alt="Avatar">
            <span class="absolute bottom-0 right-0 block w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-slate-900"></span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-white truncate font-heading">Sarah Jenkins</p>
            <p class="text-xs text-slate-500 truncate">Senior UI/UX Designer</p>
          </div>
          <button @click="openToast('Profile settings loaded', 'info')" class="p-1 text-slate-400 hover:text-slate-100 hover:bg-slate-800 rounded-lg transition-colors">
            <i data-lucide="settings-2" class="w-4 h-4"></i>
          </button>
        </div>
      </div>
    </aside>

    <!-- Overlay backdrop for mobile sidebars -->
    <div 
      v-if="sidebarOpen" 
      @click="sidebarOpen = false" 
      class="fixed inset-0 z-40 bg-slate-950/40 backdrop-blur-xs lg:hidden"
    ></div>

    <!-- Right Side: Header + Main View Area -->
    <div class="flex flex-col flex-1 min-w-0 overflow-y-auto">
      
      <!-- Top Bar Header -->
      <header class="flex items-center justify-between h-16 px-6 border-b bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800/80 sticky top-0 z-30 transition-colors duration-200">
        <div class="flex items-center gap-4">
          <!-- Mobile Menu Toggle Button -->
          <button 
            @click="sidebarOpen = !sidebarOpen" 
            class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl lg:hidden focus:outline-none"
          >
            <i data-lucide="menu" class="w-5 h-5"></i>
          </button>

          <!-- Search Widget -->
          <div class="relative w-64 md:w-80 hidden sm:block">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
              <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
            </div>
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Search documents, components..." 
              class="w-full pl-9 pr-4 py-1.5 text-xs bg-slate-50 dark:bg-slate-950/80 text-slate-800 dark:text-slate-200 placeholder-slate-400 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
            >
          </div>
        </div>

        <div class="flex items-center gap-3">
          <!-- Live Status Indicator -->
          <div class="hidden md:flex items-center gap-2 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900/50 rounded-full">
            <span class="flex w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-2xs font-medium text-emerald-700 dark:text-emerald-400">Production Node: Active</span>
          </div>

          <!-- Theme Toggle Dark/Light -->
          <button 
            @click="toggleTheme" 
            class="p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-all"
            title="Toggle Theme"
          >
            <i v-if="darkMode" data-lucide="sun" class="w-4.5 h-4.5 text-amber-400"></i>
            <i v-else data-lucide="moon" class="w-4.5 h-4.5 text-indigo-500"></i>
          </button>

          <!-- Notification Dropdown Toggle -->
          <div class="relative">
            <button 
              @click="notifMenuOpen = !notifMenuOpen" 
              class="relative p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-all"
            >
              <i data-lucide="bell" class="w-4.5 h-4.5"></i>
              <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 border-2 border-white dark:border-slate-900 rounded-full"></span>
            </button>

            <!-- Notifications Drawer -->
            <div 
              v-if="notifMenuOpen" 
              class="absolute right-0 mt-3 w-80 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-xl z-50 py-2 overflow-hidden"
            >
              <div class="px-4 py-2 border-b border-slate-50 dark:border-slate-800/60 flex justify-between items-center">
                <span class="font-bold text-xs font-heading">Updates & Alerts</span>
                <button @click="notifications = []; openToast('Cleared notifications', 'success')" class="text-3xs text-slate-400 hover:text-indigo-500 uppercase tracking-wider">Clear All</button>
              </div>
              <div class="max-h-64 overflow-y-auto">
                <div v-for="notif in notifications" :key="notif.id" class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 flex gap-3 border-b border-slate-50 dark:border-slate-850 last:border-b-0 transition-colors">
                  <div :class="['w-8 h-8 rounded-lg flex items-center justify-center shrink-0', notif.bg]">
                    <i :data-lucide="notif.icon" :class="['w-4 h-4', notif.color]"></i>
                  </div>
                  <div>
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ notif.title }}</p>
                    <p class="text-3xs text-slate-400 mt-0.5">{{ notif.time }}</p>
                  </div>
                </div>
                <div v-if="notifications.length === 0" class="py-8 text-center text-xs text-slate-400">
                  All caught up! 🎉
                </div>
              </div>
            </div>
          </div>

          <!-- User Quick Menu -->
          <div class="relative">
            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 p-1.5 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-all">
              <img class="w-7 h-7 rounded-lg object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=256" alt="Avatar">
              <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
            </button>
            <div v-if="userMenuOpen" class="absolute right-0 mt-3 w-48 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-xl shadow-xl z-50 py-1">
              <a href="#" @click.prevent="openToast('My Account Settings loaded', 'success')" class="block px-4 py-2 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60">My Profile</a>
              <a href="#" @click.prevent="openToast('Usage Analytics loaded', 'info')" class="block px-4 py-2 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60">Billing</a>
              <hr class="border-slate-100 dark:border-slate-850 my-1">
              <a href="#" @click.prevent="openToast('Terminating session safely...', 'warning')" class="block px-4 py-2 text-xs text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/25">Sign Out</a>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Action Canvas -->
      <main class="flex-1 p-6 space-y-6 max-w-7xl w-full mx-auto">
        
        <!-- Breadcrumbs Banner Dynamic Header -->
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
          <div>
            <div class="flex items-center gap-2 text-xs text-slate-400">
              <span>Workspace</span>
              <i data-lucide="chevron-right" class="w-3 h-3"></i>
              <span class="capitalize text-slate-600 dark:text-slate-300 font-medium">{{ activeTab }}</span>
            </div>
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight mt-1 font-heading text-slate-900 dark:text-white">
              {{ tabTitle }}
            </h1>
          </div>
          
          <!-- View Specific Fast Actions -->
          <div class="flex items-center gap-2 self-start md:self-auto">
            <button @click="refreshMockData" class="flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-xl transition-all">
              <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
              <span>Synch Live</span>
            </button>
            <button v-if="activeTab === 'users'" @click="showAddUserModal = true" class="flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/10 rounded-xl transition-all">
              <i data-lucide="plus" class="w-3.5 h-3.5"></i>
              <span>Add Member</span>
            </button>
            <button v-if="activeTab === 'overview' || activeTab === 'analytics'" @click="exportCSV" class="flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-850 dark:bg-slate-800 dark:hover:bg-slate-700/80 rounded-xl transition-all">
              <i data-lucide="download" class="w-3.5 h-3.5"></i>
              <span>Export CSV</span>
            </button>
          </div>
        </div>

        <!-- TAB CONTENT: OVERVIEW -->
        <div v-if="activeTab === 'overview'" class="space-y-6">
          
          <!-- Modern Stat Card Matrix Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div v-for="stat in metrics" :key="stat.title" class="p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl flex flex-col justify-between shadow-xs relative overflow-hidden transition-all hover:translate-y-[-2px] hover:shadow-md">
              <div class="flex items-start justify-between">
                <div>
                  <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">{{ stat.title }}</span>
                  <p class="text-2xl font-extrabold font-heading text-slate-900 dark:text-white mt-1">{{ stat.value }}</p>
                </div>
                <div :class="['p-2 rounded-xl', stat.iconBg]">
                  <i :data-lucide="stat.icon" :class="['w-5 h-5', stat.iconColor]"></i>
                </div>
              </div>
              <div class="flex items-center gap-2 mt-4 text-xs">
                <span :class="['flex items-center gap-0.5 font-bold', stat.trendUp ? 'text-emerald-500' : 'text-rose-500']">
                  <i :data-lucide="stat.trendUp ? 'arrow-up-right' : 'arrow-down-left'" class="w-3.5 h-3.5"></i>
                  {{ stat.change }}
                </span>
                <span class="text-slate-400">vs last month</span>
              </div>
            </div>

          </div>

          <!-- Analytics Dynamic Graph Dashboard Panel -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Major Linear Chart Canvas Card -->
            <div class="lg:col-span-2 p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-xs">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h3 class="text-sm font-extrabold font-heading">Transactional Flux</h3>
                  <p class="text-2xs text-slate-400">Total processed revenue & conversion funnels</p>
                </div>
                <div class="flex gap-1.5 p-1 bg-slate-50 dark:bg-slate-950/80 rounded-xl border border-slate-100 dark:border-slate-800">
                  <button @click="setOverviewRange('weekly')" :class="['px-2.5 py-1 text-3xs font-bold uppercase tracking-wider rounded-lg transition-all', overviewRange === 'weekly' ? 'bg-white dark:bg-slate-900 text-indigo-600 shadow-sm' : 'text-slate-400 hover:text-slate-800 dark:hover:text-slate-200']">Weekly</button>
                  <button @click="setOverviewRange('monthly')" :class="['px-2.5 py-1 text-3xs font-bold uppercase tracking-wider rounded-lg transition-all', overviewRange === 'monthly' ? 'bg-white dark:bg-slate-900 text-indigo-600 shadow-sm' : 'text-slate-400 hover:text-slate-800 dark:hover:text-slate-200']">Monthly</button>
                </div>
              </div>
              <div class="h-64 relative">
                <canvas id="overviewLineChart"></canvas>
              </div>
            </div>

            <!-- Side Pie/Doughnut Performance Share Card -->
            <div class="p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-xs flex flex-col justify-between">
              <div>
                <h3 class="text-sm font-extrabold font-heading">Channel Attribution</h3>
                <p class="text-2xs text-slate-400 mb-4">Conversions across networks & referrers</p>
                <div class="h-48 relative flex items-center justify-center">
                  <canvas id="attributionPieChart"></canvas>
                </div>
              </div>
              <div class="grid grid-cols-3 gap-2 text-center pt-4 border-t border-slate-100 dark:border-slate-800/80 mt-2">
                <div>
                  <span class="text-3xs text-slate-400 uppercase tracking-wider">Direct</span>
                  <p class="text-xs font-bold text-slate-800 dark:text-slate-200">54.2%</p>
                </div>
                <div>
                  <span class="text-3xs text-slate-400 uppercase tracking-wider">Organic</span>
                  <p class="text-xs font-bold text-slate-800 dark:text-slate-200">28.4%</p>
                </div>
                <div>
                  <span class="text-3xs text-slate-400 uppercase tracking-wider">Social</span>
                  <p class="text-xs font-bold text-slate-800 dark:text-slate-200">17.4%</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Area: Live Transactions / Activities & Tasks -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Interactive Action Items & Datatable List -->
            <div class="lg:col-span-2 p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-xs">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h3 class="text-sm font-extrabold font-heading">Recent Payments</h3>
                  <p class="text-2xs text-slate-400">Audit trail of transactions in the past 24 hrs</p>
                </div>
                <button @click="setActiveTab('transactions')" class="text-xs font-semibold text-indigo-500 hover:text-indigo-600 flex items-center gap-1">
                  <span>View All</span>
                  <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </button>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                  <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800/80 text-slate-400 text-3xs font-bold uppercase tracking-wider">
                      <th class="py-2">Beneficiary / Ref</th>
                      <th class="py-2">Timestamp</th>
                      <th class="py-2 text-right">Volume</th>
                      <th class="py-2 text-right">Fulfillment</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-50 dark:divide-slate-850">
                    <tr v-for="tx in latestTransactions" :key="tx.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                      <td class="py-3.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800/60 flex items-center justify-center font-bold text-xs text-indigo-600 dark:text-indigo-400">
                          {{ tx.initials }}
                        </div>
                        <div>
                          <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ tx.customer }}</p>
                          <p class="text-[10px] text-slate-400">{{ tx.id }}</p>
                        </div>
                      </td>
                      <td class="py-3.5 text-2xs text-slate-500 dark:text-slate-400">
                        {{ tx.time }}
                      </td>
                      <td class="py-3.5 text-2xs text-right font-bold text-slate-800 dark:text-slate-200">
                        {{ tx.amount }}
                      </td>
                      <td class="py-3.5 text-right">
                        <span :class="['px-2 py-0.5 text-3xs font-extrabold uppercase tracking-wide rounded-full', tx.statusBg, tx.statusText]">
                          {{ tx.status }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Right: Quick To-Do Task Board for Design Teams -->
            <div class="p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-xs flex flex-col justify-between">
              <div>
                <h3 class="text-sm font-extrabold font-heading">Production Road</h3>
                <p class="text-2xs text-slate-400 mb-4">Milestones & design review cycles</p>
                <div class="space-y-3">
                  <div v-for="task in sprintTasks" :key="task.id" class="p-3 bg-slate-50 dark:bg-slate-950/80 border border-slate-100 dark:border-slate-850 rounded-xl relative group">
                    <div class="flex items-start justify-between gap-2">
                      <div class="flex items-center gap-2">
                        <input type="checkbox" v-model="task.completed" @change="toggleTask(task)" class="w-3.5 h-3.5 text-indigo-600 rounded border-slate-300 dark:border-slate-800 focus:ring-indigo-500">
                        <span :class="['text-2xs font-semibold', task.completed ? 'line-through text-slate-400' : 'text-slate-700 dark:text-slate-200']">{{ task.title }}</span>
                      </div>
                      <span :class="['px-1.5 py-0.5 text-[10px] font-bold uppercase rounded-md', task.priorityBg, task.priorityText]">{{ task.priority }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-2.5 text-3xs text-slate-400">
                      <span>Owner: {{ task.owner }}</span>
                      <span>ETA: {{ task.eta }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <button @click="setActiveTab('tasks')" class="mt-4 w-full py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-2xs font-bold uppercase tracking-wider rounded-xl transition-all">Go to Taskboard</button>
            </div>
          </div>

        </div>

        <!-- TAB CONTENT: USERS -->
        <div v-if="activeTab === 'users'" class="space-y-6">
          <div class="p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-xs">
            
            <!-- Controls Table Row -->
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between mb-6">
              <div class="relative w-full md:w-80">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                </div>
                <input 
                  v-model="userSearchQuery" 
                  type="text" 
                  placeholder="Search members by name, email..." 
                  class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 dark:bg-slate-950/80 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
              </div>

              <div class="flex flex-wrap gap-2 w-full md:w-auto">
                <select v-model="userFilterRole" class="px-3 py-2 text-xs bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                  <option value="all">All Roles</option>
                  <option value="Admin">Admin</option>
                  <option value="Designer">Designer</option>
                  <option value="Developer">Developer</option>
                  <option value="Analyst">Analyst</option>
                </select>

                <select v-model="userFilterStatus" class="px-3 py-2 text-xs bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                  <option value="all">All Statuses</option>
                  <option value="Active">Active</option>
                  <option value="Away">Away</option>
                  <option value="Suspended">Suspended</option>
                </select>
              </div>
            </div>

            <!-- Team Members Datatable -->
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="border-b border-slate-100 dark:border-slate-800/80 text-slate-400 text-3xs font-bold uppercase tracking-wider">
                    <th class="py-3">Member Details</th>
                    <th class="py-3">Functional Role</th>
                    <th class="py-3">Fulfillment Status</th>
                    <th class="py-3">Sign in Epoch</th>
                    <th class="py-3 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                  <tr v-for="user in filteredUsers" :key="user.email" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="py-3 flex items-center gap-3">
                      <img class="w-10 h-10 rounded-xl object-cover" :src="user.avatar" alt="Avatar">
                      <div>
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ user.name }}</p>
                        <p class="text-[10px] text-slate-400">{{ user.email }}</p>
                      </div>
                    </td>
                    <td class="py-3 text-xs text-slate-600 dark:text-slate-300">
                      {{ user.role }}
                    </td>
                    <td class="py-3">
                      <span :class="['px-2.5 py-0.5 text-3xs font-extrabold uppercase rounded-full', user.statusClass]">
                        {{ user.status }}
                      </span>
                    </td>
                    <td class="py-3 text-2xs text-slate-400">
                      {{ user.lastActive }}
                    </td>
                    <td class="py-3 text-right">
                      <div class="flex items-center justify-end gap-1.5">
                        <button @click="editUser(user)" class="p-1.5 text-slate-400 hover:text-indigo-500 rounded-lg transition-colors">
                          <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        <button @click="deleteUser(user)" class="p-1.5 text-slate-400 hover:text-rose-500 rounded-lg transition-colors">
                          <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="filteredUsers.length === 0">
                    <td colspan="5" class="py-12 text-center text-xs text-slate-400">
                      No matching team members found.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>

        <!-- TAB CONTENT: TRANSACTIONS -->
        <div v-if="activeTab === 'transactions'" class="space-y-6">
          <div class="p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-xs">
            <!-- Full Audit Log filters -->
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-sm font-extrabold font-heading">Ledger Transactions</h3>
              <span class="text-2xs text-slate-400">{{ transactionsList.length }} ledger entries documented</span>
            </div>
            
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="border-b border-slate-100 dark:border-slate-800/80 text-slate-400 text-3xs font-bold uppercase tracking-wider">
                    <th class="py-3">Client</th>
                    <th class="py-3">Transaction ID</th>
                    <th class="py-3">Billing System</th>
                    <th class="py-3">Timestamp</th>
                    <th class="py-3 text-right">Volume</th>
                    <th class="py-3 text-right">Fulfillment</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                  <tr v-for="tx in transactionsList" :key="tx.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="py-3.5 flex items-center gap-3">
                      <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800/60 flex items-center justify-center font-bold text-xs text-indigo-600 dark:text-indigo-400">
                        {{ tx.initials }}
                      </div>
                      <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ tx.customer }}</p>
                    </td>
                    <td class="py-3.5 text-2xs font-mono text-slate-400">
                      {{ tx.id }}
                    </td>
                    <td class="py-3.5 text-2xs text-slate-600 dark:text-slate-300">
                      {{ tx.method }}
                    </td>
                    <td class="py-3.5 text-2xs text-slate-500">
                      {{ tx.time }}
                    </td>
                    <td class="py-3.5 text-2xs text-right font-extrabold text-slate-800 dark:text-slate-200">
                      {{ tx.amount }}
                    </td>
                    <td class="py-3.5 text-right">
                      <span :class="['px-2 py-0.5 text-3xs font-extrabold uppercase rounded-full', tx.statusBg, tx.statusText]">
                        {{ tx.status }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>

        <!-- TAB CONTENT: TASKS -->
        <div v-if="activeTab === 'tasks'" class="space-y-6">
          <!-- Kanban Grid -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Target Status columns -->
            <div v-for="col in kanbanColumns" :key="col.name" class="flex flex-col h-full min-h-[500px]">
              <div class="flex items-center justify-between px-3 py-2 bg-slate-100 dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/80 rounded-xl mb-4">
                <span class="text-xs font-bold font-heading text-slate-700 dark:text-slate-200 flex items-center gap-2">
                  <span :class="['w-2 h-2 rounded-full', col.color]"></span>
                  {{ col.name }}
                </span>
                <span class="text-3xs font-extrabold text-slate-400 bg-slate-200 dark:bg-slate-800 px-2 py-0.5 rounded-full">
                  {{ getTasksByColumn(col.id).length }}
                </span>
              </div>

              <!-- Task cards container -->
              <div class="flex-1 space-y-3 p-2 bg-slate-100/50 dark:bg-slate-900/20 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800/60">
                <div 
                  v-for="task in getTasksByColumn(col.id)" 
                  :key="task.id" 
                  class="p-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-xl shadow-xs group cursor-pointer hover:shadow-md transition-all relative overflow-hidden"
                >
                  <div class="absolute left-0 top-0 bottom-0 w-1" :class="col.color"></div>
                  <div class="flex justify-between items-start gap-2">
                    <span :class="['px-1.5 py-0.5 text-[10px] font-bold uppercase rounded-md', task.priorityBg, task.priorityText]">{{ task.priority }}</span>
                    
                    <!-- Quick action controls -->
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                      <button @click="moveTask(task, 'back')" v-if="col.id !== 'todo'" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                      </button>
                      <button @click="moveTask(task, 'forward')" v-if="col.id !== 'done'" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                      </button>
                    </div>
                  </div>
                  <h4 class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-2.5">{{ task.title }}</h4>
                  <p class="text-[10px] text-slate-400 mt-1 line-clamp-2">Sprint tasks and assets assigned for designer synchronization.</p>
                  
                  <div class="flex justify-between items-center mt-4 pt-2.5 border-t border-slate-50 dark:border-slate-850 text-3xs text-slate-400">
                    <div class="flex items-center gap-1.5">
                      <div class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-[8px] font-extrabold text-slate-600 dark:text-slate-300">
                        {{ task.owner.slice(0, 2).toUpperCase() }}
                      </div>
                      <span>{{ task.owner }}</span>
                    </div>
                    <span>ETA: {{ task.eta }}</span>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- TAB CONTENT: ANALYTICS -->
        <div v-if="activeTab === 'analytics'" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Full Width Graph Panel -->
            <div class="md:col-span-2 p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-xs">
              <h3 class="text-sm font-extrabold font-heading mb-4">Traffic Volume Sources</h3>
              <div class="h-80 relative">
                <canvas id="analyticsBarChart"></canvas>
              </div>
            </div>

            <!-- Side Card with Metric Breakdown -->
            <div class="p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-xs flex flex-col justify-between">
              <div>
                <h3 class="text-sm font-extrabold font-heading mb-4">Conversion Funnel</h3>
                <div class="space-y-4">
                  <div>
                    <div class="flex justify-between text-2xs mb-1 font-semibold">
                      <span>1. Page Impressions</span>
                      <span>100% (142.5k)</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                      <div class="bg-indigo-600 h-full w-full"></div>
                    </div>
                  </div>
                  <div>
                    <div class="flex justify-between text-2xs mb-1 font-semibold">
                      <span>2. Product Clicks</span>
                      <span>42.3% (60.2k)</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                      <div class="bg-indigo-500 h-full w-[42.3%]"></div>
                    </div>
                  </div>
                  <div>
                    <div class="flex justify-between text-2xs mb-1 font-semibold">
                      <span>3. Shopping Cart Add</span>
                      <span>12.8% (18.2k)</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                      <div class="bg-purple-500 h-full w-[12.8%]"></div>
                    </div>
                  </div>
                  <div>
                    <div class="flex justify-between text-2xs mb-1 font-semibold">
                      <span>4. Checkouts Completed</span>
                      <span>2.9% (4.13k)</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                      <div class="bg-emerald-500 h-full w-[2.9%]"></div>
                    </div>
                  </div>
                </div>
              </div>
              <p class="text-3xs text-slate-400 mt-6 leading-relaxed">
                Funnel optimization indices represent a premium flow through digital properties. Overall conversion rate rose +0.24% this quarter.
              </p>
            </div>
          </div>
        </div>

        <!-- TAB CONTENT: AI COPILOT -->
        <div v-if="activeTab === 'ai-copilot'" class="space-y-6">
          <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            <!-- Left Copilot Config panel -->
            <div class="lg:col-span-1 p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-xs flex flex-col justify-between">
              <div>
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-8 h-8 rounded-lg bg-amber-400/10 flex items-center justify-center">
                    <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                  </div>
                  <h3 class="text-sm font-extrabold font-heading">Aura Insights</h3>
                </div>
                <p class="text-2xs text-slate-400 leading-relaxed mb-6">
                  Aura AI utilizes Google Gemini 2.5 architecture to parse dashboard metrics, compile reports, and draft executive briefings.
                </p>

                <div class="space-y-2">
                  <span class="text-3xs font-extrabold text-slate-400 uppercase tracking-wider block">Prompt Blueprints</span>
                  <button 
                    v-for="preset in aiPresets" 
                    :key="preset" 
                    @click="applyPreset(preset)"
                    class="w-full text-left p-2.5 bg-slate-50 hover:bg-slate-100 dark:bg-slate-950/80 dark:hover:bg-slate-800/60 border border-slate-200/50 dark:border-slate-800 text-2xs rounded-xl font-medium transition-colors"
                  >
                    {{ preset }}
                  </button>
                </div>
              </div>

              <div class="pt-6 border-t border-slate-100 dark:border-slate-800/80 mt-6">
                <span class="text-3xs text-slate-400 block uppercase tracking-wider mb-2">Connected Engine</span>
                <div class="flex items-center gap-2">
                  <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></span>
                  <span class="text-2xs font-mono font-semibold">gemini-2.5-flash</span>
                </div>
              </div>
            </div>

            <!-- Right interactive Chat workspace panel -->
            <div class="lg:col-span-3 p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-xs flex flex-col h-[550px]">
              
              <!-- Messages container -->
              <div class="flex-1 overflow-y-auto space-y-4 mb-4 pr-1" ref="chatScrollContainer">
                
                <!-- Initial Copilot intro msg -->
                <div class="flex gap-3">
                  <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center justify-center shrink-0">
                    <i data-lucide="bot" class="w-4 h-4 text-white"></i>
                  </div>
                  <div class="p-3.5 bg-slate-50 dark:bg-slate-950/60 rounded-2xl rounded-tl-xs max-w-xl">
                    <p class="text-xs font-semibold">Salutations, Sarah Jenkins!</p>
                    <p class="text-2xs text-slate-400 mt-1 leading-relaxed">
                      I've synchronized with your sales databases and active sprint cycles. You can query transactional histories, or ask me to draft a comprehensive Q2 Revenue and Conversion summary. How can I assist?
                    </p>
                  </div>
                </div>

                <!-- Conversational list -->
                <div 
                  v-for="msg in aiConversations" 
                  :key="msg.id" 
                  :class="['flex gap-3', msg.role === 'user' ? 'justify-end' : '']"
                >
                  <div v-if="msg.role !== 'user'" class="w-8 h-8 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center justify-center shrink-0">
                    <i data-lucide="bot" class="w-4 h-4 text-white"></i>
                  </div>

                  <div :class="[
                    'p-3.5 rounded-2xl max-w-xl text-xs', 
                    msg.role === 'user' 
                      ? 'bg-indigo-600 text-white rounded-tr-xs' 
                      : 'bg-slate-50 dark:bg-slate-950/60 rounded-tl-xs'
                  ]">
                    <div class="whitespace-pre-wrap leading-relaxed">{{ msg.content }}</div>
                  </div>

                  <div v-if="msg.role === 'user'" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                    <i data-lucide="user" class="w-4 h-4 text-slate-500"></i>
                  </div>
                </div>

                <!-- Chat dynamic typing/query loader -->
                <div v-if="aiThinking" class="flex gap-3">
                  <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center justify-center shrink-0">
                    <i data-lucide="bot" class="w-4 h-4 text-white animate-bounce"></i>
                  </div>
                  <div class="p-3.5 bg-slate-50 dark:bg-slate-950/60 rounded-2xl rounded-tl-xs">
                    <div class="flex items-center gap-1">
                      <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></span>
                      <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                      <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                    </div>
                  </div>
                </div>

              </div>

              <!-- Typing control console -->
              <div class="flex items-center gap-2 pt-4 border-t border-slate-100 dark:border-slate-800/80">
                <input 
                  v-model="aiPromptInput" 
                  @keyup.enter="dispatchAICopilot"
                  type="text" 
                  placeholder="Ask Gemini to draft Q2 Revenue report..." 
                  class="flex-1 px-4 py-2.5 text-xs bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                  :disabled="aiThinking"
                >
                <button 
                  @click="dispatchAICopilot"
                  class="p-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-lg shadow-indigo-600/10 transition-all cursor-pointer"
                  :disabled="aiThinking"
                >
                  <i data-lucide="send" class="w-4.5 h-4.5"></i>
                </button>
              </div>

            </div>

          </div>
        </div>

        <!-- TAB CONTENT: SETTINGS -->
        <div v-if="activeTab === 'settings'" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Left panel -->
            <div class="p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-xs">
              <h3 class="text-sm font-extrabold font-heading mb-4">Account Preferences</h3>
              <div class="space-y-4">
                <div>
                  <label class="block text-3xs font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Profile Picture</label>
                  <div class="flex items-center gap-4">
                    <img class="w-12 h-12 rounded-xl object-cover ring-2 ring-indigo-500/10" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=256" alt="Avatar">
                    <button @click="openToast('Avatar update modal launched', 'info')" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 text-2xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700">Change Photo</button>
                  </div>
                </div>

                <div>
                  <label class="block text-3xs font-extrabold text-slate-400 uppercase tracking-wider mb-1">Company Entity</label>
                  <input type="text" value="Aura Labs Inc." class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none" readonly>
                </div>
              </div>
            </div>

            <!-- Right configuration settings panel -->
            <div class="md:col-span-2 p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-2xl shadow-xs space-y-6">
              
              <div>
                <h3 class="text-sm font-extrabold font-heading mb-3">Global System Variables</h3>
                <p class="text-2xs text-slate-400 mb-4">Modify system notification variables and webhook sync cycles.</p>
                
                <div class="space-y-3.5">
                  <div class="flex items-center justify-between p-3.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200/50 dark:border-slate-800/80 rounded-xl">
                    <div>
                      <h4 class="text-2xs font-bold">Email Digest Notifications</h4>
                      <p class="text-[10px] text-slate-400">Receive transactional rollups and analytics every Monday morning.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input type="checkbox" v-model="settingsToggles.emailDigest" class="sr-only peer">
                      <div class="w-9 h-5 bg-slate-200 dark:bg-slate-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                  </div>

                  <div class="flex items-center justify-between p-3.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200/50 dark:border-slate-800/80 rounded-xl">
                    <div>
                      <h4 class="text-2xs font-bold">Automatic Error Dispatch</h4>
                      <p class="text-[10px] text-slate-400">Forward all production and latency exceptions to Slack channels instantly.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input type="checkbox" v-model="settingsToggles.errorLogs" class="sr-only peer">
                      <div class="w-9 h-5 bg-slate-200 dark:bg-slate-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                  </div>

                  <div class="flex items-center justify-between p-3.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200/50 dark:border-slate-800/80 rounded-xl">
                    <div>
                      <h4 class="text-2xs font-bold">Gemini API Integrations</h4>
                      <p class="text-[10px] text-slate-400">Grant permission for conversational tools to query financial data.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input type="checkbox" v-model="settingsToggles.apiSync" class="sr-only peer">
                      <div class="w-9 h-5 bg-slate-200 dark:bg-slate-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                  </div>
                </div>
              </div>

              <div class="pt-4 flex justify-end gap-2">
                <button @click="openToast('System settings reset to defaults', 'warning')" class="px-3.5 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/80 rounded-xl transition-all">Reset</button>
                <button @click="openToast('Configuration synced to secure cloud', 'success')" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all">Save Config</button>
              </div>

            </div>
          </div>
        </div>

      </main>

      <!-- Dynamic Interactive Footer -->
      <footer class="mt-auto px-6 py-4 border-t bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800/80 text-3xs text-slate-400 flex flex-col md:flex-row items-center justify-between gap-3">
        <p>&copy; 2026 Aura OS Admin. Engineered under Vue 3 + Tailwind v4.</p>
        <div class="flex items-center gap-4">
          <a href="#" @click.prevent="openToast('Displaying compliance manuals', 'info')" class="hover:text-slate-600 dark:hover:text-slate-300">Compliance & Security</a>
          <a href="#" @click.prevent="openToast('Displaying system health specs', 'success')" class="hover:text-slate-600 dark:hover:text-slate-300">API Status</a>
        </div>
      </footer>

    </div>

    <!-- MODAL: ADD / EDIT USER DIALOG -->
    <div v-if="showAddUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800/80 flex justify-between items-center">
          <h3 class="text-sm font-bold font-heading">{{ editingUser ? 'Edit Team Member' : 'Register New Member' }}</h3>
          <button @click="closeUserModal" class="p-1 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600">
            <i data-lucide="x" class="w-4 h-4"></i>
          </button>
        </div>
        <form @submit.prevent="saveUser" class="p-5 space-y-4">
          <div>
            <label class="block text-3xs font-extrabold text-slate-400 uppercase tracking-wider mb-1">Full Legal Name</label>
            <input 
              v-model="userForm.name" 
              type="text" 
              required
              placeholder="e.g. Liam Vance" 
              class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-950/80 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
          </div>
          <div>
            <label class="block text-3xs font-extrabold text-slate-400 uppercase tracking-wider mb-1">Functional Email</label>
            <input 
              v-model="userForm.email" 
              type="email" 
              required
              :disabled="editingUser"
              placeholder="name@auradevs.io" 
              class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-950/80 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
            >
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-3xs font-extrabold text-slate-400 uppercase tracking-wider mb-1">Organization Role</label>
              <select v-model="userForm.role" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="Admin">Admin</option>
                <option value="Designer">Designer</option>
                <option value="Developer">Developer</option>
                <option value="Analyst">Analyst</option>
              </select>
            </div>
            <div>
              <label class="block text-3xs font-extrabold text-slate-400 uppercase tracking-wider mb-1">Network Status</label>
              <select v-model="userForm.status" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="Active">Active</option>
                <option value="Away">Away</option>
                <option value="Suspended">Suspended</option>
              </select>
            </div>
          </div>

          <div class="pt-4 flex justify-end gap-2">
            <button type="button" @click="closeUserModal" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl">Cancel</button>
            <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl">Save</button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODERN ALERTS SYSTEM (TOAST NOTIFIER CONTAINER) -->
    <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm w-full">
      <transition-group 
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div 
          v-for="toast in toasts" 
          :key="toast.id" 
          :class="['p-4 rounded-xl shadow-lg border flex items-center gap-3', toast.borderClass, toast.bgClass]"
        >
          <div :class="['p-1.5 rounded-lg', toast.iconBgClass]">
            <i :data-lucide="toast.icon" :class="['w-4 h-4', toast.iconClass]"></i>
          </div>
          <div class="flex-1">
            <p class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ toast.message }}</p>
          </div>
          <button @click="dismissToast(toast.id)" class="text-slate-400 hover:text-slate-600">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
          </button>
        </div>
      </transition-group>
    </div>

  </div>

  <script>
    const { createApp, ref, computed, onMounted, nextTick, watch } = Vue;

    createApp({
      setup() {
        // Core Layout/Navigation State Variables
        const activeTab = ref('overview');
        const sidebarOpen = ref(false);
        const darkMode = ref(true);
        const notifMenuOpen = ref(false);
        const userMenuOpen = ref(false);
        const searchQuery = ref('');

        const tabTitle = computed(() => {
          switch (activeTab.value) {
            case 'overview': return 'Aura Orchestration Engine';
            case 'users': return 'Team Directory';
            case 'transactions': return 'Transactional Ledger';
            case 'tasks': return 'Sprint Production Board';
            case 'analytics': return 'Performance Attribution';
            case 'ai-copilot': return 'Aura AI Insights';
            case 'settings': return 'System Settings & Webhooks';
            default: return 'Aura Dashboard';
          }
        });

        const navItems = [
          { id: 'overview', name: 'Overview', icon: 'grid', badge: '' },
          { id: 'users', name: 'Team Members', icon: 'users', badge: '8' },
          { id: 'transactions', name: 'Transactions', icon: 'credit-card', badge: '' },
          { id: 'tasks', name: 'Task Board', icon: 'trello', badge: '3' },
          { id: 'analytics', name: 'Analytics', icon: 'bar-chart-3', badge: '' },
          { id: 'settings', name: 'Settings', icon: 'settings', badge: '' }
        ];

        // Toast Notification Pipeline
        const toasts = ref([]);
        const openToast = (message, type = 'success') => {
          const id = Date.now() + Math.random().toString(36).substr(2, 5);
          let icon = 'check-circle';
          let borderClass = 'border-emerald-100 dark:border-emerald-950/40';
          let bgClass = 'bg-emerald-50 dark:bg-emerald-950/45';
          let iconClass = 'text-emerald-500';
          let iconBgClass = 'bg-emerald-100/40 dark:bg-emerald-950/60';

          if (type === 'warning') {
            icon = 'alert-triangle';
            borderClass = 'border-amber-100 dark:border-amber-950/40';
            bgClass = 'bg-amber-50 dark:bg-amber-950/45';
            iconClass = 'text-amber-500';
            iconBgClass = 'bg-amber-100/40 dark:bg-amber-950/60';
          } else if (type === 'error') {
            icon = 'x-octagon';
            borderClass = 'border-rose-100 dark:border-rose-950/40';
            bgClass = 'bg-rose-50 dark:bg-rose-950/45';
            iconClass = 'text-rose-500';
            iconBgClass = 'bg-rose-100/40 dark:bg-rose-950/60';
          } else if (type === 'info') {
            icon = 'info';
            borderClass = 'border-sky-100 dark:border-sky-950/40';
            bgClass = 'bg-sky-50 dark:bg-sky-950/45';
            iconClass = 'text-sky-500';
            iconBgClass = 'bg-sky-100/40 dark:bg-sky-950/60';
          }

          toasts.value.push({ id, message, type, icon, borderClass, bgClass, iconClass, iconBgClass });
          
          nextTick(() => {
            lucide.createIcons();
          });

          // Self destruct toast
          setTimeout(() => {
            dismissToast(id);
          }, 4500);
        };

        const dismissToast = (id) => {
          toasts.value = toasts.value.filter(t => t.id !== id);
        };

        // Static Metrics Array
        const metrics = ref([
          { title: 'Gross Revenue', value: '$124,530.22', change: '+14.2%', trendUp: true, icon: 'dollar-sign', iconColor: 'text-emerald-500', iconBg: 'bg-emerald-500/10' },
          { title: 'Subscribed Users', value: '42,912', change: '+8.4%', trendUp: true, icon: 'users', iconColor: 'text-indigo-500', iconBg: 'bg-indigo-500/10' },
          { title: 'Active Conversions', value: '3.12%', change: '-0.9%', trendUp: false, icon: 'activity', iconColor: 'text-rose-500', iconBg: 'bg-rose-500/10' },
          { title: 'Process SLA', value: '99.98%', change: '+0.04%', trendUp: true, icon: 'zap', iconColor: 'text-amber-500', iconBg: 'bg-amber-500/10' }
        ]);

        // Mock Transaction Data Set
        const transactionsList = ref([
          { id: 'TXN-9021-99', customer: 'Acme Corp Inc.', initials: 'AC', time: '10:45 AM', amount: '$4,200.00', status: 'Success', statusBg: 'bg-emerald-500/10', statusText: 'text-emerald-500', method: 'Stripe wire' },
          { id: 'TXN-8712-42', customer: 'Vercel Platform', initials: 'VP', time: '09:12 AM', amount: '$12,850.50', status: 'Success', statusBg: 'bg-emerald-500/10', statusText: 'text-emerald-500', method: 'Wire Transfer' },
          { id: 'TXN-6541-11', customer: 'Hellenic Logistics', initials: 'HL', time: '08:33 AM', amount: '$1,190.00', status: 'Failed', statusBg: 'bg-rose-500/10', statusText: 'text-rose-500', method: 'Credit Card' },
          { id: 'TXN-4903-51', customer: 'Stark Industries', initials: 'SI', time: 'Yesterday', amount: '$24,500.00', status: 'Success', statusBg: 'bg-emerald-500/10', statusText: 'text-emerald-500', method: 'Direct ACH' },
          { id: 'TXN-3290-76', customer: 'Nova Media', initials: 'NM', time: 'Yesterday', amount: '$450.00', status: 'Pending', statusBg: 'bg-amber-500/10', statusText: 'text-amber-500', method: 'PayPal Business' }
        ]);

        const latestTransactions = computed(() => transactionsList.value.slice(0, 4));

        // Sprint Task Board Array
        const sprintTasks = ref([
          { id: 'TSK-101', title: 'Complete Aura Figma Design audit', owner: 'Sarah Jenkins', eta: '2 Days', priority: 'High', priorityBg: 'bg-rose-500/10', priorityText: 'text-rose-500', completed: false, status: 'todo' },
          { id: 'TSK-102', title: 'Refactor Tailwind configuration variables', owner: 'Devon Carter', eta: '1 Day', priority: 'Medium', priorityBg: 'bg-amber-500/10', priorityText: 'text-amber-500', completed: true, status: 'inprogress' },
          { id: 'TSK-103', title: 'Launch secure user management webhooks', owner: 'Elena Rostova', eta: '3 Days', priority: 'Low', priorityBg: 'bg-emerald-500/10', priorityText: 'text-emerald-500', completed: false, status: 'done' },
          { id: 'TSK-104', title: 'Implement Gemini 2.5 API retry algorithm', owner: 'Alex Mercer', eta: '12 Hours', priority: 'High', priorityBg: 'bg-rose-500/10', priorityText: 'text-rose-500', completed: false, status: 'todo' },
          { id: 'TSK-105', title: 'Audit conversion attribution reports', owner: 'Sarah Jenkins', eta: '5 Days', priority: 'Medium', priorityBg: 'bg-amber-500/10', priorityText: 'text-amber-500', completed: false, status: 'inprogress' }
        ]);

        const kanbanColumns = [
          { id: 'todo', name: 'Backlog / To Do', color: 'bg-rose-500' },
          { id: 'inprogress', name: 'Under Active Review', color: 'bg-amber-500' },
          { id: 'done', name: 'Validated Done', color: 'bg-emerald-500' }
        ];

        const getTasksByColumn = (colId) => {
          return sprintTasks.value.filter(t => t.status === colId);
        };

        const moveTask = (task, direction) => {
          const statuses = ['todo', 'inprogress', 'done'];
          const currentIndex = statuses.indexOf(task.status);
          if (direction === 'forward' && currentIndex < statuses.length - 1) {
            task.status = statuses[currentIndex + 1];
            openToast(`Task moved to: ${task.status}`, 'success');
          } else if (direction === 'back' && currentIndex > 0) {
            task.status = statuses[currentIndex - 1];
            openToast(`Task moved back to: ${task.status}`, 'warning');
          }
        };

        const toggleTask = (task) => {
          if (task.completed) {
            task.status = 'done';
            openToast('Milestone achieved and verified', 'success');
          } else {
            task.status = 'todo';
            openToast('Reopened task element', 'info');
          }
        };

        // User Administration System variables
        const usersList = ref([
          { name: 'Sarah Jenkins', email: 'sarah.j@auradevs.io', role: 'Designer', status: 'Active', statusClass: 'bg-emerald-500/10 text-emerald-500', lastActive: '12 mins ago', avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=256' },
          { name: 'Devon Carter', email: 'd.carter@auradevs.io', role: 'Developer', status: 'Active', statusClass: 'bg-emerald-500/10 text-emerald-500', lastActive: '1 hour ago', avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=256' },
          { name: 'Elena Rostova', email: 'elena.r@auradevs.io', role: 'Developer', status: 'Away', statusClass: 'bg-amber-500/10 text-amber-500', lastActive: '3 hours ago', avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=256' },
          { name: 'Marcus Sterling', email: 'm.sterling@auradevs.io', role: 'Admin', status: 'Active', statusClass: 'bg-emerald-500/10 text-emerald-500', lastActive: 'Just now', avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=256' },
          { name: 'Chloe Fontaine', email: 'c.fontaine@auradevs.io', role: 'Analyst', status: 'Suspended', statusClass: 'bg-rose-500/10 text-rose-500', lastActive: '4 days ago', avatar: 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=256' }
        ]);

        const userSearchQuery = ref('');
        const userFilterRole = ref('all');
        const userFilterStatus = ref('all');

        const filteredUsers = computed(() => {
          return usersList.value.filter(u => {
            const matchesSearch = u.name.toLowerCase().includes(userSearchQuery.value.toLowerCase()) || 
                                  u.email.toLowerCase().includes(userSearchQuery.value.toLowerCase());
            const matchesRole = userFilterRole.value === 'all' || u.role === userFilterRole.value;
            const matchesStatus = userFilterStatus.value === 'all' || u.status === userFilterStatus.value;
            return matchesSearch && matchesRole && matchesStatus;
          });
        });

        // User Creation / Edit state variables
        const showAddUserModal = ref(false);
        const editingUser = ref(false);
        const userForm = ref({ name: '', email: '', role: 'Developer', status: 'Active' });

        const saveUser = () => {
          if (editingUser.value) {
            const index = usersList.value.findIndex(u => u.email === userForm.value.email);
            if (index !== -1) {
              usersList.value[index] = {
                ...usersList.value[index],
                name: userForm.value.name,
                role: userForm.value.role,
                status: userForm.value.status,
                statusClass: getStatusClass(userForm.value.status)
              };
              openToast('Member details altered successfully', 'success');
            }
          } else {
            usersList.value.push({
              name: userForm.value.name,
              email: userForm.value.email,
              role: userForm.value.role,
              status: userForm.value.status,
              statusClass: getStatusClass(userForm.value.status),
              lastActive: 'Never logged in',
              avatar: `https://images.unsplash.com/photo-${1500000000000 + Math.floor(Math.random() * 999999)}?auto=format&fit=crop&q=80&w=256`
            });
            openToast('New registration pipeline established', 'success');
          }
          closeUserModal();
        };

        const editUser = (user) => {
          editingUser.value = true;
          userForm.value = { name: user.name, email: user.email, role: user.role, status: user.status };
          showAddUserModal.value = true;
        };

        const deleteUser = (user) => {
          usersList.value = usersList.value.filter(u => u.email !== user.email);
          openToast('Member decoupled from directory', 'warning');
        };

        const closeUserModal = () => {
          showAddUserModal.value = false;
          editingUser.value = false;
          userForm.value = { name: '', email: '', role: 'Developer', status: 'Active' };
        };

        const getStatusClass = (status) => {
          if (status === 'Active') return 'bg-emerald-500/10 text-emerald-500';
          if (status === 'Away') return 'bg-amber-500/10 text-amber-500';
          return 'bg-rose-500/10 text-rose-500';
        };

        // System Settings variable toggles
        const settingsToggles = ref({
          emailDigest: true,
          errorLogs: false,
          apiSync: true
        });

        // AI Copilot Integration Pipeline
        const aiPromptInput = ref('');
        const aiThinking = ref(false);
        const aiConversations = ref([]);
        const chatScrollContainer = ref(null);
        const aiPresets = [
          "Explain active conversion changes",
          "Synthesize Q2 transactional metrics",
          "Draft Slack error webhook templates"
        ];

        const applyPreset = (preset) => {
          aiPromptInput.value = preset;
          dispatchAICopilot();
        };

        const dispatchAICopilot = async () => {
          const userQuery = aiPromptInput.value.trim();
          if (!userQuery) return;

          // Add user message to screen instantly
          aiConversations.value.push({
            id: Date.now().toString(),
            role: 'user',
            content: userQuery
          });

          aiPromptInput.value = '';
          aiThinking.value = true;
          scrollChat();

          // Gemini 2.5 API with exponential backoff algorithm
          const apiKey = ""; // Left empty as required by canvas runtime variables
          const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key=${apiKey}`;
          
          const systemPrompt = `You are Aura AI, an expert analytical intelligence integrated inside Sarah Jenkins' dashboard. 
          Use these metrics to synthesize queries:
          - Gross Revenue: $124,530.22 (+14.2%)
          - Subscribed Users: 42,912 (+8.4%)
          - Active Conversion Rate: 3.12% (-0.9%)
          - Target System SLA: 99.98% (+0.04%)
          Answer concisely using neat visual formatting and clear bullet points. Do not mention API credentials.`;

          const executeCall = async (retryCount = 0) => {
            try {
              const res = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                  contents: [{ parts: [{ text: userQuery }] }],
                  systemInstruction: { parts: [{ text: systemPrompt }] }
                })
              });

              if (!res.ok) throw new Error(`HTTP status code error: ${res.status}`);

              const data = await res.json();
              const responseText = data.candidates?.[0]?.content?.parts?.[0]?.text || "AI completed calculations with no return payload.";
              
              aiConversations.value.push({
                id: (Date.now() + 1).toString(),
                role: 'assistant',
                content: responseText
              });
            } catch (err) {
              if (retryCount < 5) {
                const backoffDelay = Math.pow(2, retryCount) * 1000; // Exponential delays: 1s, 2s, 4s, 8s, 16s
                await new Promise(resolve => setTimeout(resolve, backoffDelay));
                return executeCall(retryCount + 1);
              }
              
              // Fallback response for unkeyed query processing gracefully
              aiConversations.value.push({
                id: (Date.now() + 1).toString(),
                role: 'assistant',
                content: `Engine failed to load (Status API Unkeyed). Aura AI synthesized offline response:\n\n* Your Revenue rose to $124.5k (+14.2%).\n* Active conversions fell by 0.9% down to 3.12% due to checkout funnel leakage.\n* Recommending immediate review of sprint task: ${sprintTasks.value[0].title}.`
              });
              openToast("Copilot ran analytical fallback processing", "info");
            } finally {
              aiThinking.value = false;
              scrollChat();
            }
          };

          await executeCall();
        };

        const scrollChat = () => {
          nextTick(() => {
            if (chatScrollContainer.value) {
              chatScrollContainer.value.scrollTop = chatScrollContainer.value.scrollHeight;
            }
            lucide.createIcons();
          });
        };

        // System alerts and global UI interactions
        const notifications = ref([
          { id: 1, title: 'Server threshold exceeded 90%', time: '3 mins ago', icon: 'zap', bg: 'bg-rose-500/10', color: 'text-rose-500' },
          { id: 2, title: 'New registration: Marcus Sterling', time: '1 hour ago', icon: 'user-plus', bg: 'bg-indigo-500/10', color: 'text-indigo-500' },
          { id: 3, title: 'Invoice payment TXN-8712 confirmed', time: '2 hours ago', icon: 'credit-card', bg: 'bg-emerald-500/10', color: 'text-emerald-500' }
        ]);

        const toggleTheme = () => {
          darkMode.value = !darkMode.value;
          document.documentElement.classList.toggle('dark', darkMode.value);
          openToast(`Theme switched to ${darkMode.value ? 'Dark Mode' : 'Light Mode'}`, 'info');
          // Re-render chart instances with updated color states
          nextTick(() => {
            initializeCharts();
          });
        };

        const refreshMockData = () => {
          // Alter arbitrary indicators to emulate real-time sync telemetry
          metrics.value[0].value = `$${(124530.22 + (Math.random() - 0.5) * 1000).toFixed(2)}`;
          metrics.value[2].value = `${(3.12 + (Math.random() - 0.5) * 0.2).toFixed(2)}%`;
          openToast('Synchronized data pipelines across databases', 'success');
        };

        const exportCSV = () => {
          const content = "Metric, Value, Change\n" + metrics.value.map(m => `${m.title}, ${m.value}, ${m.change}`).join("\n");
          const blob = new Blob([content], { type: 'text/csv' });
          const url = URL.createObjectURL(blob);
          const link = document.createElement('a');
          link.href = url;
          link.download = 'Aura_Performance_Attribution.csv';
          link.click();
          openToast('Secure ledger exported to downloads', 'success');
        };

        const setActiveTab = (tabId) => {
          activeTab.value = tabId;
          sidebarOpen.value = false;
          nextTick(() => {
            lucide.createIcons();
            if (tabId === 'overview' || tabId === 'analytics') {
              initializeCharts();
            }
          });
        };

        // Canvas Chart Initialization Module (Chart.js)
        let overviewLineChartInstance = null;
        let attributionPieChartInstance = null;
        let analyticsBarChartInstance = null;
        const overviewRange = ref('weekly');

        const setOverviewRange = (range) => {
          overviewRange.value = range;
          openToast(`Chart filters altered to: ${range}`, 'info');
          nextTick(() => {
            initializeCharts();
          });
        };

        const initializeCharts = () => {
          const isDark = darkMode.value;
          const gridColor = isDark ? 'rgba(51, 65, 85, 0.5)' : 'rgba(226, 232, 240, 0.8)';
          const textPrimaryColor = isDark ? '#f8fafc' : '#0f172a';
          const textSecondaryColor = isDark ? '#94a3b8' : '#64748b';

          // Overview Line Graph configuration
          const lineCtx = document.getElementById('overviewLineChart');
          if (lineCtx) {
            if (overviewLineChartInstance) overviewLineChartInstance.destroy();
            
            const datasets = overviewRange.value === 'weekly' 
              ? { labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], data: [12000, 19000, 15000, 28000, 22000, 34000, 31000] }
              : { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], data: [85000, 92000, 108000, 99000, 115000, 124000] };

            overviewLineChartInstance = new Chart(lineCtx, {
              type: 'line',
              data: {
                labels: datasets.labels,
                datasets: [{
                  label: 'Net Flux Volume ($)',
                  data: datasets.data,
                  borderColor: '#6366f1',
                  borderWidth: 3,
                  backgroundColor: 'rgba(99, 102, 241, 0.05)',
                  fill: true,
                  tension: 0.4,
                  pointRadius: 4,
                  pointHoverRadius: 6
                }]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                  x: { grid: { display: false }, ticks: { color: textSecondaryColor, font: { family: 'Inter', size: 9 } } },
                  y: { grid: { color: gridColor }, ticks: { color: textSecondaryColor, font: { family: 'Inter', size: 9 } } }
                }
              }
            });
          }

          // Channel Attribution Doughnut config
          const pieCtx = document.getElementById('attributionPieChart');
          if (pieCtx) {
            if (attributionPieChartInstance) attributionPieChartInstance.destroy();
            attributionPieChartInstance = new Chart(pieCtx, {
              type: 'doughnut',
              data: {
                labels: ['Direct', 'Organic', 'Social'],
                datasets: [{
                  data: [54.2, 28.4, 17.4],
                  backgroundColor: ['#6366f1', '#a855f7', '#10b981'],
                  borderWidth: isDark ? 4 : 2,
                  borderColor: isDark ? '#0f172a' : '#ffffff'
                }]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '75%'
              }
            });
          }

          // Analytics Page detailed Column Chart
          const barCtx = document.getElementById('analyticsBarChart');
          if (barCtx) {
            if (analyticsBarChartInstance) analyticsBarChartInstance.destroy();
            analyticsBarChartInstance = new Chart(barCtx, {
              type: 'bar',
              data: {
                labels: ['USA', 'Germany', 'UK', 'Japan', 'France', 'Canada'],
                datasets: [{
                  label: 'Traffic Scale (thousands)',
                  data: [42.1, 28.4, 19.8, 15.2, 11.4, 8.9],
                  backgroundColor: '#6366f1',
                  borderRadius: 6
                }]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                  x: { grid: { display: false }, ticks: { color: textSecondaryColor, font: { family: 'Inter', size: 9 } } },
                  y: { grid: { color: gridColor }, ticks: { color: textSecondaryColor, font: { family: 'Inter', size: 9 } } }
                }
              }
            });
          }
        };

        onMounted(() => {
          document.documentElement.classList.add('dark'); // Default to clean modern dark mode
          lucide.createIcons();
          initializeCharts();
          openToast("Welcome back, Sarah Jenkins!", "success");
        });

        // Ensure lucide icons re-render on active tab updates
        watch(activeTab, () => {
          nextTick(() => {
            lucide.createIcons();
          });
        });

        return {
          activeTab,
          tabTitle,
          sidebarOpen,
          navItems,
          darkMode,
          notifMenuOpen,
          userMenuOpen,
          searchQuery,
          toasts,
          openToast,
          dismissToast,
          metrics,
          transactionsList,
          latestTransactions,
          sprintTasks,
          kanbanColumns,
          getTasksByColumn,
          moveTask,
          toggleTask,
          usersList,
          userSearchQuery,
          userFilterRole,
          userFilterStatus,
          filteredUsers,
          showAddUserModal,
          editingUser,
          userForm,
          saveUser,
          editUser,
          deleteUser,
          closeUserModal,
          settingsToggles,
          aiPromptInput,
          aiThinking,
          aiConversations,
          aiPresets,
          applyPreset,
          dispatchAICopilot,
          chatScrollContainer,
          notifications,
          toggleTheme,
          refreshMockData,
          exportCSV,
          setActiveTab,
          overviewRange,
          setOverviewRange
        };
      }
    }).mount('#app');
  </script>
</body>
</html>
</template>