<script setup lang="ts">
const { data: plans } = await useFetch<any[]>('/api/plans')
const isAnnual = ref(true)

// Підключаємо стор та роутер
const subscriptionStore = useSubscriptionStore()
const router = useRouter()

const getDisplayPrice = (basePrice: number) => {
  return isAnnual.value ? basePrice.toFixed(2) : (basePrice * 1.25).toFixed(2)
}

const selectPlan = (plan: any) => {
  // Зберігаємо вибір у Pinia
  subscriptionStore.setSubscription(plan, isAnnual.value)
  // Переходимо на сторінку чекауту
  router.push('/checkout')
}

const highlightText = (text: string) => {
  return text.replace(/(FREE|10,000|50,000|100,000)/g, '<strong class="text-gray-800 font-bold">$1</strong>')
}
</script>

<template>
  <section class="max-w-7xl mx-auto px-6">
    <svg style="width:0;height:0;position:absolute;" aria-hidden="true">
      <defs>
        <linearGradient id="starGradient" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#84cc16" />
          <stop offset="100%" stop-color="#a3e635" />
        </linearGradient>
      </defs>
    </svg>

    <div class="flex justify-between items-center mb-10 text-left">
      <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Start Your 3 Day Free Trial</h2>

      <div class="flex items-center gap-4">
        <div class="text-[13px] font-bold flex items-center gap-1 transition-all duration-300"
             :class="isAnnual ? 'text-emerald-500 opacity-100' : 'text-gray-400 opacity-50'">
          Save up to 20% with Annual <iconify-icon icon="ph:arrow-arc-right-bold" class="text-xl rotate-180 translate-y-[-2px]"></iconify-icon>
        </div>

        <div class="bg-gray-100 p-1 rounded-xl flex border border-gray-200 shadow-sm">
          <button @click="isAnnual = true" :class="['px-6 py-1.5 text-sm font-bold rounded-lg transition-all', isAnnual ? 'bg-white shadow-sm text-gray-900' : 'text-gray-400']">
            Annual
          </button>
          <button @click="isAnnual = false" :class="['px-6 py-1.5 text-sm font-bold rounded-lg transition-all', !isAnnual ? 'bg-white shadow-sm text-gray-900' : 'text-gray-400']">
            Monthly
          </button>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
      <div v-for="plan in plans" :key="plan.id" class="relative bg-white rounded-2xl p-8 border border-gray-100 shadow-sm flex flex-col transition-all duration-500">
        <div class="absolute top-0 left-0 right-0 h-1.5 rounded-t-2xl" style="background: linear-gradient(to right, #4ade80, #22d3ee);"></div>

        <h3 class="text-xl font-bold text-gray-800 mt-1">
          {{ plan.name.split(' - ')[0] }} — {{ isAnnual ? 'Annual' : 'Monthly' }}
        </h3>

        <div class="mt-4 mb-3">
          <span class="bg-gray-100 text-gray-500 text-[11px] font-bold px-2 py-1 rounded-sm uppercase italic">3-days free then:</span>
        </div>

        <div class="flex items-baseline gap-1">
          <span class="text-[44px] font-black text-gray-900 tracking-tighter leading-none">
            ${{ getDisplayPrice(plan.price) }}
          </span>
          <span class="text-[14px] text-gray-400 font-medium">/month</span>
        </div>

        <p class="text-[13px] text-gray-500 mt-3 italic min-h-[20px]">
          <template v-if="isAnnual">
            billed yearly at <span class="font-bold text-gray-600">${{ plan.oldPrice.toLocaleString() }}</span>
          </template>
          <template v-else>
            billed monthly (no annual discount)
          </template>
        </p>

        <div class="mt-3 min-h-[28px]">
          <span v-if="isAnnual" class="bg-green-50 text-green-600 border border-green-100 text-[13px] font-bold px-2.5 py-1 rounded inline-block italic animate-pulse">
            ${{ plan.savings }} in savings
          </span>
          <span v-else class="text-gray-300 text-[12px] italic">
            Switch to annual to save ${{ plan.savings }}
          </span>
        </div>

        <button
          @click="selectPlan(plan)"
          class="w-full mt-8 bg-[#ffb020] hover:bg-[#ff9f00] text-white font-bold py-4 rounded-xl shadow-lg transition-all uppercase text-xs tracking-widest text-center"
        >
          Try It Free
        </button>

        <hr class="my-8 border-gray-50">

        <ul class="space-y-4 flex-1">
          <li v-for="(feat, idx) in plan.features" :key="idx" class="flex items-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 mr-3 shrink-0" viewBox="0 0 24 24">
              <path fill="url(#starGradient)" d="M12 1L9 9l-8 3l8 3l3 8l3-8l8-3l-8-3z"/>
            </svg>
            <div class="text-[13.5px] text-gray-600 leading-snug">
              <p v-html="highlightText(feat.text)"></p>
              <p v-if="feat.subtext" class="text-[11px] text-gray-400 mt-1 italic">{{ feat.subtext }}</p>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </section>
</template>
