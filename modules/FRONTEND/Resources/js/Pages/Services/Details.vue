<template>
    <FrontendLayout>
        <!-- HERO SECTION -->
        <section
            class="relative w-full border-b border-slate-200 py-20 md:py-24 flex items-center transition-all duration-500 overflow-hidden hero-section"
            :class="['bg-slate-50', { 'is-visible': isLoaded }]"
        >
            <div class="absolute inset-0 w-full h-full z-0 pointer-events-none overflow-hidden">
                <img
                    v-if="service?.image_url"
                    :src="service?.image_url"
                    :alt="service?.title"
                    class="w-full h-full object-cover object-center opacity-30 mix-blend-multiply banner-image"
                />
                <div class="absolute -right-20 -top-20 w-[500px] h-[500px] rounded-full bg-blue-500/5 blur-[120px] blob-float"></div>
                <div class="absolute left-1/3 -bottom-20 w-[400px] h-[400px] rounded-full bg-emerald-500/5 blur-[100px] blob-float blob-float-delayed"></div>
            </div>

            <div class="container mx-auto px-6 md:px-12 relative z-10">
                <div class="max-w-3xl bg-white/70 backdrop-blur-md rounded-3xl p-6 md:p-8 border border-white/80 shadow-xl shadow-slate-200/40 card-animate shimmer-card" :class="{ 'is-visible': isLoaded }">
                    <span class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.25em] px-3.5 py-1.5 rounded-xl mb-4 bg-slate-900 text-white shadow-sm badge-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        {{ service.is_featured ? 'Featured Service' : 'Medical Service' }}
                    </span>
                    <h1 class="text-3xl md:text-5xl font-black tracking-tight text-[#0A1F44] mb-4 leading-tight">
                        {{ service.title }}
                    </h1>
                    <div class="w-16 h-1 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full mb-5"></div>
                    <p class="text-slate-600 text-sm md:text-base leading-relaxed font-semibold">
                        {{ service?.short_description || 'Advanced medical healthcare service with expert specialists and modern diagnostics.' }}
                    </p>
                    <div class="flex items-center gap-4 mt-5 flex-wrap">
                        <span v-if="service.price" class="bg-blue-50 text-blue-700 font-bold text-sm px-3 py-1.5 rounded-lg border border-blue-100">
                            ৳{{ service.price }}
                        </span>
                        <span v-if="service.duration_minutes" class="bg-emerald-50 text-emerald-700 font-medium text-sm px-3 py-1.5 rounded-lg border border-emerald-100 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ service.duration_minutes }} min
                        </span>
                        <span v-if="service.is_active" class="bg-green-50 text-green-700 font-medium text-sm px-3 py-1.5 rounded-lg border border-green-100">
                            ✓ Available
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- MAIN CONTENT -->
        <section class="py-16 bg-slate-50/70 relative min-h-[600px]">
            <div class="container mx-auto px-6 md:px-12">
                <div class="grid lg:grid-cols-12 gap-10 items-start">

                    <!-- LEFT SIDEBAR -->
                    <aside class="lg:col-span-4 sticky top-8 z-20 space-y-6">
                        <!-- Service Directory -->
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-100/50 p-6 overflow-hidden">
                            <div class="flex items-center justify-between mb-5 px-1">
                                <h3 class="text-[11px] font-black uppercase tracking-wider text-slate-400">All Services</h3>
                                <span class="text-[10px] bg-slate-100 px-2.5 py-1 rounded-md text-slate-600 font-bold">{{ services?.length || 0 }} Services</span>
                            </div>
                            <nav class="space-y-1 max-h-[500px] overflow-y-auto pr-1 premium-scroll">
                                <a
                                    v-for="svc in services"
                                    :key="svc.id"
                                    :href="'/services/' + svc.slug"
                                    :class="[
                                        'flex items-center justify-between px-4 py-3.5 rounded-2xl font-bold text-sm transition-all duration-200 group border',
                                        svc.id === service.id
                                            ? 'bg-[#0A1F44] text-white border-transparent shadow-md'
                                            : 'bg-white text-slate-600 border-slate-100 hover:bg-slate-50 hover:border-slate-200 hover:text-slate-900'
                                    ]"
                                >
                                    <div class="flex items-center gap-3 truncate">
                                        <span :class="[
                                            'flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-xs transition-all duration-200',
                                            svc.id === service.id
                                                ? 'bg-white/10 text-white'
                                                : 'bg-slate-50 text-slate-500 group-hover:bg-white group-hover:shadow-sm'
                                        ]">
                                            {{ svc.icon || '🏥' }}
                                        </span>
                                        <span class="truncate text-[13.5px] tracking-wide font-semibold">{{ svc.title }}</span>
                                    </div>
                                    <svg class="w-3 h-3 opacity-40 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </nav>
                        </div>

                        <!-- Support Card -->
                        <div class="bg-[#0A1F44] p-6 rounded-3xl border border-slate-800 shadow-xl text-white relative overflow-hidden group support-card">
                            <div class="relative z-10">
                                <h4 class="text-sm font-bold tracking-wide mb-1">Need Consultation?</h4>
                                <p class="text-xs text-slate-400 mb-4 font-medium leading-relaxed">Connect with our 24/7 support desk for this service.</p>
                                <a href="tel:+8801234567890" class="inline-flex w-full items-center justify-center gap-2 text-white font-bold text-xs uppercase tracking-wider py-3 rounded-xl transition duration-200 bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-600/20 btn-pulse">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    Contact Support
                                </a>
                            </div>
                        </div>
                    </aside>

                    <!-- RIGHT MAIN CONTENT -->
                    <main class="lg:col-span-8 space-y-8">

                        <!-- Service Description (from rich text) -->
                        <section class="bg-white rounded-2xl p-8 lg:p-12 shadow-sm border border-slate-100">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">
                                    {{ service.icon || '🏥' }}
                                </div>
                                <h2 class="text-2xl lg:text-3xl font-bold text-slate-900">About This Service</h2>
                            </div>
                            <div
                                v-if="service.description"
                                class="prose prose-slate max-w-none prose-headings:text-slate-900 prose-p:text-slate-600 prose-p:leading-relaxed prose-li:text-slate-600 prose-a:text-blue-600 prose-strong:text-slate-800"
                                v-html="service.description"
                            ></div>
                            <p v-else class="text-slate-600 leading-relaxed">
                                {{ service.short_description || 'Detailed information about this service will be available soon. Please contact us for more information.' }}
                            </p>
                        </section>

                        <!-- IMAGE GALLERY -->
                        <section
                            v-if="service.gallery_images && service.gallery_images.length > 0"
                            class="bg-white rounded-2xl p-8 lg:p-12 shadow-sm border border-slate-100"
                        >
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-slate-900">Service Gallery</h2>
                                    <p class="text-sm text-slate-500">{{ service.gallery_images.length }} image(s)</p>
                                </div>
                            </div>

                            <!-- Gallery Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <button
                                    v-for="(img, index) in service.gallery_images"
                                    :key="img.id"
                                    @click="openLightbox(index)"
                                    class="group relative overflow-hidden rounded-xl aspect-[4/3] bg-slate-100 cursor-pointer"
                                >
                                    <img
                                        :src="img.url"
                                        :alt="img.name"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    />
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                        </svg>
                                    </div>
                                </button>
                            </div>
                        </section>

                        <!-- Thumbnail/Banner if present -->
                        <section
                            v-if="service.thumbnail_url && (!service.gallery_images || service.gallery_images.length === 0)"
                            class="bg-white rounded-2xl p-8 lg:p-12 shadow-sm border border-slate-100"
                        >
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-bold text-slate-900">Service Image</h2>
                            </div>
                            <div class="rounded-xl overflow-hidden">
                                <img :src="service.thumbnail_url" :alt="service.title" class="w-full h-auto object-cover rounded-xl" />
                            </div>
                        </section>

                        <!-- Pricing Info -->
                        <section
                            v-if="service.price || service.duration_minutes"
                            class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 lg:p-12 border border-blue-100"
                        >
                            <h2 class="text-2xl font-bold text-slate-900 mb-6">Pricing & Duration</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div v-if="service.price" class="bg-white rounded-xl p-6 border border-blue-100 shadow-sm">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Service Price</p>
                                    <p class="text-3xl font-black text-blue-700">৳{{ service.price }}</p>
                                </div>
                                <div v-if="service.duration_minutes" class="bg-white rounded-xl p-6 border border-blue-100 shadow-sm">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Duration</p>
                                    <p class="text-3xl font-black text-emerald-700">{{ service.duration_minutes }} <span class="text-lg font-medium text-slate-500">minutes</span></p>
                                </div>
                            </div>
                        </section>

                        <!-- CTA Section -->
                        <section class="bg-[#0A1F44] rounded-2xl p-8 lg:p-12 text-white text-center relative overflow-hidden">
                            <div class="absolute inset-0 pointer-events-none">
                                <div class="absolute -right-10 -top-10 w-[300px] h-[300px] rounded-full bg-blue-500/10 blur-[80px]"></div>
                            </div>
                            <div class="relative z-10">
                                <h2 class="text-2xl lg:text-3xl font-bold mb-3">Interested in {{ service.title }}?</h2>
                                <p class="text-slate-300 mb-6 max-w-lg mx-auto">
                                    Book an appointment or contact us to learn more about this service and how our expert team can help you.
                                </p>
                                <div class="flex items-center justify-center gap-4 flex-wrap">
                                    <a
                                        href="/appointment"
                                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm uppercase tracking-wider px-6 py-3 rounded-xl transition shadow-lg shadow-blue-600/20"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Book Appointment
                                    </a>
                                    <a
                                        href="/contact"
                                        class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-bold text-sm uppercase tracking-wider px-6 py-3 rounded-xl transition border border-white/20"
                                    >
                                        Contact Us
                                    </a>
                                </div>
                            </div>
                        </section>

                    </main>
                </div>
            </div>
        </section>

        <!-- LIGHTBOX MODAL -->
        <Teleport to="body">
            <Transition name="lightbox-fade">
                <div
                    v-if="lightboxOpen"
                    class="fixed inset-0 z-[9999] bg-black/90 flex items-center justify-center p-4"
                    @click.self="closeLightbox"
                >
                    <!-- Close -->
                    <button
                        @click="closeLightbox"
                        class="absolute top-4 right-4 text-white/80 hover:text-white bg-black/50 rounded-full p-2 z-10"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Prev -->
                    <button
                        v-if="service.gallery_images.length > 1"
                        @click="prevImage"
                        class="absolute left-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white bg-black/50 rounded-full p-3 z-10"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <!-- Image -->
                    <img
                        :src="service.gallery_images[lightboxIndex]?.url"
                        :alt="service.gallery_images[lightboxIndex]?.name"
                        class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl"
                    />

                    <!-- Next -->
                    <button
                        v-if="service.gallery_images.length > 1"
                        @click="nextImage"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white bg-black/50 rounded-full p-3 z-10"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Counter -->
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/60 text-white text-sm font-medium px-4 py-2 rounded-full">
                        {{ lightboxIndex + 1 }} / {{ service.gallery_images.length }}
                    </div>
                </div>
            </Transition>
        </Teleport>
    </FrontendLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import FrontendLayout from '../../Layout/FrontendLayout.vue';

