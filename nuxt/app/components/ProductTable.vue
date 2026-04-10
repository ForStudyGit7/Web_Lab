<script setup lang="ts">
const { data: productsData, pending } = await useFetch<{ products: any[] }>('https://dummyjson.com/products?limit=100')
const products = computed(() => productsData.value?.products || [])

const search = ref('')
const page = ref(1)
const itemsPerPage = ref(5)


const sortCol = ref('id')
const sortDir = ref<'asc' | 'desc'>('asc')


const selectedIds = ref<number[]>([])

const sortBy = (col: string) => {
  if (sortCol.value === col) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortCol.value = col
    sortDir.value = 'asc'
  }
}


const filteredRows = computed(() => {
  if (!search.value) return products.value
  const q = search.value.toLowerCase()
  return products.value.filter((p: any) =>
    p.title.toLowerCase().includes(q) ||
    p.category.toLowerCase().includes(q) ||
    p.brand?.toLowerCase().includes(q)
  )
})


const sortedRows = computed(() => {
  return [...filteredRows.value].sort((a: any, b: any) => {
    let aVal = a[sortCol.value]
    let bVal = b[sortCol.value]
    if (aVal === bVal) return 0
    const modifier = sortDir.value === 'asc' ? 1 : -1
    return aVal > bVal ? modifier : -modifier
  })
})


const totalPages = computed(() => Math.ceil(sortedRows.value.length / itemsPerPage.value))
const paginatedRows = computed(() => {
  const start = (page.value - 1) * itemsPerPage.value
  return sortedRows.value.slice(start, start + itemsPerPage.value)
})


const isAllSelected = computed(() => {
  if (paginatedRows.value.length === 0) return false
  return paginatedRows.value.every(p => selectedIds.value.includes(p.id))
})

const toggleSelectAll = () => {
  if (isAllSelected.value) {

    const currentPageIds = paginatedRows.value.map(p => p.id)
    selectedIds.value = selectedIds.value.filter(id => !currentPageIds.includes(id))
  } else {

    paginatedRows.value.forEach(p => {
      if (!selectedIds.value.includes(p.id)) {
        selectedIds.value.push(p.id)
      }
    })
  }
}

const toggleSelect = (id: number) => {
  const index = selectedIds.value.indexOf(id)
  if (index > -1) {
    selectedIds.value.splice(index, 1)
  } else {
    selectedIds.value.push(id)
  }
}

watch(search, () => page.value = 1)
</script>

<template>
  <section class="max-w-7xl mx-auto pb-20 text-left">
    <div class="flex justify-between items-end mb-6">
      <div>
        <h2 class="text-[28px] font-bold text-gray-800 tracking-tight">Product Table</h2>
        <div class="text-xs font-bold text-emerald-500 uppercase mt-1">
          Selected: {{ selectedIds.length }}
        </div>
      </div>
      <input v-model="search" type="text" placeholder="Search products..." class="border border-gray-200 rounded-lg px-4 py-1.5 text-sm outline-none focus:border-emerald-500 shadow-sm w-64" />
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
          <thead class="bg-gray-50 border-b text-gray-400 uppercase text-[11px] font-bold tracking-widest italic">
          <tr>
            <th class="p-4 w-10">
              <input
                type="checkbox"
                :checked="isAllSelected"
                @change="toggleSelectAll"
                class="w-4 h-4 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500 cursor-pointer"
              />
            </th>
            <th @click="sortBy('id')" class="p-4 w-12 text-center cursor-pointer hover:text-emerald-500 transition-colors">
              # <span v-if="sortCol === 'id'">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
            </th>
            <th class="p-4">Фото</th>
            <th @click="sortBy('title')" class="p-4 cursor-pointer hover:text-emerald-500 transition-colors">
              Назва <span v-if="sortCol === 'title'">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
            </th>
            <th class="p-4">Опис</th>
            <th @click="sortBy('brand')" class="p-4 cursor-pointer hover:text-emerald-500 transition-colors">
              Бренд <span v-if="sortCol === 'brand'">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
            </th>
            <th @click="sortBy('price')" class="p-4 text-right cursor-pointer hover:text-emerald-500 transition-colors">
              Ціна <span v-if="sortCol === 'price'">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
            </th>
            <th @click="sortBy('rating')" class="p-4 text-center cursor-pointer hover:text-emerald-500 transition-colors">
              Оцінка <span v-if="sortCol === 'rating'">{{ sortDir === 'asc' ? '↑' : '↓' }}</span>
            </th>
          </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
          <tr v-for="product in paginatedRows" :key="product.id"
              :class="['transition-colors', selectedIds.includes(product.id) ? 'bg-emerald-50/30' : 'hover:bg-gray-50']">
            <td class="p-4">
              <input
                type="checkbox"
                :checked="selectedIds.includes(product.id)"
                @change="toggleSelect(product.id)"
                class="w-4 h-4 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500 cursor-pointer"
              />
            </td>
            <td class="p-4 text-gray-300 italic text-center">{{ product.id }}</td>
            <td class="p-4">
              <img :src="product.thumbnail" class="w-[60px] h-[60px] object-cover rounded-xl border border-gray-100" />
            </td>
            <td class="p-4 font-bold text-gray-900">{{ product.title }}</td>
            <td class="p-4 font-medium text-gray-500 max-w-[200px] leading-tight text-xs">
              {{ product.description }}
            </td>
            <td class="p-4 text-gray-400 italic text-[13px]">{{ product.brand || 'N/A' }}</td>
            <td class="p-4 text-right font-black text-gray-900">${{ product.price }}</td>
            <td class="p-4 text-center">
                <span :class="['px-2.5 py-1 rounded-lg font-black text-[12px]', product.rating < 4.5 ? 'text-red-500 bg-red-50' : 'text-emerald-500 bg-emerald-50']">
                  ★ {{ product.rating }}
                </span>
            </td>
          </tr>
          </tbody>
        </table>
      </div>

      <div class="p-6 bg-gray-50/50 border-t flex justify-center">
        <div class="flex gap-2">
          <button v-for="p in totalPages" :key="p" @click="page = p"
                  :class="['w-9 h-9 rounded-full transition-all font-black flex items-center justify-center', p === page ? 'bg-gradient-to-br from-emerald-400 to-cyan-400 text-white shadow-lg' : 'text-gray-400 hover:bg-gray-200']">
            {{ p }}
          </button>
        </div>
      </div>
    </div>
  </section>
</template>
