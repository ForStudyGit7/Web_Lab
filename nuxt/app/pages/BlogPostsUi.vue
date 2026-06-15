<template>
  <section class="max-w-[1200px] mx-auto pb-20 pt-10 px-6">
    <h2 class="text-[28px] font-bold text-gray-800 tracking-tight">Панель статей</h2>

    <div class="flex justify-between items-center mb-4 text-sm font-normal text-gray-500 mt-4">
      <div>{{ selectedIds.length }} вибрано</div>

      <div class="flex items-center gap-6">
        <input
          v-model="searchInput"
          type="text"
          placeholder="Пошук за заголовком..."
          class="w-56 pl-3 pr-4 py-1.5 bg-white border border-gray-200 rounded-md outline-none focus:border-emerald-500 transition-colors shadow-sm"
        >
        <button class="flex items-center gap-2 text-gray-900 font-semibold hover:text-gray-700 transition-colors">
          <UIcon name="i-heroicons-bars-3-bottom-left" class="text-lg" />
          Сортування
        </button>
        <button class="flex items-center gap-2 text-gray-900 font-semibold hover:text-gray-700 transition-colors">
          <UIcon name="i-heroicons-ellipsis-vertical" class="text-xl" />
          Дії
        </button>

        <UButton
          to="/admin/posts/create"
          icon="i-heroicons-plus"
          color="primary"
          variant="solid"
        >
          Створити
        </UButton>
      </div>
    </div>

    <div v-if="pending || status === 'idle'" class="flex flex-col justify-center items-center py-32 bg-white rounded-xl border border-gray-200 mt-4 shadow-sm relative overflow-hidden">
      <div class="absolute h-[6px] top-0 left-0 right-0 bg-gradient-to-r from-green-400 to-cyan-400"></div>
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-500 mb-4"></div>
      <p class="text-gray-500 font-medium">Оновлення списку статей...</p>
    </div>

    <div v-else>
      <div class="bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col mt-4 max-w-full overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm table-auto min-w-[900px]">
            <thead class="bg-gray-50 text-gray-500 border-b border-gray-200">
            <tr>
              <th class="px-3 py-3 w-12 font-medium">
                <div class="flex items-center gap-1">
                  <input
                    type="checkbox"
                    v-model="selectAll"
                    class="w-[18px] h-[18px] rounded border-gray-300 accent-emerald-500 cursor-pointer"
                  >
                  <UIcon name="i-heroicons-chevron-down" class="text-xs cursor-pointer hover:text-gray-800" />
                </div>
              </th>
              <th class="px-3 py-3 w-16 font-normal">ID</th>
              <th class="px-3 py-3 font-normal">Заголовок</th>
              <th class="px-3 py-3 font-normal w-40">Категорія</th>
              <th class="px-3 py-3 font-normal w-44">Автор</th>
              <th class="px-3 py-3 font-normal text-right w-36">Дата публікації</th>
              <th class="px-3 py-3 font-normal w-12 text-center">Дії</th>
            </tr>
            </thead>

            <tbody class="text-gray-700 divide-y divide-gray-100">
            <tr v-if="paginatedPosts.length === 0">
              <td colspan="7" class="p-12 text-center text-gray-400">Статей за такому запитом не знайдено.</td>
            </tr>

            <tr v-for="post in paginatedPosts" :key="post.id" class="hover:bg-gray-50 transition-colors cursor-pointer group">
              <td class="px-3 py-3" @click.stop>
                <input
                  type="checkbox"
                  :value="post.id"
                  v-model="selectedIds"
                  class="w-[18px] h-[18px] rounded border-gray-300 accent-emerald-500 cursor-pointer"
                >
              </td>
              <td class="px-3 py-3 font-semibold text-gray-400">
                #{{ post.id }}
              </td>
              <td class="px-3 py-3 whitespace-normal break-words max-w-md">
                <NuxtLink :to="`/admin/posts/${post.id}`" class="text-gray-800 hover:text-gray-900 font-semibold no-underline hover:underline">
                  {{ post.title }}
                </NuxtLink>
              </td>

              <td class="px-3 py-3 text-gray-600 text-[13px] whitespace-normal">
                <span class="px-2.5 py-1 text-xs font-semibold bg-green-50 text-green-700 rounded-md border border-green-100/60 inline-block max-w-[150px] truncate" :title="post.category?.title">
                  {{ post.category?.title || 'Без категорії' }}
                </span>
              </td>

              <td class="px-3 py-3 text-gray-600 font-medium text-[13px] whitespace-normal truncate max-w-[160px]" :title="post.user?.name">
                {{ post.user?.name || 'Невідомий' }}
              </td>

              <td class="px-3 py-3 text-right text-gray-500 text-sm">
                {{ formatDate(post.published_at) }}
              </td>

              <td class="px-3 py-3 text-center">
                <UDropdownMenu :items="dropdownActions(post)">
                  <button
                    type="button"
                    class="text-gray-400 hover:text-gray-600 text-xl transition-colors inline-block p-1 hover:bg-gray-100 rounded cursor-pointer"
                    @click.stop
                  >
                    <UIcon name="i-heroicons-ellipsis-vertical" />
                  </button>
                </UDropdownMenu>
              </td>
            </tr>
            </tbody>
          </table>
        </div>

        <div class="bg-white border-t border-gray-200 p-4 flex justify-between items-center text-sm text-gray-500">
          <div class="flex items-center gap-2">
            <span>Показувати</span>
            <input
              v-model="perPageInput"
              type="number"
              min="1"
              max="100"
              @change="applyPerPage"
              @keyup.enter="applyPerPage"
              class="w-12 h-8 border border-gray-200 rounded text-center text-gray-800 font-semibold outline-none focus:border-emerald-500 transition-colors"
            >
            <span>із {{ filteredPosts.length }} результатів</span>
          </div>

          <div class="flex items-center gap-2" v-if="totalClientPages > 1">
            <template v-for="p in totalClientPages" :key="p">
              <div v-if="p === page" class="w-8 h-8 rounded-full bg-gradient-to-br from-lime-400 to-cyan-400 p-[1.5px] cursor-pointer">
                <button class="w-full h-full rounded-full bg-green-50 text-gray-800 font-bold flex items-center justify-center hover:bg-green-100 transition-colors">
                  {{ p }}
                </button>
              </div>
              <button v-else @click="page = p" class="w-8 h-8 rounded-full text-gray-500 hover:bg-gray-100 font-medium flex items-center justify-center transition-colors">
                {{ p }}
              </button>
            </template>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const toast = useToast()

