<template>
    <FrontendLayout>
    <div class="min-h-screen text-slate-800 relative overflow-hidden font-sans antialiased pb-24 selection:bg-blue-600/10 selection:text-blue-800 luxe-mesh-bg">
        
        <div class="absolute top-[-10%] left-[-10%] w-[600px] h-[600px] bg-gradient-to-br from-blue-200/40 via-sky-200/20 to-transparent rounded-full blur-[120px] mix-blend-multiply animate-orb-slow pointer-events-none"></div>
        <div class="absolute top-[20%] right-[-5%] w-[500px] h-[500px] bg-gradient-to-bl from-indigo-100/50 via-purple-100/25 to-transparent rounded-full blur-[140px] mix-blend-multiply animate-orb-delay pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 pt-10 relative z-10">
            <Link :href="route('front.doctor.index')" class="inline-flex items-center gap-2 text-[11px] font-bold tracking-[0.2em] text-slate-400 hover:text-blue-600 uppercase transition-all duration-300 group">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 transform group-hover:-translate-x-1 transition-transform">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
                Medical Directory
            </Link>
        </div>

        <main class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 pt-6 relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
            
            <div class="lg:col-span-4 lg:sticky lg:top-10 flex justify-center lg:justify-start">
                <div class="relative w-[370px] rounded-[2.5rem] border border-white/90 bg-white/75 backdrop-blur-2xl shadow-[0_24px_60px_-15px_rgba(148,163,184,0.18)] p-5 overflow-hidden transition-all duration-300">
                    
                    <div class="relative w-full aspect-[3/4] rounded-[1.75rem] overflow-hidden bg-slate-100 border border-slate-200/40 shadow-sm">
                        <img v-if="doctor.profile_image_url" :src="doctor.profile_image_url" :alt="doctor.name" class="w-full h-full object-cover object-top" />
                        <div v-else class="w-[350px] h-full flex flex-col items-center justify-center text-slate-300 bg-slate-50">
                            <i class="fas fa-user-md text-5xl"></i>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/20 via-transparent to-transparent z-10 opacity-40"></div>
                    </div>

                    <div class="mt-5 px-1">
                        <span v-if="doctor.specialty" class="inline-flex items-center text-[10px] font-black tracking-[0.18em] uppercase text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-md mb-2.5">
                            {{ doctor.specialty }}
                        </span>
                        
                        <h1 class="text-xl font-black text-slate-900 tracking-tight leading-tight mb-4">
                            {{ doctor.name }}
                        </h1>
                        
                        <div class="space-y-2.5">
                            <div class="bg-slate-50/90 border border-slate-100 px-4 py-3 rounded-2xl flex flex-col gap-0.5 shadow-sm">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Consultation Fee</span>
                                <span class="text-sm font-black text-slate-900">৳ {{ doctor.visit_fee }}</span>
                            </div>
                            <div v-if="doctor.report_fee" class="bg-slate-50/90 border border-slate-100 px-4 py-3 rounded-2xl flex flex-col gap-0.5 shadow-sm">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Report View Fee</span>
                                <span class="text-sm font-black text-slate-900">৳ {{ doctor.report_fee }}</span>
                            </div>
                        </div>

                        <div v-if="doctor.phone" class="flex flex-col gap-2 mt-4">
                            <a :href="'tel:' + doctor.phone" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-slate-900 text-white hover:bg-blue-600 text-xs font-bold tracking-wider shadow-md hover:shadow-lg transition-all duration-300">
                                <i class="fas fa-phone-alt text-[10px]"></i> Contact Helpline
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="lg:col-span-8 flex flex-col gap-6 animate-layout-up w-full">
                
                <div class="rounded-[2.5rem] border border-white/90 bg-white/75 backdrop-blur-xl p-6 md:p-8 shadow-[0_20px_50px_-12px_rgba(148,163,184,0.12)]">
                    <h2 class="text-xs font-black text-slate-400 tracking-[0.2em] uppercase mb-6 flex items-center gap-2">
                        <span class="w-1.5 h-3 bg-blue-600 rounded-full"></span> Personal Profile
                    </h2>
                    
                    <div v-if="hasValidBiography" 
                         class="text-slate-600 text-sm md:text-[15px] leading-relaxed font-medium space-y-4 mb-6 prose prose-slate max-w-none border-b border-slate-100 pb-6" 
                         v-html="doctor.biography">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                        <div v-if="doctor.specialty" class="flex flex-col gap-1.5 pb-2 border-b border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Primary Specialty</span>
                            <span class="text-xs font-bold text-slate-800 tracking-wide">{{ doctor.specialty }}</span>
                        </div>
                        
                        <div v-if="doctor.designation" class="flex flex-col gap-1.5 pb-2 border-b border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Academic Designation</span>
                            <span class="text-xs font-bold text-slate-800 tracking-wide">{{ doctor.designation }}</span>
                        </div>

                        <div v-if="doctor.institute" class="flex flex-col gap-1.5 pb-2 border-b border-slate-100 md:col-span-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Institutional Affiliation</span>
                            <span class="text-xs font-bold text-slate-800 leading-relaxed">{{ doctor.institute }}</span>
                        </div>

                        <div v-if="parsedQualifications.length" class="flex flex-col gap-2.5 pt-1 md:col-span-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Professional Credentials</span>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="(qual, index) in parsedQualifications" :key="index" class="inline-flex items-center text-xs font-bold text-slate-700 bg-blue-50/40 border border-blue-100/70 px-3.5 py-1.5 rounded-xl shadow-[0_2px_4px_rgba(0,0,0,0.01)] hover:bg-blue-50 transition-colors duration-200">
                                    <i class="fas fa-graduation-cap text-blue-500 mr-2 text-[11px]"></i> {{ qual }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2.5rem] border border-white/90 bg-white/75 backdrop-blur-xl p-6 md:p-8 shadow-[0_20px_50px_-12px_rgba(148,163,184,0.12)]">
