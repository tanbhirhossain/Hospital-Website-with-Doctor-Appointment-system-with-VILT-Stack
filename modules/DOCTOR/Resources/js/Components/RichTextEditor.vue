<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Bold, Image, Italic, Link, List, ListOrdered } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

const props = defineProps<{
    id: string;
    modelValue: string;
    disabled?: boolean;
    minHeightClass?: string;
    placeholder?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const editor = ref<HTMLDivElement | null>(null);

const syncEditor = () => {
    if (editor.value && editor.value.innerHTML !== props.modelValue) {
        editor.value.innerHTML = props.modelValue;
    }
};

const updateValue = () => {
    emit('update:modelValue', editor.value?.innerHTML ?? '');
};

const runCommand = (command: string, value?: string) => {
    if (props.disabled) {
        return;
    }

    editor.value?.focus();
    document.execCommand(command, false, value);
    updateValue();
};

const addLink = () => {
    const url = window.prompt('Link URL');

    if (url) {
        runCommand('createLink', url);
    }
};

const addImage = () => {
    const url = window.prompt('Image URL');

    if (url) {
        runCommand('insertImage', url);
    }
};

watch(() => props.modelValue, syncEditor);
onMounted(syncEditor);
</script>

<template>
    <div class="overflow-hidden rounded-md border border-input bg-background">
        <div class="flex flex-wrap gap-1 border-b bg-muted/40 p-2">
            <Button type="button" variant="ghost" size="sm" :disabled="disabled" title="Bold" @click="runCommand('bold')">
                <Bold class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="sm" :disabled="disabled" title="Italic" @click="runCommand('italic')">
                <Italic class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="sm" :disabled="disabled" title="Bulleted list" @click="runCommand('insertUnorderedList')">
                <List class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="sm" :disabled="disabled" title="Numbered list" @click="runCommand('insertOrderedList')">
                <ListOrdered class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="sm" :disabled="disabled" title="Insert link" @click="addLink">
                <Link class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="sm" :disabled="disabled" title="Insert image URL" @click="addImage">
                <Image class="size-4" />
            </Button>
        </div>
        <div
            :id="id"
            ref="editor"
            :contenteditable="!disabled"
            :data-placeholder="placeholder"
            :class="[
                'prose prose-sm max-w-none px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 dark:prose-invert',
                disabled ? 'cursor-not-allowed opacity-50' : '',
                minHeightClass ?? 'min-h-28',
            ]"
            @input="updateValue"
            @blur="updateValue"
        />
    </div>
</template>