const searchInput = ref('')
const page = ref(1)


const itemsPerPage = ref(10)

const perPageInput = ref(10)

const { data: apiData, pending, status, refresh } = await useFetch<any>('http://localhost/api/admin/blog/posts', {
  query: computed(() => ({
    page: 1,
    per_page: 25
  })),
  lazy: true,
  server: false
})


const rawPosts = computed<any[]>(() => {
  const data = apiData.value?.data || []
  if (data.length === 0) return []

  const simulated: any[] = []

  for (let i = 0; i < 4; i++) {
    data.forEach((post: any, index: number) => {
      simulated.push({
        ...post,
        id: 100 - (i * data.length + index)
      })
    })
  }
  return simulated
})


const filteredPosts = computed(() => {
  const query = searchInput.value.trim().toLowerCase()
  if (!query) return rawPosts.value

  return rawPosts.value.filter((post: any) => {
    return post.title?.toLowerCase().includes(query)
  })
})


const paginatedPosts = computed(() => {
  const start = (page.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredPosts.value.slice(start, end)
})


const totalClientPages = computed(() => {
  return Math.ceil(filteredPosts.value.length / itemsPerPage.value) || 1
})


const applyPerPage = (event: Event) => {
  const target = event.target as HTMLInputElement
  let value = parseInt(target.value)

  if (isNaN(value) || value < 1) {
    value = 10
  } else if (value > 100) {
    value = 100
  }

  perPageInput.value = value
  itemsPerPage.value = value
  page.value = 1
  target.blur()
}


watch(searchInput, () => {
  page.value = 1
})

const selectedIds = ref<number[]>([])

const selectAll = computed({
  get: () => {
    return paginatedPosts.value.length > 0 && paginatedPosts.value.every((p: any) => selectedIds.value.includes(p.id))
  },
  set: (value) => {
    const pageIds = paginatedPosts.value.map((p: any) => p.id)
    if (value) {
      const newIds = new Set([...selectedIds.value, ...pageIds])
      selectedIds.value = Array.from(newIds)
    } else {
      selectedIds.value = selectedIds.value.filter(id => !pageIds.includes(id))
    }
  }
})

const formatDate = (dateStr: string) => {
  if (!dateStr) return '-'
  return dateStr.split(' ')[0]
}

const dropdownActions = (post: any) => [
  [
    {
      label: 'Редагувати',
      icon: 'i-heroicons-pencil-square',
      onSelect: () => {
        router.push(`/admin/posts/edit-${post.id}`)
      }
    },
    {
      label: 'Видалити',
      icon: 'i-heroicons-trash',
      color: 'error' as const,
      onSelect: () => {
        deletePost(post.id, post.title)
      }
    }
  ]
]

const deletePost = async (id: number, title: string) => {
  if (!confirm(`Ви впевнені, що хочете видалити статтю "${title}"?`)) return

  try {
    await $fetch(`http://localhost/api/admin/blog/posts/${id}`, {
      method: 'DELETE'
    } as any)

    toast.add({
      title: 'Успіх!',
      description: `Статтю "${title}" успішно видалено.`,
      color: 'success',
      icon: 'i-heroicons-trash'
    })
    await refresh()
  } catch (err) {
    toast.add({
      title: 'Помилка',
      description: 'Не вдалося видалити статтю.',
      color: 'error',
      icon: 'i-heroicons-exclamation-triangle'
    })
    console.error(err)
  }
}
</script>

<style scoped>
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
