<template>
    <div class="border border-slate-100 rounded-3xl p-5 bg-white shadow-sm max-w-md mx-auto">
        <!-- Calendar Header Controls -->
        <div class="flex items-center justify-between mb-6 px-1">
            <button @click="prevMonth" class="w-8 h-8 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 active:scale-95 transition-all">
                <i class="fas fa-chevron-left text-xs"></i>
            </button>
            <h3 class="text-sm font-black text-slate-800 tracking-wide uppercase">
                {{ monthYearString }}
            </h3>
            <button @click="nextMonth" class="w-8 h-8 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 active:scale-95 transition-all">
                <i class="fas fa-chevron-right text-xs"></i>
            </button>
        </div>

        <!-- Weekday Labels Grid -->
        <div class="grid grid-cols-7 gap-1 text-center mb-2">
            <span v-for="day in ['S', 'M', 'T', 'W', 'T', 'F', 'S']" :key="day" class="text-[11px] font-black text-slate-400">
                {{ day }}
            </span>
        </div>

        <!-- Days Grid Framework -->
        <div class="grid grid-cols-7 gap-1.5">
            <!-- Empty Spacer Nodes for Month Offset -->
            <div v-for="blank in blankDaysPrefix" :key="'blank-' + blank" class="aspect-square"></div>

            <!-- Operational Day Nodes -->
            <button 
                v-for="day in daysInMonthCount" 
                :key="'day-' + day"
                :disabled="!isDateAvailable(day)"
                @click="handleDateSelect(day)"
                class="aspect-square rounded-xl text-xs font-bold flex flex-col items-center justify-center transition-all relative group"
                :class="getDateClasses(day)"
            >
                <span>{{ day }}</span>
                <!-- Micro active selection indicator dot -->
                <span v-if="isSelected(day)" class="w-1 h-1 rounded-full bg-blue-600 absolute bottom-1.5"></span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    modelValue: String,
    timetables: { type: Array, default: () => [] }
});
const emit = defineSig = defineEmits(['update:modelValue', 'dateSelected']);

const currentNavDate = ref(new Date());
const weekdaysMap = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

const monthYearString = computed(() => {
    return currentNavDate.value.toLocaleString('default', { month: 'long', year: 'numeric' });
});

const daysInMonthCount = computed(() => {
    return new Date(currentNavDate.value.getFullYear(), currentNavDate.value.getMonth() + 1, 0).getDate();
});

const blankDaysPrefix = computed(() => {
    return new Date(currentNavDate.value.getFullYear(), currentNavDate.value.getMonth(), 1).getDay();
});

// Checks if day falls into rolling 7 days AND matches doctor availability schedule
const isDateAvailable = (day) => {
    const checkDate = new Date(currentNavDate.value.getFullYear(), currentNavDate.value.getMonth(), day);
    const today = new Date();
    today.setHours(0,0,0,0);
    
    const endWindow = new Date();
    endWindow.setDate(today.getDate() + 7);
    endWindow.setHours(23,59,59,999);

    if (checkDate < today || checkDate > endWindow) return false;

    const dayName = weekdaysMap[checkDate.getDay()];
    return props.timetables.some(t => t.day.trim().toLowerCase() === dayName);
};

const isSelected = (day) => {
    if (!props.modelValue) return false;
    const checkStr = formatDateString(day);
    return props.modelValue === checkStr;
};

const formatDateString = (day) => {
    const y = currentNavDate.value.getFullYear();
    const m = String(currentNavDate.value.getMonth() + 1).padStart(2, '0');
    const d = String(day).padStart(2, '0');
    return `${y}-${m}-${d}`;
};

const getDateClasses = (day) => {
    const available = isDateAvailable(day);
    const selected = isSelected(day);

    if (selected) {
        return 'bg-emerald-50 text-emerald-600 border border-emerald-300 shadow-sm z-10 font-extrabold';
    }
    if (available) {
        return 'bg-white border border-slate-200 text-slate-800 cursor-pointer hover:border-blue-400 hover:text-blue-600 hover:scale-105 shadow-sm';
    }
    return 'bg-slate-50/40 text-slate-300 cursor-not-allowed opacity-40';
};

const handleDateSelect = (day) => {
    const dateStr = formatDateString(day);
    emit('update:modelValue', dateStr);
    emit('dateSelected', dateStr);
};

const prevMonth = () => currentNavDate.value = new Date(currentNavDate.value.getFullYear(), currentNavDate.value.getMonth() - 1, 1);
const nextMonth = () => currentNavDate.value = new Date(currentNavDate.value.getFullYear(), currentNavDate.value.getMonth() + 1, 1);
</script>