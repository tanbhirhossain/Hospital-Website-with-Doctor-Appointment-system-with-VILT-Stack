<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const progress = ref(0)

const updateProgress = () => {
    const documentHeight = document.documentElement.scrollHeight - window.innerHeight
    const scrolled = documentHeight > 0 ? (window.scrollY / documentHeight) * 100 : 0
    progress.value = Math.max(0, Math.min(100, scrolled))
}

onMounted(() => window.addEventListener('scroll', updateProgress, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', updateProgress))
</script>

<template>
    <div
        id="scroll-progress"
        aria-hidden="true"
        :style="{ width: progress + '%' }"
        class="fixed top-0 left-0 h-[3px] z-[100] transition-none"
        style="background: linear-gradient(90deg, #0ea5e9, #1e40af, #10b981); box-shadow: 0 0 18px rgba(14,165,233,0.55);"
    />
</template>
