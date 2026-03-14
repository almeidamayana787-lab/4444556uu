<template>
  <div class="px-3 mt-4">
    <div class="flex items-center space-x-3 overflow-x-auto hide-scrollbar pb-2">
      <!-- Popular -->
      <a href="#popular" class="flex flex-col items-center min-w-[80px] cursor-pointer group">
        <div class="w-14 h-14 bg-[#222] rounded-2xl flex items-center justify-center mb-1 group-hover:bg-[#282828] transition-all border border-yellow-900/20 shadow-lg">
          <img src="/casino_icons/popular.avif" alt="Popular" class="w-10 h-10 object-contain drop-shadow-md" />
        </div>
        <span class="text-[11px] font-medium text-gray-400 group-hover:text-[#fca000] transition-colors">Popular</span>
      </a>

      <!-- Slots -->
      <a href="#slots" class="flex flex-col items-center min-w-[80px] cursor-pointer group">
        <div class="w-14 h-14 bg-[#222] rounded-2xl flex items-center justify-center mb-1 group-hover:bg-[#282828] transition-all border border-yellow-900/20 shadow-lg">
          <img src="/casino_icons/slots.avif" alt="Slots" class="w-10 h-10 object-contain drop-shadow-md" />
        </div>
        <span class="text-[11px] font-medium text-gray-400 group-hover:text-[#fca000] transition-colors">Slots</span>
      </a>

      <!-- Retrô -->
      <a href="#retro" class="flex flex-col items-center min-w-[80px] cursor-pointer group">
        <div class="w-14 h-14 bg-[#222] rounded-2xl flex items-center justify-center mb-1 group-hover:bg-[#282828] transition-all border border-yellow-900/20 shadow-lg">
          <img src="/casino_icons/retro.png" alt="Retrô" class="w-10 h-10 object-contain drop-shadow-md" />
        </div>
        <span class="text-[11px] font-medium text-gray-400 group-hover:text-[#fca000] transition-colors">Retrô</span>
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