const props = defineProps({
    service: { type: Object, required: true },
    departments: { type: Array, required: true },
    services: { type: Array, required: true },
});

const isLoaded = ref(false);

onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);
});

// Lightbox
const lightboxOpen = ref(false);
const lightboxIndex = ref(0);

const openLightbox = (index) => {
    lightboxIndex.value = index;
    lightboxOpen.value = true;
    document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
    lightboxOpen.value = false;
    document.body.style.overflow = '';
};

const nextImage = () => {
    lightboxIndex.value = (lightboxIndex.value + 1) % props.service.gallery_images.length;
};

const prevImage = () => {
    lightboxIndex.value = (lightboxIndex.value - 1 + props.service.gallery_images.length) % props.service.gallery_images.length;
};

// Keyboard navigation
const handleKeydown = (e) => {
    if (!lightboxOpen.value) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') nextImage();
    if (e.key === 'ArrowLeft') prevImage();
};

onMounted(() => window.addEventListener('keydown', handleKeydown));
onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = '';
});
</script>

<style scoped>
/* Scrollbar */
.premium-scroll::-webkit-scrollbar { width: 3px; }
.premium-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

/* Hero Section Entrance */
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

.banner-image {
    transition: transform 2.5s cubic-bezier(0.22, 1, 0.36, 1);
    transform: scale(1.02);
}
.hero-section.is-visible .banner-image {
    transform: scale(1);
}

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

