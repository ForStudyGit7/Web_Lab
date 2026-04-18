import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', () => {
  // --- STATE (Стан) ---
  const name = ref('Гість')
  const isLoggedIn = ref(false)
  const loginTime = ref<string | null>(null)
  const profileData = ref<any>(null)
  const isLoading = ref(false)

  // --- GETTERS (Гетери) ---
  const welcomeMessage = computed(() => `Вітаємо, ${name.value}!`)

  // --- ACTIONS (Дії) ---
  function login(userName: string) {
    name.value = userName
    isLoggedIn.value = true
    loginTime.value = new Date().toLocaleTimeString()
  }

  function logout() {
    name.value = 'Гість'
    isLoggedIn.value = false
    loginTime.value = null
    profileData.value = null
  }

  // Асинхронний запит (Крок 4)
  async function fetchProfile() {
    isLoading.value = true
    try {
      // Приклад запиту через вбудований $fetch
      const response = await $fetch('https://api.github.com/users/octocat')
      profileData.value = response
    } catch (error) {
      console.error('Error fetching profile:', error)
    } finally {
      isLoading.value = false
    }
  }

  return {
    name,
    isLoggedIn,
    loginTime,
    profileData,
    isLoading,
    welcomeMessage,
    login,
    logout,
    fetchProfile
  }
})
