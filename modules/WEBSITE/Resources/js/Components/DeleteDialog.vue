<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { AlertTriangle, X, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    show: boolean;
    title?: string;
    description?: string;
    itemName?: string;
    processing?: boolean;
}>();

const emit = defineEmits<{
    confirm: [];
    cancel: [];
}>();

const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Escape' && props.show) emit('cancel');
};

onMounted(() => document.addEventListener('keydown', handleKeydown));
onUnmounted(() => document.removeEventListener('keydown', handleKeydown));

watch(() => props.show, (val) => {
    document.body.style.overflow = val ? 'hidden' : '';
});
</script>

<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="$emit('cancel')" />

                <!-- Dialog -->
                <div class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-gray-100">
                    <button
                        type="button"
                        class="absolute right-4 top-4 rounded-xl p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                        @click="$emit('cancel')"
                    >
                        <X class="size-4" />
                    </button>

                    <div class="p-7 pt-8">
                        <!-- Icon -->
                        <div class="mx-auto mb-5 flex size-14 items-center justify-center rounded-2xl bg-red-50 ring-1 ring-red-100">
                            <AlertTriangle class="size-7 text-red-500" />
                        </div>

                        <!-- Content -->
                        <div class="mb-6 text-center">
                            <h3 class="text-lg font-bold text-gray-900">{{ title ?? 'Delete Confirmation' }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-gray-500">
                                {{ description ?? 'This action cannot be undone. All associated data will be permanently removed.' }}
                            </p>
                            <div v-if="itemName" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-gray-50 px-4 py-2 ring-1 ring-gray-100">
                                <Trash2 class="size-3.5 text-red-500" />
                                <span class="text-sm font-semibold text-gray-900">{{ itemName }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="flex-1 h-11 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                                :disabled="processing"
                                @click="$emit('cancel')"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                class="flex-1 h-11 rounded-xl bg-red-600 text-sm font-semibold text-white shadow-lg shadow-red-600/25 transition hover:bg-red-700 disabled:opacity-50"
                                :disabled="processing"
                                @click="$emit('confirm')"
                            >
                                <span class="flex items-center justify-center gap-2">
                                    <Trash2 class="size-4" />
                                    {{ processing ? 'Deleting…' : 'Yes, Delete' }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: all 0.2s ease;
}
.modal-enter-active > div:last-child,
.modal-leave-active > div:last-child {
    transition: all 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
.modal-enter-from > div:last-child,
.modal-leave-to > div:last-child {
    transform: scale(0.96) translateY(8px);
    opacity: 0;
}
</style>
