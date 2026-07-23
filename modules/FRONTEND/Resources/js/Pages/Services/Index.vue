<template>
    <FrontendLayout>
        <!-- Hero Banner -->
        <section class="relative bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 py-24 md:py-32 overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -right-20 -top-20 w-[500px] h-[500px] rounded-full bg-blue-500/10 blur-[120px]"></div>
                <div class="absolute left-1/4 -bottom-20 w-[400px] h-[400px] rounded-full bg-indigo-500/10 blur-[100px]"></div>
            </div>
            <div class="container mx-auto px-6 relative z-10 text-center">
                <span class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.25em] px-4 py-1.5 rounded-full mb-5 bg-white/10 text-white border border-white/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Our Services
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white mb-4 tracking-tight">
                    Healthcare Services
                </h1>
                <p class="text-blue-200 text-lg max-w-2xl mx-auto">
                    Comprehensive medical services powered by advanced technology and compassionate care, all under one roof.
                </p>
            </div>
        </section>

        <!-- Services Grid -->
        <section class="py-16 md:py-20 bg-slate-50">
            <div class="container mx-auto px-6">
                <div v-if="services && services.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <a
                        v-for="(service, index) in services"
                        :key="service.id"
                        :href="'/services/' + service.slug"
                        class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-blue-200 transition-all duration-300 overflow-hidden"
                        :style="{ animationDelay: index * 80 + 'ms' }"
                    >
                        <!-- Thumbnail -->
                        <div class="relative h-52 overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50">
                            <img
                                v-if="service.thumbnail_url"
                                :src="service.thumbnail_url"
                                :alt="service.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <span class="text-5xl">{{ service.icon || '🏥' }}</span>
                            </div>
                            <!-- Featured badge -->
                            <span
                                v-if="service.is_featured"
                                class="absolute top-3 right-3 bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg shadow"
                            >
                                Featured
                            </span>
                            <!-- Gallery count -->
                            <span
                                v-if="service.gallery_images && service.gallery_images.length > 0"
                                class="absolute bottom-3 right-3 bg-black/60 text-white text-[10px] font-bold px-2.5 py-1 rounded-lg backdrop-blur flex items-center gap-1"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ service.gallery_images.length }}
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <div class="flex items-start gap-3 mb-3">
                                <span v-if="service.icon && !service.thumbnail_url" class="text-2xl flex-shrink-0">{{ service.icon }}</span>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-700 transition-colors">
                                        {{ service.title }}
                                    </h3>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed mb-4 line-clamp-2">
                                {{ service.short_description || 'Professional healthcare service with expert medical staff and modern equipment.' }}
                            </p>

                            <!-- Mini gallery preview -->
                            <div
                                v-if="service.gallery_images && service.gallery_images.length > 0"
                                class="flex gap-1.5 mb-4"
                            >
                                <div
                                    v-for="(img, i) in service.gallery_images.slice(0, 4)"
                                    :key="img.id"
                                    class="relative h-14 flex-1 rounded-lg overflow-hidden"
                                >
                                    <img :src="img.url" :alt="img.name" class="w-full h-full object-cover" />
                                    <div
                                        v-if="i === 3 && service.gallery_images.length > 4"
                                        class="absolute inset-0 bg-black/50 flex items-center justify-center text-white text-xs font-bold"
                                    >
                                        +{{ service.gallery_images.length - 4 }}
                                    </div>
                                </div>
                            </div>

                            <!-- Price & Duration -->
                            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                <div class="flex items-center gap-3 text-xs text-slate-500">
                                    <span v-if="service.price" class="font-bold text-blue-700 text-sm">৳{{ service.price }}</span>
                                    <span v-if="service.duration_minutes" class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ service.duration_minutes }} min
                                    </span>
                                </div>
                                <span class="text-blue-600 text-xs font-semibold group-hover:translate-x-1 transition-transform">
                                    View Details →
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-20">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">No Services Available</h3>
                    <p class="text-slate-500">Services will be listed here once they are added.</p>
                </div>
            </div>
        </section>
    </FrontendLayout>
</template>

<script setup>
import FrontendLayout from '../../Layout/FrontendLayout.vue';

defineProps({
    services: Array,
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
