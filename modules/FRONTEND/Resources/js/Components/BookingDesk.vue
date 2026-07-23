<template>
    <div class="w-full rounded-[2.5rem] border border-white/90 bg-white/70 backdrop-blur-3xl shadow-[0_30px_70px_-15px_rgba(148,163,184,0.15)] p-6 md:p-8">
        
        <!-- TOP DESK BANNER SECTION -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-6 mb-8">
            <div>
                <span class="text-[10px] font-black text-slate-400 tracking-[0.2em] uppercase block mb-1">Appointment Desk</span>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Book Your Consultation</h2>
                <p class="text-xs text-slate-500 font-medium mt-1">Choose a department, select doctor, and confirm in 6 quick steps.</p>
            </div>
            
            <!-- PROGRESS DISPLAY STATUS FRAME -->
            <div class="flex flex-col items-end gap-1.5 self-start sm:self-auto min-w-[100px]">
                <div class="bg-white px-3 py-1.5 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between w-full">
                    <span class="text-[9px] font-black text-slate-400 tracking-widest uppercase">Progress</span>
                    <span class="text-xs font-black text-slate-800">{{ currentStep }}/6</span>
                </div>
                <!-- Dynamic Linear Loading Track Bar -->
                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-500 ease-out"
                         :style="{ width: `${(currentStep / 6) * 100}%` }"></div>
                </div>
            </div>
        </div>

        <!-- SYSTEM INTERACTIVE CORE FIELD WORKSPACE -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT NAVIGATION JOURNEY SIDEBAR BAR MAPPING -->
            <div class="lg:col-span-3 flex flex-col gap-2 border-r border-slate-100/80 pr-4">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 block pl-1">Journey</span>
                
                <div v-for="step in journeySteps" :key="step.id" 
                     class="flex items-center gap-3 px-4 py-3 rounded-2xl border text-xs font-bold transition-all duration-300"
                     :class="getJourneyStepClasses(step.id)">
                    
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black"
                          :class="currentStep >= step.id ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400'">
                        <i v-if="currentStep > step.id || step.id <= 2" class="fas fa-check text-[8px]"></i>
                        <span v-else>{{ step.id }}</span>
                    </span>
                    <span class="tracking-wide">{{ step.name }}</span>
                </div>
            </div>

            <!-- CENTER WORKSPACE: DYNAMIC CONTENT CONTAINER HOUSING ACTIVE STEP -->
            <div class="lg:col-span-6 flex flex-col min-h-[360px] justify-between bg-slate-50/40 border border-slate-100/60 p-5 rounded-[2rem]">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-5">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Current Step</span>
                        <span class="text-xs font-black text-slate-800 bg-white border border-slate-200/60 px-3 py-1 rounded-xl shadow-sm">{{ activeStepTitle }}</span>
                    </div>

                    <!-- STEP 3 PANEL: CUSTOM SEVEN DAY CALENDAR GRID FRAMEWORK -->
                    <div v-if="currentStep === 3" class="animate-layout-up">
                        <MiniCalendar 
                            v-model="form.appointment_date" 
                            :timetables="doctor.timetables"
                            @dateSelected="handleCalendarDateClick" 
                        />
                    </div>

                    <!-- STEP 4 PANEL: TIME SHIFT WINDOW LIST GRID -->
                    <div v-if="currentStep === 4" class="animate-layout-up">
                        <p class="text-xs font-medium text-slate-500 mb-4">Select an active operational shift window for your session:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button v-for="slot in filteredDaySlots" :key="slot.id" @click="selectTimeSlot(slot)"
                                    class="p-4 rounded-2xl border bg-white text-center font-bold transition-all duration-200 hover:border-blue-400 hover:shadow-sm"
                                    :class="form.start_time === slot.start_time ? 'border-blue-500 bg-blue-50/40 ring-1 ring-blue-500/20' : 'border-slate-200/80'">
                                <div class="text-xs text-slate-900 mb-0.5">{{ slot.day }} Shift</div>
                                <div class="text-[11px] text-slate-400 font-medium">{{ slot.start_time }} - {{ slot.end_time }}</div>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 5 PANEL: PATIENT PARAMETERS PARTICULARS INGESTION FORM -->
                    <div v-if="currentStep === 5" class="animate-layout-up space-y-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Patient Full Name *</label>
                            <input type="text" v-model="form.patient_name" placeholder="E.g., Tanbhir Hossain" class="w-full text-xs font-bold border border-slate-200 bg-white rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500" />
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Contact Phone *</label>
                            <input type="tel" v-model="form.patient_phone" placeholder="017XXXXXXXX" class="w-full text-xs font-bold border border-slate-200 bg-white rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Age Count *</label>
                                <input type="number" v-model="form.patient_age" class="w-full text-xs font-bold border border-slate-200 bg-white rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Gender *</label>
                                <select v-model="form.patient_gender" class="w-full text-xs font-bold border border-slate-200 bg-white rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500">
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 6 PANEL: PREVIEW SUMMARY VERIFICATION SHEET -->
                    <div v-if="currentStep === 6" class="animate-layout-up">
                        <div class="bg-blue-50/30 border border-blue-100 p-5 rounded-2xl space-y-2.5 text-xs font-bold text-slate-700">
                            <div class="flex justify-between border-b border-slate-100 pb-1.5"><span>Target Practitioner:</span> <span class="text-slate-900">{{ doctor.name }}</span></div>
                            <div class="flex justify-between border-b border-slate-100 pb-1.5"><span>Date Target:</span> <span class="text-blue-600">{{ form.appointment_date }}</span></div>
                            <div class="flex justify-between border-b border-slate-100 pb-1.5"><span>Time Target:</span> <span class="text-slate-900">{{ form.start_time }} - {{ form.end_time }}</span></div>
                            <div class="flex justify-between border-b border-slate-100 pb-1.5"><span>Patient Name:</span> <span class="text-slate-900">{{ form.patient_name }}</span></div>
                            <div class="flex justify-between"><span>Consultation Fee:</span> <span class="text-slate-900">৳ {{ doctor.visit_fee }}</span></div>
                        </div>
                        <div v-if="serverError" class="text-[11px] font-bold text-red-500 mt-3 flex items-center gap-1.5"><i class="fas fa-exclamation-circle"></i> {{ serverError }}</div>
                    </div>
                </div>

                <!-- FOOTER NAVIGATION SWITCH ROW PACKAGES -->
                <div class="mt-8 pt-4 border-t border-slate-100/60 flex items-center justify-between">
                    <button 
                        v-if="currentStep > 3" 
                        @click="stepBackward"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-50 active:scale-95 transition-all"
                    >
                        Back
                    </button>
                    <div v-else class="w-10"></div>

                    <button 
                        v-if="currentStep < 6"
                        :disabled="!isCurrentStepValid"
                        @click="stepForward"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-xs font-black tracking-wide shadow-md shadow-blue-500/10 hover:shadow-lg hover:brightness-105 active:scale-95 transition-all disabled:opacity-40 disabled:cursor-not-allowed disabled:pointer-events-none"
                    >
                        Continue
                    </button>
                    <button 
                        v-else
                        :disabled="submitting"
                        @click="submitFinalForm"
                        class="px-6 py-2.5 rounded-xl bg-emerald-500 text-white text-xs font-black tracking-wide shadow-md shadow-emerald-500/10 hover:bg-emerald-600 active:scale-95 transition-all disabled:opacity-50"
                    >
                        {{ submitting ? 'Booking...' : 'Confirm' }}
                    </button>
                </div>
            </div>

            <!-- RIGHT COLUMN: REALTIME SIDE PANEL MONITOR LIVE REVIEW -->
            <div class="lg:col-span-3 bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-5 pb-2 border-b border-slate-100">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Live Review</span>
                    <span class="text-[8px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md tracking-widest uppercase">Realtime</span>
                </div>

                <div class="space-y-3.5">
                    <div class="bg-slate-50/60 border border-slate-100/60 p-3 rounded-xl flex flex-col gap-0.5">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">Department</span>
                        <span class="text-xs font-black text-slate-800">{{ doctor.specialty || 'General Medicine' }}</span>
                    </div>

                    <div class="bg-slate-50/60 border border-slate-100/60 p-3 rounded-xl flex flex-col gap-0.5">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">Doctor</span>
                        <span class="text-xs font-black text-slate-800">{{ doctor.name }}</span>
                    </div>

                    <div class="bg-slate-50/60 border border-slate-100/60 p-3 rounded-xl flex flex-col gap-0.5">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">Date</span>
                        <span class="text-xs font-black" :class="form.appointment_date ? 'text-blue-600' : 'text-slate-400'">{{ form.appointment_date || '-' }}</span>
                    </div>

                    <div class="bg-slate-50/60 border border-slate-100/60 p-3 rounded-xl flex flex-col gap-0.5">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">Time Slot</span>
                        <span class="text-xs font-black" :class="form.start_time ? 'text-slate-800' : 'text-slate-400'">
                            {{ form.start_time ? `${form.start_time} - ${form.end_time}` : '-' }}
                        </span>
                    </div>

                    <div class="bg-slate-50/60 border border-slate-100/60 p-3 rounded-xl flex flex-col gap-0.5">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">Patient</span>
                        <span class="text-xs font-black truncate max-w-[160px]" :class="form.patient_name ? 'text-slate-800' : 'text-slate-400'">{{ form.patient_name || '-' }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import MiniCalendar from './MiniCalendar.vue';

const props = defineProps({
    doctor: { type: Object, required: true }
});

const currentStep = ref(3); // Start straight at step 3 since dept/doctor are contextualized
const submitting = ref(false);
const serverError = ref('');

const journeySteps = [
    { id: 1, name: 'Department' },
    { id: 2, name: 'Doctor' },
    { id: 3, name: 'Available Date' },
    { id: 4, name: 'Time Slot' },
    { id: 5, name: 'Patient Info' },
    { id: 6, name: 'Confirm' }
];

const form = ref({
    doctor_id: props.doctor.id,
    appointment_date: '',
    start_time: '',
    end_time: '',
    patient_name: '',
    patient_phone: '',
    patient_age: '',
    patient_gender: ''
});

const activeStepTitle = computed(() => {
    return journeySteps.find(s => s.id === currentStep.value)?.name || '';
});

const getJourneyStepClasses = (stepId) => {
    if (currentStep.value === stepId) {
        return 'border-blue-200 bg-blue-50/30 text-blue-600 shadow-sm font-extrabold ring-1 ring-blue-500/10';
    }
    if (stepId <= 2 || currentStep.value > stepId) {
        return 'border-emerald-100 bg-emerald-50/40 text-emerald-700 opacity-90';
    }
    return 'border-slate-100 bg-white text-slate-400';
};

// Filter out slots match metrics matching designated chosen names strings
const filteredDaySlots = computed(() => {
    if (!form.value.appointment_date) return [];
    const dateObj = new Date(form.value.appointment_date);
    const dayName = dateObj.toLocaleString('en-US', { weekday: 'long' }).toLowerCase();
    return props.doctor.timetables.filter(t => t.day.trim().toLowerCase() === dayName);
});

// Structural check elements tracking moving boundaries validation flags
const isCurrentStepValid = computed(() => {
    if (currentStep.value === 3) return !!form.value.appointment_date;
    if (currentStep.value === 4) return !!form.value.start_time;
    if (currentStep.value === 5) {
        return form.value.patient_name.trim() && 
               form.value.patient_phone.trim() && 
               form.value.patient_age > 0 && 
               !!form.value.patient_gender;
    }
    return true;
});

const handleCalendarDateClick = () => {
    form.value.start_time = '';
    form.value.end_time = '';
};

const selectTimeSlot = (slot) => {
    form.value.start_time = slot.start_time;
    form.value.end_time = slot.end_time;
};

const stepForward = () => { if (isCurrentStepValid.value && currentStep.value < 6) currentStep.value++; };
const stepBackward = () => { if (currentStep.value > 3) currentStep.value--; };

const submitFinalForm = () => {
    submitting.value = true;
    serverError.value = '';

    router.post(route('website.appointments.store'), form.value, {
        onSuccess: () => submitting.value = false,
        onError: (err) => {
            submitting.value = false;
            serverError.value = err.message || Object.values(err)[0] || 'Booking error occurred.';
        }
    });
};
</script>