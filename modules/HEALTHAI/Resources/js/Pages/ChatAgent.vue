<template>
  <div class="chat-wrapper">
    <!-- ── Header ─────────────────────────────────────────── -->
    <div class="chat-header">
      <div class="header-avatar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
        </svg>
      </div>
      <div class="header-info">
        <h2>AMZ Health Assistant</h2>
        <span class="status-dot"></span><span class="status-text">Online</span>
      </div>
      <button class="clear-btn" title="Clear chat" @click="clearChat">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
          <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/>
          <path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/>
        </svg>
      </button>
    </div>

    <!-- ── Messages ───────────────────────────────────────── -->
    <div class="chat-messages" ref="messagesEl">
      <div v-if="messages.length === 0" class="welcome-block">
        <p class="welcome-title">আমি কীভাবে সাহায্য করতে পারি?</p>
        <div class="suggestion-chips">
          <button
            v-for="chip in suggestionChips"
            :key="chip"
            class="chip"
            @click="sendSuggestion(chip)"
          >{{ chip }}</button>
        </div>
      </div>

      <TransitionGroup name="msg" tag="div">
        <div
          v-for="msg in messages"
          :key="msg.id"
          class="message-row"
          :class="msg.role"
        >
          <div v-if="msg.role === 'assistant'" class="avatar-sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
          </div>
          <div class="bubble" v-html="renderMarkdown(msg.content)"></div>
        </div>

        <!-- Typing indicator -->
        <div v-if="isLoading" key="typing" class="message-row assistant">
          <div class="avatar-sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
          </div>
          <div class="bubble typing">
            <span></span><span></span><span></span>
          </div>
        </div>
      </TransitionGroup>
    </div>

    <!-- ── Input ──────────────────────────────────────────── -->
    <div class="chat-input-bar">
      <textarea
        ref="inputEl"
        v-model="userInput"
        placeholder="বার্তা লিখুন..."
        rows="1"
        :disabled="isLoading"
        @keydown.enter.exact.prevent="sendMessage"
        @input="autoResize"
      ></textarea>
      <button
        class="send-btn"
        :disabled="isLoading || !userInput.trim()"
        @click="sendMessage"
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

const messages    = ref([])   // { id, role: 'user'|'assistant', content }
const history     = ref([])   // raw history array sent to / returned from API
const userInput   = ref('')
const isLoading   = ref(false)
const messagesEl  = ref(null)
const inputEl     = ref(null)

let msgIdCounter = 0
const nextId = () => ++msgIdCounter

const suggestionChips = [
  'ডাক্তারের তালিকা দেখুন',
  'অ্যাপয়েন্টমেন্ট নিতে চাই',
  'টেস্টের মূল্য জানুন',
  'বেড স্ট্যাটাস',
  'ওষুধ খুঁজুন',
]

// ── Methods ────────────────────────────────────────────────────────────────

async function sendMessage() {
  const text = userInput.value.trim()
  if (!text || isLoading.value) return

  // Push user bubble
  messages.value.push({ id: nextId(), role: 'user', content: text })
  userInput.value = ''
  await nextTick()
  resetInputHeight()
  scrollToBottom()

  isLoading.value = true

  try {
    const res = await axios.post('/api/chat', {
      message: text,
      history: history.value,   // ← send full history each turn
    })

    const reply      = res.data.reply   ?? 'দুঃখিত, কোনো উত্তর পাওয়া যায়নি।'
    history.value    = res.data.history ?? []  // ← server returns updated history

    messages.value.push({ id: nextId(), role: 'assistant', content: reply })
  } catch (err) {
    console.error('[ChatAgent] API error:', err)
    messages.value.push({
      id: nextId(),
      role: 'assistant',
      content: 'দুঃখিত, সার্ভারে সমস্যা হয়েছে। একটু পরে আবার চেষ্টা করুন।',
    })
  } finally {
    isLoading.value = false
    await nextTick()
    scrollToBottom()
  }
}

function sendSuggestion(chip) {
  userInput.value = chip
  sendMessage()
}

function clearChat() {
  messages.value = []
  history.value  = []
  userInput.value = ''
}

// ── Markdown renderer (lightweight, no dependency) ─────────────────────────

