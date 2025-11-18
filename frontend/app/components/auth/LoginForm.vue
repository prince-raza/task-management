<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="max-w-sm w-full bg-white rounded-lg shadow-sm p-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <div
          class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mx-auto mb-2"
        >
          <ListTodo class="w-6 h-6 text-white" />
        </div>
        <h2 class="text-2xl font-semibold text-gray-900 mb-2">Sign In</h2>
        <p class="text-gray-600 text-sm">Login to continue using this app</p>
      </div>

      <!-- Login Form -->
      <form class="space-y-5" @submit.prevent="handleSubmit">
        <!-- Email Input -->
        <div>
          <label
            for="email"
            class="block text-sm font-medium text-gray-700 mb-1"
            >Email</label
          >
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Enter your email"
          />
        </div>

        <!-- Password Input -->
        <div>
          <div class="flex justify-between mb-1">
            <label
              for="password"
              class="block text-sm font-medium text-gray-700"
              >Password</label
            >
            <a href="#" class="text-sm text-black hover:text-blue-500"
              >Forgot your password?</a
            >
          </div>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Enter your password"
          />
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="isLoggingIn"
          class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-black hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <span v-if="isLoggingIn" class="flex items-center">
            <svg
              class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            Signing in...
          </span>
          <span v-else>Login</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue';
import { ListTodo } from "lucide-vue-next";

interface Props {
  isLoggingIn?: boolean;
}

interface Emits {
  (event: "submit", credentials: { email: string; password: string }): void;
}

// Props and Emits
const props = withDefaults(defineProps<Props>(), {
  isLoggingIn: false,
});

const emit = defineEmits<Emits>();

// Form state
const form = reactive({
  email: "",
  password: "",
});

/**
 * Handle form submission
 */
const handleSubmit = () => {
  emit("submit", {
    email: form.email,
    password: form.password,
  });
};
</script>