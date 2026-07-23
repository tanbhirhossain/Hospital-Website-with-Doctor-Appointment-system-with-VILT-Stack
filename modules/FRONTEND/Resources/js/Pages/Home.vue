<script setup lang="ts">
import { defineOptions, computed } from 'vue'
import FrontendLayout from '../Layout/FrontendLayout.vue'


// Import our new section components (adjust path if your folder is 'Components/frontend/')
import HeroSlider from '../Components/frontend/HeroSlider.vue'
import QuickAccessCards from '../Components/frontend/QuickAccessCards.vue'
import AboutSection from '../Components/frontend/AboutSection.vue'
import WhyChooseUs from '../Components/frontend/WhyChooseUs.vue'
import DepartmentsSection from '../Components/frontend/DepartmentsSection.vue'
import CentersOfExcellence from '../Components/frontend/CentersOfExcellence.vue'
import ServicesSection from '../Components/frontend/ServicesSection.vue'
import DoctorsSection from '../Components/frontend/DoctorsSection.vue'
import LeadershipSection from '../Components/frontend/LeadershipSection.vue'
import GallerySection from '../Components/frontend/GallerySection.vue'
import HealthPackages from '../Components/frontend/HealthPackages.vue'
import AppointmentSection from '../Components/frontend/AppointmentSection.vue'
import TestimonialsSection from '../Components/frontend/TestimonialsSection.vue'
import StatsCounter from '../Components/frontend/StatsCounter.vue'
import BlogSection from '../Components/frontend/BlogSection.vue'
import NewsletterPartners from '../Components/frontend/NewsletterPartners.vue'
import ContactSection from '../Components/frontend/ContactSection.vue'
import { useScrollReveal } from '../composables/useScrollReveal'

defineOptions({ layout: FrontendLayout })

const props = defineProps({
  departments: { type: Array, required: true },
  blogs: { type: Array, required: true },
  slides: { type: Array, required: true },
  galleries: { type: Array, required: true }, 
  centers: {type: Array, required: true},
  doctors: {type: Array, required: true}
})

// MAPPING LOGIC: Transform backend data into the format GallerySection needs
const galleryItems = computed(() => {
  // Use optional chaining (?.) and provide a default empty array ([])
  return (props.galleries || []).map(item => ({
    category: item.category?.name || 'General',
    img: item.image_url,
    title: item.title,
    desc: item.description,
    badge: item.category?.name || 'Gallery',
    badgeColor: getBadgeColor(item.category?.name)
  }))
})

// Helper to keep the template clean
const getBadgeColor = (categoryName?: string) => {
  const colors: Record<string, string> = {
    'Infrastructure': 'bg-blue-800',
    'Facilities': 'bg-red-500',
    'Equipment': 'bg-emerald-500',
    'Departments': 'bg-cyan-600'
  }
  return colors[categoryName || ''] || 'bg-gray-600'
}

const quickCards = [
  { gradient: 'from-[blue] to-[blue]', icon: 'fa-user-md', title: 'Find Doctor', link: '/doctors' },
  { gradient: 'from-[#24a6db] to-[#5cc6e0]', icon: 'fa-calendar-check', title: 'Book an Appointment', link: '#appointment' },
  { gradient: 'from-[#1fb18f] to-[#6dd8bc]', icon: 'fa-home', title: 'Home Sample Collection', link: '#services' },
  { gradient: 'from-[#dda229] to-[#efd56d]', icon: 'fa-file-medical', title: 'Online Lab Report', link: '#contact' },
  { gradient: 'from-[#e56775] to-[#ef8f9b]', icon: 'fa-map-marker-alt', title: 'Find Us', link: '#contact' },
]

