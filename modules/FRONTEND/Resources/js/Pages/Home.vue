<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue'
import AppointmentBookingWizard from '../Components/frontend/AppointmentBookingWizard.vue'
import FrontendLayout from '../Layout/FrontendLayout.vue'

defineOptions({ layout: FrontendLayout })

defineProps({
    doctors: Array,
    departments: Array,
    centers: Array
});


const currentSlide = ref(0)
let autoSlideInterval = null

const slides = [
    {
        gradient: 'from-blue-800/95 via-blue-800/85',
        img: 'https://amzhospitalbd.com/storage/sliders/October2025/JV0EOX5DYsGudxtRkGQC.webp?w=1920&h=1080&fit=crop',
        alt: 'Modern Hospital',
        title: 'Welcome to AMZ Hospital',
        desc: 'Providing comprehensive healthcare services with state-of-the-art facilities and experienced medical professionals',
        ctas: [
            { href: '#appointment', label: 'Book Appointment', icon: 'fa-calendar-check', style: 'white', color: 'text-blue-800' },
            { href: 'tel:10699', label: 'Emergency: 10699', icon: 'fa-phone-alt', style: 'border' },
        ],
    },
    {
        gradient: 'from-sky-500/95 via-sky-500/85',
        img: 'https://amzhospitalbd.com/storage/sliders/October2025/qyR9OfpMxwGnJIl955ki.webp?w=1920&h=1080&fit=crop',
        alt: 'Expert Doctors',
        title: 'Expert Medical Professionals',
        desc: 'Our team of highly qualified doctors and specialists are dedicated to providing the best healthcare',
        ctas: [
            { href: '#doctors', label: 'Meet Our Doctors', icon: 'fa-user-md', style: 'white', color: 'text-sky-500' },
        ],
    },
    {
        gradient: 'from-emerald-500/95 via-emerald-500/85',
        img: 'https://images.unsplash.com/photo-1538108149393-fbbd81895907?w=1920&h=1080&fit=crop',
        alt: 'Modern Facilities',
        title: 'Advanced Medical Technology',
        desc: 'Equipped with cutting-edge technology and modern facilities for accurate diagnosis and treatment',
        ctas: [
            { href: '#services', label: 'Our Services', icon: 'fa-hospital', style: 'white', color: 'text-emerald-500' },
        ],
    },
]

const showSlide = (index) => { currentSlide.value = index }
const nextSlide = () => { currentSlide.value = (currentSlide.value + 1) % slides.length }
const prevSlide = () => { currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length }

const startAuto = () => { autoSlideInterval = setInterval(nextSlide, 5000) }
const stopAuto = () => clearInterval(autoSlideInterval)
let sectionObserver: IntersectionObserver | null = null
let counterObserver: IntersectionObserver | null = null
const smoothScrollHandlers: Array<{ anchor: Element; handler: EventListener }> = []

const quickCards = [
    { gradient: 'from-[blue] to-[blue]', icon: 'fa-user-md', title: 'Find Doctor', link: '#doctors' },
    { gradient: 'from-[#24a6db] to-[#5cc6e0]', icon: 'fa-calendar-check', title: 'Book an Appointment', link: '#appointment' },
    { gradient: 'from-[#1fb18f] to-[#6dd8bc]', icon: 'fa-home', title: 'Home Sample Collection', link: '#services' },
    { gradient: 'from-[#dda229] to-[#efd56d]', icon: 'fa-file-medical', title: 'Online Lab Report', link: '#contact' },
    { gradient: 'from-[#e56775] to-[#ef8f9b]', icon: 'fa-map-marker-alt', title: 'Find Us', link: '#contact' },
]


// const departments = [
//     { 
//         color: 'from-blue-600 to-indigo-700', 
//         textColor: 'text-blue-100', 
//         bg: 'bg-blue-50', 
//         icon: 'fa-heartbeat', 
//         name: 'Cardiology', 
//         subtitle: 'Every heartbeat matters â€” we protect it.', 
//         desc: 'The Cardiology Department at AMZ Hospital Ltd. provides comprehensive heart careâ€”from prevention and diagnosis to advanced treatment.', 
//         specialists: '12+', 
//         beds: '45',
//         image : 'https://amzhospitalbd.com/storage/departments/September2025/JOuOIapuAAiJNqpSRhHU.webp',
//         available247: true 
//     },
    
//     { 
//         color: 'from-emerald-500 to-teal-600', 
//         textColor: 'text-emerald-100', 
//         bg: 'bg-emerald-50', 
//         icon: 'fa-user-md', 
//         name: 'Medicine', 
//         subtitle: 'Right diagnosis begins with the right doctor.', 
//         desc: 'Providing patient-centered care for a wide range of acute and chronic medical conditions with a focus on internal wellness.', 
//         specialists: '4+', 
//         beds: '20',
//         image : 'https://amzhospitalbd.com/storage/departments/September2025/5y1N0lFan5dCe0oI6kbX.webp',

//     },
//     { 
//         color: 'from-purple-500 to-fuchsia-600', 
//         textColor: 'text-purple-100', 
//         bg: 'bg-purple-50', 
//         icon: 'fa-female', 
//         name: 'Gynecology', 
//         subtitle: 'Your partner in health and motherhood.', 
//         desc: 'Dedicated to delivering comprehensive, compassionate, and evidence-based care for women at every stage of life.', 
//         specialists: '9+', 
//         beds: '30',
//         image : 'https://amzhospitalbd.com/storage/departments/September2025/9Cf7Ui3e6ONTIP5Goll3.webp',

//     },
//     { 
//         color: 'from-red-500 to-orange-600', 
//         textColor: 'text-red-100', 
//         bg: 'bg-red-50', 
//         icon: 'fa-microscope', 
//         name: 'Hepatobiliary', 
//         subtitle: 'Healthy digestion for a healthier life.', 
//         desc: 'Advanced surgical care for complex diseases of the liver, gallbladder, and pancreas using the latest surgical technology.', 
//         specialists: '8+', 
//         beds: '15',
//         image : 'https://amzhospitalbd.com/storage/departments/September2025/xHagfcfncDADCrCfapm5.webp',

//     },
//     { 
//         color: 'from-amber-500 to-yellow-600', 
//         textColor: 'text-amber-100', 
//         bg: 'bg-amber-50', 
//         icon: 'fa-baby', 
//         name: 'Pediatrics', 
//         subtitle: 'Caring for today, building tomorrow.', 
//         desc: 'Dedicated to the health and well-being of infants, children, and adolescents with a gentle, child-friendly approach.', 
//         specialists: '6+', 
//         beds: '25',
//         image : 'https://amzhospitalbd.com/storage/departments/September2025/j0pM1NzDzbZpFT5ZDyov.webp',
//     },
//     { 
//         color: 'from-indigo-500 to-blue-700', 
//         textColor: 'text-indigo-100', 
//         bg: 'bg-indigo-50', 
//         icon: 'fa-procedures', 
//         name: 'Surgery', 
//         subtitle: 'Precision surgery with minimal scars.', 
//         desc: 'Specializing in Laparoscopic and Laser solutions for a wide range of conditions to ensure faster recovery times.', 
//         specialists: '7+', 
//         beds: '40',
//         image : 'https://amzhospitalbd.com/storage/departments/September2025/ArSkDHbaqGg8c8k0ZqCL.webp',
//     }
// ]

const doctors = [
    { color: 'from-blue-400 to-blue-600', name: 'Dr. Ahmedul Kabir', specialty: 'Medicine', degree: 'MBBS, FCPS, FACP, FRCP', exp: '15+', photo: 'https://amzhospitalbd.com/storage/doctors/April2024/H2qkQbIjHRLFzvPwoNEt.jpg' },
    { color: 'from-emerald-400 to-emerald-600', name: 'Dr. Mohammad Sayem', specialty: 'Medicine', degree: 'MBBS (Dhaka), FCPS (Medicine), MRCP (London) MRCPE (Edinburgh), MACP (USA)', exp: '10+', photo: 'https://amzhospitalbd.com/storage/doctors/April2024/rXuniLQlfVt3OmQ6Ntw8.jpg' },
    { color: 'from-purple-400 to-purple-600', name: 'Professor Dr. Mohammad Nashir Uddin', specialty: 'Plastic, Aesthetic & Laser Surgery', degree: 'MS (Plastic Surgery), FCPS (Surgery), FRCS (UK)', exp: '15+', photo: 'https://amzhospitalbd.com/storage/doctors/April2024/vtlYUwFUzX8gFsQLcsqJ.png' },
    { color: 'from-pink-400 to-pink-600', name: 'Professor Dr. Jesmine Banu', specialty: 'Gynecologist', degree: 'MBBS, MS (Obs. & GYN), Gynecology, Obstetrics Infertility Specialist & Laparoscopic Surgeon', exp: '20+', photo:'https://amzhospitalbd.com/storage/doctors/July2025/7Fq2bPofzv2z2c2Zz91s.jpg' },
]

