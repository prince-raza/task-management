<template>
  <div class="space-y-2">
    <div
      v-for="task in tasks"
      :key="task.id"
      class="flex items-center justify-between bg-white p-3 rounded border border-gray-200"
    >
      <!-- Left: Radio + Description -->
      <div class="flex items-center space-x-3 flex-1">
        <input
          type="radio"
          :checked="task.status === 'completed'"
          @change="$emit('toggle-task', task.id)"
          class="h-4 w-4"
        />

        <!-- Display mode -->
        <span
          v-if="!task.isEditing"
          :class="
            task.status === 'completed'
              ? 'line-through text-gray-400'
              : 'text-gray-700'
          "
          class="flex-1"
          @click="startEditing(task)"
        >
          {{ task.description }}
        </span>

        <!-- Edit mode -->
        <input
          v-else
          v-model="task.editingText"
          @blur="cancelEdit(task)"
          @keyup.enter="saveEdit(task)"
          @keyup.escape="cancelEdit(task)"
          type="text"
          class="flex-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
          v-focus
        />
      </div>

      <!-- Right: Actions -->
      <div class="flex items-center space-x-2">
        <!-- Edit/Save Button -->
        <button
          v-if="!task.isEditing"
          @click="startEditing(task)"
          class="text-blue-500 hover:text-blue-700 text-sm"
        >
          Edit
        </button>
        <button
          v-else
          @click="saveEdit(task)"
          class="text-green-500 hover:text-green-700 text-sm"
        >
          Save
        </button>

        <!-- Delete/Cancel Button -->
        <button
          v-if="task.isEditing"
          @click="cancelEdit(task)"
          class="text-red-500 hover:text-red-700 text-sm"
        >
          Cancel
        </button>
        <template v-else>
          <!-- Delete Confirmation -->
          <div
            v-if="taskToDelete === task.id"
            class="flex items-center space-x-2"
          >
            <span class="text-xs text-gray-600">Delete?</span>
            <button
              @click="confirmDelete(task.id)"
              class="text-red-500 hover:text-red-700 text-sm font-medium"
            >
              Yes
            </button>
            <button
              @click="cancelDelete"
              class="text-gray-500 hover:text-gray-700 text-sm"
            >
              No
            </button>
          </div>
          <!-- Normal Delete Button -->
          <button
            v-else
            @click="handleDelete(task.id)"
            class="text-red-500 hover:text-red-700 text-sm"
          >
            Delete
          </button>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref } from "vue";

  // Custom directive for auto-focus
  const vFocus = {
    mounted: (el: HTMLElement) => el.focus(),
  };

  interface Task {
    id: number;
    description: string;
    date: string;
    status: "pending" | "completed";
    priority: "low" | "medium" | "high";
    isEditing?: boolean;
    editingText?: string;
  }

  const props = defineProps<{
    tasks: Task[];
  }>();

  const emit = defineEmits<{
    (e: "toggle-task", id: number): void;
    (e: "delete-task", id: number): void;
    (e: "edit-task", payload: { id: number; description: string }): void;
  }>();

  // Store original text for cancel operation
  const originalTexts = ref<Map<number, string>>(new Map());

  // Track which task is pending deletion confirmation
  const taskToDelete = ref<number | null>(null);

  function startEditing(task: Task) {
    task.isEditing = true;
    task.editingText = task.description;
    originalTexts.value.set(task.id, task.description);
  }

  function saveEdit(task: Task) {
    console.log(task.editingText);
    if (task.editingText?.trim()) {
      emit("edit-task", {
        id: task.id,
        description: task.editingText.trim(),
      });
    }
    task.isEditing = false;
    originalTexts.value.delete(task.id);
  }

  function cancelEdit(task: Task) {
    task.isEditing = false;
    const original = originalTexts.value.get(task.id);
    if (original) {
      task.description = original;
    }
    originalTexts.value.delete(task.id);
  }

  function startDelete(taskId: number) {
    taskToDelete.value = taskId;
  }

  function confirmDelete(taskId: number) {
    emit("delete-task", taskId);
    taskToDelete.value = null;
  }

  function cancelDelete() {
    taskToDelete.value = null;
  }

  function handleDelete(taskId: number) {
    if (confirm("Are you sure you want to delete this task?")) {
      emit("delete-task", taskId);
    }
  }
</script>
