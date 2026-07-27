<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    footerData: { type: Object, default: null },
    footerLogo: { type: String, default: null },
    menus: { type: Array, default: () => [] },
})

// Read from shared site_settings if props not passed
const page = usePage()
const sharedSettings = computed(() => page.props.site_settings || {})
const footer = computed(() => props.footerData || sharedSettings.value?.footer || {})
const resolvedFooterLogo = computed(() => props.footerLogo || sharedSettings.value?.logo?.footer_logo_url || null)

const defaultSocials = [
    { name: 'facebook', icon: 'facebook-f', url: 'https://facebook.com/amzhospitalltd' },
    { name: 'twitter', icon: 'twitter', url: 'https://twitter.com/amzhospitalltd' },
    { name: 'instagram', icon: 'instagram', url: 'https://instagram.com/amzhospitalltd' },
    { name: 'linkedin', icon: 'linkedin-in', url: 'https://linkedin.com/company/amzhospitalltd' },
    { name: 'youtube', icon: 'youtube', url: 'https://youtube.com/@amzhospitalltd' },
]

const socials = computed(() => {
    const f = footer.value
    if (!f.facebook && !f.twitter && !f.linkedin && !f.youtube && !f.instagram) {
        return defaultSocials
    }
    return [
        f.facebook && { name: 'facebook', icon: 'facebook-f', url: f.facebook },
        f.twitter && { name: 'twitter', icon: 'twitter', url: f.twitter },
        f.instagram && { name: 'instagram', icon: 'instagram', url: f.instagram },
        f.linkedin && { name: 'linkedin', icon: 'linkedin-in', url: f.linkedin },
        f.youtube && { name: 'youtube', icon: 'youtube', url: f.youtube },
    ].filter(Boolean)
})

// Footer navigation menus (location: footer) from admin panel
const footerMenus = computed(() => {
    if (props.menus && props.menus.length > 0) {
        return props.menus.filter(m => m.location === 'footer')
    }
    return []
})

const quickLinks = computed(() => {
    if (footerMenus.value.length > 0) {
        return footerMenus.value.map(m => ({ href: m.url || '#', label: m.label }))
    }
    return [
        { href: '/', label: 'Home' },
        { href: '/about', label: 'About Us' },
        { href: '/departments', label: 'Departments' },
        { href: '/doctors', label: 'Doctors' },
        { href: '/services', label: 'Services' },
        { href: '/contact', label: 'Contact' },
    ]
})

const services = [
    'Emergency Care 24/7',
    'Diagnostic Services',
    'Surgical Services',
    'Maternity Services',
    'Pharmacy',
    'Laboratory',
]

const copyrightText = computed(() =>
    footer.value.copyright || `© ${new Date().getFullYear()} AMZ Hospital Bangladesh. All Rights Reserved.`
)
</script>

<template>
    <footer role="contentinfo" class="bg-slate-800 text-white pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- About Column -->
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-64 h-14 p-4 bg-white rounded-xl flex items-center justify-center shadow-lg">
                            <img v-if="resolvedFooterLogo" :src="resolvedFooterLogo" alt="Hospital Logo" />
                            <img v-else src="https://amzhospitalbd.com/storage/sitesettings/March2024/DmL9Y5RIYaHugklMDs2I.png" alt="AMZ Hospital Logo" />
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6 leading-relaxed">
                        {{ footer.description || 'Leading healthcare provider in Bangladesh, committed to delivering world-class medical services with compassion and expertise.' }}
                    </p>
                    <div class="flex space-x-3">
                        <a v-for="social in socials" :key="social.name" :href="social.url" target="_blank" rel="noopener noreferrer"
                            class="w-10 h-10 bg-gray-700 hover:bg-blue-800 rounded-lg flex items-center justify-center transition-colors">
                            <i :class="`fab fa-${social.icon}`"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-bold mb-6 text-white">Quick Links</h4>
                    <ul class="space-y-3">
                        <li v-for="link in quickLinks" :key="link.href">
                            <a :href="link.href" class="text-gray-400 hover:text-sky-400 transition-colors flex items-center">
                                <i class="fas fa-angle-right mr-2"></i>{{ link.label }}
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h4 class="text-lg font-bold mb-6 text-white">Our Services</h4>
                    <ul class="space-y-3">
                        <li v-for="service in services" :key="service">
                            <a href="#" class="text-gray-400 hover:text-sky-400 transition-colors flex items-center">
                                <i class="fas fa-angle-right mr-2"></i>{{ service }}
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-bold mb-6 text-white">Contact Info</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-sky-400 mt-1 mr-3"></i>
                            <span class="text-gray-400">{{ footer.address || 'Cha- 80/3, Shadhinota Sarani, Progati Sarani Rd Uttar Badda, Dhaka-1212' }}</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt text-sky-400 mt-1 mr-3"></i>
                            <div>
                                <a :href="`tel:${footer.phone || '10699'}`" class="text-gray-400 hover:text-sky-400 transition-colors block font-bold">Emergency: {{ footer.phone || '10699' }}</a>
                                <a href="tel:+8801847331047" class="text-gray-400 hover:text-sky-400 transition-colors block">+880 184 733 1047</a>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-sky-400 mt-1 mr-3"></i>
                            <a :href="`mailto:${footer.email || 'info@amzhospital.com'}`" class="text-gray-400 hover:text-sky-400 transition-colors">{{ footer.email || 'info@amzhospital.com' }}</a>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-clock text-sky-400 mt-1 mr-3"></i>
                            <span class="text-gray-400">Open 24/7</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="border-t border-gray-700 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm mb-4 md:mb-0">
                        {{ copyrightText }}
                    </p>
                    <div class="flex space-x-6 text-sm">
                        <a href="#" class="text-gray-400 hover:text-sky-400 transition-colors">Privacy Policy</a>
                        <a href="#" class="text-gray-400 hover:text-sky-400 transition-colors">Terms of Service</a>
                        <a href="#" class="text-gray-400 hover:text-sky-400 transition-colors">Sitemap</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>