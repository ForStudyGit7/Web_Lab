import { defineStore } from 'pinia'

export const useSubscriptionStore = defineStore('subscription', () => {
  // --- STATE (Стан) ---
  const selectedPlan = ref<any>(null)
  const isAnnual = ref(true)
  const userData = ref<any>(null)
  const isPending = ref(false)

  // --- ACTIONS (Дії) ---
  function setSubscription(plan: any, annual: boolean) {
    selectedPlan.value = plan
    isAnnual.value = annual
  }

  // Асинхронна дія для завантаження даних (Крок 4)
  async function fetchUserProfile() {
    isPending.value = true
    try {
      // Імітація запиту до API Github (як у прикладі лаби)
      const data = await $fetch('https://api.github.com/users/octocat')
      userData.value = data
    } catch (error) {
      console.error('Помилка завантаження профілю:', error)
    } finally {
      isPending.value = false
    }
  }

  return {
    selectedPlan,
    isAnnual,
    userData,
    isPending,
    setSubscription,
    fetchUserProfile
  }
})
