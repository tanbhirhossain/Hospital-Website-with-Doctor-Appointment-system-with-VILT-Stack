<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const visible = ref(false)

const handleScroll = () => {
    visible.value = window.pageYOffset > 300

    const navbar = document.getElementById('main-nav')
    if (navbar) {
        navbar.classList.toggle('shadow-xl', window.pageYOffset > 50)
    }
}

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => window.addEventListener('scroll', handleScroll, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<template>
    <button
        v-show="visible"
        @click="scrollToTop"
        aria-label="Back to top"
        class="fixed bottom-8 left-8 bg-gradient-to-r from-blue-800 to-sky-500 text-white w-12 h-12 rounded-full shadow-2xl hover:shadow-3xl transition-all transform hover:scale-110 z-50 flex items-center justify-center"
    >
        <i class="fas fa-arrow-up"></i>
    </button>
</template>
