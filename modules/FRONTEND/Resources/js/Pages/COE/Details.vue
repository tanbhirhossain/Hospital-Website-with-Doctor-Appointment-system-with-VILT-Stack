<template>
    <FrontendLayout>
        <!-- 🎥 CINEMATIC HERO SECTION -->
        <section 
            class="relative w-full border-b border-slate-200 py-20 md:py-24 flex items-center transition-all duration-500 overflow-hidden hero-section"
            :class="[coe['bg_color'] || 'bg-slate-50', { 'is-visible': isLoaded }]"
        >
            <div class="absolute inset-0 w-full h-full z-0 pointer-events-none overflow-hidden">
                <img 
                    v-if="coe.banner_url"
                    :src="coe.banner_url" 
                    :alt="coe.name" 
                    class="w-full h-full object-cover object-right opacity-30 mix-blend-multiply banner-image"
                />
                <!-- Floating background blobs -->
                <div class="absolute -right-20 -top-20 w-[500px] h-[500px] rounded-full bg-blue-500/5 blur-[120px] blob-float"></div>
                <div class="absolute left-1/3 -bottom-20 w-[400px] h-[400px] rounded-full bg-emerald-500/5 blur-[100px] blob-float blob-float-delayed"></div>
            </div>

            <div class="container mx-auto px-6 md:px-12 relative z-10">
                <div class="max-w-3xl bg-white/70 backdrop-blur-md rounded-3xl p-6 md:p-8 border border-white/80 shadow-xl shadow-slate-200/40 card-animate shimmer-card" :class="{ 'is-visible': isLoaded }">
                    <span class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.25em] px-3.5 py-1.5 rounded-xl mb-4 bg-slate-900 text-white shadow-sm badge-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> 
                        Center of Excellence
                    </span>
                    <h1 class="text-3xl md:text-5xl font-black tracking-tight text-[#0A1F44] mb-4 leading-tight">
                        {{ coe.name }}
                    </h1>
                    <div class="w-16 h-1 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full mb-5"></div>
                    <p class="text-slate-600 text-sm md:text-base leading-relaxed font-semibold">
                        {{ coe.tagline || 'Advanced medical healthcare systems built around specialized patient diagnostics.' }}
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
                                <h3 class="text-[11px] font-black uppercase tracking-wider text-slate-400">Directory</h3>
                                <span class="text-[10px] bg-slate-100 px-2.5 py-1 rounded-md text-slate-600 font-bold">{{ coes.length }} Units</span>
                            </div>
                            
                            <TransitionGroup 
                                tag="nav" 
                                name="department-item" 
                                appear
                                class="space-y-1 max-h-[500px] overflow-y-auto pr-1 premium-scroll"
                            >
                                <a 
                                    v-for="(dept, index) in coes" 
                                    :key="dept.id"
                                    :href="`/center-of-excellence/${dept.slug}`"
                                    :style="{ transitionDelay: index * 50 + 'ms' }"
                                    :class="[
                                        'flex items-center justify-between px-4 py-3.5 rounded-2xl font-bold text-sm transition-all duration-200 group border',
                                        dept.id === coe.id ? 'bg-[#0A1F44] text-white border-transparent shadow-md' : 'bg-white text-slate-600 border-slate-100 hover:bg-slate-50 hover:border-slate-200 hover:text-slate-900'
                                    ]"
                                >
                                    <div class="flex items-center gap-3 truncate">
                                        <span :class="['flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-xs transition-all duration-200', dept.id === coe.id ? 'bg-white/10 text-white' : 'bg-slate-50 text-slate-500 group-hover:bg-white group-hover:shadow-sm']">
                                            <i :class="['fas', dept.text_icon || 'fa-notes-medical']"></i>
                                        </span>
                                        <span class="truncate text-[13.5px] tracking-wide font-semibold">{{ dept.name }}</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-[9px] opacity-40 group-hover:opacity-100 transition-opacity"></i>
                                </a>
                            </TransitionGroup>
                        </div>

                        <!-- Support card with pulsing shadow -->
                        <div class="bg-[#0A1F44] p-6 rounded-3xl border border-slate-800 shadow-xl text-white relative overflow-hidden group support-card">
                            <div class="relative z-10">
                                <h4 class="text-sm font-bold tracking-wide mb-1">Need Consultation Help?</h4>
                                <p class="text-xs text-slate-400 mb-4 font-medium leading-relaxed">Connect with our 24/7 continuous emergency desk.</p>
                                <a href="tel:+8801234567890" class="inline-flex w-full items-center justify-center gap-2 text-white font-bold text-xs uppercase tracking-wider py-3 rounded-xl transition duration-200 bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-600/20 btn-pulse">
                                    <i class="fas fa-phone-alt text-[9px]"></i> Contact Support
                                </a>
                            </div>
                        </div>
                    </aside>

                    <!-- RIGHT COLUMN SPECIFIC MODULE MATRIX -->
                    <main class="lg:col-span-8 space-y-8">
                       <div v-html="coe.description" class="fade-in-content" :class="{ 'is-visible': isLoaded }"></div>
                    </main>
                </div>
            </div>
        </section>
    </FrontendLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import FrontendLayout from '../../Layout/FrontendLayout.vue';