const leadershipMessages = [
  {
    name: 'Prof. Dr. Ahmedul Kabir',
    role: 'Chairman',
    roleLine: 'Chairman, AMZ Hospital Ltd.',
    eyebrow: 'Message from the Chairman',
    title: 'Where Clinical Excellence Meets Medical Ethics',
    quote: 'Since our inception, AMZ Hospital Ltd. has been guided by a singular, foundational vision: to bridge healthcare gaps and establish an institution where clinical excellence meets unwavering medical ethics.',
    credentials: ['MBBS, FCPS, FACP, FRCP (Internal Medicine)', 'Healthcare Administration & Policy Expert', 'Academic Clinical Educator & Lead Clinical Researcher'],
    photo: 'https://amzhospitalbd.com/storage/homemessages/January2026/AZj5nsJ4IyaSHuDHtJke.jpeg?w=800&h=900&fit=crop',
    message: `As the Chairman, my focus is to ensure that our hospital remains a sanctuary of trust, safety, and superior clinical governance. We do not measure our success merely by numbers, but by the lives we restore, the hopes we rebuild, and the strict safety standards we uphold across our clinical departments. By bringing together a distinguished panel of over 70 world-class consultants and medical professionals, we have built a multi-disciplinary framework capable of managing the most complex medical challenges with compassion and absolute integrity.

Healthcare is a noble responsibility. We remain deeply committed to advancing medical practices in Bangladesh, continuously refining our clinical protocols, and ensuring that every patient who walks through our doors receives transparent, evidence-based care. Thank you for placing your invaluable trust in AMZ Hospital. We are honored to be your partner on your journey to health and wellness.`,
  },
  {
    name: 'Md. Zulfiquear Ali Babul',
    role: 'Managing Director',
    roleLine: 'Managing Director',
    eyebrow: 'Message from the Managing Director',
    title: 'Built on the Foundation of Trust, Partnership and Excellence',
    quote: 'Exceptional healthcare is not a performance by any single institution — it is a well-coordinated ecosystem of care.',
    credentials: ['Legal Practitioner (LLB, ITP)', 'General Body Member, FBCCI', 'Prominent Industrialist & Healthcare Entrepreneur'],
    photo: 'https://amzhospitalbd.com/storage/homemessages/June2024/FBMOKqi5ppUYJ3mhvhDS.png?w=800&h=900&fit=crop',
    message: `At AMZ Hospital Ltd., our growth has never been an individual pursuit — it has been a collective vision, built on the foundation of trust, partnership and excellence. Today, we operate an ISO-certified, state-of-the-art facility — with 100 bed-capacity, fully compliant with national regulatory bodies. It is anchored by 28 multi-specialty clinical departments, 8 highly specialized Centers of Excellence, cutting-edge diagnostic laboratories and advanced surgical infrastructure benchmarked against international standards.

Yet what truly distinguishes us is our philosophy: we believe that exceptional healthcare is not a performance by any single institution — it is a well-coordinated ecosystem of care. That is why partnership lies at the heart of everything we do. We actively welcome and cultivate collaborations with corporate clients, healthcare partners, and allied service providers who share our commitment to patient-centered excellence. Together, we are building a one-stop healthcare umbrella — where patients can access the full spectrum of services under one roof, saving precious time and finding genuine reassurance in moments of need.

Strategic growth and enterprise value, for us, mean expanding our capacity to serve — deeper, faster, and with greater reliability. AMZ Hospital is not just a destination for treatment — it is your trusted healthcare partner.`,
  },
  {
    name: 'Dr. Lima Rahman',
    role: 'CEO',
    roleLine: 'Chief Executive Officer (CEO)',
    eyebrow: 'Message from the CEO',
    title: 'Every Second Counts — Every Patient Deserves the Best',
    quote: 'My promise to you is that our team will remain relentless in delivering the highest standard of service at every single touchpoint.',
    credentials: ['MBBS, Masters in Population & Reproductive Health Research', 'Public Health Specialist, Health Care Management Expert', 'Development Activist'],
    photo: 'https://amzhospitalbd.com/storage/CEO_MAM.jpeg?w=800&h=900&fit=crop',
    message: `Our day-to-day operations are centered around a fundamental truth: every second counts, and every patient deserves seamless, high-quality care.

As CEO, my core objective is to drive continuous modernization of our hospital's physical and technological capabilities — leading an agile, highly coordinated operational team that translates our strategic vision into flawless bedside care. From round-the-clock emergency response to streamlined inpatient and outpatient services, we are dedicated to maximising clinical efficiency and eliminating friction for our patients and their families.

We foster a culture of active empathy, continuous capacity building, and operational precision. Every protocol we implement is designed with a patient-first approach — guaranteeing demand-based treatment at an affordable cost, offering safety and comfort.`,
  },
]