const leadershipMessages = [
    {
        name: 'Dr. Ahmedul Kabir',
        role: 'Chairman',
        title: 'Chairman Message',
        photo: 'https://www.tbsnews.net/sites/default/files/styles/big_3/public/images/2021/11/11/ahmedul.jpg?w=800&h=900&fit=crop',
        message: `Dear Valued Clients, Well-wishers and Neighbours Assalamu Alaikum,

I extend my sincere gratitude for entrusting AMZ Hospital Ltd. with the treatment of your health and well-being. It is a 100-bed specialized hospital with 26 departments and a distinguished set of specialist doctors located at North Badda, Dhaka 1212. Your trust is the cornerstone of our commitment to providing exceptional care for you, your family, and your friends. Our dedicated healthcare team always works tirelessly to ensure your extreme comfort and speedy recovery.
In this challenging world, especially on health care issues, your safety is always our top priority. We always implement the highest standards of hygiene and other related protocols. Our prominent and experienced specialists, medical staffs and administration are always ready for your service and we are honored to be your healthcare partner.
Thank you for choosing AMZ Hospital Ltd. We are here for you in every step of your health care.`,
    },
    {
        name: 'MD. Zulfiquear Ali Babul',
        role: 'Managing Director',
        title: 'A Note From Managing Director',
        photo: 'https://amzhospitalbd.com/storage/Leaders/Md_sir.png?w=800&h=900&fit=crop',
        message: `This is a great privilege for me and my team to present AMZ Hospital Ltd. at North Badda Dhaka,
        
We would like to express our deepest welcome and sincere greetings to our valuable patients for selecting AMZ Hospital Ltd. as their first choice for better treatment.
For your kind appraisal, we would like to say that every member of AMZ Hospital, round-the-clock, is prepared to offer you the optimum patient's comfort, true happiness and financial ease through our well-experienced consultant physician and advanced medical technology. We always try to offer you the best service with social devotion to earn your utmost satisfaction.
The main focus of our consultants, nurses and employees is to secure your life through enhanced quality of service, the best possible treatment management and highly skilled, devoted professionals. We firmly believe that, Insha'Allah, based on your best recommendations, AMZ Hospital will definitely expand its business confidence both domestically and abroad. I deeply acknowledge your resilient confidence in us.
We think that perhaps this is the most important decision you have already made to safeguard your health care with the hospitality of our AMZ Hospital Ltd.`,
    },
    {
        name: 'Dr Lima Rahman',
        role: 'CEO',
        title: 'A Message From CEO',
        photo: 'https://amzhospitalbd.com/storage/Leaders/CEO.png?w=800&h=900&fit=crop',
        message: `Dear Patients, Families, and Well-Wishers,

It is my honor to welcome you to AMZ Hospital Ltd. and thank you for placing your confidence in our healthcare services. We are committed to delivering safe, compassionate, and quality medical care through a strong team of experienced specialists, nurses, and support professionals.
At AMZ Hospital, we continuously work to improve patient experience by combining modern technology, ethical practices, and personalized attention. Every decision we take is guided by one purpose: to protect your health, dignity, and comfort at every stage of care.
With your trust and prayers, we will continue strengthening our services and expanding our excellence for the people of Bangladesh and beyond. Thank you for being part of the AMZ Hospital family.`,
    },
]

