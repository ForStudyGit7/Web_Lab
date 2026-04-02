<script setup lang="ts">
const { data: productsData, pending } = await useFetch<{ products: any[] }>('https://dummyjson.com/products?limit=100')
const products = computed(() => productsData.value?.products || [])

const search = ref('')
const page = ref(1)
const itemsPerPage = ref(5)

const filteredRows = computed(() => {
  if (!search.value) return products.value
  const q = search.value.toLowerCase()
  return products.value.filter((p: any) =>
    p.title.toLowerCase().includes(q) || p.brand?.toLowerCase().includes(q)
  )
})

const totalPages = computed(() => Math.ceil(filteredRows.value.length / itemsPerPage.value))
const paginatedRows = computed(() => {
  const start = (page.value - 1) * itemsPerPage.value
  return filteredRows.value.slice(start, start + itemsPerPage.value)
})
</script>

<template>
  <section class="max-w-7xl mx-auto px-6 text-left">
    <h2 class="text-[28px] font-bold text-gray-800 tracking-tight mb-6">Product Table</h2>

    <div class="flex justify-between items-center mb-4">
      <div class="text-xs font-bold text-gray-400 uppercase tracking-widest italic">
        Showing {{ paginatedRows.length }} of {{ filteredRows.length }} results
      </div>
      <input v-model="search" type="text" placeholder="Search..." class="border border-gray-200 rounded-lg px-4 py-1.5 text-sm outline-none focus:border-emerald-500 shadow-sm" />
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
          <thead class="bg-gray-50 border-b text-gray-400 uppercase text-[11px] font-bold tracking-widest italic">
          <tr>
            <th class="p-4 w-12 text-center">#</th>
            <th class="p-4">Фото</th>
            <th class="p-4">Назва</th>
            <th class="p-4">Опис</th>
            <th class="p-4">Бренд</th>
            <th class="p-4 text-right">Ціна</th>
            <th class="p-4 text-center">Оцінка</th>
          </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
          <tr v-for="product in paginatedRows" :key="product.id" class="hover:bg-gray-50 transition-colors">
            <td class="p-4 text-gray-300 italic text-center">{{ product.id }}</td>
            <td class="p-4">
              <img :src="product.thumbnail" class="w-[100px] h-[100px] object-cover rounded-xl border bg-gray-50 shadow-sm" />
            </td>
            <td class="p-4 font-bold text-gray-900">{{ product.title }}</td>
            <td class="p-4"><p class="text-xs text-gray-500 line-clamp-2 max-w-[200px]">{{ product.description }}</p></td>
            <td class="p-4 text-gray-500 italic">{{ product.brand || 'N/A' }}</td>
            <td class="p-4 text-right font-black text-gray-900 text-base">${{ product.price }}</td>
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

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
