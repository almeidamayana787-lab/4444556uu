<template>
  <div :id="sectionId" class="px-3 mt-6">
    <div class="flex items-center space-x-2 mb-3">
      <img :src="iconSrc" :alt="title" class="w-6 h-6" />
      <h2 class="text-lg font-bold text-gray-100">{{ title }}</h2>
    </div>
    
    <div class="grid grid-cols-3 gap-2">
      <!-- Generate 9 mock games (3 rows of 3) -->
      <div 
        v-for="i in 9" 
        :key="i"
        class="bg-[#222] rounded-lg overflow-hidden aspect-[3/4] relative group cursor-pointer shadow-lg border border-gray-800 hover:border-[#fca000] hover:-translate-y-1 transition-all duration-300"
      >
        <!-- Mock image placeholder, you can replace with actual game cover later -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10"></div>
        <div class="absolute inset-0 bg-[#333] flex items-center justify-center">
          <span class="text-gray-600 text-xs">Game {{ i }}</span>
        </div>
        <div class="absolute bottom-1 left-2 z-20">
          <span class="text-[10px] text-[#fca000] font-bold">{{ title }} {{ i }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  iconSrc: String,
  sectionId: String
});
</script>