// const galleryItems = [
//   { category: 'infrastructure', img: 'https://amzhospitalbd.com/storage/AMZ.jpg?w=800&h=600&fit=crop', badge: 'Infrastructure', badgeColor: 'bg-blue-800', title: 'AMZ Hospital Building', desc: 'ISO-certified 100-bed multi-story facility in Uttar Badda.' },
//   { category: 'facilities', img: 'https://amzhospitalbd.com/storage/galleries/May2024/uAobdzOLYOrhAGXeCQe1.jpg?w=800&h=600&fit=crop', badge: 'Facilities', badgeColor: 'bg-red-500', title: '24/7 Emergency Care', desc: 'Round-the-clock emergency and trauma services with ambulance support.' },
//   { category: 'equipment', img: 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=800&h=600&fit=crop', badge: 'Equipment', badgeColor: 'bg-emerald-500', title: 'Advanced Cath-Lab', desc: 'Modern cardiac intervention unit for Angiogram and Angioplasty.' },
//   { category: 'facilities', img: 'https://images.unsplash.com/photo-1512678080530-7760d81faba6?w=800&h=600&fit=crop', badge: 'Facilities', badgeColor: 'bg-purple-600', title: 'Critical Care Units', desc: 'Well-equipped ICU, NICU, CCU, and HDU for specialized monitoring.' },
//   { category: 'departments', img: 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=800&h=600&fit=crop', badge: 'Departments', badgeColor: 'bg-cyan-600', title: 'Fertility & Research Center', desc: 'Premier Center of Excellence for IVF and infertility treatments.' },
//   { category: 'equipment', img: 'https://images.unsplash.com/photo-1581594693702-fbdc51b2763b?w=800&h=600&fit=crop', badge: 'Equipment', badgeColor: 'bg-orange-600', title: 'Diagnostic & Radio Imaging', desc: 'Category-A lab with MRI, CT Scan, and digital X-ray capabilities.' },
// ]
const galleryFilters = ['all', 'infrastructure', 'facilities', 'equipment', 'departments']

const servicesList = [
  { bg: 'from-blue-50', icon: 'fa-ambulance', title: '24/7 Emergency Services', desc: 'Round-the-clock emergency medical care with rapid response team', items: ['Critical Care Unit', 'Intensive Care Unit', 'High Dependancy Unit', 'Dialysis'] },
  { bg: 'from-green-50', icon: 'fa-x-ray', title: 'Diagnostic Services', desc: 'Advanced diagnostic facilities with latest technology', items: ['CT Scan & MRI', 'Ultrasound', 'Laboratory Tests', 'Cath Lab'] },
  { bg: 'from-purple-50', icon: 'fa-procedures', title: 'Surgical Services', desc: 'Expert surgical care with modern operation theaters', items: ['General Surgery', 'Laparoscopic Surgery', 'Specialized Surgery', 'Laser Surgery'] },
  { bg: 'from-red-50', icon: 'fa-utensils', title: 'Canteen Service', desc: 'Hygienic in-house canteen with fresh meals for patients, attendants, and visitors.', items: ['Patient Diet Meals', 'Attendant Meal Options', 'Tea & Refreshments'] },
  { bg: 'from-yellow-50', icon: 'fa-baby', title: 'Maternity Services', desc: 'Complete care for mothers and newborns', items: ['Prenatal Care', 'Delivery Services', 'NICU'] },
  { bg: 'from-pink-50', icon: 'fa-pills', title: 'Pharmacy Services', desc: '24/7 pharmacy with all essential medicines', items: [] },
]

