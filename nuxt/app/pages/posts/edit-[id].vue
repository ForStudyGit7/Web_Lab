<template>
  <section class="min-h-[80vh] flex items-center justify-center bg-gray-50/50 px-6 py-12">
    <div class="w-full max-w-2xl bg-white p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">

      <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
        <UButton to="/posts/BlogPostsUi" color="neutral" variant="ghost" icon="i-heroicons-arrow-left" size="sm" />
        <div>
          <h2 class="text-xl font-bold text-gray-900 tracking-tight">Редагування статті</h2>
          <p class="text-xs text-gray-400 mt-0.5">ID: {{ postId }}</p>
        </div>
      </div>

      <div v-if="pending" class="py-12 text-center text-gray-500">Завантаження даних...</div>

      <UForm v-else :state="formState" class="space-y-4" @submit="onSubmit">
        <UFormField label="Заголовок" name="title" required>
          <UInput v-model="formState.title" class="w-full" size="md" />
        </UFormField>

        <UFormField label="Посилання (Slug)" name="slug">
          <UInput v-model="formState.slug" class="w-full" size="md" />
        </UFormField>

        <UFormField label="Категорія" name="category_id">
          <USelect
            v-model.number="formState.category_id"
            :items="categoryOptions"
            class="w-full"
            size="md"
          />
        </UFormField>

        <UFormField label="Текст статті" name="content_raw" required>
          <UTextarea v-model="formState.content_raw" class="w-full" size="md" :rows="8" />
        </UFormField>

        <UFormField label="Статус">
          <UCheckbox v-model="formState.is_published" label="Опубліковано" />
        </UFormField>

        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-6">
          <UButton to="/posts/BlogPostsUi" color="neutral" variant="ghost" size="md">Скасувати</UButton>
          <UButton type="submit" color="primary" variant="solid" size="md" :loading="isSubmitting" icon="i-heroicons-check">
            Зберегти зміни
          </UButton>
        </div>
      </UForm>
    </div>
  </section>
</template>

<script setup lang="ts">
const route = useRoute()
const router = useRouter()
const toast = useToast()
const postId = route.params.id

const isSubmitting = ref(false)
const pending = ref(true)
const rawCategories = ref<any[]>([])

const formState = ref({
  title: '',
  slug: '',
  content_raw: '',
  category_id: 0,
  is_published: false
})

const categoryOptions = computed(() => {
  return [{ label: '— Без категорії', value: 0 }, ...rawCategories.value.map(c => ({ label: c.title, value: c.id }))]
})


onMounted(async () => {
  try {
    const [categoriesRes, postRes] = await Promise.all([
      $fetch<any>('http://localhost/api/admin/blog/categories'),
      $fetch<any>(`http://localhost/api/admin/blog/posts/${postId}`)
    ])

    rawCategories.value = categoriesRes.data || []


    const post = postRes.data
    formState.value = {
      title: post.title,
      slug: post.slug,
      content_raw: post.content_raw,
      category_id: post.category_id || 0,
      is_published: !!post.is_published
    }
  } catch (err) {
    console.error(err)
    toast.add({ title: 'Помилка завантаження', color: 'error' })
  } finally {
    pending.value = false
  }
})

const onSubmit = async () => {
  isSubmitting.value = true
  try {

    await $fetch(`http://localhost/api/admin/blog/posts/${postId}`, {
      method: 'POST',
      body: {
        ...formState.value,
        is_published: formState.value.is_published ? 1 : 0,
        _method: 'PUT'
      }
    })

    toast.add({ title: 'Успіх!', description: 'Статтю оновлено.', color: 'success' })
    router.push('/posts/BlogPostsUi')
  } catch (err) {
    console.error(err)
    toast.add({ title: 'Помилка', description: 'Не вдалося зберегти зміни.', color: 'error' })
  } finally {
    isSubmitting.value = false
  }
}
</script>
