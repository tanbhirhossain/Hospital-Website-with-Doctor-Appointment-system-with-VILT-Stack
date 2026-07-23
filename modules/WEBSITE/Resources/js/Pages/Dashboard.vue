<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { 
  Calendar, Users, UserCheck, Clock, TrendingUp, ArrowRight, 
  Plus, Filter, MoreHorizontal 
} from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
];

// Mock data for premium dashboard
const stats = ref([
  { 
    title: "Today's Appointments", 
    value: "47", 
    change: "+18%", 
    trend: "up",
    icon: Calendar,
    color: "emerald"
  },
  { 
    title: "Active Doctors", 
    value: "32", 
    change: "+2", 
    trend: "up",
    icon: UserCheck,
    color: "blue"
  },
  { 
    title: "Total Patients", 
    value: "2,481", 
    change: "+124", 
    trend: "up",
    icon: Users,
    color: "violet"
  },
  { 
    title: "Avg. Wait Time", 
    value: "14m", 
    change: "-3m", 
    trend: "down",
    icon: Clock,
    color: "amber"
  }
]);

const upcomingAppointments = ref([
  { 
    id: 1, 
    patient: "Emily Rodriguez", 
    doctor: "Dr. Michael Torres", 
    time: "09:30 AM", 
    type: "Follow-up", 
    status: "confirmed",
    avatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&h=80&fit=crop"
  },
  { 
    id: 2, 
    patient: "David Chen", 
    doctor: "Dr. Sarah Patel", 
    time: "10:15 AM", 
    type: "New Patient", 
    status: "confirmed",
    avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop"
  },
  { 
    id: 3, 
    patient: "Olivia Martinez", 
    doctor: "Dr. James Kim", 
    time: "11:00 AM", 
    type: "Consultation", 
    status: "pending",
    avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=80&h=80&fit=crop"
  },
]);

const doctorsOnline = ref([
  { name: "Dr. Sarah Patel", specialty: "Cardiology", status: "Available", patients: 8 },
  { name: "Dr. Michael Torres", specialty: "Neurology", status: "In Session", patients: 12 },
  { name: "Dr. James Kim", specialty: "Orthopedics", status: "Available", patients: 5 },
]);

const recentActivity = ref([
  { action: "Appointment booked", detail: "John Smith with Dr. Patel", time: "2m ago" },
  { action: "Report generated", detail: "Weekly performance report", time: "35m ago" },
  { action: "Doctor added", detail: "Dr. Lisa Wong joined", time: "2h ago" },
]);

