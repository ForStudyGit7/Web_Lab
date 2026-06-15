<template>
  <section class="max-w-[800px] mx-auto pb-20 pt-10 px-6">
    <div class="mb-6">
      <NuxtLink
        to="/BlogPostsUi"
        class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors no-underline"
      >
        <UIcon name="i-heroicons-arrow-left" />
        Назад до панелі статей
      </NuxtLink>
    </div>

    <div v-if="pending" class="flex flex-col justify-center items-center py-32 bg-white rounded-xl border border-gray-200 shadow-sm">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-500 mb-4"></div>
      <p class="text-gray-500 font-medium">Завантаження статті...</p>
    </div>

    <div v-else-if="error" class="bg-white border border-gray-200 rounded-xl p-12 text-center shadow-sm">
      <UIcon name="i-heroicons-exclamation-triangle" class="text-4xl text-red-500 mb-2" />
      <h3 class="text-lg font-bold text-gray-800">Статтю не знайдено</h3>
      <p class="text-gray-500 text-sm mt-1">Можливо, ID статті не існує або виникла помилка з'єднання з API.</p>
    </div>

    <article v-else-if="post" class="bg-white border border-gray-200 rounded-xl shadow-sm p-8 overflow-hidden">
      <div class="flex items-center gap-4 mb-4 text-xs font-semibold text-gray-400">
        <span class="px-2.5 py-1 bg-green-50 text-green-700 rounded-md border border-green-100/60">
          {{ post.category?.title || 'Без категорії' }}
        </span>
        <span>•</span>
        <span>ID: #{{ post.id }}</span>
        <span>•</span>
        <span>{{ post.published_at ? post.published_at.split(' ')[0] : 'Чернетка' }}</span>
      </div>

      <h1 class="text-3xl font-bold text-gray-900 tracking-tight mb-6">
        {{ post.title }}
      </h1>

      <div class="flex items-center gap-3 border-y border-gray-100 py-4 mb-6">
        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold uppercase">
          {{ post.user?.name ? post.user.name[0] : 'Н' }}
        </div>
        <div>
          <p class="text-sm font-semibold text-gray-800">{{ post.user?.name || 'Невідомий автор' }}</p>
          <p class="text-xs text-gray-400">Автор блогу</p>
        </div>
      </div>

      <div class="text-gray-700 leading-relaxed space-y-4 whitespace-pre-line">
        {{ post.content || post.text || 'Вміст статті порожній.' }}
      </div>
    </article>
  </section>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router'

const route = useRoute()
// Витягуємо динамічний id з адреси сторінки
const postId = route.params.id

// Робимо чистий запит до твого Laravel-бекенду
const { data: post, pending, error } = await useFetch<any>(`http://localhost/api/admin/blog/posts/${postId}`, {
  lazy: true,
  server: false
})
</script>
