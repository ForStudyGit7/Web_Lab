<script setup lang="ts">
// 1. Отримуємо доступ до Pinia Store
const subscriptionStore = useSubscriptionStore()

// 2. Використовуємо дані зі стору
const isAnnual = computed(() => subscriptionStore.isAnnual)
const selectedPlan = computed(() => subscriptionStore.selectedPlan)

// 3. Динамічний розрахунок даних для відображення
const checkoutData = computed(() => {
  if (!selectedPlan.value) return null

  const currentPrice = isAnnual.value ? selectedPlan.value.price : selectedPlan.value.price * 1.25
  const totalAmount = isAnnual.value ? selectedPlan.value.oldPrice : currentPrice
  const planTypeLabel = isAnnual.value ? 'Annual Plan' : 'Monthly Plan'
  const nameSuffix = isAnnual.value ? 'Annual' : 'Monthly'

  return {
    ...selectedPlan.value,
    finalPrice: currentPrice.toFixed(2),
    finalTotal: totalAmount.toFixed(2),
    finalName: `${selectedPlan.value.name.split(' - ')[0]} - ${nameSuffix}`,
    typeLabel: planTypeLabel
  }
})

// Захист сторінки: якщо плану немає (наприклад, оновили сторінку), йдемо на головну
onMounted(() => {
  if (!selectedPlan.value) {
    navigateTo('/')
  }
})

useHead({
  title: 'Checkout',
  script: [{ src: 'https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js' }]
})

// Стан форми
const form = ref({
  cardNumber: '',
  expiry: '',
  cvc: '',
  fullName: '',
  address: '',
  consent: false
})

const errors = ref<Record<string, string>>({})
const loading = ref(false)

// Валідація
const validateNumbers = (e: Event) => {
  const input = e.target as HTMLInputElement
  input.value = input.value.replace(/\D/g, '')
}

const formatCardNumber = (e: Event) => {
  validateNumbers(e)
  let val = (e.target as HTMLInputElement).value.substring(0, 16)
  form.value.cardNumber = val.replace(/(\d{4})(?=\d)/g, '$1 ')
}

const formatExpiry = (e: Event) => {
  validateNumbers(e)
  let val = (e.target as HTMLInputElement).value.substring(0, 4)
  if (val.length >= 2) val = val.substring(0, 2) + ' / ' + val.substring(2, 4)
  form.value.expiry = val
}

const formatCVC = (e: Event) => {
  validateNumbers(e)
  form.value.cvc = (e.target as HTMLInputElement).value.substring(0, 3)
}

