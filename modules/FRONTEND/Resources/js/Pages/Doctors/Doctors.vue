<template>
    <FrontendLayout>
        
        <section class="relative w-full bg-slate-950 overflow-hidden border-b border-slate-900 py-16 md:py-28">
            <div class="container mx-auto px-6 md:px-12 relative z-10 flex flex-col lg:flex-row items-center gap-12">
                
                <div class="w-full lg:w-7/12 text-center lg:text-left flex flex-col items-center lg:items-start">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold tracking-[0.25em] uppercase text-amber-400 bg-amber-500/10 border border-amber-500/20 px-4 py-1.5 rounded-full mb-6 select-none animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Elite Medical Faculty
                    </span>
                    
                    <h1 class="text-4xl md:text-6xl font-black tracking-tight text-transparent bg-clip-text golden-flash-text mb-6 leading-tight drop-shadow-sm select-none">
                        World-Class Specialist Care
                    </h1>
                    
                    <div class="w-20 h-1 bg-gradient-to-r from-blue-500 via-amber-400 to-sky-400 rounded-full mb-6"></div>
                    
                    <p class="text-slate-300 max-w-xl text-base md:text-lg leading-relaxed font-medium">
                        Connect with globally recognized medical authorities and leading researchers dedicated to precise diagnostics and exceptional patient interventions.
                    </p>
                </div>

                <div class="w-full lg:w-5/12 relative group flex justify-center">
                    <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-blue-600/20 to-amber-500/10 blur-xl opacity-75"></div>
                    
                    <div class="relative w-full max-w-md h-[380px] md:h-[450px] overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/50 backdrop-blur-md shadow-2xl p-4">
                        <div class="flex flex-col gap-4 h-full relative overflow-hidden">
                            <div class="flex flex-col gap-4 marquee-vertical-track shrink-0">
                                <div v-for="(doctor, index) in doctors" :key="'vtrack1-' + index" 
                                    class="w-full h-[96px] shrink-0 relative overflow-hidden rounded-2xl border border-slate-800/60 bg-gradient-to-r from-slate-950 to-slate-900/90 shadow-xl flex items-center justify-between px-6 transition-all duration-300">
                                    <span class="text-sm md:text-base font-extrabold text-white tracking-wide line-clamp-2 max-w-[65%] leading-snug">
                                        {{ doctor.name }}
                                    </span>
                                    <div class="w-16 h-16 rounded-xl overflow-hidden border border-slate-800/80 bg-slate-950 shrink-0 shadow-inner">
                                        <img :src="doctor.profile_image_url" :alt="doctor.name" class="w-full h-full object-cover object-center" />
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col gap-4 marquee-vertical-track shrink-0" aria-hidden="true">
                                <div v-for="(doctor, index) in doctors" :key="'vtrack2-' + index" 
                                    class="w-full h-[96px] shrink-0 relative overflow-hidden rounded-2xl border border-slate-800/60 bg-gradient-to-r from-slate-950 to-slate-900/90 shadow-xl flex items-center justify-between px-6 transition-all duration-300">
                                    <span class="text-sm md:text-base font-extrabold text-white tracking-wide line-clamp-2 max-w-[65%] leading-snug">
                                        {{ doctor.name }}
                                    </span>
                                    <div class="w-16 h-16 rounded-xl overflow-hidden border border-slate-800/80 bg-slate-950 shrink-0 shadow-inner">
                                        <img :src="doctor.profile_image_url" :alt="doctor.name" class="w-full h-full object-cover object-center" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-slate-900 to-transparent pointer-events-none z-10"></div>
                        <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-slate-900 to-transparent pointer-events-none z-10"></div>
                    </div>
                </div>

            </div>
            <div class="absolute top-0 left-1/4 -translate-x-1/2 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
        </section>

        <section id="doctors" role="region" aria-label="Our doctors" class="py-12 md:py-20 bg-gradient-to-b from-slate-50 via-blue-50/30 to-white relative">
            <div class="container mx-auto px-4 sm:px-6 md:px-12 relative">
                
                <div class="xl:hidden sticky top-4 z-40 w-full mb-6 flex items-center justify-between bg-white/90 backdrop-blur-xl border border-slate-200/80 p-4 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.06)]">
                    <div class="pr-4 overflow-hidden">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Filter by</span>
                        <span class="text-sm font-black text-blue-900 truncate block">
                            {{ selectedDepartment === 'all' ? 'All Specialties' : selectedDepartment }}
                        </span>
                    </div>
                    <button 
                        @click="isMobileDrawerOpen = true"
                        class="shrink-0 flex items-center gap-2 bg-gradient-to-r from-blue-600 to-sky-600 hover:from-blue-700 hover:to-sky-700 text-white font-bold text-xs px-5 py-3 rounded-xl transition-all shadow-md shadow-blue-600/20 active:scale-[0.95]"
                    >
                        <i class="fas fa-sliders-h"></i>
                        <span>Filter</span>
                    </button>
                </div>

                <div class="flex flex-col xl:flex-row gap-8 xl:gap-10 items-start">
                    
                    <aside class="hidden xl:block w-3/12 sticky top-6 max-h-[calc(100vh-4rem)] overflow-y-auto no-scrollbar bg-white border border-slate-100 rounded-3xl p-6 shadow-[0_10px_30px_-5px_rgba(148,163,184,0.12)] relative group">
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-amber-400"></div>

                        <div class="mb-5 pb-4 border-b border-slate-100">
                            <h3 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 shadow-[0_0_8px_rgba(37,99,235,0.4)]"></span>
                                <span>Clinical Specialty</span>
                            </h3>
                            <p class="text-[11px] text-slate-400 mt-0.5 font-medium">Select a department target</p>
                        </div>

                        <div class="space-y-1.5">
                            <button 
                                @click="selectedDepartment = 'all'"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-xs font-bold tracking-wide transition-all duration-300 text-left cursor-pointer focus:outline-none"
                                :class="selectedDepartment === 'all' 
                                    ? 'bg-gradient-to-r from-slate-900 to-blue-950 text-white shadow-md shadow-slate-900/20' 
                                    : 'text-slate-600 hover:bg-blue-50/60 hover:text-blue-900'"
                            >
                                <span>All Specializations</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-md transition-colors"
                                      :class="selectedDepartment === 'all' ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-500'">
                                    {{ doctors.length }}
                                </span>
                            </button>

                            <button 
                                v-for="dept in uniqueDepartments" 
                                :key="dept.id"
                                @click="selectedDepartment = dept.name"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-xs font-bold tracking-wide transition-all duration-300 text-left cursor-pointer focus:outline-none"
                                :class="selectedDepartment === dept.name 
                                    ? 'bg-gradient-to-r from-blue-600 via-blue-600 to-sky-600 text-white shadow-md shadow-blue-600/10' 
                                    : 'text-slate-600 hover:bg-blue-50/60 hover:text-blue-900'"
                            >
                                <span class="truncate pr-2">{{ dept.name }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-md transition-colors shrink-0"
                                      :class="selectedDepartment === dept.name ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-500'">
                                    {{ getDepartmentCount(dept.name) }}
                                </span>
                            </button>
                        </div>
                    </aside>

                    <div class="w-full xl:w-9/12">
                        
                        <div v-if="filteredDoctors.length === 0" class="text-center py-24 bg-white border border-dashed border-slate-200 rounded-3xl shadow-inner">
                            <div class="w-16 h-16 mx-auto bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-center mb-4 shadow-sm">
                                <i class="fas fa-user-md text-slate-300 text-2xl"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-800 tracking-tight">No Active Specialists Found</h3>
                            <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1 leading-relaxed">There are currently no certified consultants assigned to the chosen medical branch registry.</p>
                        </div>

                        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="doc in filteredDoctors" :key="doc.name"
                                class="group relative bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-[0_4px_20px_-4px_rgba(148,163,184,0.12)] hover:shadow-[0_20px_40px_-12px_rgba(30,41,59,0.18)] transition-all duration-500 flex flex-col justify-between transform hover:-translate-y-1.5">
                                
                                <div>
                                    <div class="relative w-full aspect-[4/3] sm:aspect-[3/2] md:aspect-square lg:aspect-[4/3] overflow-hidden bg-slate-100 border-b border-slate-100 flex items-center justify-center">
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-10 pointer-events-none"></div>
                                        <img 
                                            v-if="doc.profile_image_url" 
                                            :src="doc.profile_image_url" 
                                            :alt="doc.name" 
                                            class="w-full h-full object-cover object-top transform scale-100 group-hover:scale-[1.03] transition-transform duration-700 ease-out" 
                                        />
                                        <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-200 to-blue-50/50">
                                            <i class="fas fa-user-md text-slate-300 text-5xl"></i>
                                        </div>
                                    </div>

                                    <div class="p-5 md:p-6 pb-4">
                                        <div class="mb-2.5">
                                            <span class="inline-block text-[10px] font-bold tracking-wider uppercase text-blue-700 bg-blue-50 border border-blue-100/60 px-2.5 py-0.5 rounded-md">
                                                {{ doc.specialty || 'Consultant' }}
                                            </span>
                                        </div>

                                        <h3 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight leading-tight group-hover:text-blue-900 transition-colors duration-300 mb-1 line-clamp-1">
                                            {{ doc.name }}
                                        </h3>

                                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 line-clamp-1">
                                            {{ doc.qualifications }}
                                        </p>

                                        <div class="space-y-2 border-t border-slate-100 pt-4">
                                            <div class="flex items-start gap-2.5">
                                                <i class="fas fa-briefcase text-blue-500/80 text-xs mt-1 shrink-0"></i>
                                                <p class="text-sm font-medium text-slate-700 leading-snug line-clamp-2">
                                                    {{ doc.designation }}
                                                </p>
                                            </div>
                                            <div class="flex items-start gap-2.5">
                                                <i class="fas fa-hospital text-slate-400 text-xs mt-1 shrink-0"></i>
                                                <p class="text-xs font-medium text-slate-500 leading-snug line-clamp-1">
                                                    {{ doc.institute }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-5 md:p-6 pt-0 mt-auto">
                                    <a :href="`/doctors/${doc.slug}`"
                                        class="flex items-center justify-center gap-2 w-full bg-gradient-to-r from-slate-900 to-blue-950 text-white text-center py-3.5 rounded-xl text-sm font-bold shadow-md shadow-slate-950/10 hover:from-blue-600 hover:to-blue-700 hover:shadow-lg hover:shadow-blue-600/20 active:scale-[0.98] transition-all duration-300">
                                        <span>Book Appointment</span>
                                        <i class="fas fa-calendar-alt text-xs opacity-80 group-hover:translate-x-0.5 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <div v-if="isMobileDrawerOpen" class="fixed inset-0 z-[60] xl:hidden flex flex-col justify-end">
            <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="isMobileDrawerOpen = false"></div>
            
            <div class="relative w-full max-h-[85vh] bg-white rounded-t-[2.5rem] flex flex-col z-10 overflow-hidden shadow-2xl animate-slide-up">
                <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto my-4 shrink-0"></div>
                
                <div class="px-6 pb-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Select Specialty</h3>
                        <p class="text-xs text-slate-400 font-medium">Showing full clinical faculty matrix</p>
                    </div>
                    <button @click="isMobileDrawerOpen = false" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-200 hover:text-slate-900 active:scale-95 focus:outline-none transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                
                <div class="overflow-y-auto px-6 py-4 space-y-2 no-scrollbar pb-12">
                    <button 
                        @click="selectedDepartment = 'all'; isMobileDrawerOpen = false"
                        class="w-full flex items-center justify-between px-5 py-4 rounded-xl text-sm font-bold transition-all text-left border"
                        :class="selectedDepartment === 'all' ? 'bg-gradient-to-r from-slate-900 to-blue-950 border-slate-900 text-white shadow-lg' : 'bg-slate-50 border-slate-100 text-slate-700 active:bg-slate-100'"
                    >
                        <span>All Specializations</span>
                        <i v-if="selectedDepartment === 'all'" class="fas fa-check text-xs"></i>
                    </button>

                    <button 
                        v-for="dept in uniqueDepartments" 
                        :key="dept.id"
                        @click="selectedDepartment = dept.name; isMobileDrawerOpen = false"
                        class="w-full flex items-center justify-between px-5 py-4 rounded-xl text-sm font-bold transition-all text-left border"
                        :class="selectedDepartment === dept.name ? 'bg-gradient-to-r from-blue-600 via-blue-600 to-sky-600 border-blue-600 text-white shadow-lg' : 'bg-slate-50 border-slate-100 text-slate-700 active:bg-slate-100'"
                    >
                        <span>{{ dept.name }}</span>
                        <i v-if="selectedDepartment === dept.name" class="fas fa-check text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

    </FrontendLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import FrontendLayout from '../../Layout/FrontendLayout.vue';

const props = defineProps({
    doctors: {
        type: Array,
        default: () => []
    }
});

// Dynamic States
const selectedDepartment = ref('all');
const isMobileDrawerOpen = ref(false);

// Generate unique array of departments safely
const uniqueDepartments = computed(() => {
    const map = new Map();
    props.doctors.forEach(doc => {
        if (doc.department && doc.department.id && doc.department.name) {
            map.set(doc.department.id, { id: doc.department.id, name: doc.department.name });
        }
    });
    return Array.from(map.values()).sort((a, b) => a.name.localeCompare(b.name));
});

// Realtime interactive dataset output
const filteredDoctors = computed(() => {
    if (selectedDepartment.value === 'all') {
        return props.doctors;
    }
    return props.doctors.filter(doc => doc.department && doc.department.name === selectedDepartment.value);
});

// Sidebar dynamic indicator counter utility 
const getDepartmentCount = (deptName) => {
    return props.doctors.filter(doc => doc.department && doc.department.name === deptName).length;
};
</script>

<style scoped>
/* Hidden utility scroller bars rule styling overrides */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* 1. HARD-WIRED TEXT FLASH ENGINE */
.golden-flash-text {
    background-image: linear-gradient(
        110deg, 
        #3b82f6 30%, 
        #fbbf24 45%, 
        #fffbeb 50%, 
        #fbbf24 55%, 
        #3b82f6 70%
    );
    background-size: 200% auto;
    animation: textFlashLoop 4.5s linear infinite;
}

@keyframes textFlashLoop {
    0% { background-position: 200% center; }
    100% { background-position: -200% center; }
}

/* 2. Vertical Marquee Track */
.marquee-vertical-track {
    animation: marqueeVertical 70s linear infinite;
}
.group:hover .marquee-vertical-track {
    animation-play-state: paused;
}
@keyframes marqueeVertical {
    0% { transform: translateY(0%); }
    100% { transform: translateY(calc(-100% - 1rem)); }
}
</style>