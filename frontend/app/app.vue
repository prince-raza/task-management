<template>
  <div class="flex flex-col h-screen" v-if="isAuthReady">
    <!-- Login Screen -->
    <AuthLoginForm
      v-if="!isAuthenticated"
      :is-logging-in="isLoggingIn"
      @submit="handleLogin"
    />

    <!-- Main App -->
    <template v-else>
      <LayoutAppHeader
        :user-name="currentUser?.name"
        @logout="handleLogout"
        @search="handleSearch"
      />

      <div class="flex flex-1 bg-gray-50">
        <LayoutAppSideBar
          :grouped-dates="groupedDates"
          :selected-date="selectedDate"
          @select-date="selectDate"
        />

        <main class="flex-1 flex flex-col p-8 bg-gray-50">
          <div v-if="displayedTasks.length > 0 || searchQuery" class="flex-1">
            <div class="relative max-w-2xl mx-auto w-full">
              <!-- Search Results Header -->
              <TaskSearch
                v-if="searchQuery"
                :search-query="searchQuery"
                :task-count="filteredTasks.length"
                @clear-search="clearSearch"
              />

              <TaskList
                :tasks="displayedTasks"
                @toggle-task="handleToggleTask"
                @delete-task="handleDeleteTask"
                @edit-task="handleEditTask"
                @re-order="saveOrder"
              />
            </div>
          </div>

          <TaskInput
            @add-task="handleTaskSubmit"
            :key="searchQuery"
            :isEmptyList="displayedTasks.length === 0 && !searchQuery"
          />
        </main>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useRuntimeConfig } from "nuxt/app";
import { useAuthStore } from "../stores/auth";
import { useTasksStore } from "../stores/tasks";
import type { Task } from "../stores/tasks";
import { useDate } from "./composables/useDate";

// Configuration
const config = useRuntimeConfig();
const apiBase = config.public.apiBase as string;

// Store instances
const auth = useAuthStore();
const tasks = useTasksStore();

// Composables
const { getToday } = useDate();

// Refs
const searchQuery = ref("");
const selectedDate = ref<string | null>(null);
const isAuthReady = ref(false);

// Computed properties
const isAuthenticated = computed(() => auth.isAuthenticated());
const isLoggingIn = computed(() => auth.isLoggingIn);
const currentUser = computed(() => auth.user);
const groupedDates = computed(() => tasks.groupedDates);

const filteredTasks = computed(() => {
  if (!searchQuery.value.trim()) return [];
  
  const query = searchQuery.value.toLowerCase().trim();
  return tasks.allTasks.filter(task => 
    task.description.toLowerCase().includes(query)
  );
});

const tasksForSelectedDate = computed(() => {
  if (!selectedDate.value) return [];
  return tasks.getTasksByDate(selectedDate.value);
});

const displayedTasks = computed(() => {
  return searchQuery.value ? filteredTasks.value : tasksForSelectedDate.value;
});

// Methods
const selectDate = (date: string) => {
  selectedDate.value = date;
  clearSearch();
};

const handleSearch = (query: string) => {
  searchQuery.value = query;
};

const clearSearch = () => {
  searchQuery.value = "";
};

const handleLogin = async (credentials: { email: string; password: string }) => {
  auth.isLoggingIn = true;

  try {
    const response = await fetch(`${apiBase}/api/login`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(credentials),
    });

    const data = await response.json();

    if (response.ok && data.token) {
      auth.setAuth(data.token, data.user);
      selectedDate.value = getToday();
      await tasks.fetchTasks(apiBase);
    }
  } catch (error) {
    console.error("Login error:", error);
  } finally {
    auth.isLoggingIn = false;
  }
};

const handleLogout = () => {
  auth.clearAuth();
  tasks.reset();
  selectedDate.value = null;
  clearSearch();
};

const handleTaskSubmit = async (taskDescription: string) => {
  const payload = {
    description: taskDescription,
    date: selectedDate.value || getToday(),
    order: 1,
    status: "in_progress",
    priority: "medium",
  };

  await tasks.addTask(apiBase, payload);
};

const handleToggleTask = async (taskId: number) => {
  await tasks.toggleTaskStatus(apiBase, taskId);
};

const handleDeleteTask = async (taskId: number) => {
  await tasks.deleteTask(apiBase, taskId);
};

const handleEditTask = async (payload: { id: number; description: string }) => {
  console.log("is Edited");
  await tasks.editTaskDescription(apiBase, payload);
};

const saveOrder = async (newList: Task[]) => {
  await tasks.saveTaskOrder(apiBase, newList);
};

// Lifecycle
onMounted(async () => {
  await auth.init();
  isAuthReady.value = true;

  if (auth.isAuthenticated()) {
    selectedDate.value = getToday();
    await tasks.fetchTasks(apiBase);
  }
});
</script>