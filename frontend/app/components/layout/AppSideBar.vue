<template>
  <aside class="w-64 bg-gray-50 border-r border-gray-200 h-full p-4 overflow-y-auto">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Dates</h2>

    <div class="space-y-2">
      <!-- Today & Tomorrow section (no heading) -->
      <div class="mb-4">
        <ul>
          <li
            v-for="date in todayTomorrowDates"
            :key="date"
            @click="$emit('select-date', date)"
            :class="[
              'cursor-pointer px-3 py-1 rounded hover:bg-gray-200',
              selectedDate === date ? 'bg-blue-500 text-white' : 'text-gray-700'
            ]"
          >
            {{ getDisplayText(date) }}
          </li>
        </ul>
      </div>

      <!-- Other date groups with headings -->
      <div v-for="group in otherDateGroups" :key="group.title" class="mb-4">
        <h3 class="text-sm font-medium text-gray-500 uppercase mb-1">
          {{ formatTitle(group.title) }}
        </h3>

        <ul>
          <li
            v-for="date in group.dates"
            :key="date || 'empty'"
            @click="$emit('select-date', date)"
            :class="[
              'cursor-pointer px-3 py-1 rounded hover:bg-gray-200',
              selectedDate === date ? 'bg-blue-500 text-white' : 'text-gray-700'
            ]"
          >
            {{ date || '-' }}
          </li>

          <!-- Show placeholder if group is empty -->
          <li v-if="group.dates.length === 0" class="px-3 py-1 text-gray-400 italic">
            -
          </li>
        </ul>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  groupedDates: {
    recent: string[]
    lastWeek: string[]
    older: string[]
  }
  selectedDate?: string | null
}>()

defineEmits<{
  (e: 'select-date', date: string): void
}>()

// Today's date in YYYY-MM-DD
const today = new Date().toISOString().slice(0, 10)

// Tomorrow's date in YYYY-MM-DD
const tomorrow = new Date()
tomorrow.setDate(tomorrow.getDate() + 1)
const tomorrowStr = tomorrow.toISOString().slice(0, 10)

// Format titles
function formatTitle(title: string) {
  const map: Record<string, string> = {
    recent: 'Recent',
    lastWeek: 'Last Week',
    older: 'Older'
  }
  return map[title] || title
}

// Get display text for dates
function getDisplayText(date: string) {
  if (date === today) return 'Today'
  if (date === tomorrowStr) return 'Tomorrow'
  return date
}

// Today & Tomorrow dates
const todayTomorrowDates = computed(() => [today, tomorrowStr])

// Other date groups (excluding today if present)
const otherDateGroups = computed(() => {
  return Object.entries(props.groupedDates).map(([title, dates]) => {
    const filtered = dates.filter(d => d !== today && d !== tomorrowStr)
    return { title, dates: filtered }
  })
})
</script>