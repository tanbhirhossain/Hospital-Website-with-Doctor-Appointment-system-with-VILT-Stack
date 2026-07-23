<template>
  <div class="relative">
    <!-- Selected icon preview + input -->
    <div class="flex items-center gap-3">
      <div
        class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gray-100 border-2 border-dashed border-gray-300 text-2xl text-gray-600 transition-all"
        :class="{ 'border-indigo-400 bg-indigo-50 text-indigo-600': modelValue }"
      >
        <i v-if="modelValue" :class="modelValue"></i>
        <span v-else class="text-xs text-gray-400">icon</span>
      </div>
      <div class="flex-1 relative">
        <input
          type="text"
          :value="modelValue"
          @input="$emit('update:modelValue', $event.target.value)"
          @focus="showDropdown = true"
          @blur="closeDropdown"
          class="w-full h-12 px-4 pr-10 rounded-2xl border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
          :placeholder="placeholder || 'Search or type class…'"
        />
        <button
          type="button"
          @mousedown.prevent="showDropdown = !showDropdown"
          class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
        >
          <i class="fas fa-chevron-down text-xs"></i>
        </button>
      </div>
    </div>

    <!-- Dropdown -->
    <div
      v-if="showDropdown"
      class="absolute z-50 mt-2 w-full max-h-60 overflow-y-auto rounded-2xl bg-white/90 backdrop-blur-md border border-gray-200 shadow-2xl p-2 transition-all"
      @mouseleave="showDropdown = false"
    >
      <input
        type="text"
        v-model="search"
        placeholder="Filter icons…"
        class="w-full px-3 py-2 mb-2 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm"
        @click.stop
      />
      <div class="grid grid-cols-4 gap-1">
        <button
          v-for="icon in filteredIcons"
          :key="icon"
          type="button"
          @mousedown.prevent="selectIcon(icon)"
          class="p-2 rounded-xl hover:bg-indigo-50 transition-colors flex items-center justify-center text-lg text-gray-700 hover:text-indigo-600"
          :class="{ 'bg-indigo-100 text-indigo-600': modelValue === icon }"
        >
          <i :class="icon"></i>
        </button>
      </div>
      <p v-if="filteredIcons.length === 0" class="text-sm text-gray-400 text-center py-2">No icons found</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  modelValue: String,
  placeholder: String,
});

const emit = defineEmits(['update:modelValue']);

const showDropdown = ref(false);
const search = ref('');

// Predefined list of FontAwesome Free icons (solid + brands)
const iconList = [
  'fas fa-robot', 'fas fa-cogs', 'fas fa-brain', 'fas fa-code',
  'fas fa-chart-line', 'fas fa-users', 'fas fa-laptop', 'fas fa-database',
  'fas fa-cloud', 'fas fa-shield-alt', 'fas fa-rocket', 'fas fa-star',
  'fas fa-microchip', 'fas fa-globe', 'fas fa-network-wired', 'fas fa-crown',
  'fas fa-graduation-cap', 'fas fa-flask', 'fas fa-atom', 'fas fa-dna',
  'fas fa-lightbulb', 'fas fa-puzzle-piece', 'fas fa-cubes', 'fas fa-layer-group',
  'fas fa-calculator', 'fas fa-chart-pie', 'fas fa-chart-bar', 'fas fa-chart-area',
  'fas fa-download', 'fas fa-upload', 'fas fa-sync-alt', 'fas fa-cog',
  'fas fa-wrench', 'fas fa-tools', 'fas fa-pencil-alt', 'fas fa-edit',
  'fas fa-tag', 'fas fa-tags', 'fas fa-book', 'fas fa-book-open',
  'fas fa-folder', 'fas fa-folder-open', 'fas fa-file', 'fas fa-file-alt',
  'fas fa-image', 'fas fa-camera', 'fas fa-video', 'fas fa-play',
  'fas fa-pause', 'fas fa-stop', 'fas fa-plus', 'fas fa-minus',
  'fas fa-times', 'fas fa-check', 'fas fa-exclamation', 'fas fa-question',
  'fas fa-info', 'fas fa-heart', 'fas fa-thumbs-up', 'fas fa-thumbs-down',
  'fab fa-react', 'fab fa-vuejs', 'fab fa-angular', 'fab fa-node',
  'fab fa-python', 'fab fa-aws', 'fab fa-google', 'fab fa-microsoft',
];

const filteredIcons = computed(() => {
  if (!search.value) return iconList;
  return iconList.filter(icon => icon.includes(search.value.toLowerCase()));
});

const selectIcon = (icon) => {
  emit('update:modelValue', icon);
  showDropdown.value = false;
  search.value = '';
};

const closeDropdown = () => {
  // Delay to allow click on dropdown items
  setTimeout(() => {
    showDropdown.value = false;
  }, 200);
};
</script>