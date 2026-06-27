<script setup>
import { AlertTriangle } from 'lucide-vue-next'
defineProps({ open: Boolean, loading: Boolean, title: String, message: String })
const emit = defineEmits(['close', 'confirm'])
</script>

<template>
    <Teleport to="body">
        <Transition enter-from-class="opacity-0" enter-active-class="transition duration-200"
            leave-to-class="opacity-0" leave-active-class="transition duration-200">
            <div v-if="open" class="fixed inset-0 z-60 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-slate-900 border border-slate-700/70 rounded-2xl w-full max-w-sm p-6 shadow-2xl">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-rose-500/10 ring-1 ring-rose-500/20 flex items-center justify-center">
                            <AlertTriangle class="w-5 h-5 text-rose-400" />
                        </div>
                        <h3 class="font-bold text-white">{{ title ?? 'Delete record?' }}</h3>
                    </div>
                    <p class="text-sm text-slate-400 mb-6 leading-relaxed">{{ message ?? 'This action cannot be undone. The record will be permanently removed.' }}</p>
                    <div class="flex gap-3">
                        <button @click="emit('close')"
                            class="flex-1 h-10 rounded-xl border border-slate-700 text-sm font-medium text-slate-300 hover:text-white hover:border-slate-500 transition-colors">
                            Cancel
                        </button>
                        <button @click="emit('confirm')" :disabled="loading"
                            class="flex-1 h-10 rounded-xl bg-rose-600 hover:bg-rose-500 disabled:opacity-60 text-sm font-semibold text-white transition-colors">
                            {{ loading ? 'Deleting…' : 'Delete' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>