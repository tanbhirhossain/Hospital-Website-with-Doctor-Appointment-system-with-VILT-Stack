<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { 
    Bold, 
    Image, 
    Italic, 
    Link, 
    List, 
    ListOrdered, 
    Underline, 
    Strikethrough, 
    Heading2, 
    Heading3, 
    Quote, 
    Eraser, 
    Undo2, 
    Redo2, 
    CodeXml 
} from 'lucide-vue-next';
import { onMounted, ref, watch, nextTick } from 'vue';

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
const isSourceMode = ref(false);
const internalHtml = ref('');

// Sync prop updates to internal storage and editor DOM
const syncEditor = () => {
    internalHtml.value = props.modelValue;
    if (editor.value && !isSourceMode.value && editor.value.innerHTML !== props.modelValue) {
        editor.value.innerHTML = props.modelValue;
    }
};

// Handle rich text input changes
const updateValue = () => {
    if (isSourceMode.value) return;
    const currentContent = editor.value?.innerHTML ?? '';
    internalHtml.value = currentContent;
    emit('update:modelValue', currentContent);
};

// Handle raw HTML updates from textarea
const updateSourceValue = (e: Event) => {
    const value = (e.target as HTMLTextAreaElement).value;
    internalHtml.value = value;
    emit('update:modelValue', value);
};

// Toggle between rich-text visual mode and HTML source code mode
const toggleSourceMode = async () => {
    if (props.disabled) return;
    
    if (isSourceMode.value) {
        // Switching back to visual mode: push textarea changes to contenteditable
        isSourceMode.value = false;
        await nextTick();
        if (editor.value) {
            editor.value.innerHTML = internalHtml.value;
        }
    } else {
        // Switching to HTML source mode
        isSourceMode.value = true;
    }
};

const runCommand = (command: string, value?: string) => {
    if (props.disabled || isSourceMode.value) {
        return;
    }

    editor.value?.focus();
    document.execCommand(command, false, value);
    updateValue();
};

const addLink = () => {
    if (isSourceMode.value) return;
    const url = window.prompt('Link URL');
    if (url) {
        runCommand('createLink', url);
    }
};

const addImage = () => {
    if (isSourceMode.value) return;
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
            <Button type="button" variant="ghost" size="sm" :disabled="disabled || isSourceMode" title="Bold" @click="runCommand('bold')">
                <Bold class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="sm" :disabled="disabled || isSourceMode" title="Italic" @click="runCommand('italic')">
                <Italic class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="sm" :disabled="disabled || isSourceMode" title="Underline" @click="runCommand('underline')">
                <Underline class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="sm" :disabled="disabled || isSourceMode" title="Strikethrough" @click="runCommand('strikeThrough')">
                <Strikethrough class="size-4" />
            </Button>

            <div class="mx-1 h-6 w-[1px] bg-border self-center"></div>

            <Button type="button" variant="ghost" size="sm" :disabled="disabled || isSourceMode" title="Heading 2" @click="runCommand('formatBlock', '<h2>')">
                <Heading2 class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="sm" :disabled="disabled || isSourceMode" title="Heading 3" @click="runCommand('formatBlock', '<h3>')">
                <Heading3 class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="sm" :disabled="disabled || isSourceMode" title="Blockquote" @click="runCommand('formatBlock', '<blockquote>')">
                <Quote class="size-4" />
            </Button>

            <div class="mx-1 h-6 w-[1px] bg-border self-center"></div>

            <Button type="button" variant="ghost" size="sm" :disabled="disabled || isSourceMode" title="Bulleted list" @click="runCommand('insertUnorderedList')">
                <List class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="sm" :disabled="disabled || isSourceMode" title="Numbered list" @click="runCommand('insertOrderedList')">
                <ListOrdered class="size-4" />
            </Button>

            <div class="mx-1 h-6 w-[1px] bg-border self-center"></div>

            <Button type="button" variant="ghost" size="sm" :disabled="disabled || isSourceMode" title="Insert link" @click="addLink">
                <Link class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="sm" :disabled="disabled || isSourceMode" title="Insert image URL" @click="addImage">
                <Image class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="sm" :disabled="disabled || isSourceMode" title="Clear formatting" @click="runCommand('removeFormat')">
                <Eraser class="size-4" />
            </Button>

            <div class="mx-1 h-6 w-[1px] bg-border self-center"></div>

            <Button type="button" variant="ghost" size="sm" :disabled="disabled || isSourceMode" title="Undo" @click="runCommand('undo')">
                <Undo2 class="size-4" />
            </Button>
            <Button type="button" variant="ghost" size="sm" :disabled="disabled || isSourceMode" title="Redo" @click="runCommand('redo')">
                <Redo2 class="size-4" />
            </Button>

            <div class="ml-auto mx-1 h-6 w-[1px] bg-border self-center"></div>

            <Button 
                type="button" 
                :variant="isSourceMode ? 'secondary' : 'ghost'" 
                size="sm" 
                :disabled="disabled" 
                title="Toggle HTML Source" 
                @click="toggleSourceMode"
            >
                <CodeXml class="size-4 text-primary" />
                <span class="ml-1.5 text-xs font-semibold">HTML</span>
            </Button>
        </div>

        <div
            v-show="!isSourceMode"
            :id="id"
            ref="editor"
            :contenteditable="!disabled"
            :data-placeholder="placeholder"
            :class="[
                'prose prose-sm max-w-none px-3 py-2 text-sm ring-offset-background focus-visible:outline-none dark:prose-invert focus-within:ring-2 focus-within:ring-ring focus-within:ring-offset-2',
                disabled ? 'cursor-not-allowed opacity-50' : '',
                minHeightClass ?? 'min-h-28',
            ]"
            @input="updateValue"
            @blur="updateValue"
        />

        <textarea
            v-if="isSourceMode"
            :value="internalHtml"
            :disabled="disabled"
            :class="[
                'w-full block font-mono text-xs bg-muted/20 text-muted-foreground p-3 border-0 focus:outline-none focus:ring-0 resize-y',
                minHeightClass ?? 'min-h-28',
            ]"
            @input="updateSourceValue"
        />
    </div>
</template>

<style scoped>
/* Placeholder trick for contenteditable */
[contenteditable]:empty:before {
    content: attr(data-placeholder);
    color: hsl(var(--muted-foreground) / 0.6);
    pointer-events: none;
    display: block;
}
</style>