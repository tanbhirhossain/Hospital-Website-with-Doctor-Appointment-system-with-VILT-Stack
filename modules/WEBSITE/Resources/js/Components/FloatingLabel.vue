<template>
  <div class="relative">
    <input
      :id="id"
      :type="type"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :placeholder="placeholder || label"
      :maxlength="maxlength"
      class="peer w-full h-12 px-4 pt-5 pb-1.5 rounded-2xl border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 placeholder:text-transparent focus:placeholder:text-gray-400"
      :class="{ 'border-red-300 focus:border-red-500': error }"
    />
    <label
      :for="id"
      class="absolute left-4 top-1 text-xs text-gray-500 transition-all duration-200 peer-placeholder-shown:text-base peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-gray-400 peer-focus:top-1 peer-focus:text-xs peer-focus:text-indigo-600"
    >
      {{ label }}
    </label>
  </div>
</template>

<script setup>
defineProps({
  modelValue: String,
  label: String,
  id: { type: String, default: () => `field-${Math.random().toString(36).substr(2, 9)}` },
  type: { type: String, default: 'text' },
  placeholder: String,
  maxlength: Number,
  error: String,
});
defineEmits(['update:modelValue']);
</script>