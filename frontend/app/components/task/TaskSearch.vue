<template>
  <div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-700">
      Search Results for "{{ searchQuery }}"
    </h2>
    <p class="text-sm text-gray-500">
      {{ taskCount }} task(s) found
      <button 
        @click="$emit('clear-search')"
        class="ml-2 text-blue-500 hover:text-blue-700 text-sm"
      >
        Clear search
      </button>
    </p>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  searchQuery: string;
  taskCount: number;
}>();

defineEmits<{
  'clear-search': [];
}>();
</script>