const props = defineProps({
    coe: { type: Object, required: true },
    departments: { type: Array, required: true },
    coes: { type: Array, required: true },
});

const isLoaded = ref(false);

onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);
});

const activeFaq = ref(null);
const toggleFaq = (index) => { activeFaq.value = activeFaq.value === index ? null : index; };

const parsedFaqs = computed(() => {
    if (!props.coe.faq) return [];
    try {
        return typeof props.coe.faq === 'string' ? JSON.parse(props.coe.faq) : props.coe.faq;
    } catch (e) { return []; }
});

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
/* --- Scrollbar --- */
.premium-scroll::-webkit-scrollbar { width: 3px; }
.premium-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

/* --- Hero Section Entrance Zoom --- */
.hero-section {
    opacity: 0;
    transform: scale(1.04);
    transition: opacity 1.4s cubic-bezier(0.22, 1, 0.36, 1),
                transform 1.8s cubic-bezier(0.22, 1, 0.36, 1);
}
.hero-section.is-visible {
    opacity: 1;
    transform: scale(1);
}

/* Background image gentle motion */
.banner-image {
    transition: transform 2.5s cubic-bezier(0.22, 1, 0.36, 1);
    transform: scale(1.02);
}
.hero-section.is-visible .banner-image {
    transform: scale(1);
}

/* --- Hero Card Slide-up --- */
.card-animate {
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 1s cubic-bezier(0.22, 1, 0.36, 1) 0.35s,
                transform 1s cubic-bezier(0.22, 1, 0.36, 1) 0.35s;
}
.card-animate.is-visible {
    opacity: 1;
    transform: translateY(0);
}

/* --- Department Items Stagger --- */
.department-item-enter-active {
    transition: all 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}
.department-item-enter-from {
    opacity: 0;
    transform: translateX(-20px);
}
.department-item-enter-to {
    opacity: 1;
    transform: translateX(0);
}

/* --- Main Content Fade-in --- */
.fade-in-content {
    opacity: 0;
    transition: opacity 0.8s ease 0.6s;
}
.fade-in-content.is-visible {
    opacity: 1;
}

/* ============================================ */
/* 🌀 INFINITE ANIMATIONS (looping)             */
/* ============================================ */

/* 1. Badge glow pulse */
.badge-pulse {
    animation: badgeGlow 3s ease-in-out infinite;
}
@keyframes badgeGlow {
    0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.2); }
    50% { box-shadow: 0 0 15px 4px rgba(16, 185, 129, 0.25); }
}

/* 2. Floating background blobs */
.blob-float {
    animation: floatBlob 8s ease-in-out infinite;
}
.blob-float-delayed {
    animation-delay: -3s;
}
@keyframes floatBlob {
    0%, 100% { transform: translateY(0px) scale(1); }
    50% { transform: translateY(-20px) scale(1.02); }
}

/* 3. Shimmer card (subtle gradient sweep) */
.shimmer-card {
    position: relative;
    overflow: hidden;
}
.shimmer-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        105deg,
        transparent 40%,
        rgba(255, 255, 255, 0.3) 50%,
        transparent 60%
    );
    background-size: 300% 100%;
    animation: shimmerSweep 6s infinite linear;
    pointer-events: none;
    mix-blend-mode: overlay;
    border-radius: inherit;
}
@keyframes shimmerSweep {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* 4. Support button pulse (shadow + scale) */
.btn-pulse {
    animation: btnPulse 2.5s ease-in-out infinite;
}
@keyframes btnPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); transform: scale(1); }
    50% { box-shadow: 0 0 20px 6px rgba(59, 130, 246, 0.25); transform: scale(1.02); }
}

/* 5. Optional: card border glow for the support card */
.support-card {
    animation: cardGlow 4s ease-in-out infinite;
}
@keyframes cardGlow {
    0%, 100% { border-color: #1e293b; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
    50% { border-color: #3b82f6; box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.15); }
}
</style>

<!-- Keep existing global styles for production-bento-list -->
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