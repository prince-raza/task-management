<template>
  <div class="flex flex-col h-screen">
    <!-- Login Screen -->
    <AuthLoginForm
      v-if="!isAuthenticated"
      :is-logging-in="isLoggingIn"
      :error="loginError"
      @submit="handleLogin"
    />

    <!-- Main App -->
    <template v-else>
      <LayoutAppHeader :user-name="currentUser?.name" @logout="handleLogout" />

      <div class="flex flex-1 bg-gray-50">
        <LayoutAppSideBar
          :grouped-dates="groupedDates"
          :selected-date="selectedDate"
          @select-date="selectDate"
        />

        <main class="flex-1 flex flex-col p-8 bg-gray-50">
          <div class="flex-1">
            <div class="max-w-3xl">
              <TaskList
                :tasks="tasksForSelectedDate"
                @toggle-task="handleToggleTask"
                @delete-task="handleDeleteTask"
                @edit-task="handleEditTask"
                @cancel-edit="handleCancelEdit"
              />
            </div>
          </div>

          <TaskInput ref="taskInputRef" @add-task="handleTaskSubmit" />
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
  import { useDate } from "./composables/useDate";

  const taskInputRef = ref();

  // Configuration
  const config = useRuntimeConfig();
  const apiBase = config.public.apiBase as string;

  // Store instances
  const auth = useAuthStore();
  const tasks = useTasksStore();

  // Composables
  const { getToday, formatDate } = useDate();

  // Computed properties
  const isAuthenticated = computed(() => auth.isAuthenticated());
  const isLoggingIn = computed(() => auth.isLoggingIn);
  const loginError = computed(() => auth.loginError);
  const currentUser = computed(() => auth.user);
  const groupedDates = computed(() => tasks.groupedDates);

  // Tasks for selected date
  const selectedDate = ref<string | null>(null);
  const tasksForSelectedDate = computed(() => {
    if (!selectedDate.value) return [];
    return tasks.getTasksByDate(selectedDate.value);
  });

  // Page title based on selected date
  const pageTitle = computed(() => {
    if (!selectedDate.value) return "Select a date";
    return formatDate(selectedDate.value);
  });

  // Methods
  const selectDate = (date: string) => {
    selectedDate.value = date;
  };

  // Lifecycle
  onMounted(async () => {
    auth.init();
    if (auth.isAuthenticated()) {
      selectedDate.value = getToday();
      await tasks.fetchTasks(apiBase);
    }
  });

  /**
   * Handle user login
   */
  const handleLogin = async (credentials: {
    email: string;
    password: string;
  }) => {
    auth.isLoggingIn = true;
    auth.loginError = "";

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
      } else {
        auth.loginError = data.message || "Login failed";
      }
    } catch (error) {
      console.error("Login error:", error);
      auth.loginError = "An error occurred during login";
    } finally {
      auth.isLoggingIn = false;
    }
  };

  /**
   * Handle user logout
   */
  const handleLogout = () => {
    auth.clearAuth();
    tasks.allDates = [];
    selectedDate.value = null;
  };

  /**
   * Handle task submission
   */
  const handleTaskSubmit = async (taskDescription: string) => {
    const payload = {
      description: taskDescription,
      date: selectedDate.value || getToday(),
      status: "in_progress",
      priority: "medium",
    };

    const ok = await tasks.addTask(apiBase, payload);
    if (ok) {
      // Task was added successfully and dates were refreshed
    }
  };

  /**
   * Handle task toggle (mark as complete/incomplete)
   */
  const handleToggleTask = async (taskId: number) => {
    try {
      const token = auth.token;
      const task = tasks.allTasks.find((t) => t.id === taskId);
      if (!task) return;

      const newStatus = task.status === "completed" ? "pending" : "completed";

      const res = await fetch(`${apiBase}/api/tasks/${taskId}`, {
        method: "PATCH",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ status: newStatus }),
      });

      if (res.ok) {
        await tasks.fetchTasks(apiBase);
      } else {
        console.error("Failed to toggle task", res.status);
      }
    } catch (error) {
      console.error("Error toggling task:", error);
    }
  };

  /**
   * Handle task deletion
   */
  const handleDeleteTask = async (taskId: number) => {
    try {
      const token = auth.token;
      const res = await fetch(`${apiBase}/api/tasks/${taskId}`, {
        method: "DELETE",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      });

      if (res.ok) {
        await tasks.fetchTasks(apiBase);
      } else {
        console.error("Failed to delete task", res.status);
      }
    } catch (error) {
      console.error("Error deleting task:", error);
    }
  };

  // Handle edit task
  const handleEditTask = async (payload: {
    id: number;
    description: string;
  }) => {
    console.log("Editing task with ID:", payload.id);
    try {
      const token = auth.token;
      const res = await fetch(`${apiBase}/api/tasks/${payload.id}`, {
        method: "PATCH",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ description: payload.description }),
      });

      if (res.ok) {
        await tasks.fetchTasks(apiBase);
      } else {
        console.error("Failed to edit task", res.status);
      }
    } catch (error) {
      console.error("Error editing task:", error);
    }
  };

  // Handle starting edit from task list
  const handleStartEdit = (task: { id: number; description: string }) => {
    taskInputRef.value?.startEdit(task);
  };

  // Handle cancel edit
  const handleCancelEdit = () => {
    // Optional: any cleanup needed when edit is cancelled
  };
</script>