const handleCheckout = async () => {
  errors.value = {}
  if (form.value.cardNumber.replace(/\s/g, '').length < 16) errors.value.cardNumber = 'Error'
  if (form.value.expiry.length < 7) errors.value.expiry = 'Error'
  if (form.value.cvc.length < 3) errors.value.cvc = 'Error'
  if (!form.value.fullName) errors.value.fullName = 'Error'
  if (!form.value.address) errors.value.address = 'Error'
  if (!form.value.consent) errors.value.consent = 'Error'

  if (Object.keys(errors.value).length > 0) return

  loading.value = true
  try {
    await $fetch('/api/subscription/create', { method: 'POST', body: form.value })
    alert('Success!')
  } catch (e) {
    alert('Error')
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-white py-10 px-4 font-sans text-left text-[#374151]">
    <svg style="width:0;height:0;position:absolute;" aria-hidden="true">
      <defs>
        <linearGradient id="starGradient" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#84cc16" />
          <stop offset="100%" stop-color="#a3e635" />
        </linearGradient>
      </defs>
    </svg>

    <div v-if="checkoutData" class="max-w-[1000px] mx-auto">
      <div class="mb-6 px-4">
        <NuxtLink to="/" class="text-[13px] text-gray-400 hover:text-gray-600 transition tracking-tight"><< back</NuxtLink>
      </div>

      <div class="mb-10 px-4">
        <h1 class="text-[24px] font-bold text-gray-800 mb-2 leading-tight">You’re Almost In - Start Your 3-Day Free Trial Now!</h1>
        <p class="text-[14px] text-gray-600">Set up your account to gain instant access! You won’t be charged if you decide to cancel within 3 days</p>
      </div>

      <div class="flex flex-col md:flex-row gap-8 justify-center items-start">
        <div class="w-full max-w-[340px] border border-gray-100 rounded-xl p-8 shadow-[0_4px_20px_rgba(0,0,0,0.03)] relative shrink-0">
          <div class="absolute top-0 left-0 right-0 h-1.5 rounded-t-xl" style="background: linear-gradient(to right, #4ade80, #22d3ee);"></div>
          <h3 class="text-[18px] font-bold text-gray-700 mb-4">{{ checkoutData.finalName }}</h3>
          <div class="bg-gray-100 text-[10px] font-bold text-gray-500 px-2.5 py-1 rounded uppercase mb-4 inline-block italic">3-days free then:</div>
          <div class="flex items-baseline gap-1 mb-1">
            <span class="text-[42px] font-bold text-gray-900 tracking-tighter leading-none">${{ checkoutData.finalPrice }}</span>
            <span class="text-gray-500 text-[14px]">/month</span>
          </div>
          <div class="text-[12px] text-gray-500 mb-4 italic">
            billed {{ isAnnual ? 'yearly' : 'monthly' }} at
            <span class="font-bold text-gray-600">${{ checkoutData.finalTotal }}</span>
          </div>
          <div v-if="isAnnual" class="bg-green-50 text-green-600 text-[11px] font-bold px-3 py-1.5 rounded-lg border border-green-100 inline-block mb-8">
            ${{ checkoutData.savings }} in savings
          </div>
          <ul class="space-y-4 pt-6 border-t border-gray-50">
            <li v-for="feat in checkoutData.features" :key="feat.text" class="flex items-start">
              <svg class="w-4 h-4 mt-0.5 mr-3 shrink-0" viewBox="0 0 24 24"><path fill="url(#starGradient)" d="M12 1L9 9l-8 3l8 3l3 8l3-8l8-3l-8-3z"/></svg>
              <span class="text-[13px] text-gray-600 leading-snug">{{ feat.text }}</span>
            </li>
          </ul>
        </div>

        <div class="w-full max-w-[460px] border border-gray-100 rounded-xl p-10 shadow-[0_2px_20px_rgba(0,0,0,0.02)] bg-white">
          <h2 class="text-[18px] font-bold text-gray-800 mb-8">Order Summary</h2>
          <div class="space-y-4 mb-8 text-[13px]">
            <div class="flex justify-between text-gray-400">
              <span>{{ checkoutData.typeLabel }}</span>
              <span class="font-bold text-gray-700">${{ checkoutData.finalTotal }}</span>
            </div>
            <div class="flex justify-between items-start text-gray-400">
              <span class="text-[11px]">Total Due <span class="italic opacity-60">(*not including sales tax)</span></span>
              <span class="font-bold text-gray-700">${{ checkoutData.finalTotal }}</span>
            </div>
            <div class="flex justify-between border-t border-gray-200 pt-4 font-bold text-gray-800 text-[16px]"><span>Due Today</span><span>$0.00</span></div>
          </div>

          <div class="bg-gray-50/80 p-3 rounded text-center text-[13px] text-gray-400 mb-10 italic">Includes 3-Day Free Trial</div>

          <div class="bg-gray-50/50 border border-gray-100 rounded-xl p-6 space-y-6">
            <h3 class="text-[14px] font-bold text-gray-700 uppercase tracking-wide">Billing Information</h3>
            <div class="space-y-2 text-left">
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-0.5">Card Details</p>
              <div :class="['border flex items-center bg-white rounded-lg shadow-sm overflow-hidden', errors.cardNumber || errors.expiry || errors.cvc ? 'border-red-400' : 'border-gray-200']">
                <div class="px-4 text-gray-300"><iconify-icon icon="ph:credit-card" class="text-lg"></iconify-icon></div>
                <input :value="form.cardNumber" @input="formatCardNumber" placeholder="Number" class="w-full py-3 outline-none text-[13px] text-gray-600" />
                <div class="h-6 w-px bg-gray-200 mx-2"></div>
                <input :value="form.expiry" @input="formatExpiry" placeholder="MM / YY" class="w-24 text-center outline-none text-[13px] text-gray-600" />
                <div class="h-6 w-px bg-gray-200 mx-2"></div>
                <input :value="form.cvc" @input="formatCVC" placeholder="CVC" class="w-16 text-center outline-none text-[13px] text-gray-600 pr-2" />
              </div>
            </div>

            <div class="space-y-2 pt-2 text-left">
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-0.5">Address</p>
              <div class="border border-gray-100 bg-white rounded-lg p-4 space-y-4 shadow-sm">
                <input v-model="form.fullName" placeholder="Full name" :class="['w-full border rounded-md p-2.5 outline-none text-[13px]', errors.fullName ? 'border-red-400' : 'border-gray-100']" />
                <input v-model="form.address" placeholder="Address" :class="['w-full border rounded-md p-2.5 outline-none text-[13px]', errors.address ? 'border-red-400' : 'border-gray-100']" />
              </div>
            </div>
          </div>

          <div class="mt-8">
            <div class="flex items-start gap-3">
              <input type="checkbox" v-model="form.consent" :class="['mt-1 w-4 h-4', errors.consent ? 'outline-red-400' : '']" />
              <p class="text-[10px] text-gray-400 text-left">
                I consent to <a href="#" class="underline">Terms of Use</a>... starting on 04/02/2026 for the amount of ${{ checkoutData.finalTotal }}.
              </p>
            </div>
          </div>

          <div class="mt-6 text-left">
            <button @click="handleCheckout" :disabled="loading" class="bg-[#e5e7eb] text-gray-600 font-medium py-2.5 px-10 rounded text-[13px] shadow-sm active:scale-95 transition-all">
              {{ loading ? 'Wait...' : 'Try It Free' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
