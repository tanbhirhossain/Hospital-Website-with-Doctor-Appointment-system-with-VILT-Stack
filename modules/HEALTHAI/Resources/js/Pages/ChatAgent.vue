<template>
  <div class="chat-wrapper">

    <!-- ── Header ───────────────────────────────────────────────── -->
    <div class="chat-header">
      <div class="header-avatar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
        </svg>
      </div>
      <div class="header-info">
        <h2>AMZ Health Assistant</h2>
        <span class="status-dot"></span>
        <span class="status-text">Online</span>
      </div>
      <button class="clear-btn" title="নতুন চ্যাট শুরু করুন" @click="clearChat">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="17" height="17">
          <polyline points="3 6 5 6 21 6"/>
          <path d="M19 6l-1 14H6L5 6"/>
          <path d="M10 11v6"/><path d="M14 11v6"/>
          <path d="M9 6V4h6v2"/>
        </svg>
      </button>
    </div>

    <!-- ── Messages ──────────────────────────────────────────────── -->
    <div class="chat-messages" ref="messagesEl">

      <!-- Welcome screen -->
      <transition name="fade">
        <div v-if="messages.length === 0" class="welcome-block">
          <div class="welcome-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="40" height="40">
              <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
          </div>
          <p class="welcome-title">আমি কীভাবে সাহায্য করতে পারি?</p>
          <p class="welcome-sub">AMZ হাসপাতালের এআই সহকারী — ডাক্তার, টেস্ট, ওষুধ, অ্যাপয়েন্টমেন্ট সব প্রশ্নের উত্তর দিতে প্রস্তুত।</p>
          <div class="suggestion-chips">
            <button
              v-for="chip in suggestionChips"
              :key="chip.label"
              class="chip"
              @click="sendSuggestion(chip.text)"
            >
              <span class="chip-icon">{{ chip.icon }}</span>
              {{ chip.label }}
            </button>
          </div>
        </div>
      </transition>

      <!-- Message list -->
      <TransitionGroup name="msg" tag="div" class="messages-inner">
        <div
          v-for="msg in messages"
          :key="msg.id"
          class="message-row"
          :class="msg.role"
        >
          <!-- Assistant avatar -->
          <div v-if="msg.role === 'assistant'" class="avatar-sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
          </div>

          <div class="bubble-wrap">
            <!-- Doctor cards (special render) -->
            <template v-if="msg.doctors && msg.doctors.length">
              <div class="bubble" v-html="renderMarkdown(msg.content)"></div>
              <div class="doctor-cards">
                <div
                  v-for="doc in msg.doctors"
                  :key="doc.doctor_id"
                  class="doctor-card"
                >
                  <div class="doctor-card-img">
                    <img
                      v-if="doc.image_url"
                      :src="doc.image_url"
                      :alt="doc.Name"
                      loading="lazy"
                      @error="onImgError($event)"
                    />
                    <div v-else class="doctor-card-img-placeholder">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="28" height="28">
                        <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                      </svg>
                    </div>
                  </div>
                  <div class="doctor-card-info">
                    <p class="dc-name">{{ doc.Name }}</p>
                    <p class="dc-desig">{{ doc.Designation }}</p>
                    <p class="dc-dept">{{ doc.Department }}</p>
                    <p v-if="doc.Schedule" class="dc-schedule">🕐 {{ doc.Schedule }}</p>
                  </div>
                  <button
                    class="dc-book-btn"
                    @click="bookDoctor(doc)"
                    title="অ্যাপয়েন্টমেন্ট নিন"
                  >
                    অ্যাপয়েন্টমেন্ট
                  </button>
                </div>
              </div>
            </template>

            <!-- Normal bubble -->
            <div v-else class="bubble" v-html="renderMarkdown(msg.content)"></div>

            <!-- Timestamp -->
            <span class="msg-time" :class="msg.role">{{ msg.time }}</span>
          </div>
        </div>

        <!-- Typing indicator -->
        <div v-if="isLoading" key="typing" class="message-row assistant">
          <div class="avatar-sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
          </div>
          <div class="bubble-wrap">
            <div class="bubble typing">
              <span></span><span></span><span></span>
            </div>
          </div>
        </div>
      </TransitionGroup>
    </div>

    <!-- ── Input bar ──────────────────────────────────────────────── -->
    <div class="chat-input-bar">
      <textarea
        ref="inputEl"
        v-model="userInput"
        placeholder="বার্তা লিখুন... (Enter পাঠান)"
        rows="1"
        :disabled="isLoading"
        @keydown.enter.exact.prevent="sendMessage"
        @keydown.shift.enter.exact="newline"
        @input="autoResize"
      ></textarea>
      <button
        class="send-btn"
        :disabled="isLoading || !userInput.trim()"
        @click="sendMessage"
        title="পাঠান"
      >
        <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
          <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
        </svg>
      </button>
    </div>

  </div>
