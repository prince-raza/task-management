<template>
  <header class="bg-white shadow-sm border-b border-gray-200 relative">
    <div class="max-w-9xl mx-auto px-4 py-3 flex justify-between items-center">
      <!-- Logo / Brand -->
      <div class="flex items-center space-x-2">
        <div
          class="w-8 h-8 bg-black rounded-lg flex items-center justify-center text-white font-bold"
        >
          <ListTodo />
        </div>
      </div>

      <!-- Search Bar --->
      <div class="absolute left-1/2 transform -translate-x-1/2 max-w-sm w-full">
        <div class="relative">
          <div
            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
          >
            <Search :size="16" />
          </div>
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Search tasks..."
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
          />
          <!-- Clear button -->
          <button
            v-if="searchQuery"
            @click="clearSearch"
            class="absolute inset-y-0 right-0 pr-3 flex items-center"
          >
            <X :size="16" />
          </button>
        </div>
      </div>

      <!-- User Info -->
      <div class="flex items-center space-x-4">
        <span class="text-gray-700 font-sm">Hello, {{ userName }}</span>
        <button
          @click="handleLogout"
          class="bg-black hover:bg-gray-600 text-white px-3 py-1 rounded-md text-sm"
        >
          Logout
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, watch } from "vue";
import { ListTodo, Search, X } from "lucide-vue-next";

interface Props {
  userName?: string;
}

interface Emits {
  (e: "logout"): void;
  (e: "search", query: string): void;
}

// Props and Emits
const props = withDefaults(defineProps<Props>(), {
  userName: "User",
});

const emit = defineEmits<Emits>();

// Reactive state
const searchQuery = ref("");

// Methods
const clearSearch = () => {
  searchQuery.value = "";
};

const handleLogout = () => {
  emit("logout");
};

// Watchers
watch(searchQuery, (newQuery) => {
  emit("search", newQuery);
});
</script>