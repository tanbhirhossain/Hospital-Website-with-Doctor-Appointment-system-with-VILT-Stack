<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const mobileMenuOpen = ref(false)
const isFloating = ref(false)
const navHeight = ref(0)
const navRef = ref(null)

const megaDepartments = [
    'Medicine', 'Oncology', 'Cardiology', 'Colorectal Surgery',
    'Dermatology & Venereology', 'ENT', 'Hepatology & Gastroenterology',
    'General, Laparoscopic & Laser Surgery', 'Gynecology & Obstetrics',
    'Haematology', 'Hepatobiliary & Pancreatic Surgery',
    'Interventional Pain Medicine', 'Nephrology', 'Neuro Medicine',
    'Neurosurgery', 'Orthopedic', 'Pediatric', 'Pediatric Surgeon',
    'Physical Medicine & Rehabilitation', 'Plastic, Aesthetic & Laser Surgery',
    'Psychiatrist', 'Respiratory And Medicine', 'Thoracic Surgery',
    'Vascular Surgeon', 'Urology', 'Diabetics, Hormone & Nutrition',
    'Surgical Oncology', 'Pediatric Hematology and Oncology',
    'Liver & Gastroenterology'
]

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value
}

const closeMobileMenu = () => {
    mobileMenuOpen.value = false
}

const updateFloatingNav = () => {
    isFloating.value = window.scrollY > 20
    if (navRef.value && !navHeight.value) {
        navHeight.value = navRef.value.offsetHeight
    }
}

const updateNavHeight = () => {
    if (navRef.value) {
        navHeight.value = navRef.value.offsetHeight
    }
}

onMounted(() => {
    updateFloatingNav()
    updateNavHeight()
    window.addEventListener('scroll', updateFloatingNav, { passive: true })
    window.addEventListener('resize', updateNavHeight)
})

onUnmounted(() => {
    window.removeEventListener('scroll', updateFloatingNav)
    window.removeEventListener('resize', updateNavHeight)
})
</script>