const leadershipExpanded = ref<Record<number, boolean>>({})
const toggleLeadershipMessage = (index: number) => {
    leadershipExpanded.value[index] = !leadershipExpanded.value[index]
}
const leadershipPreview = (message: string) => {
    const firstParagraph = message.split('\n\n')[0]?.trim() ?? ''
    return firstParagraph.length > 360 ? `${firstParagraph.slice(0, 360)}...` : firstParagraph
}



    const scheduleDoctors = [
        { id: 1, name: 'Dr. Ahmedul Kabir', specialty: 'Medicine' },
        { id: 2, name: 'Dr. Mohammad Sayem', specialty: 'Medicine' },
        { id: 3, name: 'Professor Dr. Mohammad Nashir Uddin', specialty: 'Plastic, Aesthetic & Laser Surgery' },
        { id: 4, name: 'Professor Dr. Jesmine Banu', specialty: 'Gynecology' },
    ]

    const doctorTimeSchedules = [
        { doctor_id: 1, day: 'sat', start_time: '09:00', end_time: '12:00' },
        { doctor_id: 1, day: 'mon', start_time: '17:00', end_time: '20:00' },
        { doctor_id: 1, day: 'wed', start_time: '09:00', end_time: '12:00' },
        { doctor_id: 2, day: 'sun', start_time: '10:00', end_time: '13:00' },
        { doctor_id: 2, day: 'tue', start_time: '16:00', end_time: '19:00' },
        { doctor_id: 2, day: 'thu', start_time: '10:00', end_time: '13:00' },
        { doctor_id: 3, day: 'sat', start_time: '14:00', end_time: '18:00' },
        { doctor_id: 3, day: 'mon', start_time: '14:00', end_time: '18:00' },
        { doctor_id: 3, day: 'thu', start_time: '14:00', end_time: '18:00' },
        { doctor_id: 4, day: 'sun', start_time: '09:30', end_time: '12:30' },
        { doctor_id: 4, day: 'tue', start_time: '09:30', end_time: '12:30' },
        { doctor_id: 4, day: 'fri', start_time: '10:00', end_time: '12:00' },
    ]

    const scheduleDays = ['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri'] as const
    const dayLabel: Record<(typeof scheduleDays)[number], string> = {
        sat: 'Saturday',
        sun: 'Sunday',
        mon: 'Monday',
        tue: 'Tuesday',
        wed: 'Wednesday',
        thu: 'Thursday',
        fri: 'Friday',
    }

    const to12Hour = (time: string) => {
        const [h, m] = time.split(':').map(Number)
        const hour = h % 12 || 12
        const meridiem = h >= 12 ? 'PM' : 'AM'
        return `${hour}:${String(m).padStart(2, '0')} ${meridiem}`
    }

    const groupedDoctorSchedules = computed(() =>
        scheduleDoctors.map((doctor) => ({
            ...doctor,
            days: scheduleDays.map((day) => {
                const slots = doctorTimeSchedules
                    .filter((entry) => entry.doctor_id === doctor.id && entry.day === day)
                    .map((entry) => `${to12Hour(entry.start_time)} - ${to12Hour(entry.end_time)}`)
                return { day, label: dayLabel[day], slots }
            }),
        })),
    )

    const activeFilter = ref('all')
    const galleryItems = [
        { 
            category: 'infrastructure', 
            img: 'https://amzhospitalbd.com/storage/AMZ.jpg?w=800&h=600&fit=crop', 
            badge: 'Infrastructure', 
            badgeColor: 'bg-blue-800', 
            title: 'AMZ Hospital Building', 
            desc: 'ISO-certified 100-bed multi-story facility in Uttar Badda.' 
        },
        { 
            category: 'facilities', 
            img: 'https://amzhospitalbd.com/storage/galleries/May2024/uAobdzOLYOrhAGXeCQe1.jpg?w=800&h=600&fit=crop', 
            badge: 'Facilities', 
            badgeColor: 'bg-red-500', 
            title: '24/7 Emergency Care', 
            desc: 'Round-the-clock emergency and trauma services with ambulance support.' 
        },
        { 
            category: 'equipment', 
            img: 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=800&h=600&fit=crop', 
            badge: 'Equipment', 
            badgeColor: 'bg-emerald-500', 
            title: 'Advanced Cath-Lab', 
            desc: 'Modern cardiac intervention unit for Angiogram and Angioplasty.' 
        },
        { 
            category: 'facilities', 
            img: 'https://images.unsplash.com/photo-1512678080530-7760d81faba6?w=800&h=600&fit=crop', 
            badge: 'Facilities', 
            badgeColor: 'bg-purple-600', 
            title: 'Critical Care Units', 
            desc: 'Well-equipped ICU, NICU, CCU, and HDU for specialized monitoring.' 
        },
        { 
            category: 'departments', 
            img: 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=800&h=600&fit=crop', 
            badge: 'Departments', 
            badgeColor: 'bg-cyan-600', 
            title: 'Fertility & Research Center', 
            desc: 'Premier Center of Excellence for IVF and infertility treatments.' 
        },
        { 
            category: 'equipment', 
            img: 'https://images.unsplash.com/photo-1581594693702-fbdc51b2763b?w=800&h=600&fit=crop', 
            badge: 'Equipment', 
            badgeColor: 'bg-orange-600', 
            title: 'Diagnostic & Radio Imaging', 
            desc: 'Category-A lab with MRI, CT Scan, and digital X-ray capabilities.' 
        },
    ]

    const filters = ['all', 'infrastructure', 'facilities', 'equipment', 'departments']
    const filteredGallery = () => activeFilter.value === 'all' ? galleryItems : galleryItems.filter(i => i.category === activeFilter.value)

   
    const servicesList = [
        { bg: 'from-blue-50', icon: 'fa-ambulance', title: '24/7 Emergency Services', desc: 'Round-the-clock emergency medical care with rapid response team', items: ['Critical Care Unit', 'Intensive Care Unit', 'High Dependancy Unit', 'Dialysis'] },
        { bg: 'from-green-50', icon: 'fa-x-ray', title: 'Diagnostic Services', desc: 'Advanced diagnostic facilities with latest technology', items: ['CT Scan & MRI', 'Ultrasound', 'Laboratory Tests', 'Cath Lab'] },
        { bg: 'from-purple-50', icon: 'fa-procedures', title: 'Surgical Services', desc: 'Expert surgical care with modern operation theaters', items: ['General Surgery', 'Laparoscopic Surgery', 'Specialized Surgery', 'Laser Surgery'] },
        { bg: 'from-red-50', icon: 'fa-utensils', title: 'Canteen Service', desc: 'Hygienic in-house canteen with fresh meals for patients, attendants, and visitors.', items: ['Patient Diet Meals', 'Attendant Meal Options', 'Tea & Refreshments'] },
        { bg: 'from-yellow-50', icon: 'fa-baby', title: 'Maternity Services', desc: 'Complete care for mothers and newborns', items: ['Prenatal Care', 'Delivery Services', 'NICU'] },
        { bg: 'from-pink-50', icon: 'fa-pills', title: 'Pharmacy Services', desc: '24/7 pharmacy with all essential medicines', items: [] },
    ]

    const packages = [
        { bg: 'bg-blue-100', color: 'text-blue-800', icon: 'fa-user-check', title: 'Basic Checkup', desc: 'General physician consultation, CBC, blood sugar, urine R/E, and ECG.', price: 'BDT 2,500' },
        { bg: 'bg-pink-100', color: 'text-pink-700', icon: 'fa-heartbeat', title: 'Women Wellness', desc: 'Gyne consultation, thyroid profile, vitamin D, breast exam, and pelvic USG.', price: 'BDT 4,800' },
        { bg: 'bg-emerald-100', color: 'text-emerald-700', icon: 'fa-user-shield', title: 'Senior Care', desc: 'Cardiac risk panel, chest X-ray, kidney profile, diabetic panel, and specialist review.', price: 'BDT 6,200' },
        { bg: 'bg-amber-100', color: 'text-amber-700', icon: 'fa-users', title: 'Family Package', desc: 'Combined health screening for 2 adults including consultation and core labs.', price: 'BDT 8,900' },
    ]

    const centers = [
        { id: 'coe-fertility-research-center', bg: 'bg-blue-100', color: 'text-blue-800', icon: 'fa-flask', title: 'Fertility & Research Center', desc: 'Personalized fertility assessment, treatment planning, and research-driven care pathways.' },
        { id: 'coe-hypospadias-center', bg: 'bg-emerald-100', color: 'text-emerald-700', icon: 'fa-user-shield', title: 'Hypospadias center', desc: 'Child-focused surgical correction with coordinated follow-up and family support.' },
        { id: 'coe-laser-proctology-surgery-center', bg: 'bg-rose-100', color: 'text-rose-700', icon: 'fa-bolt', title: 'Laser & Proctology Surgery Center', desc: 'Modern laser-based proctology procedures with reduced pain and faster recovery.' },
        { id: 'coe-plastic-aesthetic-laser-surgery-center', bg: 'bg-violet-100', color: 'text-violet-700', icon: 'fa-magic', title: 'Plastic, Aesthetic & Laser Surgery Center', desc: 'Reconstructive, cosmetic, and laser procedures tailored to patient goals.' },
        { id: 'coe-primary-care-center', bg: 'bg-cyan-100', color: 'text-cyan-700', icon: 'fa-stethoscope', title: 'Primary Care Center', desc: 'Preventive, chronic, and family medicine services in one coordinated center.' },
        { id: 'coe-stroke-neuro-rehabilitation-center', bg: 'bg-orange-100', color: 'text-orange-700', icon: 'fa-brain', title: 'Stroke & Neuro Rehabilitation Center', desc: 'Neuro recovery with physiotherapy, speech therapy, and multidisciplinary monitoring.' },
        { id: 'coe-cancer-care-center', bg: 'bg-pink-100', color: 'text-pink-700', icon: 'fa-ribbon', title: 'Cancer Care Center', desc: 'Integrated oncology support from diagnosis to treatment and survivorship care.' },
        { id: 'coe-hepatobiliary-pancreatic-surgery-center', bg: 'bg-lime-100', color: 'text-lime-700', icon: 'fa-procedures', title: 'Hepatobiliary & Pancreatic Surgery Center', desc: 'Advanced surgical management for complex liver, biliary, and pancreatic diseases.' },
    ]

    const blogPosts = [
        {
            id: 'heart-care',
            category: 'Cardiology',
            title: '7 Daily Habits That Keep Your Heart Healthy',
            excerpt: 'Simple lifestyle changes that reduce cardiac risk and improve long-term heart health outcomes.',
            date: 'March 05, 2026',
            readTime: '5 min read',
            author: 'Dr. Farhan Rahman',
            img: 'https://images.unsplash.com/photo-1460672985063-6764ac8b9c74?w=1200&h=700&fit=crop',
            href: '#',
        },
        {
            id: 'womens-wellness',
            category: 'Women Wellness',
            title: 'When To Visit a Gynecologist: Key Warning Signs',
            excerpt: 'Know the common symptoms that should not be ignored and when specialist care is recommended.',
            date: 'March 02, 2026',
            readTime: '6 min read',
            author: 'Dr. Ruba Ahmed',
            img: 'https://images.unsplash.com/photo-1516549655169-df83a0774514?w=1200&h=700&fit=crop',
            href: '#',
        },
        {
            id: 'child-care',
            category: 'Pediatrics',
            title: 'How to Build Strong Immunity in Children',
            excerpt: 'Practical nutrition, sleep, and vaccination guidance to support healthy childhood development.',
            date: 'February 28, 2026',
            readTime: '4 min read',
            author: 'Dr. Ayaan Hasan',
            img: 'https://images.unsplash.com/photo-1576765608866-5b51046452be?w=1200&h=700&fit=crop',
            href: '#',
        },
    ]

    const testimonials = [
        { initials: 'AH', bg: 'from-blue-800 to-sky-500', cardBg: 'from-blue-50', name: 'Abdul Hamid', short: 'Excellent hospital with caring staff and very professional doctors.', full: ' The facilities are modern, and the care quality was consistently high from admission to discharge. Highly recommended for trusted healthcare.' },
        { initials: 'RB', bg: 'from-emerald-500 to-emerald-600', cardBg: 'from-green-50', name: 'Rahima Begum', short: 'I received excellent care during my stay and felt supported.', full: ' The nursing team was attentive, and doctors explained each step clearly so my family and I stayed confident throughout treatment.' },
        { initials: 'MK', bg: 'from-purple-500 to-purple-600', cardBg: 'from-purple-50', name: 'Mahmud Khan', short: 'Top-notch medical facility with advanced equipment and fast service.', full: ' The emergency department handled my case quickly, and follow-up care was just as good. I am grateful for their professionalism.' },
        { initials: 'SN', bg: 'from-cyan-500 to-cyan-600', cardBg: 'from-cyan-50', name: 'Shila Nasrin', short: 'Clean environment and excellent diagnostics at AMZ Hospital.', full: ' My reports were delivered quickly, and consultation was clear and reassuring. The whole process felt organized and patient-friendly.' },
        { initials: 'TU', bg: 'from-amber-500 to-amber-600', cardBg: 'from-amber-50', name: 'Tanvir Uddin', short: 'Appointment process was smooth, and waiting time was reasonable.', full: ' Staff guided me politely, and the doctor gave practical advice with clear medicine instructions. Overall, a dependable experience.' },
        { initials: 'FP', bg: 'from-rose-500 to-rose-600', cardBg: 'from-rose-50', name: 'Farzana Parvin', short: 'Excellent maternal care and supportive nursing staff throughout.', full: ' The doctors checked in regularly, answered questions patiently, and ensured comfort before and after procedures.' },
    ]
    const doubledTestimonials = [...testimonials, ...testimonials]

    const expandedTestimonials = ref({})
    const toggleTestimonial = (i) => { expandedTestimonials.value[i] = !expandedTestimonials.value[i] }

    const stats = [
        { target: 300000, label: 'Patients Treated' },
        { target: 79, label: 'Expert Doctors' },
        { target: 7, label: 'Years Experience' },
        { target: 100, label: 'Hospital Beds' },
    ]
    const statValues = ref(stats.map(() => 0))
    let counterAnimated = false

    const partners = [
    { name: 'AB Bank', logo: 'https://amzhospitalbd.com/storage/corporate-partners/March2024/9Y2oTmRomsnbfdh3cG50.png' },
    { name: 'Ekushe TV', logo: 'https://amzhospitalbd.com/storage/corporate-partners/March2024/SLboYCDAU6ngOHkQ0TxA.png' },
    { name: 'ICON', logo: 'https://amzhospitalbd.com/storage/corporate-partners/March2024/stFeEleAssgKAwC97aeC.png' },
    { name: 'Brac', logo: 'https://amzhospitalbd.com/storage/corporate-partners/March2024/TtikDkxa6iYuaaMoRyBg.png' },
    { name: 'Huwaei', logo: 'https://amzhospitalbd.com/storage/corporate-partners/March2024/9XfI0DamcAjt2JgjgV3T.png' },
    // ... add the rest
    ]
    const MIN_PARTNERS_ON_SCREEN = 9
    const partnersForMarquee = computed(() => {
        if (!partners.length) return []
        if (partners.length >= MIN_PARTNERS_ON_SCREEN) return partners.slice(0, MIN_PARTNERS_ON_SCREEN)

        const filledPartners = [...partners]
        while (filledPartners.length < MIN_PARTNERS_ON_SCREEN) {
            const missing = MIN_PARTNERS_ON_SCREEN - filledPartners.length
            const repeatChunk = partners.slice(Math.max(0, partners.length - missing))
            filledPartners.push(...repeatChunk)
        }
        return filledPartners.slice(0, MIN_PARTNERS_ON_SCREEN)
    })

    const appointmentAvailableWeekdays = [0, 1, 2, 3, 4, 6]
    const appointmentBlockedDates = []

    const newsletterEmail = ref('')
    const newsletterSuccess = ref(false)
    const submitNewsletter = () => {
        if (!newsletterEmail.value) return
        newsletterSuccess.value = true
        newsletterEmail.value = ''
    }

    onMounted(() => {
        startAuto()

        // Scroll animations (IntersectionObserver)
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches
        if (prefersReducedMotion) {
            document.querySelectorAll('.scroll-reveal').forEach((el) => el.classList.add('animate-in'))
        } else {
            sectionObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in')
                        sectionObserver?.unobserve(entry.target)
                    }
                })
            }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' })

            document.querySelectorAll('.scroll-reveal').forEach((el) => sectionObserver?.observe(el))
        }

        // Counter animation
        counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !counterAnimated) {
                    counterAnimated = true
                    stats.forEach((stat, i) => {
                        let current = 0
                        const increment = stat.target / 120
                        const tick = () => {
                            current += increment
                            if (current < stat.target) {
                                statValues.value[i] = Math.ceil(current)
                                requestAnimationFrame(tick)
                            } else {
                                statValues.value[i] = stat.target
                            }
                        }
                        tick()
                    })
                }
            })
        }, { threshold: 0.5 })

        const statsEl = document.getElementById('stats-section')
        if (statsEl) counterObserver.observe(statsEl)

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            const handler: EventListener = (e) => {
                const href = anchor.getAttribute('href')
                if (href && href !== '#' && href.length > 1) {
                    const target = document.querySelector(href)
                    if (target) {
                        e.preventDefault()
                        window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' })
                    }
                }
            }
            anchor.addEventListener('click', handler)
            smoothScrollHandlers.push({ anchor, handler })
        })
    })

    onUnmounted(() => {
        stopAuto()
        sectionObserver?.disconnect()
        counterObserver?.disconnect()
        smoothScrollHandlers.forEach(({ anchor, handler }) => anchor.removeEventListener('click', handler))
    })
