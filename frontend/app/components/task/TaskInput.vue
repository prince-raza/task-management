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
        :placeholder="placeholder"
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

    <button
      v-if="isEditing"
      @click="cancelEdit"
      type="button"
      class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 font-medium whitespace-nowrap"
    >
      Cancel
    </button>
  </form>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { CircleArrowUp } from "lucide-vue-next";

interface Emits {
  (e: "add-task", task: string): void;
  (e: "edit-task", payload: { id: number; description: string }): void;
  (e: "cancel-edit"): void;
}

interface Props {
  isEmptyList?: boolean;
}

// Props and Emits
const props = defineProps<Props>();
const emit = defineEmits<Emits>();

// Reactive state
const taskText = ref("");
const isEditing = ref(false);
const currentEditId = ref<number | null>(null);

// Computed properties
const showEmptyState = computed(() => props.isEmptyList && !isEditing.value);
const placeholder = computed(() => 
  isEditing.value ? "Edit task..." : "Add a new task..."
);

// Methods
const submitTask = () => {
  if (taskText.value.trim() === "") return;

  if (isEditing.value && currentEditId.value !== null) {
    emit("edit-task", {
      id: currentEditId.value,
      description: taskText.value.trim(),
    });
    resetForm();
  } else {
    emit("add-task", taskText.value.trim());
    taskText.value = "";
  }
};

const cancelEdit = () => {
  resetForm();
  emit("cancel-edit");
};

const resetForm = () => {
  taskText.value = "";
  isEditing.value = false;
  currentEditId.value = null;
};

const startEdit = (task: { id: number; description: string }) => {
  taskText.value = task.description;
  isEditing.value = true;
  currentEditId.value = task.id;
};

// Expose API
defineExpose({
  startEdit,
});
</script>