<template>
  <aside class="w-64 bg-gray-50 border-r border-gray-200 h-full p-4 overflow-y-auto">
    <div class="space-y-2">
      <!-- Today & Yesterday section -->
      <div class="mb-4">
        <ul>
          <li
            v-for="date in todayYesterdayDates"
            :key="date"
            @click="emit('select-date', date)"
            :class="[
              'cursor-pointer px-3 py-1 rounded hover:bg-black',
              selectedDate === date ? 'bg-black text-white' : 'text-gray-700'
            ]"
          >
            {{ getDisplayText(date) }}
          </li>
        </ul>
      </div>

      <!-- Other date groups -->
      <div v-for="group in otherDateGroups" :key="group.title" class="mb-4">
        <h3 class="text-sm font-medium text-gray-500 mb-1">
          {{ formatTitle(group.title) }}
        </h3>

        <ul>
          <li
            v-for="date in group.dates"
            :key="date"
            @click="emit('select-date', date)"
            :class="[
              'cursor-pointer px-3 py-1 rounded hover:bg-black',
              selectedDate === date ? 'bg-black text-white' : 'text-gray-700'
            ]"
          >
            {{ date }}
          </li>

          <!-- Empty state -->
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

interface Props {
  groupedDates: {
    recent: string[]
    lastWeek: string[]
    older: string[]
  }
  selectedDate?: string | null
}

interface Emits {
  (e: 'select-date', date: string): void
}

// Props and Emits
const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// Constants
const today = new Date().toISOString().slice(0, 10)
const yesterday = new Date(Date.now() - 86400000).toISOString().slice(0, 10)

// Computed
const todayYesterdayDates = computed(() => [today, yesterday])

const otherDateGroups = computed(() => {
  return Object.entries(props.groupedDates).map(([title, dates]) => {
    const filtered = dates.filter(d => d !== today && d !== yesterday)
    return { title, dates: filtered }
  })
})

// Methods
const formatTitle = (title: string): string => {
  const titleMap: Record<string, string> = {
    recent: 'Recent',
    lastWeek: 'Last Week',
    older: 'Older'
  }
  return titleMap[title] || title
}

const getDisplayText = (date: string): string => {
  if (date === today) return 'Today'
  if (date === yesterday) return 'Yesterday'
  return date
}
</script>