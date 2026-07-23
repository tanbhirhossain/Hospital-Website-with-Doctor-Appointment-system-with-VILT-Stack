<script setup>
import { ref, nextTick, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

// ── Hub state ──────────────────────────────────────────────────────
const hubOpen = ref(false)
const botPanelOpen = ref(false)

const toggleHub = () => { hubOpen.value = !hubOpen.value }
const openBot = () => { botPanelOpen.value = true; hubOpen.value = false }
const closeBot = () => { botPanelOpen.value = false }

// ── Chat state ─────────────────────────────────────────────────────
const messages = ref([])
const history = ref([])
const userInput = ref('')
const isLoading = ref(false)
const messagesEl = ref(null)
const inputEl = ref(null)

let msgIdCounter = 0
const nextId = () => ++msgIdCounter
const now = () => new Date().toLocaleTimeString('bn-BD', { hour: '2-digit', minute: '2-digit' })

const suggestionChips = [
    { icon: '👨‍⚕️', label: 'ডাক্তারের তালিকা', text: 'ডাক্তারের তালিকা দেখুন' },
    { icon: '📅', label: 'অ্যাপয়েন্টমেন্ট', text: 'অ্যাপয়েন্টমেন্ট নিতে চাই' },
    { icon: '🧪', label: 'টেস্টের মূল্য', text: 'টেস্টের মূল্য তালিকা দেখুন' },
    { icon: '🛏️', label: 'বেড স্ট্যাটাস', text: 'বর্তমানে কোনো বেড খালি আছে?' },
    { icon: '💊', label: 'ওষুধ খুঁজুন', text: 'ওষুধ খুঁজুন' },
]

// ── Send message to HEALTHAI API ───────────────────────────────────
async function sendMessage() {
    const text = userInput.value.trim()
    if (!text || isLoading.value) return

    pushMessage('user', text)
    userInput.value = ''
    await nextTick()
    resetInputHeight()
    scrollToBottom()

    isLoading.value = true

    try {
        const res = await axios.post('/api/chat', {
            message: text,
            history: history.value,
        })

        const reply = res.data.reply ?? 'দুঃখিত, কোনো উত্তর পাওয়া যায়নি।'
        const rawHistory = res.data.history ?? []

        history.value = rawHistory

        // Check for embedded doctor card data
        const doctorMatch = reply.match(/<!--DOCTORS:([\s\S]*?)-->/)
        let doctors = null
        let cleanReply = reply

        if (doctorMatch) {
            try {
                doctors = JSON.parse(doctorMatch[1])
                cleanReply = reply.replace(/<!--DOCTORS:[\s\S]*?-->/, '').trim()
            } catch {}
        }

        pushMessage('assistant', cleanReply, doctors)
    } catch (err) {
        console.error('[ChatHub] API error:', err)
        pushMessage('assistant', 'দুঃখিত, সার্ভারে সমস্যা হয়েছে। একটু পরে আবার চেষ্টা করুন।')
    } finally {
        isLoading.value = false
        await nextTick()
        scrollToBottom()
    }
}

function sendSuggestion(text) {
    userInput.value = text
    sendMessage()
}

function bookDoctor(doc) {
    userInput.value = `${doc.Name} এর অ্যাপয়েন্টমেন্ট নিতে চাই`
    sendMessage()
}

function clearChat() {
    messages.value = []
    history.value = []
    userInput.value = ''
}

// ── Message helpers ────────────────────────────────────────────────
function pushMessage(role, content, doctors = null) {
    messages.value.push({ id: nextId(), role, content, doctors, time: now() })
}

// ── Markdown renderer (lightweight, safe) ──────────────────────────
function renderMarkdown(text) {
    if (!text) return ''
    return text
        .replace(/%%ACTION:BOOK_APPOINTMENT%%[\s\S]*?%%END_ACTION%%/g, '')
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        .replace(/^[\-\*•] (.+)$/gm, '<li>$1</li>')
        .replace(/((?:<li>.*<\/li>\n?)+)/g, '<ul>$1</ul>')
        .replace(/^\d+\. (.+)$/gm, '<li>$1</li>')
        .replace(/\n/g, '<br>')
        .replace(/<script[\s\S]*?<\/script>/gi, '')
}

// ── UI helpers ─────────────────────────────────────────────────────
function scrollToBottom() {
    const el = messagesEl.value
    if (el) el.scrollTop = el.scrollHeight
}

function autoResize(e) {
    const el = e.target
    el.style.height = 'auto'
    el.style.height = Math.min(el.scrollHeight, 80) + 'px'
}

function resetInputHeight() {
    if (inputEl.value) inputEl.value.style.height = 'auto'
}

function onImgError(e) {
    e.target.style.display = 'none'
    const placeholder = e.target.parentElement?.querySelector('.dc-img-placeholder')
    if (placeholder) placeholder.style.display = 'flex'
}

// Focus input when bot panel opens
onMounted(() => {
    if (inputEl.value) inputEl.value.focus()
})
</script>

<template>
    <!-- ════════════════════════════════════════════════════════════ -->
    <!-- HEALTH AI BOT PANEL                                         -->
    <!-- ════════════════════════════════════════════════════════════ -->
    <div v-show="botPanelOpen"
        class="fixed right-4 bottom-[10.4rem] z-[60] overflow-hidden rounded-2xl border border-slate-200 shadow-2xl bg-white flex flex-col"
        style="width: min(380px, calc(100vw - 2rem)); height: min(540px, calc(100vh - 10rem));"
        role="dialog" aria-label="AMZ Health Assistant">

        <!-- ── Header ──────────────────────────────────────── -->
        <div class="flex items-center gap-2.5 px-4 py-3 bg-gradient-to-r from-blue-800 to-sky-500 text-white flex-shrink-0">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-white">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-sm leading-tight">AMZ Health Assistant</h3>
                <span class="flex items-center gap-1 text-[10px] opacity-85">
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                    Online
                </span>
            </div>
            <button @click="clearChat" title="নতুন চ্যাট" class="p-1.5 rounded-lg hover:bg-white/15 transition-colors">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14H6L5 6"/>
                    <path d="M10 11v6"/><path d="M14 11v6"/>
                    <path d="M9 6V4h6v2"/>
                </svg>
            </button>
            <button @click="closeBot" title="বন্ধ করুন" class="p-1.5 rounded-lg hover:bg-white/15 transition-colors">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- ── Messages ────────────────────────────────────── -->
        <div ref="messagesEl" class="flex-1 overflow-y-auto px-3 py-3 space-y-2.5 bg-slate-50 ch-scroll">

            <!-- Welcome screen -->
            <div v-if="messages.length === 0" class="flex flex-col items-center justify-center h-full text-center px-4">
                <div class="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center mb-3">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-7 h-7 text-blue-700">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-800 mb-1">আমি কীভাবে সাহায্য করতে পারি?</p>
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    ডাক্তার, টেস্ট, ওষুধ, অ্যাপয়েন্টমেন্ট —<br>যেকোনো স্বাস্থ্য প্রশ্ন করুন।
                </p>
                <div class="flex flex-wrap gap-1.5 justify-center">
                    <button
                        v-for="chip in suggestionChips"
                        :key="chip.label"
                        @click="sendSuggestion(chip.text)"
                        class="flex items-center gap-1 px-2.5 py-1.5 bg-white border border-slate-200 rounded-full text-[11px] text-slate-600 hover:border-blue-400 hover:text-blue-700 hover:bg-blue-50 transition-all shadow-sm cursor-pointer"
                    >
                        <span>{{ chip.icon }}</span>
                        {{ chip.label }}
                    </button>
                </div>
            </div>

            <!-- Message list -->
            <template v-for="msg in messages" :key="msg.id">
                <div class="flex items-end gap-1.5" :class="msg.role === 'user' ? 'flex-row-reverse' : 'flex-row'">

                    <!-- Bot avatar -->
                    <div v-if="msg.role === 'assistant'" class="w-6 h-6 rounded-full bg-blue-700 flex items-center justify-center flex-shrink-0">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3 h-3 text-white">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                    </div>

                    <div class="flex flex-col max-w-[82%]" :class="msg.role === 'user' ? 'items-end' : 'items-start'">
                        <!-- Normal bubble -->
                        <div
                            class="px-3 py-2 text-[13px] leading-relaxed break-words"
                            :class="msg.role === 'user'
                                ? 'bg-gradient-to-br from-blue-700 to-blue-800 text-white rounded-2xl rounded-br-sm'
                                : 'bg-white text-slate-800 rounded-2xl rounded-bl-sm shadow-sm border border-slate-100'"
                            v-html="renderMarkdown(msg.content)"
                        ></div>

                        <!-- Doctor cards (if present) -->
                        <div v-if="msg.doctors && msg.doctors.length" class="w-full mt-1.5 space-y-1.5">
                            <div
                                v-for="doc in msg.doctors"
                                :key="doc.doctor_id"
                                class="flex items-center gap-2.5 bg-white border border-slate-200 rounded-xl p-2.5 shadow-sm hover:border-blue-300 hover:shadow transition-all"
                            >
                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0 overflow-hidden border border-blue-100">
                                    <img v-if="doc.image_url" :src="doc.image_url" :alt="doc.Name" class="w-full h-full object-cover" @error="onImgError" />
                                    <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5 text-blue-400 dc-img-placeholder">
                                        <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 truncate">{{ doc.Name }}</p>
                                    <p class="text-[10px] text-slate-500 truncate">{{ doc.Designation }}</p>
                                    <p class="text-[10px] text-blue-600 font-medium truncate">{{ doc.Department }}</p>
                                </div>
                                <button
                                    @click="bookDoctor(doc)"
                                    class="flex-shrink-0 bg-blue-700 text-white text-[10px] font-medium px-2 py-1 rounded-lg hover:bg-blue-800 transition-colors"
                                >
                                    বুক
                                </button>
                            </div>
                        </div>

                        <!-- Timestamp -->
                        <span class="text-[9px] text-slate-400 mt-0.5 px-1">{{ msg.time }}</span>
                    </div>
                </div>
            </template>

            <!-- Typing indicator -->
            <div v-if="isLoading" class="flex items-end gap-1.5">
                <div class="w-6 h-6 rounded-full bg-blue-700 flex items-center justify-center flex-shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3 h-3 text-white">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                </div>
                <div class="bg-white rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm border border-slate-100 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                </div>
            </div>
        </div>

        <!-- ── Input bar ───────────────────────────────────── -->
        <div class="flex items-end gap-2 px-3 py-2.5 bg-white border-t border-slate-200 flex-shrink-0">
            <textarea
                ref="inputEl"
                v-model="userInput"
                placeholder="বার্তা লিখুন..."
                rows="1"
                :disabled="isLoading"
                @keydown.enter.exact.prevent="sendMessage"
                @input="autoResize"
                class="flex-1 resize-none border border-slate-200 rounded-xl px-3 py-2 text-[13px] text-slate-800 bg-slate-50 focus:border-blue-400 focus:bg-white focus:outline-none transition-colors overflow-y-hidden"
                style="max-height: 80px;"
            ></textarea>
            <button
                @click="sendMessage"
                :disabled="isLoading || !userInput.trim()"
                class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-700 to-blue-800 text-white flex items-center justify-center flex-shrink-0 hover:scale-105 transition-transform disabled:opacity-40 disabled:scale-100 disabled:cursor-not-allowed shadow-sm"
            >
                <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════ -->
    <!-- CHAT HUB (unchanged structure)                               -->
    <!-- ════════════════════════════════════════════════════════════ -->
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

/* Scrollbar for chat messages */
.ch-scroll::-webkit-scrollbar { width: 4px; }
.ch-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.ch-scroll::-webkit-scrollbar-track { background: transparent; }
</style>
