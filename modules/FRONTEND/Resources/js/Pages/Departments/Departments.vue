<template>
    <FrontendLayout>
        <!-- Premium Dual-Tone Hero Header Section -->
        <section class="relative w-full bg-slate-950 overflow-hidden border-b border-slate-900 py-16 md:py-28">
            <div class="container mx-auto px-6 md:px-12 relative z-10 flex flex-col lg:flex-row items-center gap-12">
                
                <!-- LEFT SIDE: Content Block -->
                <div class="w-full lg:w-7/12 text-center lg:text-left flex flex-col items-center lg:items-start">
                    <span class="inline-block text-xs font-bold tracking-[0.25em] uppercase text-blue-400 bg-blue-500/10 border border-blue-500/20 px-4 py-1.5 rounded-full mb-6">
                        Medical Excellence
                    </span>
                    
                    <!-- BOOM: Title with a guaranteed Left-to-Right Golden Flashing Shimmer -->
                    <h1 class="text-4xl md:text-6xl font-black tracking-tight text-transparent bg-clip-text golden-flash-text mb-6 leading-tight drop-shadow-sm select-none">
                        Specialized Departments
                    </h1>
                    
                    <div class="w-16 h-1 bg-gradient-to-r from-blue-500 via-amber-400 to-sky-400 rounded-full mb-6"></div>
                    
                    <p class="text-slate-300 max-w-xl text-base md:text-lg leading-relaxed font-medium">
                        World-class medical expertise paired with state-of-the-art technology to provide the highest standard of healthcare.
                    </p>
                </div>

                <!-- RIGHT SIDE: Vertical Infinite Cinematic Filmstrip -->
                <div class="w-full lg:w-5/12 relative group flex justify-center">
                    <!-- Subtle premium ambient background glow -->
                    <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-blue-600/20 to-amber-500/10 blur-xl opacity-75"></div>
                    
                    <!-- Main Frame Box Container -->
                    <div class="relative w-full max-w-md h-[380px] md:h-[450px] overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/50 backdrop-blur-md shadow-2xl p-4">
                        
                        <!-- Infinite Vertical Scrolling Container Wrapper -->
                        <div class="flex flex-col gap-4 h-full relative overflow-hidden">
                            
                            <!-- Track 1: First list moving upward -->
                            <div class="flex flex-col gap-4 marquee-vertical-track shrink-0">
                                <div v-for="(dept, index) in departments" :key="'vtrack1-' + index" 
                                    class="w-full h-[110px] shrink-0 relative overflow-hidden rounded-2xl border border-slate-800/80 bg-slate-950 shadow-lg flex items-center">
                                    <img :src="dept.banner_url" :alt="dept.name" class="absolute inset-0 w-full h-full object-cover object-right" />
                                    <!-- Rich protective dark gradient mask for premium contrast (Ref: image_6f29e7.png) -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/70 to-slate-950/20"></div>
                                    <span class="relative z-10 pl-6 text-base font-extrabold text-white tracking-wide pr-4 line-clamp-2 max-w-[70%]">
                                        {{ dept.name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Track 2: Identical duplicate for an uninterrupted seamless loop animation -->
                            <div class="flex flex-col gap-4 marquee-vertical-track shrink-0" aria-hidden="true">
                                <div v-for="(dept, index) in departments" :key="'vtrack2-' + index" 
                                    class="w-full h-[110px] shrink-0 relative overflow-hidden rounded-2xl border border-slate-800/80 bg-slate-950 shadow-lg flex items-center">
                                    <img :src="dept.banner_url" :alt="dept.name" class="absolute inset-0 w-full h-full object-cover object-right" />
                                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/70 to-slate-950/20"></div>
                                    <span class="relative z-10 pl-6 text-base font-extrabold text-white tracking-wide pr-4 line-clamp-2 max-w-[70%]">
                                        {{ dept.name }}
                                    </span>
                                </div>
                            </div>
                            
                        </div>

                        <!-- Vignette gradient shading masks to smoothly fade out top/bottom elements -->
                        <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-slate-900 to-transparent pointer-events-none z-10"></div>
                        <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-slate-900 to-transparent pointer-events-none z-10"></div>
                    </div>
                </div>

            </div>

            <!-- Ambient background vector light -->
            <div class="absolute top-0 left-1/4 -translate-x-1/2 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
        </section>

        <!-- Departments Grid Section -->
        <section id="departments" class="py-24 bg-slate-50 relative overflow-hidden scroll-reveal reveal-departments">
            <div class="container mx-auto px-4 relative z-10">
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <div v-for="dept in departments" :key="dept.id"
                        class="premium-sheen group relative flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200/70 bg-white shadow-lg transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-200/50">

                        <div class="relative overflow-hidden p-8">
                            <img
                                :src="dept?.banner_url"
                                alt="Department background"
                                class="absolute inset-0 h-full w-full object-cover object-right"
                            />
                            <div :class="`absolute inset-0 bg-gradient-to-br ${dept['color']} opacity-70`"></div>
                            <div class="absolute -right-14 -top-10 h-40 w-40 rounded-full bg-white/10 blur-3xl transition-all duration-500 group-hover:bg-white/25"></div>
                            <div class="absolute bottom-0 left-0 h-px w-full bg-white/25"></div>

                            <div class="relative z-10 mb-6 flex h-16 w-16 items-center justify-center rounded-2xl border border-white/25 bg-white/20 shadow-inner backdrop-blur-md transition-transform duration-500 group-hover:rotate-6">
                                <i :class="`fas ${dept['text_icon']} text-3xl text-white transition-transform duration-500 group-hover:scale-110`"></i>
                            </div>

                            <h3 class="relative z-10 mb-2 text-2xl font-black tracking-tight text-white">{{ dept['name'] }}</h3>
                            <p :class="`${dept['text-color']} relative z-10 text-sm leading-snug opacity-90 line-clamp-2`">
                                {{ dept.tagline }}
                            </p>
                        </div>

                        <div class="flex flex-grow flex-col p-8">
                            <p class="mb-8 min-h-[4.5rem] line-clamp-3 text-base leading-relaxed text-slate-600">
                                {{ dept['striped_desc'] }}
                            </p>

                            <div class="mb-7 grid grid-cols-1 gap-3">
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">Specialists</p>
                                    <p class="mt-1 text-sm font-extrabold text-slate-800">{{ dept.specialists }}+</p>
                                </div>
                            </div>

                            <a href="#appointment"
                                class="group/btn mt-auto inline-flex items-center justify-between rounded-2xl bg-gradient-to-r from-slate-900 to-blue-900 px-5 py-3.5 text-white shadow-lg shadow-blue-900/20 transition-all duration-300 hover:from-blue-800 hover:to-sky-600 hover:shadow-xl hover:shadow-sky-300/40 active:scale-[0.98]">
                                <span class="text-xs font-bold uppercase tracking-[0.16em]">Read More</span>
                                <span class="ml-3 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20 transition-all duration-300 group-hover/btn:translate-x-1 group-hover/btn:bg-white/30">
                                    <i class="fas fa-arrow-right text-[11px]"></i>
                                </span>
                            </a>
                        </div>

                        <div v-if="dept.available247"
                            class="absolute right-5 top-5 rounded-full border border-white/30 bg-white/20 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-white backdrop-blur-md">
                            Emergency 24/7
                        </div>
                    </div>
                </div>
                
                <div class="mt-20 text-center">
                    <a href="#departments" class="inline-flex items-center group text-blue-800 font-bold tracking-widest uppercase text-sm">
                        View All Medical Units 
                        <span class="ml-4 w-12 h-px bg-blue-800 transition-all group-hover:w-20"></span>
                    </a>
                </div>
            </div>
        </section>
    </FrontendLayout>
</template>

<script setup>
    import FrontendLayout from '../../Layout/FrontendLayout.vue';

    defineProps({
        departments: Array
    })
</script>

<style scoped>
/* 1. HARD-WIRED TEXT FLASH ENGINE */
.golden-flash-text {
    /* Premium Bluish Base with a sharp, reflective Golden Core */
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
    0% {
        background-position: 200% center;
    }
    100% {
        background-position: -200% center;
    }
}

/* 2. Vertical Marquee Track */
.marquee-vertical-track {
    animation: marqueeVertical 22s linear infinite;
}

.group:hover .marquee-vertical-track {
    animation-play-state: paused;
}

@keyframes marqueeVertical {
    0% {
        transform: translateY(0%);
    }
    100% {
        transform: translateY(calc(-100% - 1rem));
    }
}
</style>