<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppNavbar from '../Components/frontend/AppNavbar.vue'
import AppFooter from '../Components/frontend/AppFooter.vue'
import ScrollProgress from '../Components/frontend/ScrollProgress.vue'
import BackToTop from '../Components/frontend/BackToTop.vue'
import ChatHub from '../Components/frontend/ChatHub.vue'

defineOptions({ name: 'FrontendLayout' })

// Site settings come from shared Inertia props (HandleInertiaRequests middleware)
const page = usePage()
const siteSettings = computed(() => page.props.site_settings || {})
const topbar = computed(() => siteSettings.value?.topbar || {})
const footer = computed(() => siteSettings.value?.footer || {})
const footerLogo = computed(() => siteSettings.value?.logo?.footer_logo_url || null)
const navigationMenus = computed(() => siteSettings.value?.navigation_menus || [])
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
                        <a v-if="topbar.emergency" :href="`tel:${topbar.emergency}`" class="flex items-center hover:text-sky-400 transition-colors">
                            <i class="fas fa-phone-alt mr-2"></i>
                            <span class="font-semibold">Emergency Hotline: {{ topbar.emergency }}</span>
                        </a>
                        <a v-else href="tel:10699" class="flex items-center hover:text-sky-400 transition-colors">
                            <i class="fas fa-phone-alt mr-2"></i>
                            <span class="font-semibold">Emergency Hotline: 10699</span>
                        </a>
                        <a :href="`mailto:${topbar.email || 'info@amzhospitalbd.com'}`" class="flex items-center hover:text-sky-400 transition-colors">
                            <i class="fas fa-envelope mr-2"></i>
                            {{ topbar.email || 'info@amzhospitalbd.com' }}
                        </a>
                        <div class="flex items-center">
                            <i class="fas fa-clock mr-2"></i>
                            <span>{{ topbar.notice || 'Open 24/7 - Always Here For You' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm">Follow Us:</span>
                        <a v-if="footer.facebook" :href="footer.facebook" target="_blank" class="hover:text-sky-400 transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a v-else href="https://facebook.com/amzhospitalltd" class="hover:text-sky-400 transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a v-if="footer.twitter" :href="footer.twitter" target="_blank" class="hover:text-sky-400 transition-colors"><i class="fab fa-twitter"></i></a>
                        <a v-else href="https://twitter.com/amzhospitalltd" class="hover:text-sky-400 transition-colors"><i class="fab fa-twitter"></i></a>
                        <a v-if="footer.instagram" :href="footer.instagram" target="_blank" class="hover:text-sky-400 transition-colors"><i class="fab fa-instagram"></i></a>
                        <a v-else href="https://instagram.com/amzhospitalltd" class="hover:text-sky-400 transition-colors"><i class="fab fa-instagram"></i></a>
                        <a v-if="footer.linkedin" :href="footer.linkedin" target="_blank" class="hover:text-sky-400 transition-colors"><i class="fab fa-linkedin-in"></i></a>
                        <a v-else href="https://linkedin.com/company/amzhospitalltd" class="hover:text-sky-400 transition-colors"><i class="fab fa-linkedin-in"></i></a>
                        <a v-if="footer.youtube" :href="footer.youtube" target="_blank" class="hover:text-sky-400 transition-colors"><i class="fab fa-youtube"></i></a>
                        <a v-else href="https://www.youtube.com/@amzhospitalltd" class="hover:text-sky-400 transition-colors"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <AppNavbar :menus="navigationMenus" />

        <!-- Main Content -->
        <main id="main-content" role="main">
            <slot />
        </main>

        <!-- Footer -->
        <AppFooter :footer-data="footer" :footer-logo="footerLogo" :menus="navigationMenus" />

        <!-- Back to Top -->
        <BackToTop />

        <!-- Chat Hub -->
        <ChatHub />

        <!-- Aria Live Region -->
        <div id="aria-announcements" role="status" aria-live="polite" aria-atomic="true" class="sr-only"></div>
    </div>
</template>


