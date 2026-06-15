import { defineStore } from 'pinia'

export const useSubscriptionStore = defineStore('subscription', () => {

  const selectedPlan = ref<any>(null)
  const isAnnual = ref(true)
  const userData = ref<any>(null)
  const isPending = ref(false)

  function setSubscription(plan: any, annual: boolean) {
    selectedPlan.value = plan
    isAnnual.value = annual
  }


  async function fetchUserProfile() {
    isPending.value = true
    try {

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
