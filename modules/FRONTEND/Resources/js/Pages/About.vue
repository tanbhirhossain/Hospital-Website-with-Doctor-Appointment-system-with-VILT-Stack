<template>
    <FrontendLayout>
        <!-- Full-Width Cinematic Video-Movement Banner Placed Directly Against the Navbar -->
        <section class="relative w-full overflow-hidden bg-slate-950 group">
            <!-- Video Movement Motion Banner Container -->
            <div class="relative w-full h-[420px] sm:h-[520px] lg:h-[620px] overflow-hidden flex items-center justify-center">
                
                <!-- Simulated Smooth Video-Motion Pan & Zoom Background Image -->
                <div class="absolute inset-0 bg-cover bg-center animate-video-motion scale-105 filter brightness-75 contrast-110"
                     style="background-image: url('https://amzhospitalbd.com/storage/AMZ.jpg');">
                </div>

                <!-- Multi-Layer Cinematic Gradient Overlays for Depth and Readability -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-blue-950/70 via-slate-950/30 to-cyan-950/50 mix-blend-multiply"></div>

                <!-- Floating Ambient Glow Elements -->
                <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/20 rounded-full blur-[120px] animate-pulse pointer-events-none"></div>
                <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-cyan-600/20 rounded-full blur-[150px] animate-pulse pointer-events-none" style="animation-duration: 5s;"></div>

                <!-- Centered Hero Title and Badge content -->
                <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-blue-200 bg-white/10 backdrop-blur-md border border-white/20 rounded-full shadow-lg animate-fade-in-down">
                        <span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span>
                        About AMZ Hospital Ltd.
                    </div>

                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold text-white tracking-tight leading-[1.1] drop-shadow-xl">
                        Redefining Healthcare Excellence <span class="bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-300 bg-clip-text text-transparent">in Dhaka</span>
                    </h1>

                    <p class="text-lg sm:text-2xl text-blue-100/90 font-light max-w-3xl mx-auto drop-shadow-md">
                        AMZ Hospital Ltd. measures its success by counting lives saved, hopes restored, and care delivered with unparalleled quality and professionalism.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                        <a href="#virtual-tour" class="px-7 py-3.5 rounded-full bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-semibold text-sm shadow-xl hover:shadow-blue-600/50 hover:scale-105 transition-all duration-300 border border-blue-400/30">
                            Take Virtual Tour
                        </a>
                        <a href="#overview-section" class="px-7 py-3.5 rounded-full bg-white/10 hover:bg-white/20 text-white backdrop-blur-md border border-white/20 font-semibold text-sm transition-all duration-300">
                            Explore Facilities
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Page Body Container -->
        <div class="relative bg-gradient-to-b from-slate-50 via-white to-blue-50/25 min-h-screen py-20 px-4 sm:px-6 lg:px-8 overflow-hidden" id="overview-section">
            
            <!-- Dynamic Ambient Background Glow Accents -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1200px] h-[450px] bg-gradient-to-tr from-blue-200/40 via-cyan-100/30 to-transparent blur-[120px] pointer-events-none -z-10"></div>
            <div class="absolute top-[40%] right-[-10%] w-[500px] h-[500px] bg-blue-100/30 blur-[150px] pointer-events-none -z-10"></div>

            <div class="max-w-7xl mx-auto space-y-24">
                
                <!-- Trust Stats Section -->
                <div class="text-center max-w-4xl mx-auto space-y-8 animate-fade-in-down">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl mx-auto">
                        <div class="bg-white/90 backdrop-blur-xl border border-blue-100 p-4 rounded-2xl shadow-sm hover:shadow-md transition-all text-center group">
                            <div class="text-2xl sm:text-3xl font-black bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">100+</div>
                            <div class="text-xs uppercase font-semibold tracking-wider text-slate-500 mt-1">State-of-Art Beds</div>
                        </div>
                        <div class="bg-white/90 backdrop-blur-xl border border-blue-100 p-4 rounded-2xl shadow-sm hover:shadow-md transition-all text-center group">
                            <div class="text-2xl sm:text-3xl font-black bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">28+</div>
                            <div class="text-xs uppercase font-semibold tracking-wider text-slate-500 mt-1">Departments</div>
                        </div>
                        <div class="bg-white/90 backdrop-blur-xl border border-blue-100 p-4 rounded-2xl shadow-sm hover:shadow-md transition-all text-center group">
                            <div class="text-2xl sm:text-3xl font-black bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">70+</div>
                            <div class="text-xs uppercase font-semibold tracking-wider text-slate-500 mt-1">World-Class Experts</div>
                        </div>
                        <div class="bg-white/90 backdrop-blur-xl border border-blue-100 p-4 rounded-2xl shadow-sm hover:shadow-md transition-all text-center group">
                            <div class="text-2xl sm:text-3xl font-black bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">8</div>
                            <div class="text-xs uppercase font-semibold tracking-wider text-slate-500 mt-1">Centers of Excellence</div>
                        </div>
                    </div>
                </div>

                <!-- Main Content & Overview Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    
                    <!-- Text Details Enhanced Card -->
                    <div class="lg:col-span-7 space-y-6 text-slate-700 leading-relaxed text-base sm:text-lg bg-white/90 backdrop-blur-2xl p-8 sm:p-12 rounded-[2.5rem] border border-blue-100 shadow-xl shadow-blue-900/5">
                        <p class="font-medium text-slate-900 text-xl leading-snug">
                            It was founded by a team of visionary clinicians, leaders from civil society, and various disciplines with a shared vision of reducing healthcare inequalities.
                        </p>
                        
                        <div class="py-2 space-y-4">
                            <p>
                                This <strong class="text-slate-900 font-semibold bg-blue-50 px-2.5 py-1 rounded-lg text-blue-700 border border-blue-200/60">ISO-certified 100-bed state-of-the-art facility</strong> is anchored by comprehensive departments and specialized centers. It comprises 24/7 emergency services, OPD, advanced Operation Theatres, critical care units (ICU, HDU, CCU, NICU), Dialysis, Stroke unit, Cath-lab, IVF, Normal Delivery, and a Category-A Diagnostic Laboratory.
                            </p>
                        </div>

                        <div class="pt-4 border-t border-blue-100/80 flex items-center gap-4 text-slate-600 text-sm">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 shrink-0 font-bold shadow-sm">
                                ?
                            </div>
                            <span>Renowned, trusted, and leading referral hospital in Dhaka serving patients nationwide alongside certified Contract Research Organization (CRO) operations.</span>
                        </div>
                    </div>

                    <!-- Highlight Quote Box -->
                    <div class="lg:col-span-5">
                        <div class="relative group">
                            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 via-cyan-600 to-blue-700 rounded-[2.5rem] blur-xl opacity-30 group-hover:opacity-50 transition duration-500"></div>
                            
                            <div class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-blue-950 text-white rounded-[2.5rem] p-8 sm:p-10 shadow-2xl overflow-hidden border border-blue-400/30">
                                <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                                <div class="absolute -left-12 -top-12 w-48 h-48 bg-black/20 rounded-full blur-2xl pointer-events-none"></div>
                                
                                <div class="relative z-10 space-y-6">
                                    <svg class="w-12 h-12 text-blue-200/80" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.996v10h-9.996z"/>
                                    </svg>
                                    <blockquote class="text-xl sm:text-2xl font-medium italic leading-relaxed text-blue-50">
                                        “AMZ is built to dismantle the distress of patients through Timely Service and Affordable Cost.”
                                    </blockquote>
                                    <div class="pt-4 border-t border-blue-500/40 text-xs font-bold uppercase tracking-widest text-blue-200 flex items-center justify-between">
                                        <span>— AMZ Hospital Philosophy</span>
                                        <span class="w-8 h-0.5 bg-blue-300/50"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Commitment Section -->
                <div class="relative overflow-hidden bg-gradient-to-r from-white via-blue-50/40 to-white rounded-[2.5rem] p-8 sm:p-14 shadow-xl border border-blue-100 backdrop-blur-xl">
                    <div class="absolute top-0 right-0 w-80 h-80 bg-blue-200/40 rounded-full blur-[100px] -z-10 pointer-events-none"></div>
                    <div class="max-w-3xl space-y-5">
                        <div class="inline-flex items-center gap-2 text-blue-700 font-bold text-xs uppercase tracking-widest bg-blue-100/80 px-3.5 py-1.5 rounded-full border border-blue-200/60">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Social Responsibility
                        </div>
                        <h3 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Beyond Treatment – Our Social Commitment</h3>
                        <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                            Our commitment is praiseworthy as we have served selflessly during the COVID-19 pandemic, disease outbreaks, national crises like the July ’24 Revolution, natural calamities (floods, earthquakes), and man-made disasters (fires, accidents).
                        </p>
                    </div>
                </div>

                <!-- Video Section -->
                <div class="space-y-10 text-center" id="virtual-tour">
                    <div class="space-y-3">
                        <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Take a Virtual Tour</h3>
                        <p class="text-slate-500 text-base">Experience our state-of-the-art facilities from anywhere</p>
                    </div>

                    <div class="max-w-4xl mx-auto group relative">
                        <div class="absolute -inset-2 bg-gradient-to-r from-blue-600 via-cyan-600 to-blue-700 rounded-[2.5rem] blur-2xl opacity-25 group-hover:opacity-45 transition duration-700"></div>
                        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl bg-black border-2 border-white/80 aspect-video">
                            <iframe 
                                class="absolute top-0 left-0 w-full h-full transition-transform duration-700 group-hover:scale-[1.01]"
                                src="https://www.youtube.com/embed/XYLjEWdhnE4?si=sMGY0m3KckDS9FVT" 
                                title="AMZ Hospital Introduction" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500 font-medium">
                        Watch original video on YouTube: 
                        <a href="http://www.youtube.com/watch?v=XYLjEWdhnE4" target="_blank" class="text-blue-600 underline font-semibold hover:text-blue-700 transition-colors ml-1">
                            AMZ Hospital Introduction
                        </a> [00:00]
                    </div>
                </div>

            </div>
        </div>
    </FrontendLayout>
</template>

<script setup>
    import FrontendLayout from '../Layout/FrontendLayout.vue';
</script>

<style scoped>
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Premium Cinematic Video-Movement Animation */
@keyframes videoMotion {
    0% {
        transform: scale(1.03) translate(0px, 0px);
    }
    50% {
        transform: scale(1.08) translate(-12px, -8px);
    }
    100% {
        transform: scale(1.03) translate(0px, 0px);
    }
}

.animate-fade-in-down {
    animation: fadeInDown 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.animate-video-motion {
    animation: videoMotion 15s ease-in-out infinite;
    will-change: transform;
}
</style>