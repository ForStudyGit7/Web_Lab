<script setup lang="ts">
const { data: plans } = await useFetch('/api/plans')

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

    <div class="flex justify-between items-center mb-10">
      <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Start Your 3 Day Free Trial</h2>
      <div class="flex items-center gap-4">
        <div class="text-emerald-500 text-[13px] font-bold flex items-center gap-1">
          Save up to 20% <iconify-icon icon="ph:arrow-arc-right-bold" class="text-xl"></iconify-icon>
        </div>
        <div class="flex bg-white border border-gray-200 rounded text-[14px] p-0.5 shadow-sm">
          <button class="px-5 py-1.5 font-bold text-gray-800 bg-white shadow-sm rounded border border-gray-100">Annual</button>
          <button class="px-5 py-1.5 font-bold text-gray-400">Monthly</button>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
      <div v-for="plan in plans" :key="plan.id" class="relative bg-white rounded-2xl p-8 border border-gray-100 shadow-sm flex flex-col">

        <div class="absolute top-0 left-0 right-0 h-1.5 rounded-t-2xl"
             style="background: linear-gradient(to right, #4ade80, #22d3ee);"></div>

        <h3 class="text-xl font-bold text-gray-800 mt-1">{{ plan.name }}</h3>

        <div class="mt-4 mb-3">
          <span class="bg-gray-100 text-gray-500 text-[11px] font-bold px-2 py-1 rounded-sm uppercase italic">3-days free then:</span>
        </div>

        <div class="flex items-baseline gap-1">
          <span class="text-[44px] font-black text-gray-900 tracking-tighter leading-none">${{ plan.price.toFixed(2) }}</span>
          <span class="text-[14px] text-gray-400 font-medium">/month</span>
        </div>

        <p class="text-[13px] text-gray-500 mt-3 italic">
          billed yearly at
          <span class="text-gray-300 line-through mr-1">${{ (plan.oldPrice + 189).toLocaleString() }}</span>
          <span class="font-bold text-gray-600">${{ plan.oldPrice.toLocaleString() }}</span>
        </p>

        <div class="mt-3">
          <span class="bg-green-50 text-green-600 border border-green-100 text-[13px] font-bold px-2.5 py-1 rounded inline-block italic">
            ${{ plan.savings }} in savings
          </span>
        </div>

        <button class="w-full mt-8 bg-[#ffb020] hover:bg-[#ff9f00] text-white font-bold py-4 rounded-xl shadow-lg transition-all uppercase text-xs tracking-widest">
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