<div class="max-w-4xl mx-auto p-6">
    <div class="mb-8 flex items-center justify-between">
      <div v-for="step in 4" :key="step" class="flex items-center">
        <div :class="['w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm', 
          bookingStep >= step ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-500']">
          {{ step }}
        </div>
        <div v-if="step < 4" class="w-16 h-1 bg-slate-200 mx-2"></div>
      </div>
    </div>

    <div v-if="bookingStep === 1" class="grid grid-cols-7 gap-2">
      <button v-for="day in rollingSevenDays" :key="day.formattedDate"
        @click="selectDate(day)"
        :class="['p-4 rounded-xl border flex flex-col items-center', 
          form.appointment_date === day.formattedDate ? 'border-blue-500 bg-blue-50' : 'hover:border-blue-300']">
        <span class="text-xs uppercase font-bold text-slate-500">{{ day.dayName }}</span>
        <span class="text-xl font-black mt-1">{{ day.dayOfMonth }}</span>
      </button>
    </div>

    <div v-if="bookingStep === 2" class="grid grid-cols-3 gap-4">
      <button v-for="slot in activeDaySlots" :key="slot.start_time"
        @click="selectSlot(slot)"
        :disabled="!slot.available"
        :class="['p-4 rounded-xl text-center border font-bold transition-all', 
          slot.available ? 'hover:bg-blue-600 hover:text-white border-blue-200' : 'bg-slate-100 text-slate-400 cursor-not-allowed']">
        {{ slot.start_time }} - {{ slot.end_time }}
      </button>
    </div>

    <div v-if="bookingStep === 3" class="space-y-4">
      <input v-model="form.patient_name" placeholder="আপনার নাম" class="w-full p-4 border rounded-xl" />
      <input v-model="form.patient_phone" placeholder="মোবাইল নম্বর" class="w-full p-4 border rounded-xl" />
      <div class="grid grid-cols-2 gap-4">
        <input v-model="form.patient_age" type="number" placeholder="বয়স" class="p-4 border rounded-xl" />
        <select v-model="form.patient_gender" class="p-4 border rounded-xl">
          <option value="">লিঙ্গ নির্বাচন করুন</option>
          <option value="Male">পুরুষ</option>
          <option value="Female">মহিলা</option>
        </select>
      </div>
      <button @click="validateStepThree" class="w-full p-4 bg-slate-900 text-white rounded-xl font-bold">পরবর্তী</button>
    </div>

    <div v-if="bookingStep === 4" class="p-6 bg-slate-50 rounded-2xl border">
      <h3 class="font-black mb-4">তথ্য যাচাই করুন</h3>
      <div class="space-y-2 text-sm mb-6">
        <p>নাম: {{ form.patient_name }}</p>
        <p>সময়: {{ form.start_time }} ({{ form.appointment_date }})</p>
      </div>
      <button @click="submitAppointmentToBackend" class="w-full p-4 bg-emerald-600 text-white rounded-xl font-black">
        কনফার্ম অ্যাপয়েন্টমেন্ট
      </button>
    </div>
  </div>
                </div>

            </div>
        </main>
    </div>
    </FrontendLayout>
</template>

<script setup>

import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import FrontendLayout from '../../Layout/FrontendLayout.vue';

const props = defineProps({
    doctor: {
        type: Object,
        required: true
    }
});

const selectedSlotId = ref(null);

const hasValidBiography = computed(() => {
    if (!props.doctor.biography) return false;
    const cleanBio = props.doctor.biography.trim();
    if (cleanBio.startsWith("Here’s a situation") || cleanBio.includes("Here’s a situation that comes up")) {
        return false;
    }
    return cleanBio.length > 0;
});

const parseJsonField = (field) => {
    if (!field) return [];
    if (Array.isArray(field)) return field;
    try {
        return typeof field === 'string' ? JSON.parse(field) : [];
    } catch (e) {
        return field.split(',').map(item => item.trim());
    }
};

const parsedQualifications = computed(() => parseJsonField(props.doctor.qualifications));

const activeSlotDetails = computed(() => {
    const matched = props.doctor.timetables?.find(t => t.id === selectedSlotId.value);
    return matched ? `${matched.day} (${matched.start_time} - ${matched.end_time})` : '';
});

const selectSlot = (slot) => {
    selectedSlotId.value = slot.id;
};

const confirmBooking = () => {
    console.log('Routing parameters sent for target schedule node ID:', selectedSlotId.value);
};
</script>

<style>
.luxe-mesh-bg {
    background-color: #f8fafc;
    background-image: 
        radial-gradient(at 0% 0%, rgba(219, 234, 254, 0.35) 0, transparent 50%),
        radial-gradient(at 100% 0%, rgba(243, 232, 255, 0.45) 0, transparent 50%),
        radial-gradient(at 50% 100%, rgba(254, 243, 199, 0.25) 0, transparent 50%);
}

@keyframes orbSlow {
    0%, 100% { transform: translate(0px, 0px) scale(1); }
    50% { transform: translate(25px, -15px) scale(1.03); }
}
@keyframes orbDelay {
    0%, 100% { transform: translate(0px, 0px) scale(1.03); }
    50% { transform: translate(-20px, 25px) scale(1); }
}

.animate-orb-slow {
    animation: orbSlow 14s infinite ease-in-out;
}
.animate-orb-delay {
    animation: orbDelay 16s infinite ease-in-out;
}

.animate-layout-up {
    animation: layoutUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes layoutUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>