</script>
<template>
    <section id="home" role="region" aria-label="Hero section" class="relative scroll-reveal reveal-home fade-in-0 zoom-in-95 duration-700">
        <div class="relative h-[500px] md:h-[600px] lg:h-[700px] overflow-hidden">
            <div v-for="(slide, i) in slides" :key="i"
                class="hero-slide absolute inset-0"
                :class="i === currentSlide
                    ? 'opacity-100 z-20 pointer-events-auto'
                    : 'opacity-0 z-0 pointer-events-none'">
                <div :class="`absolute inset-0 bg-gradient-to-r ${slide.gradient} to-transparent z-10 transition-opacity duration-1000`"></div>
                <img :src="slide.img" :alt="slide.alt"
                    class="hero-slide-image w-full h-full object-cover"
                    :class="i === currentSlide ? 'hero-image-active' : 'hero-image-inactive'"
                    loading="eager" />
                <div class="absolute inset-0 z-20 flex items-center">
                    <div class="container mx-auto px-4">
                        <div class="hero-slide-content max-w-3xl text-white"
                            :class="i === currentSlide ? 'hero-content-active' : 'hero-content-inactive'">
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight">{{ slide.title }}</h2>
                            <p class="text-lg md:text-xl mb-8 text-gray-100">{{ slide.desc }}</p>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a v-for="cta in slide.ctas" :key="cta.href" :href="cta.href"
                                    :class="cta.style === 'white'
                                        ? `bg-white ${cta.color} px-8 py-4 rounded-lg font-semibold hover:shadow-xl transition-all transform hover:scale-105 text-center`
                                        : 'border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-blue-800 transition-all text-center'">
                                    <i :class="`fas ${cta.icon} mr-2`"></i>{{ cta.label }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <button @click="() => { stopAuto(); prevSlide(); startAuto() }" aria-label="Previous slide"
                class="absolute left-4 top-1/2 -translate-y-1/2 z-30 bg-white/30 hover:bg-white/50 backdrop-blur-sm text-white p-3 rounded-full transition">
                <i class="fas fa-chevron-left text-xl"></i>
            </button>
            <button @click="() => { stopAuto(); nextSlide(); startAuto() }" aria-label="Next slide"
                class="absolute right-4 top-1/2 -translate-y-1/2 z-30 bg-white/30 hover:bg-white/50 backdrop-blur-sm text-white p-3 rounded-full transition">
                <i class="fas fa-chevron-right text-xl"></i>
            </button>

            <!-- Dots -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 flex space-x-2">
                <button v-for="(_, i) in slides" :key="i" @click="() => { stopAuto(); showSlide(i); startAuto() }"
                    :class="['w-3 h-3 rounded-full transition-all', i === currentSlide ? 'bg-white' : 'bg-white/50']" />
            </div>
        </div>
    </section>

    <section class="py-0 relative -mt-16 z-40 scroll-reveal reveal-quick fade-in-0 slide-in-from-bottom-8 duration-700">
        <!-- <div class="pointer-events-none absolute inset-x-0 -top-24 h-48 bg-gradient-to-r from-sky-100 via-emerald-50 to-amber-100 blur-2xl"></div>
        <div class="pointer-events-none absolute inset-x-0 -bottom-24 h-48 bg-gradient-to-r from-indigo-50 via-rose-50 to-cyan-50 blur-2xl"></div> -->
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-6 stagger-grid">
                <a v-for="card in quickCards" :key="card.title" :href="card.link" :aria-label="card.title"
                    class="group premium-sheen relative overflow-hidden rounded-[28px] border border-white/80 bg-gradient-to-br from-white/95 via-white/85 to-slate-50/90 p-6 shadow-[0_30px_70px_-40px_rgba(2,8,23,0.9)] backdrop-blur transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_36px_80px_-45px_rgba(2,8,23,0.95)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/60 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                    <div :class="`absolute inset-0 bg-gradient-to-br ${card.gradient} opacity-[0.12]`"></div>
                    <div class="absolute inset-0 border border-white/60 rounded-[26px] pointer-events-none"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.7),transparent_55%)] opacity-60"></div>
                    <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-gradient-to-br from-white/70 to-white/5 blur-3xl transition-opacity duration-300 group-hover:opacity-90"></div>
                    <div :class="`absolute -left-14 -bottom-14 h-32 w-32 rounded-full bg-gradient-to-br ${card.gradient} opacity-30 blur-3xl`"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between">
                            <div :class="`h-12 w-12 rounded-2xl bg-gradient-to-br ${card.gradient} text-white flex items-center justify-center shadow-[0_12px_28px_-12px_rgba(2,6,23,0.7)] ring-1 ring-white/50`">
                                <i :class="`fas ${card.icon} text-lg`"></i>
                            </div>
                            <div class="h-9 w-9 rounded-full border border-slate-200/70 text-slate-500 flex items-center justify-center transition-all duration-300 group-hover:border-slate-300 group-hover:text-slate-700 group-hover:translate-x-0.5 bg-white/80 shadow-inner">
                                <i class="fas fa-arrow-right text-[11px]"></i>
                            </div>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-[18px] leading-tight font-extrabold text-slate-900">{{ card.title }}</h3>
                            <p class="text-[11px] uppercase tracking-[0.26em] text-slate-400 mt-2">&nbsp;</p>
                        </div>
                        <div :class="`mt-6 h-1.5 w-16 rounded-full bg-gradient-to-r ${card.gradient} shadow-[0_8px_20px_-12px_rgba(15,23,42,0.8)]`"></div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <section id="about" role="region" aria-label="About us" class="py-20 bg-white scroll-reveal reveal-about fade-in-0 slide-in-from-left-12 duration-700">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="relative">
                    <div class="relative w-full overflow-hidden rounded-2xl shadow-2xl bg-slate-900" style="padding-top: 56.25%;">
                    <iframe
                        class="absolute inset-0 h-full w-full"
                        src="https://www.youtube.com/embed/CBQMG4-YHrs?si=jbzFDpgKXlvQUQdR&start=101&autoplay=1&mute=1&playsinline=1&vq=hd1080"
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen>
                    </iframe>
                    </div>
                    <div class="absolute -bottom-6 -right-6 bg-blue-800 text-white p-8 rounded-2xl shadow-xl">
                        <div class="text-5xl font-bold">7+</div>
                        <div class="text-sm">Years of Excellence</div>
                    </div>
                </div>
                <div>
                    <div class="inline-block bg-blue-50 text-blue-800 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                        <i class="fas fa-hospital mr-2"></i>About AMZ Hospital
                    </div>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">Welcome to AMZ Hospital Ltd</h2>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                        Since 2018, AMZ Hospital Ltd., measured its success by counting of lives been saved, hopes been restored, and care been delivered with quality service & professionalism. 
                    </p>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        It was founded by a team of visionary clinicians, leaders from civil society and different discipline with a shared vision of reducing health care inequalities.
                    </p>
                    <div class="grid sm:grid-cols-2 gap-4 mb-8">
                        <div v-for="feat in ['24/7 Emergency Care', 'Expert Specialists', 'Modern Equipment', 'Patient-Centered Care']" :key="feat"
                            class="flex items-start space-x-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-blue-800 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ feat }}</h4>
                                <p class="text-sm text-gray-600">Quality care you can trust</p>
                            </div>
                        </div>
                    </div>
                    <a href="#appointment"
                        class="inline-flex items-center bg-gradient-to-r from-blue-800 to-sky-500 text-white px-8 py-4 rounded-lg font-semibold hover:shadow-xl transition-all transform hover:scale-105">
                        <i class="fas fa-book-open mr-2"></i> Read More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-gradient-to-br from-blue-50 to-indigo-50 scroll-reveal reveal-why fade-in-0 slide-in-from-bottom-10 duration-700">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <div class="inline-block bg-white text-blue-800 px-4 py-2 rounded-full text-sm font-semibold mb-4 shadow-sm">
                    <i class="fas fa-award mr-2"></i>Why Choose Us
                </div>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Why AMZ Hospital is Your Best Choice</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Patients choose AMZ for reliability, transparency, faster access, and a safer care experience from admission to follow-up.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 stagger-grid">
                <div v-for="(feat, i) in [
                    { color: 'from-blue-500 to-blue-600', icon: 'fa-award', title: 'Trusted Outcomes', desc: 'Consistent treatment quality with strong clinical governance and senior specialist oversight.' },
                    { color: 'from-emerald-500 to-emerald-600', icon: 'fa-calendar-check', title: 'Fast Appointments', desc: 'Streamlined booking and coordinated scheduling to reduce waiting and delays.' },
                    { color: 'from-red-500 to-red-600', icon: 'fa-shield-heart', title: 'Patient Safety First', desc: 'Strict infection-control protocols, safety checklists, and monitored care pathways.' },
                    { color: 'from-purple-500 to-purple-600', icon: 'fa-comments', title: 'Clear Communication', desc: 'Doctors explain diagnosis and options in plain language before every key decision.' },
                    { color: 'from-yellow-500 to-yellow-600', icon: 'fa-file-waveform', title: 'Digital Reports', desc: 'Faster test report access and smoother follow-up through digital sharing workflows.' },
                    { color: 'from-pink-500 to-pink-600', icon: 'fa-people-roof', title: 'Family-Centered Support', desc: 'Care teams guide patients and families throughout treatment, discharge, and recovery.' },
                    { color: 'from-indigo-500 to-indigo-600', icon: 'fa-file-invoice-dollar', title: 'Transparent Billing', desc: 'Clear package details and cost visibility to avoid confusion during care.' },
                    { color: 'from-teal-500 to-teal-600', icon: 'fa-headset', title: '24/7 Help Desk', desc: 'Round-the-clock support for urgent guidance, admissions, and service coordination.' },
                ]" :key="feat.title"
                    class="premium-sheen bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2">
                    <div :class="`w-16 h-16 bg-gradient-to-br ${feat.color} rounded-xl flex items-center justify-center mb-6`"
                        style="animation: float 3s ease-in-out infinite" :style="`animation-delay: ${i * 0.2}s`">
                        <i :class="`fas ${feat.icon} text-white text-3xl`"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ feat.title }}</h3>
                    <p class="text-gray-600">{{ feat.desc }}</p>
                </div>
            </div>
        </div>
    </section>

    <section id="departments" class="py-24 bg-slate-50 relative overflow-hidden scroll-reveal reveal-departments fade-in-0 slide-in-from-bottom-8 duration-700">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-200 to-transparent"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 scroll-reveal">
                <span class="text-blue-600 font-bold uppercase tracking-[0.2em] text-xs mb-3 block">Medical Excellence</span>
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-6 tracking-tight">Specialized Departments</h2>
                <div class="w-20 h-1.5 bg-blue-600 mx-auto rounded-full"></div>
                <p class="mt-6 text-slate-500 max-w-2xl mx-auto text-lg leading-relaxed">
                    World-class medical expertise paired with state-of-the-art technology to provide the highest standard of care.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
                <div v-for="dept in departments" :key="dept.id"
                    class="premium-sheen group relative flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200/70 bg-white shadow-lg transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-200/50 scroll-reveal">

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

    <section id="center-of-excellence" role="region" aria-label="Center Of Excellence" class="py-20 bg-gradient-to-br from-slate-50 to-blue-50 scroll-reveal reveal-centers fade-in-0 slide-in-from-bottom-8 duration-700">
        <div class="container mx-auto px-4">
            <div class="text-center mb-14">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Center Of Excellence</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Advanced multidisciplinary centers delivering focused, high-quality care.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 stagger-grid">
                <article v-for="center in centers" :key="center.id" :id="center.id"
                    class="premium-sheen bg-white rounded-2xl p-6 border border-slate-100 shadow-md hover:shadow-xl transition-all">
                    <div :class="`w-12 h-12 rounded-lg ${center.bg} ${center.color} flex items-center justify-center mb-4`">
                        <i :class="`fas ${center.icon}`"></i>
                    </div>
                    <h3 class="font-bold text-gray-900">{{ center.title }}</h3>
                    <p class="text-sm text-gray-600 mt-2">{{ center.desc }}</p>
                </article>
            </div>
            <div class="text-center mt-10">
                <a href="#center-of-excellence"
                    class="inline-flex items-center bg-gradient-to-r from-blue-800 to-sky-500 text-white px-8 py-3 rounded-lg font-semibold hover:shadow-xl transition-all">
                    View All Center Of Excellence <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <section id="services" role="region" aria-label="Healthcare services" class="py-20 bg-white scroll-reveal reveal-services fade-in-0 slide-in-from-bottom-8 duration-700">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Comprehensive Healthcare Services</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">From emergency response to diagnostics, surgery, maternity, pharmacy, and recovery support in one integrated system.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 stagger-grid">
                <div v-for="svc in servicesList" :key="svc.title"
                    :class="`premium-sheen bg-gradient-to-br ${svc.bg} to-white border-2 border-gray-100 rounded-2xl p-8 hover:border-blue-800 hover:shadow-xl transition`">
                    <i :class="`fas ${svc.icon} text-5xl text-blue-800 mb-4`"></i>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ svc.title }}</h3>
                    <p class="text-gray-600 mb-4">{{ svc.desc }}</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li v-for="item in svc.items" :key="item" class="flex items-center">
                            <i class="fas fa-check-circle text-emerald-500 mr-2"></i>{{ item }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

   <!--<section id="doctors" role="region" aria-label="Our doctors" class="py-20 bg-gradient-to-br from-gray-50 to-blue-50 scroll-reveal reveal-doctors fade-in-0 slide-in-from-right-10 duration-700">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Meet Our Expert Doctors</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Highly qualified and experienced medical professionals dedicated to your health</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 stagger-grid">
                <div v-for="doc in doctors" :key="doc.name"
                    class="premium-sheen bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2">
                    
                    <div class="h-64 overflow-hidden bg-gray-100 flex items-center justify-center">
                        <img v-if="doc.photo" :src="doc.photo" :alt="doc.name" class="w-full h-full object-cover" />
                        <i v-else class="fas fa-user-md text-gray-400 text-6xl"></i>
                    </div>

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ doc.name }}</h3>
                        <p class="text-sm text-blue-800 font-semibold mb-3">{{ doc.specialty }}</p>
                        <p class="text-sm text-gray-600 mb-4">{{ doc.degree }}<br />{{ doc.exp }} Years Experience</p>
                        <a href="#appointment"
                            class="block w-full bg-blue-800 text-white text-center py-2.5 rounded-lg font-semibold hover:bg-sky-500 transition-colors">
                            Book Appointment
                        </a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-12">
                <a href="#doctors"
                    class="inline-flex items-center bg-white text-blue-800 px-8 py-4 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                    <i class="fas fa-users mr-2"></i>View All Doctors
                </a>
            </div>
        </div>
    </section> -->

    <!-- Leadership -->
    <section id="leadership" role="region" aria-label="Leadership messages" class="py-20 bg-gradient-to-b from-blue-50/60 via-white to-white scroll-reveal fade-in-0 slide-in-from-bottom-8 duration-700">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <p class="text-xs md:text-sm font-bold uppercase tracking-[0.2em] text-blue-700 mb-3">Our Leadership</p>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Leadership Messages</h2>
                <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto">A message from the leaders guiding AMZ Hospital toward trusted, patient-focused care.</p>
            </div>
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-8">
                <article
                    v-for="(leader, idx) in leadershipMessages"
                    :key="leader.name"
                    class="group premium-sheen rounded-3xl border border-blue-100 bg-white/95 shadow-xl shadow-blue-100/60 overflow-hidden backdrop-blur-sm">
                    <div class="relative h-72 bg-slate-100">
                        <img :src="leader.photo" :alt="leader.name" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" />
                        <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-slate-900/60 to-transparent"></div>
                        <p class="absolute left-4 bottom-4 inline-flex items-center rounded-full bg-white/95 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-blue-800">
                            {{ leader.role }}
                        </p>
                    </div>
                    <div class="p-6 md:p-7">
                        <h3 class="text-2xl font-bold text-slate-900">{{ leader.name }}</h3>
                        <p v-if="leader.title" class="mt-3 text-sm font-bold text-slate-800">{{ leader.title }}</p>
                        <p
                            v-if="!leadershipExpanded[idx]"
                            class="mt-3 text-slate-600 leading-relaxed">
                            {{ leadershipPreview(leader.message) }}
                        </p>
                        <p
                            v-else
                            class="mt-3 text-slate-600 leading-relaxed whitespace-pre-line max-h-[320px] overflow-y-auto pr-1">
                            {{ leader.message }}
                        </p>
                        <button
                            type="button"
                            @click="toggleLeadershipMessage(idx)"
                            class="mt-5 inline-flex items-center rounded-full border border-blue-200 bg-blue-50 px-4 py-2 text-xs md:text-sm font-semibold text-blue-800 transition hover:bg-blue-100">
                            {{ leadershipExpanded[idx] ? 'Show Less' : 'Read Full Message' }}
                        </button>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section id="gallery" role="region" aria-label="Hospital gallery" class="py-20 bg-white scroll-reveal reveal-gallery fade-in-0 zoom-in-95 duration-700">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Hospital Gallery</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Explore our state-of-the-art facilities, modern infrastructure, and patient-centered environment</p>
            </div>
            <!-- Filter Tabs -->
            <div class="flex flex-wrap justify-center gap-3 mb-12">
                <button v-for="f in filters" :key="f" @click="activeFilter = f"
                    :class="['px-6 py-3 rounded-lg font-semibold transition capitalize', activeFilter === f ? 'bg-blue-800 text-white' : 'bg-white text-slate-800 border border-slate-200 hover:bg-sky-500 hover:text-white hover:border-sky-500']">
                    {{ f === 'all' ? 'All Photos' : f }}
                </button>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="item in filteredGallery()" :key="item.title"
                    class="group cursor-pointer">
                    <div class="premium-sheen relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300">
                        <img :src="item.img" :alt="item.title"
                            class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy" />
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                                <h3 class="text-xl font-bold mb-2">{{ item.title }}</h3>
                                <p class="text-sm text-gray-200">{{ item.desc }}</p>
                            </div>
                        </div>
                        <div :class="`absolute top-4 left-4 ${item.badgeColor} px-3 py-1 rounded-full text-white text-xs font-semibold`">
                            {{ item.badge }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="health-packages" role="region" aria-label="Health packages" class="py-20 bg-gradient-to-b from-slate-50 to-white scroll-reveal reveal-packages fade-in-0 slide-in-from-bottom-8 duration-700">
        <div class="container mx-auto px-4">
            <div class="text-center mb-14">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Health Packages</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Affordable preventive checkup plans designed for individuals, women, seniors, and families.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 stagger-grid">
                <article v-for="pkg in packages" :key="pkg.title"
                    class="premium-sheen bg-white rounded-2xl p-6 border border-slate-200 shadow-md hover:shadow-xl transition">
                    <div :class="`w-12 h-12 rounded-lg ${pkg.bg} ${pkg.color} flex items-center justify-center mb-4`">
                        <i :class="`fas ${pkg.icon}`"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ pkg.title }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ pkg.desc }}</p>
                    <p class="text-2xl font-bold text-blue-800 mb-4">{{ pkg.price }}</p>
                    <a href="#appointment"
                        class="block text-center bg-blue-800 text-white py-2.5 rounded-lg font-semibold hover:bg-sky-500 transition-colors">Book Package</a>
                </article>
            </div>
        </div>
    </section>

    <section id="appointment" role="region" aria-label="Book appointment"
        class="py-20 bg-gradient-to-br from-blue-800 to-sky-500 text-white relative overflow-hidden premium-shine-section scroll-reveal reveal-appointment fade-in-0 zoom-in-95 duration-700">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        <div class="container mx-auto max-w-[1500px] px-4 relative z-10">
            <div class="grid lg:grid-cols-12 gap-10 items-start">
                <div class="lg:col-span-5">
                    <div class="inline-block bg-white/20 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                        <i class="fas fa-calendar-check mr-2"></i>Book Your Appointment
                    </div>
                    <h2 class="text-4xl md:text-5xl font-bold mb-6">Schedule Your Visit Today</h2>
                    <p class="text-xl mb-8 text-white/90">Get expert medical care at your convenience. Book an appointment with our specialists.</p>
                    <div class="space-y-4">
                        <div v-for="feat in [
                            { icon: 'fa-clock', title: 'Quick & Easy', desc: 'Book in just a few steps' },
                            { icon: 'fa-user-md', title: 'Choose Your Doctor', desc: 'Select from our expert specialists' },
                            { icon: 'fa-calendar-alt', title: 'Flexible Scheduling', desc: 'Pick your preferred time slot' },
                        ]" :key="feat.title" class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <i :class="`fas ${feat.icon} text-2xl`"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-lg">{{ feat.title }}</h4>
                                <p class="text-white/80">{{ feat.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <AppointmentBookingWizard
                        :available-weekdays="appointmentAvailableWeekdays"
                        :blocked-dates="appointmentBlockedDates"
                    />
                </div>
            </div>
        </div>
    </section>

    <section id="testimonials" role="region" aria-label="Patient testimonials" class="py-20 bg-white scroll-reveal reveal-testimonials fade-in-0 slide-in-from-bottom-8 duration-700">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">What Our Patients Say</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Real experiences from patients who trusted us with their healthcare</p>
            </div>
            <div class="overflow-hidden" style="mask-image: linear-gradient(to right, transparent, black 6%, black 94%, transparent);">
                <div class="flex items-stretch gap-6" style="width: max-content; animation: marquee 34s linear infinite;">
                    <article
                        v-for="(t, i) in doubledTestimonials"
                        :key="`${t.name}-${i}`"
                        :class="`mb-4 premium-sheen premium-sheen-auto flex-shrink-0 w-[320px] sm:w-[360px] bg-gradient-to-br ${t.cardBg} to-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition`"
                    >
                        <div class="flex items-center mb-6">
                            <div :class="`w-16 h-16 bg-gradient-to-br ${t.bg} rounded-full flex items-center justify-center text-white text-2xl font-bold mr-4`">
                                {{ t.initials }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">{{ t.name }}</h4>
                                <div class="flex text-yellow-400 text-sm">
                                    <i v-for="s in 5" :key="s" class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-600 italic">
                            {{ t.short }}
                            <span v-if="expandedTestimonials[i]">{{ t.full }}</span>
                        </p>
                        <button @click="toggleTestimonial(i)" class="mt-4 text-sm font-semibold text-blue-800 hover:text-sky-500">
                            {{ expandedTestimonials[i] ? 'Show less' : 'Read more' }}
                        </button>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section id="stats-section" class="py-20 bg-gradient-to-r from-blue-800 to-sky-500 text-white premium-shine-section scroll-reveal fade-in-0 slide-in-from-bottom-8 duration-700">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div v-for="(stat, i) in stats" :key="stat.label">
                    <div class="text-5xl font-bold mb-2" style="font-feature-settings: 'tnum'; font-variant-numeric: tabular-nums;">
                        {{ statValues[i].toLocaleString() }}{{ statValues[i] >= stat.target ? '+' : '' }}
                    </div>
                    <p class="text-lg text-white/90">{{ stat.label }}</p>
                </div>
            </div>
        </div>
    </section>

   
   <!-- <section id="blog" role="region" aria-label="Latest blog posts" class="py-20 bg-white scroll-reveal reveal-blog fade-in-0 slide-in-from-bottom-8 duration-700">
        <div class="container mx-auto px-4">
            <div class="mb-14 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-sky-600">Health Journal</p>
                    <h2 class="mt-2 text-4xl md:text-5xl font-bold text-gray-900">Latest Blog & Health Insights</h2>
                    <p class="mt-3 max-w-3xl text-lg text-gray-600">Practical medical guidance, prevention tips, and wellness updates from AMZ specialists.</p>
                </div>
                <a href="#blog" class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-5 py-3 text-sm font-semibold text-blue-800 transition hover:bg-blue-100">
                    View All Posts <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-3 stagger-grid">
                <article
                    v-for="post in blogPosts"
                    :key="post.id"
                    class="premium-sheen overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg transition hover:-translate-y-1 hover:shadow-2xl"
                >
                    <div class="relative h-56 overflow-hidden">
                        <img :src="post.img" :alt="post.title" class="h-full w-full object-cover transition duration-500 hover:scale-105" loading="lazy" />
                        <span class="absolute left-4 top-4 rounded-full bg-blue-800 px-3 py-1 text-xs font-semibold text-white">
                            {{ post.category }}
                        </span>
                    </div>
                    <div class="p-6">
                        <div class="mb-3 flex items-center gap-3 text-xs font-medium text-slate-500">
                            <span><i class="fas fa-calendar-alt mr-1"></i>{{ post.date }}</span>
                            <span><i class="fas fa-clock mr-1"></i>{{ post.readTime }}</span>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-gray-900">{{ post.title }}</h3>
                        <p class="mb-5 text-sm leading-6 text-gray-600">{{ post.excerpt }}</p>
                        <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">By {{ post.author }}</p>
                            <a :href="post.href" class="text-sm font-semibold text-blue-800 transition hover:text-sky-500">
                                Read Article <i class="fas fa-arrow-right ml-1 text-xs"></i>
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>-->

    <section id="newsletter-partners" class="py-20 bg-gradient-to-b from-white to-slate-50 scroll-reveal reveal-newsletter fade-in-0 zoom-in-95 duration-700">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto mb-14">
                <div class="relative rounded-3xl p-[1px] shadow-2xl"
                    style="background: linear-gradient(to right, #1e40af, #0ea5e9, #10b981);">
                    <div class="rounded-3xl bg-white p-6 md:p-8">
                        <div class="grid lg:grid-cols-2 gap-6 items-stretch">
                            <div class="premium-sheen rounded-2xl bg-gradient-to-br from-blue-800 to-sky-500 text-white p-6 md:p-8">
                                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-4">
                                    <i class="fas fa-envelope-open-text text-xl"></i>
                                </div>
                                <p class="text-white/80 text-xs font-semibold uppercase tracking-[0.2em] mb-2">Health Updates</p>
                                <h2 class="text-3xl md:text-4xl font-bold mb-3 leading-tight">Subscribe to Our Newsletter</h2>
                                <p class="text-white/90">Get latest health tips, package offers, and hospital announcements directly in your inbox.</p>
                                <div class="mt-6 flex flex-wrap gap-2 text-xs">
                                    <span class="px-3 py-1 rounded-full bg-white/20">Weekly insights</span>
                                    <span class="px-3 py-1 rounded-full bg-white/20">No spam</span>
                                    <span class="px-3 py-1 rounded-full bg-white/20">Instant updates</span>
                                </div>
                            </div>
                            <div class="premium-sheen rounded-2xl border border-slate-200 bg-slate-50 p-5 md:p-6 flex flex-col justify-center">
                                <p class="text-sm text-slate-600 mb-3 font-medium">Enter your email to join our subscriber list.</p>
                                <div v-if="newsletterSuccess" class="text-green-600 font-semibold text-center py-3 mb-3">
                                    âœ… Subscribed successfully. Thank you!
                                </div>
                                <form @submit.prevent="submitNewsletter" class="space-y-3">
                                    <input v-model="newsletterEmail" type="email" required placeholder="name@email.com"
                                        class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-800 focus:ring-2 focus:ring-blue-800 focus:border-transparent outline-none bg-white" />
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-blue-800 to-sky-500 text-white px-6 py-3 rounded-lg font-semibold transition-all hover:shadow-lg">
                                        Subscribe Now
                                    </button>
                                </form>
                                <p class="text-xs text-slate-500 mt-3">By subscribing, you agree to receive healthcare updates from AMZ Hospital.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Partners marquee -->
            <div>
                <div class="text-center mb-6">
                    <p class="text-sm uppercase tracking-wider text-slate-500 font-semibold">Our Corporate Partners</p>
                </div>
                <div class="partner-marquee-wrap overflow-hidden" style="mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);">
                    <div class="partner-marquee-track">
                        <div class="partner-marquee-group">
                            <div
                                v-for="(partner, i) in partnersForMarquee"
                                :key="`${partner.name}-primary-${i}`"
                                class="partner-logo-item premium-sheen border border-slate-200 rounded-2xl bg-white p-3 flex items-center justify-center shadow-lg">
                                <img
                                    :src="partner.logo"
                                    :alt="partner.name"
                                    class="w-full h-full object-contain grayscale hover:grayscale-0 transition-all" />
                            </div>
                        </div>
                        <div class="partner-marquee-group" aria-hidden="true">
                            <div
                                v-for="(partner, i) in partnersForMarquee"
                                :key="`${partner.name}-clone-${i}`"
                                class="partner-logo-item premium-sheen border border-slate-200 rounded-2xl bg-white p-3 flex items-center justify-center shadow-lg">
                                <img
                                    :src="partner.logo"
                                    :alt="partner.name"
                                    class="w-full h-full object-contain grayscale hover:grayscale-0 transition-all" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" role="region" aria-label="Contact information" class="py-20 bg-white scroll-reveal reveal-contact fade-in-0 slide-in-from-bottom-8 duration-700">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Contact Information</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Visit us or reach out for any inquiries about our services</p>
            </div>
            <div class="grid lg:grid-cols-3 gap-8 mb-12 stagger-grid">
                <div class="premium-sheen bg-gradient-to-br from-blue-50 to-white rounded-2xl p-8 shadow-lg text-center hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-blue-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Visit Us</h3>
                    <p class="text-gray-600">Cha- 80/3, Shadhinota Sarani, Progati Sarani Rd<br> Uttar Badda, Dhaka-1212</p>
                </div>
                <div class="premium-sheen bg-gradient-to-br from-green-50 to-white rounded-2xl p-8 shadow-lg text-center hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-phone-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Call Us</h3>
                    <p class="text-gray-600">
                        Emergency: <a href="tel:10699" class="text-blue-800 font-bold hover:underline">10699</a><br>
                        General: <a href="tel:+8801234567890" class="text-blue-800 hover:underline">+880 184 733 1047</a>
                    </p>
                </div>
                <div class="premium-sheen bg-gradient-to-br from-purple-50 to-white rounded-2xl p-8 shadow-lg text-center hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Email Us</h3>
                    <p class="text-gray-600">
                        <a href="mailto:info@amzhospital.com" class="text-blue-800 hover:underline">info@amzhospital.com</a><br>
                        <a href="mailto:emergency@amzhospital.com" class="text-blue-800 hover:underline">emergency@amzhospital.com</a>
                    </p>
                </div>
            </div>
            <div class="rounded-2xl overflow-hidden shadow-2xl h-96">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d912.7411925766279!2d90.42537456963056!3d23.78426882587095!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c7af2fcf1c87%3A0x3c70327ba1b06ef7!2sAMZ%20Hospital%20Ltd.!5e0!3m2!1sen!2sbd!4v1773125024838!5m2!1sen!2sbd"
                    width="100%" height="100%" style="border:0;" allowfullscreen="false" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    class="grayscale hover:grayscale-0 transition duration-500">
                </iframe>
            </div>
        </div>
    </section>
</template>

<style>
/* Premium reveal base */

.premium-sheen,
.premium-card {
    position: relative;
    overflow: hidden;
    transform-style: preserve-3d;
    transition: transform 0.45s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.45s ease, border-color 0.35s ease;
    will-change: transform;
}

.premium-sheen::before,
.premium-card::before {
    content: '';
    position: absolute;
    top: -120%;
    left: -30%;
    width: 45%;
    height: 340%;
    pointer-events: none;
    z-index: 2;
    opacity: 0;
    transform: rotate(16deg);
    background: linear-gradient(
        120deg,
        rgba(255, 255, 255, 0) 25%,
        rgba(255, 255, 255, 0.32) 50%,
        rgba(255, 255, 255, 0) 75%
    );
}

.premium-sheen:hover::before,
.premium-sheen:focus-within::before,
.premium-card:hover::before,
.premium-card:focus-within::before {
    opacity: 1;
    animation: premiumShine 1.05s ease;
}

.premium-sheen-auto::before {
    animation: premiumShineAuto 6.8s ease-in-out infinite;
}

#testimonials .premium-sheen-auto:nth-child(2)::before { animation-delay: 0.9s; }
#testimonials .premium-sheen-auto:nth-child(3)::before { animation-delay: 1.8s; }
#testimonials .premium-sheen-auto:nth-child(4)::before { animation-delay: 2.7s; }
#testimonials .premium-sheen-auto:nth-child(5)::before { animation-delay: 3.6s; }
#testimonials .premium-sheen-auto:nth-child(6)::before { animation-delay: 4.5s; }

.premium-shine-section::before,
.premium-shine-section::after {
    content: '';
    position: absolute;
    top: -120%;
    left: -35%;
    width: 42%;
    height: 340%;
    pointer-events: none;
    z-index: 1;
    opacity: 0;
    mix-blend-mode: screen;
    background: linear-gradient(
        120deg,
        rgba(255, 255, 255, 0) 22%,
        rgba(255, 255, 255, 0.22) 48%,
        rgba(255, 255, 255, 0.56) 52%,
        rgba(255, 255, 255, 0.22) 56%,
        rgba(255, 255, 255, 0) 82%
    );
}

.premium-shine-section::before {
    animation: sectionShineSweep 6.5s cubic-bezier(0.22, 0.7, 0.25, 1) infinite;
}

.premium-shine-section::after {
    width: 32%;
    left: -48%;
    opacity: 0.35;
    animation: sectionShineSweep 8.2s cubic-bezier(0.2, 0.78, 0.24, 1) infinite 1.2s;
}

.scroll-reveal {
    opacity: 0;
    transform: translate3d(0, 56px, 0) scale(0.98);
    filter: blur(8px);
    transition: opacity 0.9s cubic-bezier(0.22, 1, 0.36, 1),
        transform 1s cubic-bezier(0.22, 1, 0.36, 1),
        filter 0.9s cubic-bezier(0.22, 1, 0.36, 1);
    will-change: transform, opacity, filter;
}

.scroll-reveal.animate-in {
    opacity: 1;
    transform: translate3d(0, 0, 0) scale(1);
    filter: blur(0);
}

.reveal-home { transform: scale(1.05); filter: brightness(0.88) saturate(1.05); }
.reveal-home.animate-in { transform: scale(1); filter: brightness(1) saturate(1); }

.reveal-quick { transform: translate3d(0, 28px, 0) perspective(1200px) rotateX(7deg); }
.reveal-about { transform: translate3d(-42px, 18px, 0) scale(0.97); }
.reveal-why { transform: translate3d(0, 38px, 0) scale(0.96) skewY(-1deg); }
.reveal-departments { transform: translate3d(0, 34px, 0) scale(0.98); }
.reveal-doctors { transform: translate3d(28px, 26px, 0) scale(0.97); }
.reveal-schedules { transform: translate3d(0, 34px, 0) scale(0.98); }
.reveal-gallery { transform: translate3d(0, 42px, 0) scale(0.985) rotateX(5deg); }
.reveal-services { transform: translate3d(0, 48px, 0) scale(0.97); }
.reveal-packages { transform: translate3d(0, 38px, 0) scale(0.985) rotateZ(-0.8deg); }
.reveal-centers { transform: translate3d(0, 34px, 0) scale(0.97); }
.reveal-appointment { transform: translate3d(0, 24px, 0) scale(0.99); filter: brightness(0.92) saturate(0.9); }
.reveal-appointment.animate-in { filter: brightness(1) saturate(1); }
.reveal-testimonials { transform: translate3d(0, 46px, 0) scale(0.975); }
.reveal-blog { transform: translate3d(0, 42px, 0) scale(0.98); }
.reveal-newsletter { transform: translate3d(0, 40px, 0) scale(0.975); }
.reveal-contact { transform: translate3d(0, 36px, 0) scale(0.985); }

.scroll-reveal .stagger-grid > * {
    opacity: 0;
    transform: translate3d(0, 26px, 0) scale(0.97);
    filter: blur(4px);
}

.scroll-reveal.animate-in .stagger-grid > * {
    animation: premiumStaggerIn 0.8s cubic-bezier(0.2, 0.9, 0.2, 1) forwards;
}

.scroll-reveal.animate-in .stagger-grid > *:nth-child(2) { animation-delay: 0.07s; }
.scroll-reveal.animate-in .stagger-grid > *:nth-child(3) { animation-delay: 0.14s; }
.scroll-reveal.animate-in .stagger-grid > *:nth-child(4) { animation-delay: 0.21s; }
.scroll-reveal.animate-in .stagger-grid > *:nth-child(5) { animation-delay: 0.28s; }
.scroll-reveal.animate-in .stagger-grid > *:nth-child(6) { animation-delay: 0.35s; }
.scroll-reveal.animate-in .stagger-grid > *:nth-child(7) { animation-delay: 0.42s; }
.scroll-reveal.animate-in .stagger-grid > *:nth-child(8) { animation-delay: 0.49s; }

.reveal-doctors.animate-in .stagger-grid > *,
.reveal-packages.animate-in .stagger-grid > *,
.reveal-centers.animate-in .stagger-grid > * {
    animation-name: premiumLiftRotateIn;
}

.reveal-gallery.animate-in .stagger-grid > *,
.reveal-testimonials.animate-in .stagger-grid > * {
    animation-name: premiumBloomIn;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes premiumShine {
    0% { left: -35%; }
    100% { left: 130%; }
}

@keyframes premiumShineAuto {
    0%, 12% { left: -35%; opacity: 0; }
    18% { opacity: 1; }
    34% { left: 130%; opacity: 0; }
    100% { left: 130%; opacity: 0; }
}

@keyframes sectionShineSweep {
    0% { transform: rotate(15deg) translateX(-8%); opacity: 0; }
    8% { opacity: 0.18; }
    32% { opacity: 0.46; }
    58% { opacity: 0.14; }
    100% { transform: rotate(15deg) translateX(430%); opacity: 0; }
}

@keyframes premiumStaggerIn {
    0% { opacity: 0; transform: translate3d(0, 26px, 0) scale(0.96); filter: blur(4px); }
    100% { opacity: 1; transform: translate3d(0, 0, 0) scale(1); filter: blur(0); }
}

@keyframes premiumLiftRotateIn {
    0% { opacity: 0; transform: translate3d(0, 30px, 0) rotateX(8deg) scale(0.95); filter: blur(4px); }
    100% { opacity: 1; transform: translate3d(0, 0, 0) rotateX(0) scale(1); filter: blur(0); }
}

@keyframes premiumBloomIn {
    0% { opacity: 0; transform: translate3d(0, 22px, 0) scale(0.92); filter: blur(6px) saturate(0.8); }
    100% { opacity: 1; transform: translate3d(0, 0, 0) scale(1); filter: blur(0) saturate(1); }
}

.hero-slide {
    transition: opacity 1.1s cubic-bezier(0.22, 1, 0.36, 1);
}

.hero-slide-image {
    animation: heroZoomInOut 7.5s ease-in-out infinite alternate;
    filter: brightness(0.9) saturate(1.02);
    transition: filter 1s ease;
    will-change: transform, filter;
    transform-origin: center center;
}

.hero-image-active {
    filter: brightness(0.98) saturate(1.08);
}

.hero-image-inactive {
    filter: brightness(0.75) saturate(0.92);
}

.hero-slide-content {
    transition: opacity 0.75s ease, transform 0.85s cubic-bezier(0.22, 1, 0.36, 1), filter 0.75s ease;
    will-change: transform, opacity, filter;
}

.hero-content-active {
    opacity: 1;
    transform: translateY(0) scale(1);
    filter: blur(0);
}

.hero-content-inactive {
    opacity: 0;
    transform: translateY(24px) scale(0.98);
    filter: blur(2px);
}

.partner-marquee-wrap {
    width: 100%;
}

.partner-marquee-track {
    display: flex;
    width: 200%;
    animation: marquee 26s linear infinite;
    will-change: transform;
}

.partner-marquee-group {
    flex: 0 0 50%;
    width: 50%;
    display: grid;
    grid-template-columns: repeat(9, minmax(0, 1fr));
    gap: 0.75rem;
}

.partner-logo-item {
    height: 74px;
}



@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

@keyframes pageFadeIn {
    0% { opacity: 0; transform: translateY(8px); }
    100% { opacity: 1; transform: translateY(0); }
}

@keyframes heroZoomInOut {
    0% { transform: scale(1.04) translate3d(0, 0, 0); }
    50% { transform: scale(1.12) translate3d(-1.2%, -0.8%, 0); }
    100% { transform: scale(1.06) translate3d(1%, 0.6%, 0); }
}

body {
    animation: pageFadeIn 0.7s ease-out;
}

@media (prefers-reduced-motion: reduce) {
    .scroll-reveal,
    .scroll-reveal .stagger-grid > *,
    .scroll-reveal.animate-in .stagger-grid > * {
        opacity: 1 !important;
        transform: none !important;
        filter: none !important;
        animation: none !important;
        transition: none !important;
    }

    .premium-sheen::before {
        opacity: 0 !important;
        animation: none !important;
        transform: none !important;
    }

    .premium-shine-section::before,
    .premium-shine-section::after {
        opacity: 0 !important;
        animation: none !important;
        transform: none !important;
    }

    .hero-slide,
    .hero-slide-image,
    .hero-slide-content {
        transition: none !important;
        animation: none !important;
    }

    html {
        scroll-behavior: auto;
    }
}

::-webkit-scrollbar { width: 10px; }
::-webkit-scrollbar-track { background: #f1f5f9; }
::-webkit-scrollbar-thumb { background: #1e40af; border-radius: 5px; }
::-webkit-scrollbar-thumb:hover { background: #0ea5e9; }
</style>


