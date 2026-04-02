<script setup>
useHead({
  // Цей шаблон автоматично додаватиме назву сторінки до вкладки браузера
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
        </div>
      </template>

      <template #right>
        <UColorModeButton />
        <UButton
          to="https://github.com/"
          target="_blank"
          icon="i-simple-icons-github"
          color="neutral"
          variant="ghost"
        />
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
