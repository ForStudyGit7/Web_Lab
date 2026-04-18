<script setup>
import { storeToRefs } from 'pinia'

// Отримуємо екземпляр стору
const userStore = useUserStore()

// Деструктуризація зі збереженням реактивності (як у методичці)
const { name, isLoggedIn, welcomeMessage, profileData, isLoading } = storeToRefs(userStore)

const inputName = ref('')

const handleLogin = () => {
  if (inputName.value) {
    userStore.login(inputName.value)
    inputName.value = ''
  }
}
</script>

<template>
  <div class="max-w-2xl mx-auto py-20 px-6 font-sans">
    <div class="bg-white border border-gray-100 shadow-xl rounded-3xl p-10">
      <h1 class="text-3xl font-black text-gray-900 mb-2">{{ welcomeMessage }}</h1>
      <p class="text-gray-400 text-sm mb-8 italic">Демонстрація керування станом (Pinia)</p>

      <div v-if="!isLoggedIn" class="space-y-4">
        <input
          v-model="inputName"
          placeholder="Введіть ваше ім'я"
          class="w-full border border-gray-200 rounded-xl px-4 py-3 outline-none focus:border-emerald-400 transition-all"
        />
        <button
          @click="handleLogin"
          class="w-full bg-emerald-500 text-white font-bold py-3 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-100"
        >
          Увійти
        </button>
      </div>

      <div v-else class="space-y-6">
        <div class="p-4 bg-emerald-50 rounded-2xl text-emerald-700 text-sm border border-emerald-100">
          Ви зайшли о: <span class="font-bold">{{ userStore.loginTime }}</span>
        </div>

        <div class="flex gap-3">
          <button
            @click="userStore.fetchProfile"
            class="flex-1 bg-gray-900 text-white font-bold py-3 rounded-xl hover:bg-gray-800 transition-all"
          >
            {{ isLoading ? 'Завантаження...' : 'Завантажити GitHub API' }}
          </button>
          <button
            @click="userStore.logout"
            class="px-6 bg-red-50 text-red-500 font-bold py-3 rounded-xl hover:bg-red-100 transition-all"
          >
            Вийти
          </button>
        </div>

        <div v-if="profileData" class="mt-8 p-6 border border-gray-100 rounded-2xl bg-gray-50 text-left">
          <div class="flex items-center gap-4 mb-4">
            <img :src="profileData.avatar_url" class="w-12 h-12 rounded-full border-2 border-white shadow-sm" />
            <div>
              <p class="font-bold text-gray-900">{{ profileData.name }}</p>
              <p class="text-xs text-gray-400">@{{ profileData.login }}</p>
            </div>
          </div>
          <p class="text-xs text-gray-500 leading-relaxed">{{ profileData.bio }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