</template>

<script setup>
import { ref, nextTick, onMounted } from 'vue'
import axios from 'axios'

// ── State ──────────────────────────────────────────────────────────────────

const messages   = ref([])
const history    = ref([])   // [{role, content}] — full history sent to API
const userInput  = ref('')
const isLoading  = ref(false)
const messagesEl = ref(null)
const inputEl    = ref(null)

let msgIdCounter = 0
const nextId     = () => ++msgIdCounter

const now = () => new Date().toLocaleTimeString('bn-BD', { hour: '2-digit', minute: '2-digit' })

const suggestionChips = [
  { icon: '👨‍⚕️', label: 'ডাক্তারের তালিকা',       text: 'ডাক্তারের তালিকা দেখুন' },
  { icon: '📅', label: 'অ্যাপয়েন্টমেন্ট',         text: 'অ্যাপয়েন্টমেন্ট নিতে চাই' },
  { icon: '🧪', label: 'টেস্টের মূল্য',             text: 'টেস্টের মূল্য তালিকা দেখুন' },
  { icon: '🛏️', label: 'বেড স্ট্যাটাস',            text: 'বর্তমানে কোনো বেড খালি আছে?' },
  { icon: '💊', label: 'ওষুধ খুঁজুন',              text: 'ওষুধ খুঁজুন' },
]

