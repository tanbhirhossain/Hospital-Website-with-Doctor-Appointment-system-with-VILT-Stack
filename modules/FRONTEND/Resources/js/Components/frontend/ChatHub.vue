<script setup>
import { ref } from 'vue'

const hubOpen = ref(false)
const botPanelOpen = ref(false)
const botInput = ref('')
const messages = ref([
    { sender: 'bot', text: 'Hello, how can I help you today?' }
])

const toggleHub = () => { hubOpen.value = !hubOpen.value }

const openBot = () => {
    botPanelOpen.value = true
    hubOpen.value = false
}

const closeBot = () => { botPanelOpen.value = false }

const getBotReply = (msg) => {
    const m = msg.toLowerCase()
    if (m.includes('appointment') || m.includes('book'))
        return 'For appointment booking, please use the Book Appointment section or call 10699.'
    if (m.includes('emergency') || m.includes('urgent'))
        return 'For emergencies, please call our 24/7 hotline immediately: 10699.'
    if (m.includes('doctor') || m.includes('specialist'))
        return 'Please share your symptoms, and I can suggest which department to visit.'
    return 'Thanks for your message. A medical support member will assist you shortly.'
}

const sendMessage = () => {
    const text = botInput.value.trim()
    if (!text) return
    messages.value.push({ sender: 'user', text })
    botInput.value = ''
    setTimeout(() => {
        messages.value.push({ sender: 'bot', text: getBotReply(text) })
    }, 500)
}
</script>

<template>
    <!-- Health Bot Panel -->
    <div v-show="botPanelOpen"
        class="fixed right-6 bottom-[10.4rem] z-60 overflow-hidden rounded-2xl border border-slate-200 shadow-2xl bg-white"
        style="width: min(360px, calc(100vw - 2rem));"
        role="dialog" aria-label="Health Assistant Bot">
        <div class="bg-gradient-to-r from-blue-800 to-sky-500 text-white px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-robot"></i>
                <h3 class="font-semibold">Health Assistant Bot</h3>
            </div>
            <button @click="closeBot" aria-label="Close Health Assistant Bot" class="text-white/90 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4 bg-white h-72 overflow-y-auto space-y-3" id="health-bot-messages">
            <div v-for="(msg, i) in messages" :key="i"
                :class="[
                    'max-w-[85%] px-3 py-2 rounded-2xl text-sm leading-5',
                    msg.sender === 'user'
                        ? 'ml-auto bg-blue-100 text-blue-900'
                        : 'mr-auto bg-slate-100 text-slate-700'
                ]">
                {{ msg.text }}
            </div>
        </div>
        <form @submit.prevent="sendMessage" class="p-3 border-t border-slate-200 bg-slate-50 flex gap-2">
            <input v-model="botInput" type="text" placeholder="Type your health question..."
                class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-800 focus:border-transparent outline-none" />
            <button type="submit"
                class="bg-gradient-to-r from-blue-800 to-sky-500 text-white px-3 py-2 rounded-lg text-sm font-semibold">
                Send
            </button>
        </form>
    </div>

    <!-- Chat Hub -->
    <div class="fixed right-6 bottom-24 z-50">
        <!-- Actions (visible on hub open) -->
        <transition name="fade-up">
            <div v-show="hubOpen" class="absolute right-0 bottom-16 flex flex-col gap-3 items-end">
                <!-- WhatsApp -->
                <a href="https://wa.me/8801234567890" target="_blank" rel="noopener noreferrer"
                    aria-label="Chat on WhatsApp"
                    class="relative w-13 h-13 rounded-full flex items-center justify-center text-white shadow-xl transition-transform hover:-translate-y-1 hover:scale-110"
                    style="background-color: #25D366; width:3.25rem;height:3.25rem;">
                    <span
                        class="absolute right-14 top-1/2 -translate-y-1/2 whitespace-nowrap text-xs font-semibold bg-slate-900 text-white rounded-full px-2 py-1 opacity-0 group-hover:opacity-100 transition-all pointer-events-none">WhatsApp
                        Chat</span>
                    <i class="fab fa-whatsapp text-2xl"></i>
                </a>
                <!-- Health Bot -->
                <button @click="openBot" type="button" aria-label="Open Health Assistant Bot"
                    class="relative rounded-full flex items-center justify-center text-white shadow-xl transition-transform hover:-translate-y-1 hover:scale-110 bg-gradient-to-r from-blue-800 to-sky-500 border-0"
                    style="width:3.25rem;height:3.25rem;">
                    <i class="fas fa-robot text-xl"></i>
                </button>
            </div>
        </transition>

        <!-- Main Toggle Button -->
        <button @click="toggleHub" type="button" aria-label="Open chat options"
            class="rounded-full flex items-center justify-center text-white shadow-xl hover:-translate-y-1 hover:scale-110 transition-transform bg-gradient-to-r from-blue-800 to-sky-500 border-0"
            style="width:3.25rem;height:3.25rem;">
            <i class="fas fa-comments text-xl"></i>
        </button>
    </div>
</template>

<style scoped>
.fade-up-enter-active,
.fade-up-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-up-enter-from,
.fade-up-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
</style>
