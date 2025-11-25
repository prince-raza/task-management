<template>
  <div class="space-y-2">
    <draggable
      :list="localTasks"
      @change="onDragChange"
      item-key="id"
      animation="150"
    >
      <template #item="{ element: task }">
        <div
          class="flex items-center justify-between bg-white p-3 rounded border border-gray-200"
        >
          <!-- Drag handle -->
          <div
            class="drag-handle cursor-move mr-2 text-gray-400 hover:text-gray-600"
          >
            <GripVertical color="black" :size="17"/>
          </div>

          <!-- Left: Radio + Description -->
          <div class="flex items-center space-x-3 flex-1">
            <input
              type="radio"
              :checked="task.status === 'completed'"
              @change="$emit('toggle-task', task.id)"
              class="h-4 w-4"
            />

            <span
              v-if="!task.isEditing"
              :class="[
                'flex-1 cursor-text',
                task.status === 'completed' 
                  ? 'line-through text-gray-400' 
                  : 'text-gray-700'
              ]"
              @click="startEditing(task)"
            >
              {{ task.description }}
            </span>

            <input
              v-else
              v-model="task.editingText"
              @blur="cancelEdit(task)"
              @keyup.enter="saveEdit(task)"
              @keyup.escape="cancelEdit(task)"
              type="text"
              class="flex-1 px-2 py-1 border border-gray-300 rounded"
              v-focus
            />
          </div>

          <!-- Right: Actions -->
          <div class="flex items-center space-x-2">
            <button
              v-if="!task.isEditing"
              @click="startEditing(task)"
              class="text-black text-sm"
            >
              <SquarePen :size="20" />
            </button>
            <button
              v-else
              @mousedown.prevent
              @click="saveEdit(task)"
              class="text-black text-sm"
            >
              <Save :size="20" />
            </button>

            <button
              v-if="task.isEditing"
              @mousedown.prevent
              @click="cancelEdit(task)"
              class="text-black text-sm"
            >
              <Ban :size="20" />
            </button>
            <button
              v-else
              @click="handleDelete(task.id)"
              class="text-black text-sm"
            >
              <Trash :size="20"/>
            </button>
          </div>
        </div>
      </template>
    </draggable>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from "vue";
import draggable from "vuedraggable";
import { Ban, GripVertical, Save, SquarePen, Trash } from 'lucide-vue-next';

interface Task {
  id: number;
  description: string;
  date: string;
  status: "pending" | "completed";
  priority: "low" | "medium" | "high";
  isEditing?: boolean;
  editingText?: string;
}

interface Emits {
  (e: "toggle-task", id: number): void;
  (e: "delete-task", id: number): void;
  (e: "edit-task", payload: { id: number; description: string }): void;
  (e: "re-order", newList: Task[]): void;
}

// Custom directive for auto-focus
const vFocus = {
  mounted: (el: HTMLElement) => el.focus(),
};

// Props and Emits
const props = defineProps<{
  tasks: Task[];
}>();

const emit = defineEmits<Emits>();

// Reactive state
const localTasks = ref([...props.tasks]);
const originalTexts = ref<Map<number, string>>(new Map());

// Watchers
watch(
  () => props.tasks,
  (newVal) => {
    localTasks.value = [...newVal];
  }
);

// Methods
const onDragChange = (element: any) => {
  if (element.moved) {
    emit("re-order", localTasks.value);
  }
};

const startEditing = (task: Task) => {
  task.isEditing = true;
  task.editingText = task.description;
  originalTexts.value.set(task.id, task.description);
};

const saveEdit = (task: Task) => {
  console.log("EDITTTT");
  if (task.editingText?.trim()) {
    emit("edit-task", {
      id: task.id,
      description: task.editingText.trim(),
    });
  }
  task.isEditing = false;
  originalTexts.value.delete(task.id);
};

const cancelEdit = (task: Task) => {
  task.isEditing = false;
  const original = originalTexts.value.get(task.id);
  if (original) {
    task.description = original;
  }
  originalTexts.value.delete(task.id);
};

const handleDelete = (taskId: number) => {
  if (confirm("Are you sure you want to delete this task?")) {
    emit("delete-task", taskId);
  }
};
</script>