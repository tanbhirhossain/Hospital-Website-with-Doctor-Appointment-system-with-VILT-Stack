<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import FrontendLayout from '../Layout/FrontendLayout.vue'

const props = defineProps<{
  contact: {
    email: string
    phone: string
    hotline: string
    address: string
  }
}>()

const page = usePage()
const flashSuccess = computed(() => (page.props as any).flash?.success)
const flashWarning = computed(() => (page.props as any).flash?.warning)

const form = useForm({
  name: '',
  email: '',
  phone: '',
  department: '',
  subject: '',
  message: '',
  privacy_consent: false,
})

const submit = () => {
  form.post('/contact', {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  })
}

const departments = [
  'General Inquiry',
  'Appointment Desk',
  'Emergency',
  'Diagnostics & Lab',
  'Corporate Service',
  'Billing & Accounts',
]
</script>

<template>
  <FrontendLayout>
    <Head title="Contact AMZ Hospital" />

    <!-- Premium Cinematic Hero Banner -->
    <section class="relative overflow-hidden bg-slate-950 py-28 text-white sm:py-36">
      <!-- Option A: Cinematic Moving Image with Ken Burns Zoom & Pan Effect -->
      <div 
        class="absolute inset-0 bg-cover bg-center animate-cinematic-zoom scale-105 filter brightness-90 contrast-105"
        style="background-image: url('https://amzhospitalbd.com/storage/AMZ.jpg');"
      ></div>

      <!-- Option B (Alternative): If you have a real hospital video file, uncomment below & remove the <div> above: -->
      <!-- 
      <video autoplay muted loop playsinline class="absolute inset-0 h-full w-full object-cover filter brightness-90">
        <source src="/path-to-your-hospital-video.mp4" type="video/mp4" />
      </video> 
      -->

      <!-- Premium Multi-Layer Gradient Overlays for Depth -->
      <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-blue-950/70 to-slate-900/40"></div>
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,rgba(2,6,23,0.6)_100%)]"></div>

      <!-- Glowing Ambient Accent Elements -->
      <div class="absolute -left-20 top-1/2 h-72 w-72 -translate-y-1/2 rounded-full bg-cyan-500/20 blur-[100px]"></div>
      <div class="absolute -right-20 top-1/2 h-72 w-72 -translate-y-1/2 rounded-full bg-emerald-500/20 blur-[100px]"></div>

      <div class="relative mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
        <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-cyan-400/30 bg-cyan-950/40 px-4 py-1.5 text-xs font-black uppercase tracking-[0.25em] text-cyan-200 backdrop-blur-md shadow-lg shadow-cyan-950/50">
          <span class="size-2 rounded-full bg-cyan-400 animate-pulse"></span>
          AMZ Hospital Support
        </div>
        <h1 class="text-4xl font-black tracking-tight sm:text-6xl lg:text-7xl">
          Get in <span class="bg-gradient-to-r from-cyan-300 via-sky-200 to-emerald-300 bg-clip-text text-transparent">Touch</span> With Us
        </h1>
        <p class="mx-auto mt-5 max-w-2xl text-lg text-slate-300 sm:text-xl font-medium leading-relaxed">
          World-class healthcare backed by compassionate professionals. Reach out for appointments, inquiries, or 24/7 emergency care.
        </p>
      </div>
    </section>

    <main class="overflow-hidden bg-gradient-to-b from-blue-50/70 via-white to-slate-50">
      <section class="relative px-4 py-20 sm:px-6 lg:px-8">
        <div class="pointer-events-none absolute left-1/2 top-0 h-[420px] w-[1100px] -translate-x-1/2 rounded-full bg-gradient-to-r from-blue-200/50 via-sky-100/60 to-emerald-100/50 blur-[120px]"></div>
        <div class="relative mx-auto max-w-7xl">
          <div class="mx-auto max-w-4xl text-center">
            <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-200 bg-white/80 px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-blue-800 shadow-sm backdrop-blur">
              <span class="size-2 rounded-full bg-emerald-500"></span>
              Contact AMZ Hospital
            </div>
            <h2 class="text-3xl font-black tracking-tight text-slate-950 sm:text-5xl">We are here for your healthcare needs</h2>
            <p class="mx-auto mt-5 max-w-3xl text-lg leading-8 text-slate-600">
              Send your question, appointment request, or service inquiry. Your message will be saved securely and our team will receive an email immediately. You will also receive a confirmation copy.
            </p>
          </div>

          <div class="mt-14 grid gap-8 lg:grid-cols-[0.92fr_1.08fr]">
            <aside class="space-y-5">
              <div class="rounded-[2rem] bg-gradient-to-br from-blue-900 via-sky-700 to-emerald-600 p-8 text-white shadow-2xl shadow-blue-900/25 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-200">24/7 support</p>
                <h3 class="mt-3 text-3xl font-black">Reach the right desk faster</h3>
                <p class="mt-3 text-sm leading-7 text-blue-50">For emergencies please call the hotline directly. For all other inquiries, submit the form and we will respond as soon as possible.</p>
                <div class="mt-8 grid gap-3">
                  <a :href="`tel:${contact.hotline}`" class="rounded-2xl bg-white/15 p-4 backdrop-blur transition hover:bg-white/25">
                    <span class="block text-xs font-bold uppercase tracking-wider text-cyan-100">Emergency Hotline</span>
                    <span class="mt-1 block text-2xl font-black">{{ contact.hotline }}</span>
                  </a>
                  <a :href="`tel:${contact.phone}`" class="rounded-2xl bg-white/15 p-4 backdrop-blur transition hover:bg-white/25">
                    <span class="block text-xs font-bold uppercase tracking-wider text-cyan-100">Phone</span>
                    <span class="mt-1 block text-xl font-black">{{ contact.phone }}</span>
                  </a>
                  <a :href="`mailto:${contact.email}`" class="rounded-2xl bg-white/15 p-4 backdrop-blur transition hover:bg-white/25">
                    <span class="block text-xs font-bold uppercase tracking-wider text-cyan-100">Email</span>
                    <span class="mt-1 block break-all text-lg font-black">{{ contact.email }}</span>
                  </a>
                </div>
              </div>

              <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/50">
                <h3 class="text-xl font-black text-slate-950">Visit AMZ</h3>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ contact.address }}</p>
                <div class="mt-5 overflow-hidden rounded-3xl border border-slate-200 shadow-lg">
                  <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d912.7411925766279!2d90.42537456963056!3d23.78426882587095!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c7af2fcf1c87%3A0x3c70327ba1b06ef7!2sAMZ%20Hospital%20Ltd.!5e0!3m2!1sen!2sbd!4v1773125024838!5m2!1sen!2sbd"
                    width="100%" height="260" style="border:0;" :allowfullscreen="false" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                  </iframe>
                </div>
              </div>
            </aside>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-2xl shadow-slate-200/60 sm:p-8">
              <div class="mb-6">
                <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-700">Send a message</p>
                <h3 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Contact form</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">Fields marked with * are required.</p>
              </div>

              <div v-if="flashSuccess" class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                {{ flashSuccess }}
              </div>
              <div v-if="flashWarning" class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">
                {{ flashWarning }}
              </div>

              <form class="space-y-5" @submit.prevent="submit">
                <div class="grid gap-5 md:grid-cols-2">
                  <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Full name *</label>
                    <input v-model="form.name" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100" placeholder="Your name" />
                    <p v-if="form.errors.name" class="mt-1 text-xs font-semibold text-rose-600">{{ form.errors.name }}</p>
                  </div>
                  <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Email address *</label>
                    <input v-model="form.email" type="email" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100" placeholder="name@email.com" />
                    <p v-if="form.errors.email" class="mt-1 text-xs font-semibold text-rose-600">{{ form.errors.email }}</p>
                  </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                  <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Phone</label>
                    <input v-model="form.phone" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100" placeholder="+880..." />
                    <p v-if="form.errors.phone" class="mt-1 text-xs font-semibold text-rose-600">{{ form.errors.phone }}</p>
                  </div>
                  <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Department</label>
                    <select v-model="form.department" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100">
                      <option value="">Select department</option>
                      <option v-for="department in departments" :key="department" :value="department">{{ department }}</option>
                    </select>
                    <p v-if="form.errors.department" class="mt-1 text-xs font-semibold text-rose-600">{{ form.errors.department }}</p>
                  </div>
                </div>

                <div>
                  <label class="mb-2 block text-sm font-bold text-slate-700">Subject *</label>
                  <input v-model="form.subject" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100" placeholder="How can we help?" />
                  <p v-if="form.errors.subject" class="mt-1 text-xs font-semibold text-rose-600">{{ form.errors.subject }}</p>
                </div>

                <div>
                  <label class="mb-2 block text-sm font-bold text-slate-700">Message *</label>
                  <textarea v-model="form.message" rows="7" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-800 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100" placeholder="Write your message..."></textarea>
                  <p v-if="form.errors.message" class="mt-1 text-xs font-semibold text-rose-600">{{ form.errors.message }}</p>
                </div>

                <label class="flex items-start gap-3 rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">
                  <input v-model="form.privacy_consent" type="checkbox" class="mt-1 rounded border-slate-300 text-blue-700 focus:ring-blue-500" />
                  <span>I agree that AMZ Hospital may contact me using the submitted information. *</span>
                </label>
                <p v-if="form.errors.privacy_consent" class="-mt-3 text-xs font-semibold text-rose-600">{{ form.errors.privacy_consent }}</p>

                <button type="submit" :disabled="form.processing" class="inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-blue-900 via-sky-700 to-emerald-600 px-6 py-4 font-black text-white shadow-xl shadow-blue-500/20 transition hover:-translate-y-0.5 hover:shadow-2xl disabled:opacity-60">
                  {{ form.processing ? 'Sending message...' : 'Send Message' }}
                </button>
              </form>
            </section>
          </div>
        </div>
      </section>
    </main>
  </FrontendLayout>
</template>

<style>
@keyframes cinematicZoom {
  0% {
    transform: scale(1.05) translate(0px, 0px);
  }
  50% {
    transform: scale(1.15) translate(-20px, -15px);
  }
  100% {
    transform: scale(1.05) translate(0px, 0px);
  }
}

.animate-cinematic-zoom {
  animation: cinematicZoom 10s ease-in-out infinite;
}
</style>