import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useAuthStore } from './auth'

export interface Task {
  id: number
  description: string
  date: string
  status: 'pending' | 'completed'
  priority: 'low' | 'medium' | 'high'
}

export const useTasksStore = defineStore('tasks', () => {
  const allDates = ref<string[]>([])
  const allTasks = ref<Task[]>([])

  const fetchTasks = async (apiBase: string) => {
    try {
      const auth = useAuthStore()
      const token = auth.token

      const response = await fetch(`${apiBase}/api/tasks`, {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.data && Array.isArray(data.data)) {
          const dates = data.data
            .map((task: any) => task.date || null)
            .filter((d: any) => d !== null)
            .sort()
            .reverse()

          allDates.value = Array.from(new Set(dates as string[]))
          allTasks.value = data.data
          return
        }
      }

      // fallback: fill sample dates
      showSampleDates()
    } catch (error) {
      console.error('Error fetching tasks in store:', error)
      showSampleDates()
    }
  }

  const addTask = async (apiBase: string, payload: any) => {
    console.log('Adding task with payload:', payload)
    try {
      const auth = useAuthStore()
      const token = auth.token

      const response = await fetch(`${apiBase}/api/tasks`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      })

      if (response.ok) {
        // refresh
        await fetchTasks(apiBase)
        return true
      }

      console.error('Failed to add task:', response.status)
      return false
    } catch (error) {
      console.error('Error adding task in store:', error)
      return false
    }
  }

  const showSampleDates = () => {
    const today = new Date().toISOString().split('T')[0]
    const dates: string[] = []
    for (let i = 0; i < 15; i++) {
      const date = new Date()
      date.setDate(date.getDate() - i)
      dates.push(date.toISOString().split('T')[0]!)
    }
    allDates.value = dates
  }

  const getTasksByDate = (date: string) => {
    return allTasks.value.filter((t) => t.date === date)
  }

  const groupedDates = computed(() => {
    const todayVal = new Date().toISOString().split('T')[0] || ''
    const oneWeekAgo = new Date(todayVal + 'T00:00:00')
    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7)

    const recent: string[] = []
    const lastWeek: string[] = []
    const older: string[] = []

    allDates.value.forEach((dateString) => {
      if (dateString === todayVal) return
      const date = new Date(dateString + 'T00:00:00')

      if (date > oneWeekAgo && date < new Date(todayVal + 'T00:00:00')) {
        recent.push(dateString)
      } else if (date >= oneWeekAgo && date <= new Date(todayVal + 'T00:00:00')) {
        lastWeek.push(dateString)
      } else {
        older.push(dateString)
      }
    })

    return { recent, lastWeek, older }
  })

  return {
    allDates,
    allTasks,
    fetchTasks,
    addTask,
    showSampleDates,
    getTasksByDate,
    groupedDates
  }
})