// const doctors = [
//   { color: 'from-blue-400 to-blue-600', name: 'Dr. Ahmedul Kabir', specialty: 'Medicine', degree: 'MBBS, FCPS, FACP, FRCP', exp: '15+', photo: 'https://amzhospitalbd.com/storage/doctors/April2024/H2qkQbIjHRLFzvPwoNEt.jpg' },
//   { color: 'from-emerald-400 to-emerald-600', name: 'Dr. Mohammad Sayem', specialty: 'Medicine', degree: 'MBBS (Dhaka), FCPS (Medicine), MRCP (London) MRCPE (Edinburgh), MACP (USA)', exp: '10+', photo: 'https://amzhospitalbd.com/storage/doctors/April2024/rXuniLQlfVt3OmQ6Ntw8.jpg' },
//   { color: 'from-purple-400 to-purple-600', name: 'Professor Dr. Mohammad Nashir Uddin', specialty: 'Plastic, Aesthetic & Laser Surgery', degree: 'MS (Plastic Surgery), FCPS (Surgery), FRCS (UK)', exp: '15+', photo: 'https://amzhospitalbd.com/storage/doctors/April2024/vtlYUwFUzX8gFsQLcsqJ.png' },
//   { color: 'from-pink-400 to-pink-600', name: 'Professor Dr. Jesmine Banu', specialty: 'Gynecologist', degree: 'MBBS, MS (Obs. & GYN), Gynecology, Obstetrics Infertility Specialist & Laparoscopic Surgeon', exp: '20+', photo:'https://amzhospitalbd.com/storage/doctors/July2025/7Fq2bPofzv2z2c2Zz91s.jpg' },
// ]

// const centers = [
//   { id: 'coe-fertility-research-center', bg_color: 'bg-blue-100', text_color: 'text-blue-800', icon: 'fa-flask', name: 'Fertility & Research Center', desc: 'Personalized fertility assessment, treatment planning, and research-driven care pathways.' },
//   { id: 'coe-hypospadias-center', bg_color: 'bg-emerald-100', text_color: 'text-emerald-700', icon: 'fa-user-shield', name: 'Hypospadias center', desc: 'Child-focused surgical correction with coordinated follow-up and family support.' },
//   { id: 'coe-laser-proctology-surgery-center', bg_color: 'bg-rose-100', text_color: 'text-rose-700', icon: 'fa-bolt', name: 'Laser & Proctology Surgery Center', desc: 'Modern laser-based proctology procedures with reduced pain and faster recovery.' },
//   { id: 'coe-plastic-aesthetic-laser-surgery-center', bg_color: 'bg-violet-100', text_color: 'text-violet-700', icon: 'fa-magic', name: 'Plastic, Aesthetic & Laser Surgery Center', desc: 'Reconstructive, cosmetic, and laser procedures tailored to patient goals.' },
//   { id: 'coe-primary-care-center', bg_color: 'bg-cyan-100', text_color: 'text-cyan-700', icon: 'fa-stethoscope', name: 'Primary Care Center', desc: 'Preventive, chronic, and family medicine services in one coordinated center.' },
//   { id: 'coe-stroke-neuro-rehabilitation-center', bg_color: 'bg-orange-100', text_color: 'text-orange-700', icon: 'fa-brain', name: 'Stroke & Neuro Rehabilitation Center', desc: 'Neuro recovery with physiotherapy, speech therapy, and multidisciplinary monitoring.' },
//   { id: 'coe-cancer-care-center', bg_color: 'bg-pink-100', text_color: 'text-pink-700', icon: 'fa-ribbon', name: 'Cancer Care Center', desc: 'Integrated oncology support from diagnosis to treatment and survivorship care.' },
//   { id: 'coe-hepatobiliary-pancreatic-surgery-center', bg_color: 'bg-lime-100', text_color: 'text-lime-700', icon: 'fa-procedures', name: 'Hepatobiliary & Pancreatic Surgery Center', desc: 'Advanced surgical management for complex liver, biliary, and pancreatic diseases.' },
// ]

const packages = [
  { bg: 'bg-blue-100', color: 'text-blue-800', icon: 'fa-user-check', title: 'Basic Checkup', desc: 'General physician consultation, CBC, blood sugar, urine R/E, and ECG.', price: 'BDT 2,500' },
  { bg: 'bg-pink-100', color: 'text-pink-700', icon: 'fa-heartbeat', title: 'Women Wellness', desc: 'Gyne consultation, thyroid profile, vitamin D, breast exam, and pelvic USG.', price: 'BDT 4,800' },
  { bg: 'bg-emerald-100', color: 'text-emerald-700', icon: 'fa-user-shield', title: 'Senior Care', desc: 'Cardiac risk panel, chest X-ray, kidney profile, diabetic panel, and specialist review.', price: 'BDT 6,200' },
  { bg: 'bg-amber-100', color: 'text-amber-700', icon: 'fa-users', title: 'Family Package', desc: 'Combined health screening for 2 adults including consultation and core labs.', price: 'BDT 8,900' },
]

