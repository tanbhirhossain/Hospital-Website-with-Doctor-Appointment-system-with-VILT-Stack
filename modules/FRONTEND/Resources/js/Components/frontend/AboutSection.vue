<template>
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
              allowfullscreen
            ></iframe>
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
            AMZ Hospital Ltd. measured its success by counting of lives been saved, hopes been restored, and care been delivered with quality service & professionalism.
          </p>
          <p class="text-gray-600 mb-8 leading-relaxed">
            It was founded by a team of visionary clinicians, leaders from civil society and different discipline with a shared vision of reducing health care inequalities.
          </p>
          <div class="grid sm:grid-cols-2 gap-4 mb-8">
            <div v-for="feat in ['24/7 Emergency Care', 'Expert Specialists', 'Modern Equipment', 'Patient-Centered Care']" :key="feat" class="flex items-start space-x-3">
              <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check text-blue-800 text-xl"></i>
              </div>
              <div>
                <h4 class="font-semibold text-gray-900">{{ feat }}</h4>
                <p class="text-sm text-gray-600">Quality care you can trust</p>
              </div>
            </div>
          </div>
          <a href="/about" class="inline-flex items-center bg-gradient-to-r from-blue-800 to-sky-500 text-white px-8 py-4 rounded-lg font-semibold hover:shadow-xl transition-all transform hover:scale-105">
            <i class="fas fa-book-open mr-2"></i> Read More
          </a>
        </div>
      </div>
    </div>
  </section>
</template>