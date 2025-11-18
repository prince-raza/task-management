<template>
  <form @submit.prevent="submitTask" class="flex items-center space-x-2">
    <input
      v-model="taskText"
      type="text"
      :placeholder="placeholder"
      class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
    />

    <button
      type="submit"
      class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
    >
      {{ buttonText }}
    </button>

    <button
      v-if="isEditing"
      @click="cancelEdit"
      type="button"
      class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
    >
      Cancel
    </button>
  </form>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue' // Added computed import

const taskText = ref('')
const isEditing = ref(false)
const currentEditId = ref<number | null>(null)

const emit = defineEmits<{
  (e: 'add-task', task: string): void
  (e: 'edit-task', payload: { id: number; description: string }): void
  (e: 'cancel-edit'): void
}>()

const placeholder = computed(() => 
  isEditing.value ? 'Edit task...' : 'Add a new task...'
)

const buttonText = computed(() => 
  isEditing.value ? 'Update' : 'Add'
)

function submitTask() {
  if (taskText.value.trim() === '') return
  
  if (isEditing.value && currentEditId.value !== null) {
    emit('edit-task', { 
      id: currentEditId.value, 
      description: taskText.value.trim() 
    })
    resetForm()
  } else {
    emit('add-task', taskText.value.trim())
    taskText.value = ''
  }
}

function cancelEdit() {
  resetForm()
  emit('cancel-edit')
}

function resetForm() {
  taskText.value = ''
  isEditing.value = false
  currentEditId.value = null
}

// Function to start editing from parent
function startEdit(task: { id: number; description: string }) {
  taskText.value = task.description
  isEditing.value = true
  currentEditId.value = task.id
}

// Expose the startEdit function to parent
defineExpose({
  startEdit
})
</script>