/* Badge glow pulse */
.badge-pulse {
    animation: badgeGlow 3s ease-in-out infinite;
}
@keyframes badgeGlow {
    0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.2); }
    50% { box-shadow: 0 0 15px 4px rgba(16, 185, 129, 0.25); }
}

/* Floating background blobs */
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

/* Shimmer card */
.shimmer-card {
    position: relative;
    overflow: hidden;
}
.shimmer-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255, 255, 255, 0.3) 50%, transparent 60%);
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

/* Support button pulse */
.btn-pulse {
    animation: btnPulse 2.5s ease-in-out infinite;
}
@keyframes btnPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); transform: scale(1); }
    50% { box-shadow: 0 0 20px 6px rgba(59, 130, 246, 0.25); transform: scale(1.02); }
}

/* Support card glow */
.support-card {
    animation: cardGlow 4s ease-in-out infinite;
}
@keyframes cardGlow {
    0%, 100% { border-color: #1e293b; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
    50% { border-color: #3b82f6; box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.15); }
}

/* Lightbox transitions */
.lightbox-fade-enter-active,
.lightbox-fade-leave-active {
    transition: opacity 0.3s ease;
}
.lightbox-fade-enter-from,
.lightbox-fade-leave-to {
    opacity: 0;
}
</style>