function renderMarkdown(text) {
  if (!text) return ''
  return text
    // Bold
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    // Italic
    .replace(/\*(.*?)\*/g,     '<em>$1</em>')
    // Inline code
    .replace(/`([^`]+)`/g,     '<code>$1</code>')
    // Bullet lists (lines starting with - or *)
    .replace(/^[\-\*] (.+)$/gm, '<li>$1</li>')
    .replace(/((<li>.*<\/li>\n?)+)/g, '<ul>$1</ul>')
    // Numbered list
    .replace(/^\d+\. (.+)$/gm, '<li>$1</li>')
    // Line breaks
    .replace(/\n/g, '<br>')
    // Sanitise – strip any script tags that might slip through
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

onMounted(() => inputEl.value?.focus())
</script>

<style scoped>
/* ── Layout ────────────────────────────────────────── */
.chat-wrapper {
  display: flex;
  flex-direction: column;
  height: 100vh;
  max-width: 780px;
  margin: 0 auto;
  background: #f8fafc;
  font-family: 'Inter', 'Noto Sans Bengali', sans-serif;
}

/* ── Header ────────────────────────────────────────── */
.chat-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 20px;
  background: #0f766e;
  color: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,.15);
}
.header-avatar {
  width: 38px; height: 38px;
  background: rgba(255,255,255,.2);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.header-avatar svg { width: 20px; height: 20px; stroke: #fff; }
.header-info { flex: 1; }
.header-info h2 { margin: 0; font-size: 15px; font-weight: 600; }
.status-dot {
  display: inline-block;
  width: 8px; height: 8px;
  background: #4ade80;
  border-radius: 50%;
  margin-right: 5px;
}
.status-text { font-size: 12px; opacity: .85; }
.clear-btn {
  background: none; border: none; cursor: pointer;
  color: #fff; opacity: .7; padding: 6px;
  border-radius: 6px; transition: opacity .2s;
}
.clear-btn:hover { opacity: 1; background: rgba(255,255,255,.15); }

/* ── Messages ──────────────────────────────────────── */
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 20px 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  scroll-behavior: smooth;
}

.welcome-block { text-align: center; margin: auto; }
.welcome-title { font-size: 17px; color: #374151; font-weight: 600; margin-bottom: 16px; }
.suggestion-chips { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; }
.chip {
  background: #fff;
  border: 1.5px solid #0f766e;
  color: #0f766e;
  border-radius: 20px;
  padding: 7px 16px;
  font-size: 13px;
  cursor: pointer;
  transition: all .18s;
  font-family: inherit;
}
.chip:hover { background: #0f766e; color: #fff; }

.message-row {
  display: flex;
  align-items: flex-end;
  gap: 8px;
  max-width: 82%;
}
.message-row.user { margin-left: auto; flex-direction: row-reverse; }
.message-row.assistant { margin-right: auto; }

.avatar-sm {
  width: 30px; height: 30px;
  background: #0f766e;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.avatar-sm svg { width: 16px; height: 16px; stroke: #fff; }

.bubble {
  background: #fff;
  border-radius: 18px 18px 18px 4px;
  padding: 11px 15px;
  font-size: 14px;
  line-height: 1.65;
  color: #1f2937;
  box-shadow: 0 1px 4px rgba(0,0,0,.08);
  word-break: break-word;
}
.message-row.user .bubble {
  background: #0f766e;
  color: #fff;
  border-radius: 18px 18px 4px 18px;
}

/* Typing indicator */
.bubble.typing {
  display: flex; align-items: center; gap: 4px; padding: 14px 18px;
}
.bubble.typing span {
  width: 7px; height: 7px;
  background: #94a3b8;
  border-radius: 50%;
  animation: bounce 1.2s infinite;
}
.bubble.typing span:nth-child(2) { animation-delay: .2s; }
.bubble.typing span:nth-child(3) { animation-delay: .4s; }
@keyframes bounce {
  0%, 60%, 100% { transform: translateY(0); }
  30%            { transform: translateY(-6px); }
}

/* Transition */
.msg-enter-active { transition: all .25s ease; }
.msg-enter-from   { opacity: 0; transform: translateY(10px); }

/* ── Input bar ─────────────────────────────────────── */
.chat-input-bar {
  display: flex;
  align-items: flex-end;
  gap: 10px;
  padding: 14px 16px;
  background: #fff;
  border-top: 1px solid #e5e7eb;
  box-shadow: 0 -2px 8px rgba(0,0,0,.06);
}
.chat-input-bar textarea {
  flex: 1;
  resize: none;
  border: 1.5px solid #d1d5db;
  border-radius: 12px;
  padding: 10px 14px;
  font-size: 14px;
  line-height: 1.5;
  font-family: inherit;
  color: #1f2937;
  background: #f9fafb;
  outline: none;
  transition: border-color .2s;
  overflow-y: hidden;
  max-height: 140px;
}
.chat-input-bar textarea:focus { border-color: #0f766e; background: #fff; }
.chat-input-bar textarea:disabled { opacity: .6; cursor: not-allowed; }

.send-btn {
  width: 42px; height: 42px;
  background: #0f766e;
  border: none; border-radius: 12px;
  color: #fff; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all .18s; flex-shrink: 0;
}
.send-btn:hover:not(:disabled) { background: #0d6560; transform: scale(1.05); }
.send-btn:disabled { background: #9ca3af; cursor: not-allowed; transform: none; }

/* Scrollbar */
.chat-messages::-webkit-scrollbar { width: 5px; }
.chat-messages::-webkit-scrollbar-track { background: transparent; }
.chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