const testimonials = [
  { initials: 'AH', bg: 'from-blue-800 to-sky-500', cardBg: 'from-blue-50', name: 'Abdul Hamid', short: 'Excellent hospital with caring staff and very professional doctors.', full: ' The facilities are modern, and the care quality was consistently high from admission to discharge. Highly recommended for trusted healthcare.' },
  { initials: 'RB', bg: 'from-emerald-500 to-emerald-600', cardBg: 'from-green-50', name: 'Rahima Begum', short: 'I received excellent care during my stay and felt supported.', full: ' The nursing team was attentive, and doctors explained each step clearly so my family and I stayed confident throughout treatment.' },
  { initials: 'MK', bg: 'from-purple-500 to-purple-600', cardBg: 'from-purple-50', name: 'Mahmud Khan', short: 'Top-notch medical facility with advanced equipment and fast service.', full: ' The emergency department handled my case quickly, and follow-up care was just as good. I am grateful for their professionalism.' },
  { initials: 'SN', bg: 'from-cyan-500 to-cyan-600', cardBg: 'from-cyan-50', name: 'Shila Nasrin', short: 'Clean environment and excellent diagnostics at AMZ Hospital.', full: ' My reports were delivered quickly, and consultation was clear and reassuring. The whole process felt organized and patient-friendly.' },
  { initials: 'TU', bg: 'from-amber-500 to-amber-600', cardBg: 'from-amber-50', name: 'Tanvir Uddin', short: 'Appointment process was smooth, and waiting time was reasonable.', full: ' Staff guided me politely, and the doctor gave practical advice with clear medicine instructions. Overall, a dependable experience.' },
  { initials: 'FP', bg: 'from-rose-500 to-rose-600', cardBg: 'from-rose-50', name: 'Farzana Parvin', short: 'Excellent maternal care and supportive nursing staff throughout.', full: ' The doctors checked in regularly, answered questions patiently, and ensured comfort before and after procedures.' },
]

const stats = [
  { target: 300000, label: 'Patients Treated' },
  { target: 79, label: 'Expert Doctors' },
  { target: 7, label: 'Years Experience' },
  { target: 100, label: 'Hospital Beds' },
]

const partners = [
  { name: 'AB Bank', logo: 'https://amzhospitalbd.com/storage/corporate-partners/March2024/9Y2oTmRomsnbfdh3cG50.png' },
  { name: 'Ekushe TV', logo: 'https://amzhospitalbd.com/storage/corporate-partners/March2024/SLboYCDAU6ngOHkQ0TxA.png' },
  { name: 'ICON', logo: 'https://amzhospitalbd.com/storage/corporate-partners/March2024/stFeEleAssgKAwC97aeC.png' },
  { name: 'Brac', logo: 'https://amzhospitalbd.com/storage/corporate-partners/March2024/TtikDkxa6iYuaaMoRyBg.png' },
  { name: 'Huwaei', logo: 'https://amzhospitalbd.com/storage/corporate-partners/March2024/9XfI0DamcAjt2JgjgV3T.png' },
]

const whyChooseUsFeatures = [
  { color: 'from-blue-500 to-blue-600', icon: 'fa-award', title: 'Trusted Outcomes', desc: 'Consistent treatment quality with strong clinical governance and senior specialist oversight.' },
  { color: 'from-emerald-500 to-emerald-600', icon: 'fa-calendar-check', title: 'Fast Appointments', desc: 'Streamlined booking and coordinated scheduling to reduce waiting and delays.' },
  { color: 'from-red-500 to-red-600', icon: 'fa-shield-heart', title: 'Patient Safety First', desc: 'Strict infection-control protocols, safety checklists, and monitored care pathways.' },
  { color: 'from-purple-500 to-purple-600', icon: 'fa-comments', title: 'Clear Communication', desc: 'Doctors explain diagnosis and options in plain language before every key decision.' },
  { color: 'from-yellow-500 to-yellow-600', icon: 'fa-file-waveform', title: 'Digital Reports', desc: 'Faster test report access and smoother follow-up through digital sharing workflows.' },
  { color: 'from-pink-500 to-pink-600', icon: 'fa-people-roof', title: 'Family-Centered Support', desc: 'Care teams guide patients and families throughout treatment, discharge, and recovery.' },
  { color: 'from-indigo-500 to-indigo-600', icon: 'fa-file-invoice-dollar', title: 'Transparent Billing', desc: 'Clear package details and cost visibility to avoid confusion during care.' },
  { color: 'from-teal-500 to-teal-600', icon: 'fa-headset', title: '24/7 Help Desk', desc: 'Round-the-clock support for urgent guidance, admissions, and service coordination.' },
]

