<script setup lang="ts">
defineProps<{
  messages: Array<{
    name: string
    role: string
    roleLine: string
    eyebrow: string
    title: string
    quote: string
    credentials: string[]
    photo: string
    message: string
  }>
}>()
</script>

<template>
  <section id="leadership" role="region" aria-label="Leadership messages" class="py-20 scroll-reveal reveal-leadership fade-in-0 slide-in-from-bottom-8 duration-700" style="background:#f7f3ea;">
     <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-200 to-transparent"></div>

    <div class="container mx-auto px-4 max-w-7xl">
      <div class="text-center mb-14">
        <h2 class="font-display font-extrabold text-4xl md:text-5xl tracking-tight" style="color:#152c46;">Leadership Message</h2>
        <p class="mt-4 text-sm md:text-base tracking-wide" style="color:#64748b;">Guiding our organization with vision and dedication</p>
      </div>
      <div class="space-y-10 md:space-y-14">
        <article v-for="(leader, idx) in messages" :key="leader.name" class="scroll-reveal" :class="idx % 2 === 0 ? 'reveal-leader-left' : 'reveal-leader-right'">
          <div class="premium-sheen bg-white rounded-3xl overflow-hidden md:flex md:items-stretch leader-card-shadow">
            <!-- Photo -->
            <div class="leader-photo relative w-full h-72 md:h-auto md:w-[350px] md:flex-shrink-0 overflow-hidden" :class="idx % 2 === 1 ? 'md:order-2' : 'md:order-1'">
              <img :src="leader.photo" :alt="leader.name" class="absolute inset-0 w-full h-full object-cover object-top" loading="lazy" />
              <div class="absolute bottom-0 left-0 right-0 p-6 z-10">
                <h3 class="font-display font-bold text-white text-2xl leading-tight">{{ leader.name }}</h3>
                <p class="text-[13px] font-semibold tracking-widest uppercase mt-1" style="color:#c07b4b;">{{ leader.roleLine }}</p>
              </div>
            </div>
            <!-- Content -->
            <div class="flex-1 min-w-0 p-7 sm:p-9 md:p-11" :class="idx % 2 === 1 ? 'md:order-1' : 'md:order-2'">
              <p class="flex items-center gap-2.5 text-[11px] font-bold tracking-[0.18em] uppercase" style="color:#c07b4b;">
                <span class="inline-block w-6 h-[2px]" style="background:#c07b4b;"></span>
                {{ leader.eyebrow }}
              </p>
              <h3 class="font-display font-bold text-[22px] sm:text-[26px] leading-[1.2] mt-3" style="color:#152c46;">
                {{ leader.title }}
              </h3>
              <div class="h-px my-5" style="background:linear-gradient(to right, rgba(27,54,84,.16), rgba(27,54,84,.02));"></div>
              <p class="italic text-[14px] sm:text-[15px] leading-[1.6]" style="color:#c07b4b;">"{{ leader.quote }}"</p>
              <div class="h-px my-5" style="background:linear-gradient(to right, rgba(27,54,84,.16), rgba(27,54,84,.02));"></div>
              <div class="space-y-3.5 text-[13.5px] sm:text-[14px] leading-[1.75]" style="color:#4b5768;">
                <p v-for="(para, pIdx) in leader.message.split('\n\n')" :key="pIdx" class="whitespace-pre-line">
                  {{ para.trim() }}
                </p>
              </div>
              <div class="h-px my-5" style="background:linear-gradient(to right, rgba(27,54,84,.16), rgba(27,54,84,.02));"></div>
              <div class="flex items-start gap-3">
                <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-base" style="background:#1b3654; box-shadow:0 6px 16px -4px rgba(27,54,84,.45);">
                  +
                </div>
                <div>
                  <p class="font-display font-bold text-[15px]" style="color:#152c46;">{{ leader.name }}</p>
                  <p class="text-[11.5px] mt-1 leading-[1.6]" style="color:#64748b;">
                    <span v-for="(cred, cIdx) in leader.credentials" :key="cIdx">
                      {{ cred }}<br v-if="cIdx < leader.credentials.length - 1" />
                    </span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>