<template>
  <div class="px-3 mt-4">
    <div class="flex items-center space-x-4 overflow-x-auto hide-scrollbar pb-2">
      <!-- Popular -->
      <a href="#popular" class="flex flex-col items-center min-w-[70px] cursor-pointer group">
        <div class="w-12 h-12 bg-[#2a2a2a] rounded-xl flex items-center justify-center mb-1 group-hover:bg-[#333] transition-colors border border-gray-700">
          <img src="/icons/popular.avif" alt="Popular" class="w-8 h-8 drop-shadow-lg" />
        </div>
        <span class="text-xs text-gray-400 group-hover:text-white transition-colors">Popular</span>
      </a>

      <!-- Slots -->
      <a href="#slots" class="flex flex-col items-center min-w-[70px] cursor-pointer group">
        <div class="w-12 h-12 bg-[#2a2a2a] rounded-xl flex items-center justify-center mb-1 group-hover:bg-[#333] transition-colors border border-gray-700">
          <img src="/icons/slots.avif" alt="Slots" class="w-8 h-8 drop-shadow-lg" />
        </div>
        <span class="text-xs text-gray-400 group-hover:text-white transition-colors">Slots</span>
      </a>

      <!-- Retrô -->
      <a href="#retro" class="flex flex-col items-center min-w-[70px] cursor-pointer group">
        <div class="w-12 h-12 bg-[#2a2a2a] rounded-xl flex items-center justify-center mb-1 group-hover:bg-[#333] transition-colors border border-gray-700">
          <img src="/icons/retro.png" alt="Retrô" class="w-8 h-8 drop-shadow-lg" />
        </div>
        <span class="text-xs text-gray-400 group-hover:text-white transition-colors">Retrô</span>
      </a>
    </div>
  </div>
</template>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
