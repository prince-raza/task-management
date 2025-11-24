<template>
  <!-- Empty state with prominent input -->
  <div v-if="showEmptyState" class="text-center py-20">
    <h3 class="text-2xl font-semibold text-gray-900 mb-2">
      What do you have in mind?
    </h3>

    <form @submit.prevent="submitTask" class="flex flex-col space-y-4">
      <div class="relative max-w-2xl mx-auto w-full">
        <textarea
          v-model="taskText"
          placeholder="Write the task you plan to do today here..."
          class="w-full px-4 py-3 h-40 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg resize-none"
        ></textarea>

        <button
          type="submit"
          class="absolute right-2 bottom-2 p-2 text-black hover:text-gray-700 transition-colors"
          :class="{
            'opacity-50 cursor-not-allowed': taskText.trim() === '',
          }"
          :disabled="taskText.trim() === ''"
        >
          <CircleArrowUp fill="black" color="white" :size="30" />
        </button>
      </div>
    </form>
  </div>

  <!-- Compact input when there are tasks -->
  <form v-else @submit.prevent="submitTask" class="flex items-center space-x-3">
    <div class="relative max-w-2xl mx-auto w-full">
      <input
        v-model="taskText"
        type="text"
        placeholder="Add a new task..."
        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
      />

      <button
        type="submit"
        class="absolute right-2 top-1/2 transform -translate-y-1/2 p-2 text-black hover:text-gray-700 transition-colors"
        :class="{
          'opacity-50 cursor-not-allowed': taskText.trim() === '',
        }"
        :disabled="taskText.trim() === ''"
      >
        <CircleArrowUp fill="black" color="white" :size="30" />
      </button>
    </div>

  </form>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { CircleArrowUp } from "lucide-vue-next";

interface Emits {
  (e: "add-task", task: string): void;
}

interface Props {
  isEmptyList?: boolean;
}

// Props and Emits
const props = defineProps<Props>();
const emit = defineEmits<Emits>();

// Reactive state
const taskText = ref("");

// Computed properties
const showEmptyState = computed(() => props.isEmptyList);

// Methods
const submitTask = () => {
  if (taskText.value.trim() === "") return;
  emit("add-task", taskText.value.trim());
  taskText.value = "";
};

</script>