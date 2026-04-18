<script setup>
// 1. Підключаємо стор
const subscriptionStore = useSubscriptionStore()

useHead({
  titleTemplate: (titleChunk) => {
    return titleChunk ? `${titleChunk}` : 'Список продуктів'
  },
  script: [
    { src: 'https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js' }
  ],
  htmlAttrs: { lang: 'uk' }
})
</script>

<template>
  <UApp>
    <UHeader>
      <template #left>
        <NuxtLink to="/" class="flex items-center gap-2">
          <AppLogo class="w-auto h-6 shrink-0" />
        </NuxtLink>

        <div class="hidden lg:flex items-center gap-4 ml-8">
          <UButton to="/" variant="ghost" color="neutral">Список продуктів</UButton>
          <UButton to="/products" variant="ghost" color="neutral">Таблиця</UButton>
          <UButton to="/store" variant="ghost" color="primary" icon="i-ph-user-circle-bold">Профіль (Pinia)</UButton>

          <div v-if="subscriptionStore.selectedPlan"
               class="flex items-center gap-2 px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-full ml-2">
            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
            <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">
              Active: {{ subscriptionStore.selectedPlan.name.split(' ')[0] }}
              ({{ subscriptionStore.isAnnual ? 'Annual' : 'Monthly' }})
            </span>
          </div>
        </div>
      </template>

      <template #right>
        <div class="flex items-center gap-2">
          <TemplateMenu />

          <UColorModeButton />
          <UButton
            to="https://github.com/"
            target="_blank"
            icon="i-simple-icons-github"
            color="neutral"
            variant="ghost"
          />
        </div>
      </template>
    </UHeader>

    <UMain>
      <NuxtPage />
    </UMain>

    <USeparator icon="i-simple-icons-nuxtdotjs" />

    <UFooter>
      <template #left>
        <p class="text-sm text-muted">
          Побудовано на Nuxt UI • © {{ new Date().getFullYear() }}
        </p>
      </template>
    </UFooter>
  </UApp>
</template>
