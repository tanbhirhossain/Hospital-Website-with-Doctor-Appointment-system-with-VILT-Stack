<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ArrowUpDown, ChevronDown, ChevronUp, GripVertical, Save, Shuffle } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Section Order', href: '/admin/home-sections' }];
const flash = computed(() => (usePage().props as any).flash);

const props = defineProps<{
  sections: Array<{ key: string; label: string; icon: string }>;
  currentOrder: string[];
  availableSections: Record<string, { label: string; icon: string }>;
}>();

const orderedSections = ref([...props.sections]);

const iconMap: Record<string, string> = {
  Image: '🖼️', Info: 'ℹ️', Video: '🎬', Award: '🏆', Building2: '🏢', Star: '⭐',
  HeartPulse: '❤️', Users: '👥', Crown: '👑', ImageIcon: '🖼️', Package: '📦',
  Calendar: '📅', Quote: '💬', BarChart3: '📊', Newspaper: '📰', Handshake: '🤝',
  MapPin: '📍', LayoutGrid: '⊞',
};

function moveUp(idx: number) {
  if (idx === 0) return;
  const arr = [...orderedSections.value];
  [arr[idx - 1], arr[idx]] = [arr[idx], arr[idx - 1]];
  orderedSections.value = arr;
}
function moveDown(idx: number) {
  if (idx >= orderedSections.value.length - 1) return;
  const arr = [...orderedSections.value];
  [arr[idx], arr[idx + 1]] = [arr[idx + 1], arr[idx]];
  orderedSections.value = arr;
}
function saveOrder() {
  router.put('/admin/home-sections/order', { order: orderedSections.value.map(s => s.key) });
}
function resetOrder() { orderedSections.value = [...props.sections]; }
function shuffleOrder() {
  const arr = [...orderedSections.value];
  for (let i = arr.length - 1; i > 0; i--) { const j = Math.floor(Math.random() * (i + 1)); [arr[i], arr[j]] = [arr[j], arr[i]]; }
  orderedSections.value = arr;
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Homepage Section Order" />
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-md"><ArrowUpDown class="h-5 w-5 text-white" /></div>
          <div><h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Homepage Section Order</h1><p class="text-sm text-gray-500 dark:text-gray-400">Drag to reorder homepage sections</p></div>
        </div>
        <div class="flex gap-2">
          <Button variant="outline" @click="resetOrder" class="gap-2"><Shuffle class="h-4 w-4" /> Reset</Button>
          <Button @click="saveOrder" class="gap-2 bg-violet-600 hover:bg-violet-700"><Save class="h-4 w-4" /> Save Order</Button>
        </div>
      </div>

      <div v-if="flash?.success" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400">{{ flash.success }}</div>
      <div v-if="flash?.error" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 dark:bg-red-900/20 dark:text-red-400">{{ flash.error }}</div>

      <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700 flex items-center justify-between">
          <h2 class="font-semibold text-gray-900 dark:text-white">Section Display Order ({{ orderedSections.length }} sections)</h2>
          <p class="text-xs text-gray-500">Top items appear first on the homepage</p>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-700">
          <div v-for="(section, idx) in orderedSections" :key="section.key"
            class="group flex items-center gap-4 px-5 py-3 transition hover:bg-gray-50 dark:hover:bg-gray-800/50">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-gray-100 text-xs font-bold text-gray-500 dark:bg-gray-800">{{ idx + 1 }}</span>
            <GripVertical class="h-4 w-4 text-gray-300 group-hover:text-gray-500 transition" />
            <span class="text-lg">{{ iconMap[section.icon] || '📄' }}</span>
            <span class="flex-1 text-sm font-semibold text-gray-900 dark:text-white">{{ section.label }}</span>
            <span class="text-xs text-gray-400 font-mono">{{ section.key }}</span>
            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition">
              <button @click="moveUp(idx)" :disabled="idx === 0" class="rounded p-1 text-gray-400 hover:text-gray-700 disabled:opacity-30"><ChevronUp class="h-4 w-4" /></button>
              <button @click="moveDown(idx)" :disabled="idx === orderedSections.length - 1" class="rounded p-1 text-gray-400 hover:text-gray-700 disabled:opacity-30"><ChevronDown class="h-4 w-4" /></button>
            </div>
          </div>
        </div>

        <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-700 flex items-center justify-between">
          <span class="text-sm text-gray-500">{{ orderedSections.length }} sections total</span>
          <Button @click="saveOrder" class="gap-2 bg-violet-600 hover:bg-violet-700"><Save class="h-4 w-4" /> Save Section Order</Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
