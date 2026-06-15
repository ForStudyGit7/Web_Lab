<template>
  <section class="max-w-[1200px] mx-auto pb-20 pt-10 px-6">
    <h2 class="text-[28px] font-bold text-gray-800 tracking-tight">Панель категорій</h2>

    <div class="flex justify-between items-center mb-4 text-sm font-normal text-gray-500 mt-4">
      <div>{{ selectedIds.length }} вибрано</div>

      <div class="flex items-center gap-6">
        <input
          v-model="searchInput"
          type="text"
          placeholder="Пошук за назвою..."
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
          to="/categories/create"
          color="primary"
          variant="solid"
          icon="i-heroicons-plus"
        >
          Створити
        </UButton>
      </div>
    </div>

    <div v-if="loading" class="flex flex-col justify-center items-center py-32 bg-white rounded-xl border border-gray-200 mt-4 shadow-sm relative overflow-hidden">
      <div class="absolute h-[6px] top-0 left-0 right-0 bg-gradient-to-r from-green-400 to-cyan-400"></div>
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-500 mb-4"></div>
      <p class="text-gray-500 font-medium">Оновлення списку категорій...</p>
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
              <th class="px-3 py-3 font-normal">Назва категорії</th>
              <th class="px-3 py-3 font-normal w-64">Посилання (Slug)</th>
              <th class="px-3 py-3 font-normal w-48">Батьківська ID</th>
              <th class="px-3 py-3 font-normal w-12 text-center">Дії</th>
            </tr>
            </thead>

            <tbody class="text-gray-700 divide-y divide-gray-100">
            <tr v-if="paginatedCategories.length === 0">
              <td colspan="6" class="p-12 text-center text-gray-400">Категорій за таким запитом не знайдено.</td>
            </tr>

            <tr v-for="category in paginatedCategories" :key="category.id" class="hover:bg-gray-50 transition-colors cursor-pointer group">
              <td class="px-3 py-3" @click.stop>
                <input
                  type="checkbox"
                  :value="category.id"
                  v-model="selectedIds"
                  class="w-[18px] h-[18px] rounded border-gray-300 accent-emerald-500 cursor-pointer"
                >
              </td>
              <td class="px-3 py-3 font-semibold text-gray-400">
                #{{ category.id }}
              </td>
              <td class="font-semibold text-gray-800">
                {{ category.title }}
              </td>

              <td class="px-3 py-3 text-gray-500 text-[13px]">
                {{ category.slug || '—' }}
              </td>

              <td class="px-3 py-3 text-gray-600 font-medium text-[13px]">
                <span v-if="!category.parent_id || category.parent_id === 0" class="text-gray-400 italic">Коренева</span>
                <span v-else class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded border border-gray-200">ID: #{{ category.parent_id }}</span>
              </td>

              <td class="px-3 py-3 text-center">
                <UDropdownMenu :items="dropdownActions(category)">
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
            <span>із {{ filteredCategories.length }} результатів</span>
          </div>

          <div class="flex items-center gap-2" v-if="totalClientPages > 1">
            <template v-for="p in totalClientPages" :key="p">
              <div v-if="p === page" class="w-8 h-8 rounded-full bg-gradient-to-br from-lime-400 to-cyan-400 p-[1.5px] cursor-pointer">
                <button class="w-full h-full rounded-full bg-green-50 text-gray-800 font-bold flex items-center justify-center hover:bg-green-100 transition-colors">
                  {{ p }}
                </button>
              </div>
              <button @click="page = p" class="w-8 h-8 rounded-full text-gray-500 hover:bg-gray-100 font-medium flex items-center justify-center transition-colors" v-else>
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
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const toast = useToast()

const searchInput = ref('')
const page = ref(1)
const itemsPerPage = ref(10)
const perPageInput = ref(10)
const loading = ref(true)

const rawCategories = ref<any[]>([])
const baseUrl = 'http://localhost/api/admin/blog/categories'

const loadAllCategories = async () => {
  loading.value = true
  try {
    const firstResponse = await $fetch<any>(baseUrl)
    let allData = firstResponse.data || []
    const lastPage = firstResponse.meta?.last_page || 1

    if (lastPage > 1) {
      for (let p = 2; p <= lastPage; p++) {
        const res = await $fetch<any>(`${baseUrl}?page=${p}`)
        if (res.data) {
          allData = [...allData, ...res.data]
        }
      }
    }
    rawCategories.value = allData
  } catch (err) {
    console.error('Помилка отримання даних:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadAllCategories()
})

const filteredCategories = computed(() => {
  const query = searchInput.value.trim().toLowerCase()
  if (!query) return rawCategories.value

  return rawCategories.value.filter((category: any) => {
    return category.title?.toLowerCase().includes(query)
  })
})

const paginatedCategories = computed(() => {
  const start = (page.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredCategories.value.slice(start, end)
})

const totalClientPages = computed(() => {
  return Math.ceil(filteredCategories.value.length / itemsPerPage.value) || 1
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
    return paginatedCategories.value.length > 0 && paginatedCategories.value.every((c: any) => selectedIds.value.includes(c.id))
  },
  set: (value) => {
    const pageIds = paginatedCategories.value.map((c: any) => c.id)
    if (value) {
      const newIds = new Set([...selectedIds.value, ...pageIds])
      selectedIds.value = Array.from(newIds)
    } else {
      selectedIds.value = selectedIds.value.filter(id => !pageIds.includes(id))
    }
  }
})

const dropdownActions = (category: any) => [
  [
    {
      label: 'Редагувати',
      icon: 'i-heroicons-pencil-square',
      onSelect: () => {
        router.push(`/categories/edit-${category.id}`)
      }
    },
    {
      label: 'Видалити',
      icon: 'i-heroicons-trash',
      color: 'error' as const,
      onSelect: () => {
        deleteCategory(category.id, category.title)
      }
    }
  ]
]

// ФІКС: Чистий, REST-валідний DELETE запит
const deleteCategory = async (id: number, title: string) => {
  if (!confirm(`Ви впевнені, що хочете видалити категорію "${title}" (ID: #${id})?`)) return

  try {
    await $fetch(`${baseUrl}/${id}`, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })

    toast.add({
      title: 'Видалено!',
      description: `Категорію "${title}" успішно видалено.`,
      color: 'success',
      icon: 'i-heroicons-trash'
    })

    await loadAllCategories()
    selectedIds.value = selectedIds.value.filter(selectedId => selectedId !== id)

  } catch (err: any) {
    console.error('Помилка при видаленні категорії:', err)
    toast.add({
      title: 'Помилка видалення',
      description: err.data?.message || 'Не вдалося виконати операцію видалення на сервері.',
      color: 'error',
      icon: 'i-heroicons-exclamation-triangle'
    })
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