// ── Send message ───────────────────────────────────────────────────────────

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

    const reply        = res.data.reply   ?? 'দুঃখিত, কোনো উত্তর পাওয়া যায়নি।'
    const rawHistory   = res.data.history ?? []

    // Update history (server is the source of truth)
    history.value = rawHistory

    // Check if reply contains doctor data embedded as JSON comment
    // Format: <!--DOCTORS:[...]-->
    const doctorMatch = reply.match(/<!--DOCTORS:([\s\S]*?)-->/)
    let doctors = null
    let cleanReply = reply

    if (doctorMatch) {
      try {
        doctors    = JSON.parse(doctorMatch[1])
        cleanReply = reply.replace(/<!--DOCTORS:[\s\S]*?-->/, '').trim()
      } catch {}
    }

    pushMessage('assistant', cleanReply, doctors)

  } catch (err) {
    console.error('[ChatAgent] API error:', err)
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

// One-click booking from doctor card
function bookDoctor(doc) {
  userInput.value = `${doc.Name} এর অ্যাপয়েন্টমেন্ট নিতে চাই`
  sendMessage()
}

function clearChat() {
  messages.value  = []
  history.value   = []
  userInput.value = ''
}

// ── Message helpers ────────────────────────────────────────────────────────

function pushMessage(role, content, doctors = null) {
  messages.value.push({ id: nextId(), role, content, doctors, time: now() })
}

// ── Markdown renderer ──────────────────────────────────────────────────────

function renderMarkdown(text) {
  if (!text) return ''
  return text
    // Strip any stray action blocks (shouldn't reach UI but safety net)
    .replace(/%%ACTION:BOOK_APPOINTMENT%%[\s\S]*?%%END_ACTION%%/g, '')
    // Bold
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    // Italic
    .replace(/\*(.*?)\*/g,     '<em>$1</em>')
    // Inline code
    .replace(/`([^`]+)`/g,     '<code>$1</code>')
    // Unordered list items
    .replace(/^[\-\*•] (.+)$/gm, '<li>$1</li>')
    // Wrap consecutive <li> in <ul>
    .replace(/(<li>[\s\S]*?<\/li>)(\n<li>|$)/g, (m) => m)
    .replace(/((?:<li>.*<\/li>\n?)+)/g, '<ul>$1</ul>')
    // Numbered list
    .replace(/^\d+\. (.+)$/gm, '<li>$1</li>')
    // Line breaks
    .replace(/\n/g, '<br>')
    // Strip script tags
    .replace(/<script[\s\S]*?<\/script>/gi, '')
}

// ── UI helpers ─────────────────────────────────────────────────────────────

function scrollToBottom() {
  const el = messagesEl.value
  if (el) el.scrollTop = el.scrollHeight
}

function autoResize(e) {
  const el = e.target
  el.style.height = 'auto'
  el.style.height = Math.min(el.scrollHeight, 140) + 'px'
}

function resetInputHeight() {
  if (inputEl.value) inputEl.value.style.height = 'auto'
}

function newline(e) {
  // Shift+Enter inserts newline — default browser behaviour, no override needed
}

function onImgError(e) {
  // Hide broken image, show placeholder instead
  e.target.style.display = 'none'
  e.target.parentElement.querySelector('.doctor-card-img-placeholder')?.style.setProperty('display', 'flex')
}

onMounted(() => inputEl.value?.focus())
</script>

<style scoped>
/* ── Reset / base ───────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.chat-wrapper {
  display: flex;
  flex-direction: column;
  height: 100vh;
  max-width: 820px;
  margin: 0 auto;
  background: #f0f4f8;
  font-family: 'Inter', 'Noto Sans Bengali', system-ui, sans-serif;
  font-size: 14px;
}

/* ── Header ─────────────────────────────────────────────── */
.chat-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 13px 18px;
  background: linear-gradient(135deg, #0f766e 0%, #0e6b64 100%);
  color: #fff;
  box-shadow: 0 2px 12px rgba(0,0,0,.18);
  z-index: 10;
  flex-shrink: 0;
}
.header-avatar {
  width: 40px; height: 40px;
  background: rgba(255,255,255,.18);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.header-avatar svg { width: 22px; height: 22px; stroke: #fff; }
.header-info { flex: 1; }
.header-info h2 { font-size: 15px; font-weight: 600; letter-spacing: .01em; }
.status-dot {
  display: inline-block;
  width: 8px; height: 8px;
  background: #4ade80;
  border-radius: 50%;
  margin-right: 5px;
  box-shadow: 0 0 0 2px rgba(74,222,128,.3);
  animation: pulse 2s infinite;
}
@keyframes pulse {
  0%, 100% { box-shadow: 0 0 0 2px rgba(74,222,128,.3); }
  50%       { box-shadow: 0 0 0 5px rgba(74,222,128,.1); }
}
.status-text { font-size: 11.5px; opacity: .85; }
.clear-btn {
  background: none; border: none; cursor: pointer;
  color: #fff; opacity: .75; padding: 7px;
  border-radius: 8px; transition: all .2s; line-height: 0;
}
.clear-btn:hover { opacity: 1; background: rgba(255,255,255,.15); }

/* ── Messages area ──────────────────────────────────────── */
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 20px 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 2px;
  scroll-behavior: smooth;
}
.chat-messages::-webkit-scrollbar { width: 5px; }
.chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

/* ── Welcome block ──────────────────────────────────────── */
.welcome-block {
  text-align: center;
  margin: auto;
  padding: 24px 12px;
}
.welcome-icon {
  width: 72px; height: 72px;
  background: linear-gradient(135deg, #0f766e22, #0f766e44);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 16px;
}
.welcome-icon svg { stroke: #0f766e; }
.welcome-title {
  font-size: 18px; font-weight: 700;
  color: #1e293b; margin-bottom: 6px;
}
.welcome-sub {
  font-size: 13px; color: #64748b; margin-bottom: 20px;
  max-width: 340px; margin-left: auto; margin-right: auto; line-height: 1.6;
}
.suggestion-chips {
  display: flex; flex-wrap: wrap; gap: 8px; justify-content: center;
}
.chip {
  display: flex; align-items: center; gap: 6px;
  background: #fff;
  border: 1.5px solid #e2e8f0;
  color: #334155;
  border-radius: 20px;
  padding: 7px 15px;
  font-size: 13px;
  cursor: pointer;
  transition: all .18s;
  font-family: inherit;
  box-shadow: 0 1px 3px rgba(0,0,0,.06);
}
.chip:hover {
  border-color: #0f766e;
  color: #0f766e;
  background: #f0fdfa;
  box-shadow: 0 2px 8px rgba(15,118,110,.12);
  transform: translateY(-1px);
}
.chip-icon { font-size: 15px; }

/* ── Message rows ───────────────────────────────────────── */
.messages-inner { display: flex; flex-direction: column; gap: 10px; }

.message-row {
  display: flex;
  align-items: flex-end;
  gap: 8px;
}
.message-row.user      { flex-direction: row-reverse; }
.message-row.assistant { flex-direction: row; }

.avatar-sm {
  width: 30px; height: 30px;
  background: #0f766e;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 2px 6px rgba(15,118,110,.3);
}
.avatar-sm svg { width: 15px; height: 15px; stroke: #fff; }

.bubble-wrap {
  display: flex;
  flex-direction: column;
  max-width: min(75%, 540px);
  gap: 4px;
}
.message-row.user .bubble-wrap { align-items: flex-end; }

/* ── Bubbles ────────────────────────────────────────────── */
.bubble {
  padding: 11px 15px;
  font-size: 14px;
  line-height: 1.7;
  word-break: break-word;
  border-radius: 18px;
  box-shadow: 0 1px 4px rgba(0,0,0,.08);
}
.message-row.assistant .bubble {
  background: #fff;
  color: #1e293b;
  border-radius: 4px 18px 18px 18px;
}
.message-row.user .bubble {
  background: linear-gradient(135deg, #0f766e, #0d6560);
  color: #fff;
  border-radius: 18px 18px 4px 18px;
}

/* Inline elements inside bubble */
.bubble :deep(strong) { font-weight: 600; }
.bubble :deep(code) {
  background: #f1f5f9; color: #0f766e;
  padding: 1px 5px; border-radius: 4px;
  font-size: 12.5px; font-family: monospace;
}
.bubble :deep(ul) { padding-left: 18px; margin: 4px 0; }
.bubble :deep(li) { margin: 2px 0; }
.message-row.user .bubble :deep(code) { background: rgba(255,255,255,.2); color: #fff; }

/* Typing indicator */
.bubble.typing {
  display: flex; align-items: center; gap: 5px;
  padding: 14px 18px; min-width: 56px;
}
.bubble.typing span {
  width: 7px; height: 7px;
  background: #94a3b8; border-radius: 50%;
  animation: bounce 1.3s infinite;
}
.bubble.typing span:nth-child(2) { animation-delay: .18s; }
.bubble.typing span:nth-child(3) { animation-delay: .36s; }
@keyframes bounce {
  0%, 60%, 100% { transform: translateY(0);    opacity: .6; }
  30%            { transform: translateY(-7px); opacity: 1;  }
}

/* Timestamp */
.msg-time {
  font-size: 10.5px;
  color: #94a3b8;
  padding: 0 4px;
}
.msg-time.user { text-align: right; }

/* ── Doctor cards ───────────────────────────────────────── */
.doctor-cards {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-top: 6px;
  width: 100%;
  max-width: 480px;
}
.doctor-card {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #fff;
  border: 1.5px solid #e2e8f0;
  border-radius: 14px;
  padding: 12px 14px;
  box-shadow: 0 2px 8px rgba(0,0,0,.06);
  transition: box-shadow .2s, border-color .2s;
}
.doctor-card:hover {
  border-color: #0f766e;
  box-shadow: 0 4px 16px rgba(15,118,110,.12);
}

.doctor-card-img {
  width: 54px; height: 54px; flex-shrink: 0;
  border-radius: 50%;
  overflow: hidden;
  background: #f0fdfa;
  border: 2px solid #0f766e22;
  display: flex; align-items: center; justify-content: center;
}
.doctor-card-img img {
  width: 100%; height: 100%; object-fit: cover;
}
.doctor-card-img-placeholder {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
}
.doctor-card-img-placeholder svg { stroke: #0f766e; opacity: .6; }

.doctor-card-info { flex: 1; min-width: 0; }
.dc-name   { font-weight: 600; font-size: 14px; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dc-desig  { font-size: 12px; color: #64748b; margin-top: 1px; }
.dc-dept   { font-size: 12px; color: #0f766e; font-weight: 500; margin-top: 1px; }
.dc-schedule { font-size: 11.5px; color: #94a3b8; margin-top: 3px; }

.dc-book-btn {
  flex-shrink: 0;
  background: #0f766e;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 7px 12px;
  font-size: 12px;
  font-family: inherit;
  font-weight: 500;
  cursor: pointer;
  transition: background .18s, transform .15s;
  white-space: nowrap;
}
.dc-book-btn:hover { background: #0d6560; transform: scale(1.03); }

/* ── Input bar ──────────────────────────────────────────── */
.chat-input-bar {
  display: flex;
  align-items: flex-end;
  gap: 10px;
  padding: 12px 16px;
  background: #fff;
  border-top: 1px solid #e5e7eb;
  box-shadow: 0 -2px 12px rgba(0,0,0,.07);
  flex-shrink: 0;
}
.chat-input-bar textarea {
  flex: 1;
  resize: none;
  border: 1.5px solid #e2e8f0;
  border-radius: 14px;
  padding: 10px 14px;
  font-size: 14px;
  line-height: 1.55;
  font-family: inherit;
  color: #1e293b;
  background: #f8fafc;
  outline: none;
  transition: border-color .2s, background .2s;
  overflow-y: hidden;
  max-height: 140px;
}
.chat-input-bar textarea:focus {
  border-color: #0f766e;
  background: #fff;
}
.chat-input-bar textarea:disabled { opacity: .55; cursor: not-allowed; }

.send-btn {
  width: 44px; height: 44px;
  background: linear-gradient(135deg, #0f766e, #0d6560);
  border: none; border-radius: 14px;
  color: #fff; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all .18s; flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(15,118,110,.3);
}
.send-btn:hover:not(:disabled) {
  transform: scale(1.06);
  box-shadow: 0 4px 14px rgba(15,118,110,.4);
}
.send-btn:disabled {
  background: #94a3b8;
  box-shadow: none;
  cursor: not-allowed;
  transform: none;
}

/* ── Transitions ────────────────────────────────────────── */
.msg-enter-active { transition: all .28s cubic-bezier(.34,1.56,.64,1); }
.msg-enter-from   { opacity: 0; transform: translateY(14px) scale(.97); }
.fade-enter-active, .fade-leave-active { transition: opacity .3s; }
.fade-enter-from,  .fade-leave-to      { opacity: 0; }

/* ── Responsive ─────────────────────────────────────────── */
@media (max-width: 480px) {
  .chat-header { padding: 11px 14px; }
  .chat-messages { padding: 14px 10px 8px; }
  .chat-input-bar { padding: 10px 12px; }
  .bubble-wrap { max-width: 88%; }
  .doctor-cards { max-width: 100%; }
  .dc-book-btn { padding: 6px 10px; font-size: 11px; }
}
</style>