const activeTab = ref<'today' | 'week'>('today');
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-4xl font-semibold tracking-tighter">Good morning, Dr. Chen</h1>
        <p class="text-slate-600 dark:text-slate-400 mt-1">Here's what's happening at MediCore today.</p>
      </div>
      
      <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-2xl text-sm font-medium transition-all">
          <Filter class="w-4 h-4" /> Filter
        </button>
        <button class="flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-sm font-medium transition-all shadow-lg shadow-emerald-600/30">
          <Plus class="w-4 h-4" /> New Appointment
        </button>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
      <div 
        v-for="stat in stats" 
        :key="stat.title"
        class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 hover:shadow-md transition-all"
      >
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm font-medium text-slate-500">{{ stat.title }}</p>
            <p class="text-4xl font-semibold tracking-tighter mt-2">{{ stat.value }}</p>
          </div>
          <div :class="`p-3 rounded-2xl bg-${stat.color}-100 dark:bg-${stat.color}-950/40`">
            <component :is="stat.icon" :class="`w-5 h-5 text-${stat.color}-600 dark:text-${stat.color}-400`" />
          </div>
        </div>
        <div class="flex items-center gap-1.5 mt-4">
          <span 
            class="text-xs px-2 py-0.5 rounded-full font-medium"
            :class="stat.trend === 'up' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-400'"
          >
            {{ stat.change }}
          </span>
          <span class="text-xs text-slate-500">from yesterday</span>
        </div>
      </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
      
      <!-- Today's Appointments -->
      <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden">
        <div class="px-6 pt-6 pb-4 flex items-center justify-between border-b border-slate-100 dark:border-slate-800">
          <div>
            <h3 class="font-semibold text-xl tracking-tight">Today's Appointments</h3>
            <p class="text-sm text-slate-500">47 total • 12 remaining</p>
          </div>
          
          <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-2xl">
            <button 
              @click="activeTab = 'today'" 
              :class="['px-4 py-1 text-sm font-medium rounded-[14px] transition-all', activeTab === 'today' ? 'bg-white dark:bg-slate-900 shadow' : 'text-slate-500']"
            >
              Today
            </button>
            <button 
              @click="activeTab = 'week'" 
              :class="['px-4 py-1 text-sm font-medium rounded-[14px] transition-all', activeTab === 'week' ? 'bg-white dark:bg-slate-900 shadow' : 'text-slate-500']"
            >
              This Week
            </button>
          </div>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800">
          <div 
            v-for="appt in upcomingAppointments" 
            :key="appt.id"
            class="px-6 py-5 flex items-center gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors group"
          >
            <img :src="appt.avatar" class="w-11 h-11 rounded-2xl object-cover ring-2 ring-white dark:ring-slate-900" alt="" />
            
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between">
                <div>
                  <p class="font-medium">{{ appt.patient }}</p>
                  <p class="text-sm text-slate-500">{{ appt.doctor }} • {{ appt.type }}</p>
                </div>
                <div class="text-right">
                  <div class="font-mono text-sm font-medium">{{ appt.time }}</div>
                  <div 
                    class="inline-block mt-0.5 px-2.5 py-px text-[10px] font-medium rounded-full"
                    :class="appt.status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                  >
                    {{ appt.status }}
                  </div>
                </div>
              </div>
            </div>

            <button class="opacity-0 group-hover:opacity-100 p-2 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-all">
              <MoreHorizontal class="w-4 h-4" />
            </button>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-center">
          <button class="text-emerald-600 hover:text-emerald-700 flex items-center gap-2 text-sm font-medium">
            View all appointments <ArrowRight class="w-4 h-4" />
          </button>
        </div>
      </div>

      <!-- Right Column -->
      <div class="space-y-6">
        
        <!-- Doctors Online -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6">
          <div class="flex items-center justify-between mb-5">
            <div>
              <h3 class="font-semibold">Doctors Online</h3>
              <p class="text-xs text-emerald-600 dark:text-emerald-400">28 currently available</p>
            </div>
            <button class="text-xs px-3 py-1 rounded-full border border-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">View all</button>
          </div>

          <div class="space-y-4">
            <div v-for="doctor in doctorsOnline" :key="doctor.name" class="flex items-center gap-3">
              <div class="relative">
                <div class="w-9 h-9 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-2xl flex items-center justify-center text-white text-xs font-semibold">
                  {{ doctor.name.split(' ').map(n => n[0]).join('') }}
                </div>
                <div class="absolute -bottom-px -right-px w-3 h-3 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full"></div>
              </div>
              <div class="flex-1">
                <div class="font-medium text-sm">{{ doctor.name }}</div>
                <div class="text-xs text-slate-500">{{ doctor.specialty }}</div>
              </div>
              <div class="text-right text-xs">
                <div class="font-medium">{{ doctor.patients }}</div>
                <div class="text-emerald-600">online</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6">
          <h3 class="font-semibold mb-5">Recent Activity</h3>
          
          <div class="space-y-4 text-sm">
            <div v-for="(activity, index) in recentActivity" :key="index" class="flex gap-3">
              <div class="w-1.5 h-1.5 mt-2 rounded-full bg-emerald-500 flex-shrink-0"></div>
              <div>
                <div class="font-medium">{{ activity.action }}</div>
                <div class="text-slate-500 text-xs">{{ activity.detail }}</div>
                <div class="text-[10px] text-slate-400 mt-px">{{ activity.time }}</div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>