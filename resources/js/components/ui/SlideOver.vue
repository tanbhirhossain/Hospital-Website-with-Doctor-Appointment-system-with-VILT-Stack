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
            <div v-if="open" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm" @click.self="emit('close')" />
        </Transition>

        <Transition enter-from-class="translate-x-full" enter-active-class="transition duration-300 ease-out"
            leave-to-class="translate-x-full" leave-active-class="transition duration-250 ease-in">
            <div v-if="open" :class="['fixed right-0 top-0 z-50 flex h-full w-full flex-col border-l border-slate-200 bg-white shadow-2xl shadow-slate-950/20', width]">
                <div class="border-b border-slate-200 bg-gradient-to-br from-white to-slate-50 px-6 py-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <span v-if="badge" class="mb-2 inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.16em] text-blue-700 ring-1 ring-blue-100">
                                {{ badge }}
                            </span>
                            <h2 class="text-xl font-black tracking-tight text-slate-950">{{ title }}</h2>
                            <p v-if="subtitle" class="mt-1 text-sm leading-6 text-slate-500">{{ subtitle }}</p>
                        </div>
                        <button @click="emit('close')"
                            class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:text-slate-950 hover:shadow-md">
                            <X class="size-4" />
                        </button>
                    </div>
                </div>

                <div class="flex-1 space-y-5 overflow-y-auto bg-slate-50/60 px-6 py-5">
                    <slot />
                </div>

                <div v-if="$slots.footer" class="border-t border-slate-200 bg-white px-6 py-4">
                    <slot name="footer" />
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
