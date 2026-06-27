<template>
    <footer role="contentinfo" class="bg-slate-800 text-white pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- About Column -->
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-800 to-sky-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-hospital text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">AMZ Hospital</h3>
                            <p class="text-xs text-gray-400">Excellence in Healthcare</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6 leading-relaxed">
                        Leading healthcare provider in Bangladesh, committed to delivering world-class medical services with compassion and expertise.
                    </p>
                    <div class="flex space-x-3">
                        <a v-for="(icon, platform) in socials" :key="platform" href="#"
                            class="w-10 h-10 bg-gray-700 hover:bg-blue-800 rounded-lg flex items-center justify-center transition-colors">
                            <i :class="`fab fa-${icon}`"></i>
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
                            <span class="text-gray-400">Cha- 80/3, Shadhinota Sarani, Progati Sarani Rd Uttar Badda, Dhaka-1212</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt text-sky-400 mt-1 mr-3"></i>
                            <div>
                                <a href="tel:10699" class="text-gray-400 hover:text-sky-400 transition-colors block font-bold">Emergency: 10699</a>
                                <a href="tel:+8801234567890" class="text-gray-400 hover:text-sky-400 transition-colors block">+880 184 733 1047</a>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-sky-400 mt-1 mr-3"></i>
                            <a href="mailto:info@amzhospital.com" class="text-gray-400 hover:text-sky-400 transition-colors">info@amzhospital.com</a>
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
                        &copy; {{ new Date().getFullYear() }} AMZ Hospital Bangladesh. All Rights Reserved.
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

<script setup>
const socials = {
    facebook: 'facebook-f',
    twitter: 'twitter',
    instagram: 'instagram',
    linkedin: 'linkedin-in',
    youtube: 'youtube',
}

const quickLinks = [
    { href: '#home', label: 'Home' },
    { href: '#about', label: 'About Us' },
    { href: '#departments', label: 'Departments' },
    { href: '#doctors', label: 'Doctors' },
    { href: '#services', label: 'Services' },
    { href: '#testimonials', label: 'Testimonials' },
    { href: '#contact', label: 'Contact' },
]

const services = [
    'Emergency Care 24/7',
    'Diagnostic Services',
    'Surgical Services',
    'Cardiac Care',
    'Maternity Services',
    'Pharmacy',
    'Laboratory',
]
</script>
