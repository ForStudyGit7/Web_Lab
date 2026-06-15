<template>
  <section class="min-h-[80vh] flex items-center justify-center bg-gray-50/50 px-6 py-12">
    <div class="w-full max-w-md bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-6">

      <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
        <UButton to="/categories/BlogCategories" color="neutral" variant="ghost" icon="i-heroicons-arrow-left" size="sm" />
        <div>
          <h2 class="text-xl font-bold text-gray-900 tracking-tight">Нова категорія</h2>
          <p class="text-xs text-gray-400 mt-0.5">Створення нового розділу блогу</p>
        </div>
      </div>

      <UForm :schema="categorySchema" :state="formState" class="space-y-4" @submit="onSubmit">

        <UFormField label="Назва категорії" name="title" required>
          <UInput v-model="formState.title" placeholder="Наприклад: Програмування" icon="i-heroicons-tag" class="w-full" size="md" />
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
          <UButton type="submit" color="primary" variant="solid" size="md" :loading="isSubmitting" icon="i-heroicons-check">
            Зберегти
          </UButton>
        </div>
      </UForm>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { z } from 'zod'
import { useRouter } from 'vue-router'

const router = useRouter()
const toast = useToast()
const isSubmitting = ref(false)
const rawCategories = ref<any[]>([])

const categorySchema = z.object({
  title: z.string().min(3, 'Назва повинна містити мінімум 3 symbols').max(100, 'Занадто довга назва'),
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
    options.push({
      label: item.title,
      value: item.id
    })
  })
  return options
})

// ФІКС: Викачуємо посторінково геть усі категорії з бази, щоб «Спорт» (ID 18) залізобетонно з'явився у списку
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
    console.error('Помилка завантаження категорій:', err)
  }
}

onMounted(() => {
  loadDropdownCategories()
})

const generateSlug = (text: string) => {
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
    submissionData.slug = generateSlug(submissionData.title)
  }

  try {
    await $fetch('http://localhost/api/admin/blog/categories', {
      method: 'POST',
      body: submissionData
    })

    toast.add({
      title: 'Успіх!',
      description: `Категорію успішно створено.`,
      color: 'success',
      icon: 'i-heroicons-check-circle'
    })

    router.push('/categories/BlogCategories')
  } catch (err) {
    console.error(err)
    toast.add({
      title: 'Помилка валідації',
      description: 'Не вдалося зберегти категорію на сервері.',
      color: 'error',
      icon: 'i-heroicons-exclamation-triangle'
    })
  } finally {
    isSubmitting.value = false
  }
}
</script>