<template>
    <div :style="{ height: isFloating ? `${navHeight}px` : 'auto' }">
        <nav ref="navRef" id="main-nav" role="navigation" aria-label="Main navigation"
            class="bg-white shadow-md z-50 transition-all duration-300"
            :class="isFloating ? 'fixed top-0 inset-x-0' : 'relative'">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="#home" class="flex items-center space-x-3">
                    <div class="w-64 h-14 p-4 bg-white rounded-xl flex items-center justify-center shadow-lg">
                        <img src="https://amzhospitalbd.com/storage/sitesettings/March2024/DmL9Y5RIYaHugklMDs2I.png"
                            alt="AMZ Hospital Logo" />
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-blue-800 transition-colors font-medium">Home</a>
                    <a href="#about" class="text-gray-700 hover:text-blue-800 transition-colors font-medium">About</a>

                    <!-- Mega Menu: Departments -->
                    <div class="desktop-menu-item relative">
                        <button type="button"
                            class="text-gray-700 hover:text-blue-800 transition-colors font-medium inline-flex items-center gap-2">
                            Departments
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="desktop-submenu-right absolute top-full left-0 translate-y-3 opacity-0 invisible pointer-events-none transition-all duration-200 z-60"
                            style="width: min(1120px, calc(100vw - 2rem));">
                            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-6">
                                <div class="grid grid-cols-12 gap-6 items-stretch">
                                    <div class="col-span-4 rounded-xl overflow-hidden" style="min-height: 420px;">
                                        <img src="https://amzhospitalbd.com/storage/AMZ.jpg" alt="AMZ Hospital"
                                            class="w-full h-full object-cover" />
                                    </div>
                                    <div class="col-span-8 py-1">
                                        <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Our Departments</p>
                                        <p class="text-sm text-slate-600 mb-4">Specialized clinical units led by experienced consultants, modern diagnostics, and coordinated patient-first treatment pathways.</p>
                                        <div class="grid grid-cols-3 gap-x-4 gap-y-1">
                                            <a v-for="dept in megaDepartments" :key="dept" href="#departments"
                                                class="block text-slate-700 font-medium rounded-lg px-2 py-1 text-sm hover:text-blue-800 hover:bg-blue-50 transition-all">
                                                {{ dept }}
                                            </a>
                                        </div>
                                        <a href="#departments"
                                            class="mt-4 inline-flex items-center bg-gradient-to-r from-blue-800 to-sky-500 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:shadow-lg transition-all">
                                            View All Departments <i class="fas fa-arrow-right ml-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="#doctors" class="text-gray-700 hover:text-blue-800 transition-colors font-medium">Doctors</a>

                    <!-- Dropdown: Services -->
                    <div class="desktop-menu-item relative">
                        <button type="button"
                            class="text-gray-700 hover:text-blue-800 transition-colors font-medium inline-flex items-center gap-2">
                            Services
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div
                            class="desktop-submenu-right absolute top-full right-0 translate-y-3 opacity-0 invisible pointer-events-none transition-all duration-200 z-60 w-72">
                            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-3">
                                <a href="#services"
                                    class="block text-slate-700 font-medium rounded-lg px-3 py-2 hover:text-blue-800 hover:bg-blue-50 transition-all">24/7
                                    Emergency</a>
                                <a href="#services"
                                    class="block text-slate-700 font-medium rounded-lg px-3 py-2 hover:text-blue-800 hover:bg-blue-50 transition-all">Diagnostic
                                    & Lab</a>
                                <a href="#services"
                                    class="block text-slate-700 font-medium rounded-lg px-3 py-2 hover:text-blue-800 hover:bg-blue-50 transition-all">Ambulance
                                    Support</a>
                                <a href="#services"
                                    class="block text-slate-700 font-medium rounded-lg px-3 py-2 hover:text-blue-800 hover:bg-blue-50 transition-all">Health
                                    Check Packages</a>
                                <a href="#services"
                                    class="block text-slate-700 font-medium rounded-lg px-3 py-2 hover:text-blue-800 hover:bg-blue-50 transition-all">Online
                                    Report Delivery</a>
                            </div>
                        </div>
                    </div>

                    <!-- Mega Menu: Research -->
                    <div class="desktop-menu-item relative">
                        <button type="button"
                            class="text-gray-700 hover:text-blue-800 transition-colors font-medium inline-flex items-center gap-2">
                            Research
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="desktop-submenu-right absolute top-full right-0 translate-y-3 opacity-0 invisible pointer-events-none transition-all duration-200 z-60"
                            style="width: min(860px, calc(100vw - 2rem));">
                            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-6">
                                <div class="grid grid-cols-12 gap-6 items-stretch">
                                    <div class="col-span-4 rounded-xl overflow-hidden" style="min-height: 300px;">
                                        <img src="https://amzhospitalbd.com/storage/AMZ.jpg" alt="Research"
                                            class="w-full h-full object-cover" />
                                    </div>
                                    <div class="col-span-8 py-1">
                                        <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Research</p>
                                        <p class="text-sm text-slate-600 mb-4">Dedicated research units advancing clinical knowledge, fertility science, and regulated clinical operations.</p>
                                        <div class="grid grid-cols-2 gap-3">
                                            <a href="#coe-fertility-research-center"
                                                class="block rounded-xl border border-slate-100 px-4 py-3 hover:bg-blue-50 transition-colors">
                                                <span class="block text-sm font-semibold text-slate-800">Infertility Research Center</span>
                                                <span class="block text-xs text-slate-500">Evidence-based fertility research and innovation.</span>
                                            </a>
                                            <a href="#research-cro"
                                                class="block rounded-xl border border-slate-100 px-4 py-3 hover:bg-blue-50 transition-colors">
                                                <span class="block text-sm font-semibold text-slate-800">CRO (Contract Research Organisation)</span>
                                                <span class="block text-xs text-slate-500">Clinical research coordination and compliance.</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mega Menu: Centre of Excellence -->
                    <div class="desktop-menu-item relative">
                        <button type="button"
                            class="text-gray-700 hover:text-blue-800 transition-colors font-medium inline-flex items-center gap-2">
                            Center Of Excellence
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="desktop-submenu-right absolute top-full right-0 translate-y-3 opacity-0 invisible pointer-events-none transition-all duration-200 z-60"
                            style="width: min(1120px, calc(100vw - 2rem));">
                            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-6">
                                <div class="grid grid-cols-12 gap-6 items-stretch">
                                    <div class="col-span-4 rounded-xl overflow-hidden" style="min-height:420px">
                                        <img src="https://amzhospitalbd.com/storage/AMZ.jpg" alt="Center Of Excellence"
                                            class="w-full h-full object-cover" />
                                    </div>
                                    <div class="col-span-8 py-1">
                                        <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Center Of Excellence</p>
                                        <p class="text-sm text-slate-600 mb-4">Dedicated specialty centers with multidisciplinary care, advanced procedures, and focused recovery programs.</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <a href="#coe-fertility-research-center"
                                                class="block rounded-lg px-3 py-2 hover:bg-blue-50 transition-colors">
                                                <span class="block text-sm font-semibold text-slate-800">Fertility & Research Center</span>
                                                <span class="block text-xs text-slate-500">Comprehensive fertility diagnostics and care.</span>
                                            </a>
                                            <a href="#coe-hypospadias-center"
                                                class="block rounded-lg px-3 py-2 hover:bg-blue-50 transition-colors">
                                                <span class="block text-sm font-semibold text-slate-800">Hypospadias center</span>
                                                <span class="block text-xs text-slate-500">Specialized pediatric urology correction.</span>
                                            </a>
                                            <a href="#coe-laser-proctology-surgery-center"
                                                class="block rounded-lg px-3 py-2 hover:bg-blue-50 transition-colors">
                                                <span class="block text-sm font-semibold text-slate-800">Laser & Proctology Surgery Center</span>
                                                <span class="block text-xs text-slate-500">Minimally invasive anorectal procedures.</span>
                                            </a>
                                            <a href="#coe-plastic-aesthetic-laser-surgery-center"
                                                class="block rounded-lg px-3 py-2 hover:bg-blue-50 transition-colors">
                                                <span class="block text-sm font-semibold text-slate-800">Plastic, Aesthetic & Laser Surgery Center</span>
                                                <span class="block text-xs text-slate-500">Reconstructive and aesthetic solutions.</span>
                                            </a>
                                            <a href="#coe-primary-care-center"
                                                class="block rounded-lg px-3 py-2 hover:bg-blue-50 transition-colors">
                                                <span class="block text-sm font-semibold text-slate-800">Primary Care Center</span>
                                                <span class="block text-xs text-slate-500">Continuity care for all age groups.</span>
                                            </a>
                                            <a href="#coe-stroke-neuro-rehabilitation-center"
                                                class="block rounded-lg px-3 py-2 hover:bg-blue-50 transition-colors">
                                                <span class="block text-sm font-semibold text-slate-800">Stroke & Neuro Rehabilitation Center</span>
                                                <span class="block text-xs text-slate-500">Neurological recovery and therapy plans.</span>
                                            </a>
                                            <a href="#coe-cancer-care-center"
                                                class="block rounded-lg px-3 py-2 hover:bg-blue-50 transition-colors">
                                                <span class="block text-sm font-semibold text-slate-800">Cancer Care Center</span>
                                                <span class="block text-xs text-slate-500">Integrated oncology diagnosis and treatment.</span>
                                            </a>
                                            <a href="#coe-hepatobiliary-pancreatic-surgery-center"
                                                class="block rounded-lg px-3 py-2 hover:bg-blue-50 transition-colors">
                                                <span class="block text-sm font-semibold text-slate-800">Hepatobiliary & Pancreatic Surgery Center</span>
                                                <span class="block text-xs text-slate-500">Advanced hepatobiliary surgical expertise.</span>
                                            </a>
                                        </div>
                                        <a href="#center-of-excellence"
                                            class="mt-4 inline-flex items-center bg-gradient-to-r from-blue-800 to-sky-500 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:shadow-lg transition-all">
                                            View All Center Of Excellence <i class="fas fa-arrow-right ml-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="#contact" class="text-gray-700 hover:text-blue-800 transition-colors font-medium">Contact</a>
                    <a href="#appointment"
                        class="bg-gradient-to-r from-blue-800 to-sky-500 text-white px-6 py-2.5 rounded-lg font-semibold hover:shadow-lg transition-all transform hover:scale-105">
                        <i class="fas fa-calendar-check mr-2"></i>Book Appointment
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="toggleMobileMenu" id="mobile-menu-btn" :aria-expanded="mobileMenuOpen"
                    aria-controls="mobile-menu" aria-label="Toggle mobile menu"
                    class="lg:hidden text-gray-700 text-2xl focus:outline-none">
                    <i :class="mobileMenuOpen ? 'fas fa-times' : 'fas fa-bars'"></i>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div v-show="mobileMenuOpen" id="mobile-menu" role="menu" aria-label="Mobile navigation menu"
                class="lg:hidden pb-4 border-t border-gray-200 mt-2">
                <div class="flex flex-col space-y-3 pt-4">
                    <a href="#home" @click="closeMobileMenu"
                        class="text-gray-700 hover:text-blue-800 transition-colors py-2 font-medium">Home</a>
                    <a href="#about" @click="closeMobileMenu"
                        class="text-gray-700 hover:text-blue-800 transition-colors py-2 font-medium">About</a>
                    <details class="group">
                        <summary
                            class="list-none cursor-pointer text-gray-700 hover:text-blue-800 transition-colors py-2 font-medium flex items-center justify-between">
                            Departments
                            <i class="fas fa-chevron-down text-xs transition-transform group-open:rotate-180"></i>
                        </summary>
                        <div class="pl-4 pb-2 flex flex-col space-y-2 border-l-2 border-slate-100">
                            <a href="#departments" class="text-slate-600 hover:text-blue-800 transition-colors">Cardiology</a>
                            <a href="#departments" class="text-slate-600 hover:text-blue-800 transition-colors">Neurology</a>
                            <a href="#departments" class="text-slate-600 hover:text-blue-800 transition-colors">Orthopedics</a>
                            <a href="#departments" class="text-slate-600 hover:text-blue-800 transition-colors">Pediatrics</a>
                        </div>
                    </details>
                    <a href="#doctors" @click="closeMobileMenu"
                        class="text-gray-700 hover:text-blue-800 transition-colors py-2 font-medium">Doctors</a>
                    <details class="group">
                        <summary
                            class="list-none cursor-pointer text-gray-700 hover:text-blue-800 transition-colors py-2 font-medium flex items-center justify-between">
                            Services
                            <i class="fas fa-chevron-down text-xs transition-transform group-open:rotate-180"></i>
                        </summary>
                        <div class="pl-4 pb-2 flex flex-col space-y-2 border-l-2 border-slate-100">
                            <a href="#services" class="text-slate-600 hover:text-blue-800 transition-colors">Emergency</a>
                            <a href="#services" class="text-slate-600 hover:text-blue-800 transition-colors">Diagnostic & Lab</a>
                            <a href="#services" class="text-slate-600 hover:text-blue-800 transition-colors">Ambulance</a>
                            <a href="#services" class="text-slate-600 hover:text-blue-800 transition-colors">Health Packages</a>
                        </div>
                    </details>
                    <details class="group">
                        <summary
                            class="list-none cursor-pointer text-gray-700 hover:text-blue-800 transition-colors py-2 font-medium flex items-center justify-between">
                            Research
                            <i class="fas fa-chevron-down text-xs transition-transform group-open:rotate-180"></i>
                        </summary>
                        <div class="pl-4 pb-2 flex flex-col space-y-2 border-l-2 border-slate-100">
                            <a href="#coe-fertility-research-center" class="text-slate-600 hover:text-blue-800 transition-colors">Infertility Research Center</a>
                            <a href="#research-cro" class="text-slate-600 hover:text-blue-800 transition-colors">CRO (Contract Research Organisation)</a>
                        </div>
                    </details>
                    <details class="group">
                        <summary
                            class="list-none cursor-pointer text-gray-700 hover:text-blue-800 transition-colors py-2 font-medium flex items-center justify-between">
                            Center Of Excellence
                            <i class="fas fa-chevron-down text-xs transition-transform group-open:rotate-180"></i>
                        </summary>
                        <div class="pl-4 pb-2 flex flex-col space-y-2 border-l-2 border-slate-100">
                            <a href="#center-of-excellence" class="text-slate-600 hover:text-blue-800 transition-colors">All Centers</a>
                            <a href="#coe-fertility-research-center" class="text-slate-600 hover:text-blue-800 transition-colors">Fertility & Research Center</a>
                            <a href="#coe-hypospadias-center" class="text-slate-600 hover:text-blue-800 transition-colors">Hypospadias center</a>
                            <a href="#coe-cancer-care-center" class="text-slate-600 hover:text-blue-800 transition-colors">Cancer Care Center</a>
                        </div>
                    </details>
                    <a href="#contact" @click="closeMobileMenu"
                        class="text-gray-700 hover:text-blue-800 transition-colors py-2 font-medium">Contact</a>
                    <a href="#appointment" @click="closeMobileMenu"
                        class="bg-gradient-to-r from-blue-800 to-sky-500 text-white px-6 py-3 rounded-lg font-semibold text-center mt-2">
                        <i class="fas fa-calendar-check mr-2"></i>Book Appointment
                    </a>
                </div>
            </div>
        </div>
        </nav>
    </div>
</template>

<style scoped>
.desktop-menu-item:hover .desktop-submenu,
.desktop-menu-item:focus-within .desktop-submenu {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    transform: translateX(-50%) translateY(0) !important;
}

.desktop-menu-item:hover .desktop-submenu-right,
.desktop-menu-item:focus-within .desktop-submenu-right {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    transform: translateY(0) !important;
}
</style>
