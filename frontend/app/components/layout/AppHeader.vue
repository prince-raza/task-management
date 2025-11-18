<!-- components/AppHeader.vue -->
<template>
  <header class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
      <!-- Logo / Brand -->
      <div class="flex items-center space-x-2">
        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold">
          A
        </div>
        <span class="text-lg font-semibold text-gray-700">MyApp</span>
      </div>

      <!-- Search Bar -->
      <div class="flex-1 max-w-md mx-8">
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            type="text"
            v-model="searchQuery"
            @input="handleSearch"
            placeholder="Search tasks..."
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
          />
          <!-- Clear button -->
          <button
            v-if="searchQuery"
            @click="clearSearch"
            class="absolute inset-y-0 right-0 pr-3 flex items-center"
          >
            <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- User Info -->
      <div class="flex items-center space-x-4">
        <span class="text-gray-700 font-medium">Hello, {{ userName }}</span>
        <button
          @click="$emit('logout')"
          class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm"
        >
          Logout
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const searchQuery = ref('')

defineProps<{ userName?: string }>()
defineEmits<{
  (e: 'logout'): void
  (e: 'search', query: string): void
}>()

const handleSearch = () => {
  // Emit the search query to parent component
  // You can add debouncing here if needed
}

const clearSearch = () => {
  searchQuery.value = ''
  // Optionally emit empty search to clear results
}
</script>