const appointmentAvailableWeekdays = [0, 1, 2, 3, 4, 6]
const appointmentBlockedDates: string[] = []

// ============ ACTIVATE SCROLL REVEAL ============
useScrollReveal()
</script>

<template>
  <main>
    <HeroSlider :slides="props.slides" />
    <QuickAccessCards :cards="quickCards" />
    <AboutSection />
    <WhyChooseUs :features="whyChooseUsFeatures" />
    <DepartmentsSection :departments="props.departments" />
    <CentersOfExcellence :centers="centers" />
    <ServicesSection :services="servicesList" />
    <DoctorsSection :doctors="doctors" />
    <LeadershipSection :messages="leadershipMessages" />
    <GallerySection :items="props.galleries" />
    <HealthPackages :packages="packages" />
    <AppointmentSection :available-weekdays="appointmentAvailableWeekdays" :blocked-dates="appointmentBlockedDates" />
    <TestimonialsSection :testimonials="testimonials" />
    <StatsCounter :stats="stats" />
    <BlogSection :posts="props.blogs" />
    <NewsletterPartners :partners="partners" />
    <ContactSection />
  </main>
</template>

<style>
/* ===== GLOBAL STYLES (unchanged from your original) ===== */
/* ... copy all the <style> content from your original Home.vue ... */
/* I'm including it below for completeness, but you can keep it in a separate global file */
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
  background: linear-gradient(120deg, rgba(255,255,255,0) 25%, rgba(255,255,255,0.32) 50%, rgba(255,255,255,0) 75%);
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
  background: linear-gradient(120deg, rgba(255,255,255,0) 22%, rgba(255,255,255,0.22) 48%, rgba(255,255,255,0.56) 52%, rgba(255,255,255,0.22) 56%, rgba(255,255,255,0) 82%);
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
.reveal-leadership { transform: translate3d(0, 34px, 0) scale(0.98); }
.reveal-leader-left { transform: translate3d(-42px, 24px, 0) scale(0.97); }
.reveal-leader-right { transform: translate3d(42px, 24px, 0) scale(0.97); }

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
.hero-image-active { filter: brightness(0.98) saturate(1.08); }
.hero-image-inactive { filter: brightness(0.75) saturate(0.92); }
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
@keyframes heroZoomInOut {
  0% { transform: scale(1.04) translate3d(0, 0, 0); }
  50% { transform: scale(1.12) translate3d(-1.2%, -0.8%, 0); }
  100% { transform: scale(1.06) translate3d(1%, 0.6%, 0); }
}

.partner-marquee-wrap { width: 100%; }
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
.partner-logo-item { height: 74px; }
@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

@keyframes pageFadeIn {
  0% { opacity: 0; transform: translateY(8px); }
  100% { opacity: 1; transform: translateY(0); }
}
body { animation: pageFadeIn 0.7s ease-out; }

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
  .premium-sheen::before { opacity: 0 !important; animation: none !important; transform: none !important; }
  .premium-shine-section::before,
  .premium-shine-section::after { opacity: 0 !important; animation: none !important; transform: none !important; }
  .hero-slide, .hero-slide-image, .hero-slide-content { transition: none !important; animation: none !important; }
  html { scroll-behavior: auto; }
}

::-webkit-scrollbar { width: 10px; }
::-webkit-scrollbar-track { background: #f1f5f9; }
::-webkit-scrollbar-thumb { background: #1e40af; border-radius: 5px; }
::-webkit-scrollbar-thumb:hover { background: #0ea5e9; }

.font-display { font-family: 'Poppins', sans-serif; }
.leader-card-shadow { box-shadow: 0 20px 50px -20px rgba(27,54,84,0.25); }
.leader-photo::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(21,44,70,.92) 0%, rgba(21,44,70,.35) 42%, rgba(21,44,70,0) 62%);
  pointer-events: none;
}
</style>