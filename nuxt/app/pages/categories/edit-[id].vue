<template>
  <section class="min-h-[80vh] flex items-center justify-center bg-gray-50/50 px-6 py-12">
    <div class="w-full max-w-md bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-6">

      <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
        <UButton to="/categories/BlogCategories" color="neutral" variant="ghost" icon="i-heroicons-arrow-left" size="sm" />
        <div>
          <h2 class="text-xl font-bold text-gray-900 tracking-tight">Редагування #{{ id }}</h2>
          <p class="text-xs text-gray-400 mt-0.5">Зміна параметрів поточної категорії</p>
        </div>
      </div>

      <div v-if="pending" class="flex flex-col justify-center items-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500 mb-2"></div>
        <p class="text-gray-400 text-xs">Отримання даних з бази...</p>
      </div>

      <UForm :schema="categorySchema" :state="formState" class="space-y-4" @submit="onSubmit" v-else>

        <UFormField label="Назва категорії" name="title" required>
          <UInput v-model="formState.title" placeholder="Введіть нову назву" icon="i-heroicons-tag" class="w-full" size="md" />
        </UFormField>

        <UFormField label="Посилання (Slug)" name="slug">
          <UInput v-model="formState.slug" placeholder="Автоматична генерація" icon="i-heroicons-link" class="w-full" size="md" />
        </UFormField>

        <UFormField label="Батьківська категорія" name="parent_id">
          <USelect
            v-model.number="formState.parent_id"
            :items="categoryOptions"
            placeholder="Оберіть категорію..."
            class="w-full"
            size="md"
          />
        </UFormField>

        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-6">
          <UButton to="/categories/BlogCategories" color="neutral" variant="ghost" size="md">
            Скасувати
          </UButton>
          <UButton type="submit" color="primary" variant="solid" size="md" :loading="isSubmitting" icon="i-heroicons-arrow-path">
            Оновити
          </UButton>
        </div>
      </UForm>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { z } from 'zod'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const id = route.params.id

const isSubmitting = ref(false)
const pending = ref(true)
const rawCategories = ref<any[]>([])

const categorySchema = z.object({
  title: z.string().min(3, 'Назва повинна містити мінімум 3 символи').max(100, 'Занадто довга назва'),
  slug: z.string().optional(),
  parent_id: z.number().min(0).default(0)
})

const formState = ref({
  title: '',
  slug: '',
  parent_id: 0
})

const categoryOptions = computed(() => {
  const options = [{ label: '— Немає (Коренева)', value: 0 }]
  rawCategories.value.forEach((item: any) => {
    if (item.id !== Number(id)) {
      options.push({
        label: item.title,
        value: item.id
      })
    }
  })
  return options
})

// Надійне завантаження всього списку для випадаючого вікна USelect
const loadDropdownCategories = async () => {
  try {
    const firstResponse = await $fetch<any>('http://localhost/api/admin/blog/categories')
    let allData = firstResponse.data || []
    const lastPage = firstResponse.meta?.last_page || 1

    if (lastPage > 1) {
      for (let p = 2; p <= lastPage; p++) {
        const res = await $fetch<any>(`http://localhost/api/admin/blog/categories?page=${p}`)
        if (res.data) allData = [...allData, ...res.data]
      }
    }
    rawCategories.value = allData
  } catch (err) {
    console.error('Помилка завантаження списку для селектора:', err)
  }
}

onMounted(async () => {
  // 1. Спершу викачуємо всі категорії для випадаючого списку
  await loadDropdownCategories()

  // 2. ФІКС: Шукаємо потрібну категорію серед викачаного списку по справжньому ID
  const currentCategory = rawCategories.value.find((c: any) => c.id === Number(id))

  if (currentCategory) {
    formState.value = {
      title: currentCategory.title || '',
      slug: currentCategory.slug || '',
      parent_id: currentCategory.parent_id || 0
    }
    pending.value = false
  } else {
    // 3. Якщо раптом у списку немає (наприклад, пряме посилання), робимо точковий запит
    try {
      const response = await $fetch<any>(`http://localhost/api/admin/blog/categories/${id}`)
      const category = response.data || response
      if (category) {
        formState.value = {
          title: category.title || '',
          slug: category.slug || '',
          parent_id: category.parent_id || 0
        }
      }
    } catch (err) {
      console.error('Категорію не знайдено в базі:', err)
    } finally {
      pending.value = false
    }
  }
})

const transliterate = (text: string) => {
  const ukr: { [key: string]: string } = {
    'а': 'a', 'б': 'b', 'в': 'v', 'г': 'h', 'ґ': 'g', 'д': 'd', 'е': 'e', 'є': 'ye', 'ж': 'zh', 'з': 'z',
    'и': 'y', 'і': 'i', 'ї': 'yi', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o', 'п': 'p',
    'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'kh', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'shch',
    'ь': '', 'ю': 'yu', 'я': 'ya'
  }
  return text
    .toLowerCase()
    .split('')
    .map(char => ukr[char] || char)
    .join('')
    .trim()
    .replace(/[^\w\s-]/g, '')
    .replace(/[\s_-]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

const onSubmit = async () => {
  isSubmitting.value = true
  const submissionData: any = { ...formState.value }

  if (!submissionData.slug || !submissionData.slug.trim()) {
    submissionData.slug = transliterate(submissionData.title)
  }

  try {
    await $fetch(`http://localhost/api/admin/blog/categories/${id}`, {
      method: 'PUT',
      body: submissionData
    })

    toast.add({
      title: 'Оновлено!',
      description: 'Зміни збережено в базі даних.',
      color: 'success',
      icon: 'i-heroicons-check-circle'
    })

    router.push('/categories/BlogCategories')
  } catch (err) {
    console.error(err)
    toast.add({
      title: 'Помилка оновлення',
      description: 'Не вдалося зберегти зміни на сервері.',
      color: 'error',
      icon: 'i-heroicons-exclamation-triangle'
    })
  } finally {
    isSubmitting.value = false
  }
}
</script>
