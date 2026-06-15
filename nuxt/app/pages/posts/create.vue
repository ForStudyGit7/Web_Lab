<template>
  <section class="min-h-[80vh] flex items-center justify-center bg-gray-50/50 px-6 py-12">
    <div class="w-full max-w-2xl bg-white p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">

      <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
        <UButton to="/posts/BlogPostsUi" color="neutral" variant="ghost" icon="i-heroicons-arrow-left" size="sm" />
        <div>
          <h2 class="text-xl font-bold text-gray-900 tracking-tight">Нова стаття</h2>
          <p class="text-xs text-gray-400 mt-0.5">Створення публікації</p>
        </div>
      </div>

      <UForm :state="formState" class="space-y-4" @submit="onSubmit">

        <UFormField label="Заголовок" name="title" required>
          <UInput v-model="formState.title" placeholder="Наприклад: Мій досвід" class="w-full" size="md" />
        </UFormField>

        <UFormField label="Посилання (Slug)" name="slug">
          <UInput v-model="formState.slug" placeholder="Автоматична генерація" class="w-full" size="md" />
        </UFormField>

        <UFormField label="Категорія" name="category_id">
          <USelect
            v-model.number="formState.category_id"
            :items="categoryOptions"
            placeholder="Оберіть категорію..."
            class="w-full"
            size="md"
          />
        </UFormField>

        <UFormField label="Текст статті" name="content_raw" required>
          <UTextarea v-model="formState.content_raw" placeholder="Введіть основний текст..." class="w-full" size="md" :rows="8" />
        </UFormField>

        <UFormField label="Статус">
          <UCheckbox v-model="formState.is_published" label="Опублікувати одразу" />
        </UFormField>

        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-6">
          <UButton to="/posts/BlogPostsUi" color="neutral" variant="ghost" size="md">
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
const toast = useToast()
const isSubmitting = ref(false)
const rawCategories = ref<any[]>([])

const formState = ref({
  title: '',
  slug: '',
  content_raw: '',
  category_id: 0,
  is_published: true
})

const categoryOptions = computed(() => {
  return [{ label: '— Без категорії', value: 0 }, ...rawCategories.value.map(c => ({ label: c.title, value: c.id }))]
})

onMounted(async () => {
  try {
    const res = await $fetch<any>('http://localhost/api/admin/blog/categories')
    rawCategories.value = res.data || []
  } catch (err) {
    console.error('Помилка завантаження категорій:', err)
  }
})

const onSubmit = async () => {
  isSubmitting.value = true
  try {
    await $fetch('http://localhost/api/admin/blog/posts', {
      method: 'POST',
      body: {
        ...formState.value,
        is_published: formState.value.is_published ? 1 : 0
      }
    })

    toast.add({
      title: 'Успіх!',
      description: 'Статтю успішно створено.',
      color: 'success',
      icon: 'i-heroicons-check-circle'
    })

    navigateTo('/posts/BlogPostsUi')
  } catch (err: any) {
    console.error(err)
    toast.add({
      title: 'Помилка збереження',
      description: err.data?.message || 'Не вдалося зберегти статтю.',
      color: 'error',
      icon: 'i-heroicons-exclamation-triangle'
    })
  } finally {
    isSubmitting.value = false
  }
}
</script>
