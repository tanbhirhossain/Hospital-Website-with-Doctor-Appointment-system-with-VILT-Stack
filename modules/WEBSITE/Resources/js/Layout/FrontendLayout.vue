<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppNavbar from '../Components/frontend/AppNavbar.vue'
import AppFooter from '../Components/frontend/AppFooter.vue'
import ScrollProgress from '../Components/frontend/ScrollProgress.vue'
import BackToTop from '../Components/frontend/BackToTop.vue'
import ChatHub from '../Components/frontend/ChatHub.vue'

defineOptions({ name: 'FrontendLayout' })
</script>

<template>
    <div class="antialiased" style="font-family: 'Inter', sans-serif; background-color: #f9fafb; overflow-x: hidden;">
        <!-- Scroll Progress -->
        <ScrollProgress />

        <!-- Skip to Main Content (Accessibility) -->
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:bg-blue-800 focus:text-white focus:px-6 focus:py-3 focus:rounded-lg focus:font-semibold focus:shadow-xl"
        >
            Skip to main content
        </a>

        <!-- Top Bar -->
        <div class="bg-blue-800 text-white py-2.5 hidden md:block">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center space-x-6">
                        <a href="tel:10699" class="flex items-center hover:text-sky-400 transition-colors">
                            <i class="fas fa-phone-alt mr-2"></i>
                            <span class="font-semibold">Emergency Hotline: 10699</span>
                        </a>
                        <a href="mailto:info@amzhospital.com" class="flex items-center hover:text-sky-400 transition-colors">
                            <i class="fas fa-envelope mr-2"></i>
                            info@amzhospitalbd.com
                        </a>
                        <div class="flex items-center">
                            <i class="fas fa-clock mr-2"></i>
                            <span>Open 24/7 - Always Here For You</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm">Follow Us:</span>
                        <a href="#" class="hover:text-sky-400 transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="hover:text-sky-400 transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-sky-400 transition-colors"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="hover:text-sky-400 transition-colors"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="hover:text-sky-400 transition-colors"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <AppNavbar />

        <!-- Main Content -->
        <main id="main-content" role="main">
            <slot />
        </main>

        <!-- Footer -->
        <AppFooter />

        <!-- Back to Top -->
        <BackToTop />

        <!-- Chat Hub -->
        <ChatHub />

        <!-- Aria Live Region -->
        <div id="aria-announcements" role="status" aria-live="polite" aria-atomic="true" class="sr-only"></div>
    </div>
</template>


