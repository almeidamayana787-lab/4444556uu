<template>
  <div class="px-3 mt-4">
    <div class="flex items-center justify-center space-x-2 pb-3">
      <!-- Popular -->
      <a href="#popular" class="flex flex-col items-center min-w-[90px] cursor-pointer group">
        <div class="w-16 h-16 bg-[#222] rounded-2xl flex items-center justify-center mb-1.5 group-hover:bg-[#282828] transition-all border border-yellow-900/20 shadow-xl">
          <img :src="popularIcon" alt="Popular" class="w-11 h-11 object-contain drop-shadow-md scale-110" />
        </div>
        <span class="text-xs font-semibold text-gray-400 group-hover:text-[#fca000] transition-colors">Popular</span>
      </a>

      <!-- Slots -->
      <a href="#slots" class="flex flex-col items-center min-w-[90px] cursor-pointer group">
        <div class="w-16 h-16 bg-[#222] rounded-2xl flex items-center justify-center mb-1.5 group-hover:bg-[#282828] transition-all border border-yellow-900/20 shadow-xl">
          <img :src="slotsIcon" alt="Slots" class="w-11 h-11 object-contain drop-shadow-md scale-110" />
        </div>
        <span class="text-xs font-semibold text-gray-400 group-hover:text-[#fca000] transition-colors">Slots</span>
      </a>

      <!-- Retrô -->
      <a href="#retro" class="flex flex-col items-center min-w-[90px] cursor-pointer group">
        <div class="w-16 h-16 bg-[#222] rounded-2xl flex items-center justify-center mb-1.5 group-hover:bg-[#282828] transition-all border border-yellow-900/20 shadow-xl">
          <img :src="retroIcon" alt="Retrô" class="w-11 h-11 object-contain drop-shadow-md scale-110" />
        </div>
        <span class="text-xs font-semibold text-gray-400 group-hover:text-[#fca000] transition-colors">Retrô</span>
      </a>
    </div>
  </div>
</template>

<script setup>
defineProps({
  popularIcon: { type: String, default: '/casino_icons/popular.avif' },
  slotsIcon: { type: String, default: '/casino_icons/slots.avif' },
  retroIcon: { type: String, default: '/casino_icons/retro.png' }
});
</script>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
