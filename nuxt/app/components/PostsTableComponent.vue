<template>
  <div class="container">
    <div class="flex justify-center">
      <div class="w-full">
        <nav class="navbar bg-gray-100 p-3 mb-4 rounded">
          <a href="/admin/blog/posts/create" class="btn btn-primary">Додати</a>
        </nav>
        <div class="card shadow-sm border border-gray-200 rounded">
          <div class="card-body p-0">
            <table class="table table-auto w-full text-left text-sm divide-y divide-gray-200">
              <thead class="bg-gray-50 text-gray-500 font-medium">
              <tr>
                <th class="p-3">#</th>
                <th class="p-3">Автор</th>
                <th class="p-3">Категорія</th>
                <th class="p-3">Заголовок</th>
                <th class="p-3">Дата публікації</th>
              </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 text-gray-700">
              <tr v-if="posts.length === 0">
                <td colspan="5" class="p-4 text-center text-gray-400 italic">
                  Завантаження статей...
                </td>
              </tr>

              <tr v-for="post in posts" :key="post.id" class="hover:bg-gray-50 transition-colors">
                <td class="p-3 font-semibold text-gray-400">#{{ post.id }}</td>

                <td class="p-3">{{ post.user?.name || 'Невідомий' }}</td>

                <td class="p-3">
                    <span class="px-2 py-0.5 text-xs bg-gray-100 rounded text-gray-600 border border-gray-200/60">
                      {{ post.category?.title || 'Без категорії' }}
                    </span>
                </td>

                <td class="p-3 max-w-md break-words">
                  <a :href="'/admin/blog/posts/' + post.id + '/edit'" class="text-blue-600 hover:underline font-medium">
                    {{ post.title }}
                  </a>
                </td>

                <td class="p-3 text-gray-500">
                  {{ post.published_at ? post.published_at.split(' ')[0] : 'Не опубліковано' }}
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const posts = ref<any[]>([])

const getPosts = () => {

  $fetch<any>('http://localhost/api/admin/blog/posts')
    .then(response => {
      console.log('Дані з сервера:', response)

      posts.value = response?.data || []
    })
    .catch(error => {
      console.error('Помилка запиту:', error)
    })
}


onMounted(() => {
  getPosts()
})
</script>
