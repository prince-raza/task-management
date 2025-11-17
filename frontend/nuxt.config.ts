// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  modules: ['@nuxtjs/tailwindcss'],
  runtimeConfig: {
    public: {
      apiBase: 'http://localhost'
    }
  },
  app: {
    head: {
      title: 'Task Management App',
      bodyAttrs: {
        class: 'white bg-gray-100 dark:bg-gray-900'
      }
    }
  }
})