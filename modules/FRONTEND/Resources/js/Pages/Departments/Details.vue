<template>
    <FrontendLayout>
        <!-- 🎨 REFACTORED ULTRA-PREMIUM CLEAN BANNER HERO SECTION -->
        <section 
            class="relative w-full border-b border-slate-200 py-20 md:py-24 flex items-center transition-all duration-500 overflow-hidden"
            :class="currentDepartment['bg-color'] || 'bg-slate-50'"
        >
            <div class="absolute inset-0 w-full h-full z-0 pointer-events-none overflow-hidden">
                <img 
                    v-if="currentDepartment.banner_url"
                    :src="currentDepartment.banner_url" 
                    :alt="currentDepartment.name" 
                    class="w-full h-full object-cover object-right opacity-30 mix-blend-multiply scale-100"
                />
                <div class="absolute -right-20 -top-20 w-[500px] h-[500px] rounded-full bg-blue-500/5 blur-[120px]"></div>
                <div class="absolute left-1/3 -bottom-20 w-[400px] h-[400px] rounded-full bg-emerald-500/5 blur-[100px]"></div>
            </div>

            <div class="container mx-auto px-6 md:px-12 relative z-10">
                <div class="max-w-3xl bg-white/70 backdrop-blur-md rounded-3xl p-6 md:p-8 border border-white/80 shadow-xl shadow-slate-200/40">
                    <span class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.25em] px-3.5 py-1.5 rounded-xl mb-4 bg-slate-900 text-white shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> 
                        Medical Specialties Portal
                    </span>
                    <h1 class="text-3xl md:text-5xl font-black tracking-tight text-[#0A1F44] mb-4 leading-tight">
                        {{ currentDepartment.name }}
                    </h1>
                    <div class="w-16 h-1 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full mb-5"></div>
                    <p class="text-slate-600 text-sm md:text-base leading-relaxed font-semibold">
                        {{ currentDepartment.tagline || 'Advanced medical healthcare systems built around specialized patient diagnostics.' }}
                    </p>
                </div>
            </div>
        </section>

        <!-- MAIN LAYOUT MODULE CONTAINER -->
        <section class="py-16 bg-slate-50/70 relative min-h-[800px]">
            <div class="container mx-auto px-6 md:px-12">
                <div class="grid lg:grid-cols-12 gap-10 items-start">
                    
                    <!-- LEFT COLUMN SIDEBAR COMPONENT INDEX -->
                    <aside class="lg:col-span-4 sticky top-8 z-20 space-y-6">
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-100/50 p-6 overflow-hidden">
                            <div class="flex items-center justify-between mb-5 px-1">
                                <h3 class="text-[11px] font-black uppercase tracking-wider text-slate-400">Clinical Units Directory</h3>
                                <span class="text-[10px] bg-slate-100 px-2.5 py-1 rounded-md text-slate-600 font-bold">{{ departments.length }} Units</span>
                            </div>
                            
                            <nav class="space-y-1 max-h-[500px] overflow-y-auto pr-1 premium-scroll">
                                <a 
                                    v-for="dept in departments" 
                                    :key="dept.id"
                                    :href="`/departments/${dept.slug}`"
                                    :class="[
                                        'flex items-center justify-between px-4 py-3.5 rounded-2xl font-bold text-sm transition-all duration-200 group border',
                                        dept.id === currentDepartment.id ? 'bg-[#0A1F44] text-white border-transparent shadow-md' : 'bg-white text-slate-600 border-slate-100 hover:bg-slate-50 hover:border-slate-200 hover:text-slate-900'
                                    ]"
                                >
                                    <div class="flex items-center gap-3 truncate">
                                        <span :class="['flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-xs transition-all duration-200', dept.id === currentDepartment.id ? 'bg-white/10 text-white' : 'bg-slate-50 text-slate-500 group-hover:bg-white group-hover:shadow-sm']">
                                            <i :class="['fas', dept.text_icon || 'fa-notes-medical']"></i>
                                        </span>
                                        <span class="truncate text-[13.5px] tracking-wide font-semibold">{{ dept.name }}</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-[9px] opacity-40 group-hover:opacity-100 transition-opacity"></i>
                                </a>
                            </nav>
                        </div>

                        <div class="bg-[#0A1F44] p-6 rounded-3xl border border-slate-800 shadow-xl text-white relative overflow-hidden group">
                            <div class="relative z-10">
                                <h4 class="text-sm font-bold tracking-wide mb-1">Need Consultation Help?</h4>
                                <p class="text-xs text-slate-400 mb-4 font-medium leading-relaxed">Connect with our 24/7 continuous emergency desk.</p>
                                <a href="tel:+8801234567890" class="inline-flex w-full items-center justify-center gap-2 text-white font-bold text-xs uppercase tracking-wider py-3 rounded-xl transition duration-200 bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-600/20">
                                    <i class="fas fa-phone-alt text-[9px]"></i> Contact Support
                                </a>
                            </div>
                        </div>
                    </aside>

                    <!-- RIGHT COLUMN SPECIFIC MODULE MATRIX -->
                    <main class="lg:col-span-8 space-y-8">
                        <div v-if="currentDepartment.shortDesc" class="bg-white rounded-3xl border border-slate-200/60 shadow-sm p-6 md:p-8 relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-[4px] h-full bg-[#0A1F44]"></div>
                            <h2 class="text-xl font-bold text-[#0A1F44] tracking-tight mb-4">Clinical Framework</h2>
                            <p class="text-slate-600 text-[14.5px] leading-relaxed font-medium">{{ currentDepartment.shortDesc }}</p>
                        </div>

                        <!-- Bento Layout Cards Grid mapping -->
                        <div class="grid sm:grid-cols-2 gap-6 items-stretch">
                            <div v-if="currentDepartment.content_section_1" class="bg-white rounded-3xl border border-slate-200/60 p-6 flex flex-col h-full">
                                <h3 class="text-[15px] font-bold text-[#0A1F44] mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                                    <i class="fas fa-stethoscope text-blue-500 text-xs"></i> Key Services & Procedures
                                </h3>
                                <div v-html="currentDepartment.content_section_1" class="production-bento-list text-xs flex-grow"></div>
                            </div>

                            <div v-if="currentDepartment.content_section_2" class="bg-white rounded-3xl border border-slate-200/60 p-6 flex flex-col h-full">
                                <h3 class="text-[15px] font-bold text-[#0A1F44] mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                                    <i class="fas fa-heartbeat text-red-500 text-xs"></i> Conditions Treated
                                </h3>
                                <div v-html="currentDepartment.content_section_2" class="production-bento-list text-xs flex-grow"></div>
                            </div>
                        </div>

                        <!-- 🧑‍⚕️ REFACTORED WORKABLE MEDICAL CONSULTANTS MATRIX GRID -->
                        <div v-if="currentDepartment.doctors && currentDepartment.doctors.length > 0" class="space-y-5 pt-4">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-3 px-1">
                                <div>
                                    <h2 class="text-xl font-bold text-[#0A1F44] tracking-tight">Specialist Consultants</h2>
                                    <p class="text-xs text-slate-400 font-medium mt-0.5">Assigned physicians for {{ currentDepartment.name }}</p>
                                </div>
                                <span class="text-xs bg-slate-100 text-slate-700 font-bold px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm">
                                    {{ currentDepartment.doctors.length }} Professionals Available
                                </span>
                            </div>

                            <div class="grid sm:grid-cols-2 gap-6">
                                <div 
                                    v-for="doctor in currentDepartment.doctors" 
                                    :key="doctor.id || doctor.slug" 
                                    class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-300 flex flex-col overflow-hidden group"
                                >
                                    <!-- Header Profile Banner Module -->
                                    <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex gap-4 items-center">
                                        <!-- Safe Spatie-Aware Image Engine Slot -->
                                        <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 overflow-hidden shrink-0 shadow-inner flex items-center justify-center">
                                            <img 
                                                v-if="doctor.profile_image || getMediaUrl(doctor)" 
                                                :src="doctor.profile_image || getMediaUrl(doctor)" 
                                                :alt="doctor.name" 
                                                class="w-full h-full object-cover object-top transition-transform duration-300 group-hover:scale-105"
                                            />
                                            <div v-else class="text-slate-300">
                                                <i class="fas fa-user-md text-xl"></i>
                                            </div>
                                        </div>

                                        <div class="min-w-0 flex-grow">
                                            <h3 class="text-[15px] font-bold text-slate-900 truncate group-hover:text-blue-600 transition-colors tracking-wide">
                                                {{ doctor.name }}
                                            </h3>
                                            <span class="inline-block text-[10px] font-bold uppercase tracking-wider text-blue-600 mt-0.5 truncate max-w-full">
                                                {{ doctor.designations || 'Consultant Specialist' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Content Body Info -->
                                    <div class="p-5 flex-grow flex flex-col justify-between space-y-4">
                                        <div class="space-y-2.5">
                                            <div class="flex gap-2 items-start">
                                                <span class="text-[10px] bg-slate-100 px-2 py-0.5 rounded font-extrabold text-slate-500 tracking-wide shrink-0 mt-0.5">EDU</span>
                                                <p class="text-[12.5px] text-slate-600 font-medium leading-relaxed line-clamp-2">
                                                    {{ doctor.qualifications || 'Information pending verification' }}
                                                </p>
                                            </div>
                                            <div class="flex gap-2 items-start">
                                                <span class="text-[10px] bg-slate-100 px-2 py-0.5 rounded font-extrabold text-slate-500 tracking-wide shrink-0 mt-0.5">DEPT</span>
                                                <p class="text-[12.5px] text-slate-500 font-semibold italic truncate">
                                                    {{ doctor.specialty || currentDepartment.name }}
                                                </p>
                                            </div>
                                        </div>

                                        <a 
                                            :href="`/doctors/${doctor.slug}`" 
                                            class="inline-flex w-full items-center justify-center gap-1.5 text-center text-[11px] font-bold uppercase tracking-wider text-slate-700 bg-slate-50 hover:bg-[#0A1F44] hover:text-white border border-slate-200 hover:border-[#0A1F44] py-2.5 rounded-xl transition-all duration-200 shadow-sm"
                                        >
                                            View Professional Profile <i class="fas fa-arrow-right text-[9px]"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Panels Section -->
                        <div v-if="parsedFaqs && parsedFaqs.length > 0" class="bg-white rounded-3xl border border-slate-200/60 p-6 md:p-8">
                            <h2 class="text-xl font-bold text-[#0A1F44] tracking-tight mb-5">Departmental FAQs</h2>
                            <div class="space-y-3">
                                <div 
                                    v-for="(faq, index) in parsedFaqs" 
                                    :key="'faq-' + index" 
                                    class="border rounded-2xl overflow-hidden transition-all duration-200"
                                    :class="activeFaq === index ? 'bg-slate-50/50 border-slate-300' : 'bg-white border-slate-100 hover:border-slate-200'"
                                >
                                    <button 
                                        @click="toggleFaq(index)"
                                        class="w-full flex items-center justify-between p-4 text-left font-bold text-slate-800 focus:outline-none text-[14px]"
                                    >
                                        <span class="pr-4 font-bold text-slate-900">{{ faq.question || faq.title }}</span>
                                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 text-[9px] transition-transform" :class="{ 'rotate-180 bg-[#0A1F44] text-white': activeFaq === index }">
                                            <i class="fas fa-chevron-down"></i>
                                        </span>
                                    </button>
                                    <div v-show="activeFaq === index" class="px-4 pb-4 pt-1 text-[13.5px] text-slate-600 border-t border-slate-100/70 leading-relaxed">
                                        {{ faq.answer || faq.body || faq.content }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </section>
    </FrontendLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import FrontendLayout from '../../Layout/FrontendLayout.vue';

const props = defineProps({
    currentDepartment: { type: Object, required: true },
    departments: { type: Array, required: true }
});

const activeFaq = ref(null);
const toggleFaq = (index) => { activeFaq.value = activeFaq.value === index ? null : index; };

const parsedFaqs = computed(() => {
    if (!props.currentDepartment.faq) return [];
    try {
        return typeof props.currentDepartment.faq === 'string' ? JSON.parse(props.currentDepartment.faq) : props.currentDepartment.faq;
    } catch (e) { return []; }
});

/**
 * 🛠️ Spatie Media Collection Extractor Helper Function
 * Inspects nested JSON relationship data structures for file generation.
 */
const getMediaUrl = (doctor) => {
    if (doctor.media && Array.isArray(doctor.media) && doctor.media.length > 0) {
        const mediaItem = doctor.media.find(m => m.collection_name === 'profile_image') || doctor.media[0];
        if (mediaItem && mediaItem.id && mediaItem.file_name) {
            return `/storage/${mediaItem.id}/${mediaItem.file_name}`;
        }
    }
    return null;
};
</script>

<style scoped>
.premium-scroll::-webkit-scrollbar { width: 3px; }
.premium-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>

<style>
.production-bento-list ul {
    list-style-type: none !important;
    padding-left: 0 !important;
    margin: 0 !important;
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
}
.production-bento-list li {
    position: relative !important;
    padding: 0.75rem 0.75rem 0.75rem 2.25rem !important;
    font-size: 13px !important;
    line-height: 1.5 !important;
    color: #475569 !important;
    background: #f8fafc !important;
    border: 1px solid #f1f5f9 !important;
    border-radius: 0.75rem !important;
    transition: all 0.2s ease;
}
.production-bento-list li:hover {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    transform: translateX(2px);
}
.production-bento-list li::before {
    content: "\f00c" !important;
    font-family: "Font Awesome 5 Free" !important;
    font-weight: 900 !important;
    position: absolute !important;
    left: 0.85rem !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #10b981 !important;
    font-size: 8px !important;
}
.production-bento-list span.text-success, .production-bento-list li span { display: none !important; }
</style>