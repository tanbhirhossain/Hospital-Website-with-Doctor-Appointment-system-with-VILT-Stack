<script setup>
import { X } from 'lucide-vue-next'

defineProps({
    open: Boolean,
    title: String,
    subtitle: String,
    badge: String,
    width: { type: String, default: 'max-w-xl' },
})
const emit = defineEmits(['close'])
</script>

<template>
    <Teleport to="body">
        <Transition enter-from-class="opacity-0" enter-active-class="transition duration-200"
            leave-to-class="opacity-0" leave-active-class="transition duration-200">
            <div v-if="open" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm" @click.self="emit('close')" />
        </Transition>

        <Transition enter-from-class="translate-x-full" enter-active-class="transition duration-300 ease-out"
            leave-to-class="translate-x-full" leave-active-class="transition duration-250 ease-in">
            <div v-if="open" :class="['fixed top-0 right-0 z-50 h-full bg-slate-900 border-l border-slate-700/70 flex flex-col shadow-2xl w-full', width]">

                <!-- Header -->
                <div class="flex items-start justify-between gap-4 px-6 py-5 border-b border-slate-700/50 bg-slate-800/40">
                    <div>
                        <span v-if="badge" class="inline-flex mb-1.5 items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-widest text-violet-300 bg-violet-500/10 ring-1 ring-violet-500/20">
                            {{ badge }}
                        </span>
                        <h2 class="text-lg font-bold text-white">{{ title }}</h2>
                        <p v-if="subtitle" class="text-xs text-slate-400 mt-0.5">{{ subtitle }}</p>
                    </div>
                    <button @click="emit('close')"
                        class="w-8 h-8 flex items-center justify-center rounded-xl text-slate-400 hover:text-white hover:bg-slate-700/60 transition-colors shrink-0 mt-0.5">
                        <X class="w-4 h-4" />
                    </button>
                </div>

                <!-- Body -->
                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">
                    <slot />
                </div>

                <!-- Footer -->
                <div v-if="$slots.footer" class="px-6 py-4 border-t border-slate-700/50 bg-slate-800/30">
                    <slot name="footer" />
                </div>
            </div>
        </Transition>
    </Teleport>
</template>