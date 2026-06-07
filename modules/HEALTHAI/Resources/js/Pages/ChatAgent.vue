<template>
  <div class="flex flex-col h-screen max-w-lg mx-auto bg-gray-50 border border-gray-200 shadow-lg rounded-lg overflow-hidden my-5">
    <div class="bg-blue-600 text-white p-4 flex items-center justify-between">
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-blue-600 font-bold text-xl">
          🏥
        </div>
        <div>
          <h2 class="font-semibold text-lg">Hospital AI Assistant</h2>
          <p class="text-xs text-blue-100 flex items-center">
            <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span> অনলাইন (Gemma 31B Cloud)
          </p>
        </div>
      </div>
    </div>

    <div ref="messageBox" class="flex-1 p-4 overflow-y-auto space-y-4">
      <div 
        v-for="(msg, index) in messages" 
        :key="index" 
        :class="['flex', msg.sender === 'user' ? 'justify-end' : 'justify-start']"
      >
        <div 
        :class="[
            'max-w-[75%] rounded-lg p-3 text-sm shadow-sm prose prose-sm', 
            msg.sender === 'user' ? 'bg-blue-500 text-white' : 'bg-white text-gray-800'
        ]"
        v-html="msg.sender === 'user' ? msg.text : parseMarkdown(msg.text)"
        >
        </div>
      </div>

      <div v-if="isLoading" class="flex justify-start">
        <div class="bg-white border border-gray-200 rounded-lg rounded-bl-none p-3 shadow-sm flex items-center space-x-2">
          <span class="text-sm text-gray-500">এজেন্ট চিন্তা করছে</span>
          <div class="flex space-x-1">
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce [animation-delay:0.2s]"></div>
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce [animation-delay:0.4s]"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="p-3 bg-white border-t border-gray-200">
      <form @submit.prevent="sendMessage" class="flex items-center space-x-2">
        <input 
          v-model="userInput"
          type="text" 
          placeholder="এখানে প্রশ্ন করুন (যেমন: CBC টেস্টের দাম কত?)" 
          class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          :disabled="isLoading"
        />
        <button 
          type="submit" 
          class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-2 w-9 h-9 flex items-center justify-center transition-colors shadow-md"
          :disabled="!userInput.trim() || isLoading"
        >
          🕊️
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import { marked } from 'marked';
import axios from 'axios';

// স্টেটসমূহ
const userInput = ref('');
const isLoading = ref(false);
const messageBox = ref(null);



// প্রাথমিক মেসেজ (ওয়েলকাম মেসেজ)
const messages = ref([
  { 
    sender: 'ai', 
    text: 'আসসালামু আলাইকুম! আমি হাসপাতালের এআই অ্যাসিস্ট্যান্ট। আমি আপনাকে সঠিক ডাক্তার খুঁজে পেতে, অ্যাপয়েন্টমেন্ট বুক করতে এবং টেস্টের প্রাইস লিস্ট (যেমন: CBC, ECG ইত্যাদি) জানাতে সাহায্য করতে পারি। বলুন, কীভাবে সাহায্য করতে পারি?' 
  }
]);

const parseMarkdown = (text) => {
  return marked.parse(text, { breaks: true });
};

// স্ক্রল অটোমেটিকভাবে নিচে নামানোর ফাংশন
const scrollToBottom = async () => {
  await nextTick();
  if (messageBox.value) {
    messageBox.value.scrollTop = messageBox.value.scrollHeight;
  }
};

// মেসেজ পাঠানোর ফাংশন
const sendMessage = async () => {
  if (!userInput.value.trim() || isLoading.value) return;

  const userQuery = userInput.value;
  
  // ইউজারের মেসেজ চ্যাটে পুশ করা
  messages.value.push({ sender: 'user', text: userQuery });
  userInput.value = '';
  isLoading.value = true;
  await scrollToBottom();

  try {
    // লারাভেল ব্যাকএন্ডের রাউটে রিকোয়েস্ট পাঠানো
    // (আপনার লারাভেল প্রোজেক্টের আসল ডোমেইন/ইউআরএল বসিয়ে নেবেন)
    const response = await axios.post('http://localhost:8000/api/chat', {
      message: userQuery
    });

    // ব্যাকএন্ডের ফাইনাল বাংলা রিপ্লাই চ্যাটে পুশ করা
    messages.value.push({ 
      sender: 'ai', 
      text: response.data.reply 
    });

  } catch (error) {
    console.error("Error communicating with AI Agent:", error);
    messages.value.push({ 
      sender: 'ai', 
      text: 'দুঃখিত, এই মুহূর্তে ব্যাকএন্ড সার্ভারের সাথে যোগাযোগ করা যাচ্ছে না। দয়া করে আবার চেষ্টা করুন।' 
    });
  } finally {
    isLoading.value = false;
    await scrollToBottom();
  }
};
</script>