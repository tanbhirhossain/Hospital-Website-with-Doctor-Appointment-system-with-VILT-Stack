<script setup>
import { AlertTriangle } from 'lucide-vue-next'
defineProps({ open: Boolean, loading: Boolean, title: String, message: String })
const emit = defineEmits(['close', 'confirm'])
</script>

<template>
    <Teleport to="body">
        <Transition enter-from-class="opacity-0" enter-active-class="transition duration-200"
            leave-to-class="opacity-0" leave-active-class="transition duration-200">
            <div v-if="open" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
                <div class="w-full max-w-sm rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl shadow-slate-950/20">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex size-11 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 ring-1 ring-rose-100">
                            <AlertTriangle class="size-5" />
                        </div>
                        <h3 class="font-black text-slate-950">{{ title ?? 'Delete record?' }}</h3>
                    </div>
                    <p class="mb-6 text-sm leading-relaxed text-slate-500">{{ message ?? 'This action cannot be undone. The record will be permanently removed.' }}</p>
                    <div class="flex gap-3">
                        <button @click="emit('close')"
                            class="flex-1 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                            Cancel
                        </button>
                        <button @click="emit('confirm')" :disabled="loading"
                            class="flex-1 rounded-2xl bg-rose-600 px-4 py-2.5 text-sm font-black text-white shadow-lg shadow-rose-500/20 transition hover:bg-rose-500 disabled:opacity-60">
                            {{ loading ? 'Deleting…' : 'Delete' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
