<template>
  <nav class="fixed bottom-0 left-0 right-0 z-50 flex justify-center pb-safe">
    <div class="w-full max-w-[480px] bg-[#1a1a1a] border-t border-yellow-900/30 flex items-center justify-around py-2 px-1 shadow-[0_-5px_15px_rgba(0,0,0,0.5)]">
      
      <!-- Começar -->
      <button class="flex flex-col items-center justify-center space-y-1 flex-1 group">
        <div class="relative">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#fca000]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
        </div>
        <span class="text-[10px] text-[#fca000] font-bold">Começar</span>
      </button>

      <!-- Ofertas -->
      <button class="flex flex-col items-center justify-center space-y-1 flex-1 group">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400 group-hover:text-[#fca000] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
        </svg>
        <span class="text-[10px] text-gray-500 group-hover:text-[#fca000]">Ofertas</span>
      </button>

      <!-- Registro -->
      <button class="flex flex-col items-center justify-center space-y-1 flex-1 group">
        <div class="relative">
           <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400 group-hover:text-[#fca000] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
          </svg>
          <span class="absolute -top-2 -right-2 bg-green-600 text-[8px] text-white px-1 rounded-full animate-pulse">2-9</span>
        </div>
        <span class="text-[10px] text-gray-500 group-hover:text-[#fca000]">Registro</span>
      </button>

      <!-- Suporte -->
      <button class="flex flex-col items-center justify-center space-y-1 flex-1 group">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400 group-hover:text-[#fca000] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 18v-6a9 9 0 0118 0v6M3 18a3 3 0 106 0v-5M21 18a3 3 0 11-6 0v-5m-3 12h.01" />
        </svg>
        <span class="text-[10px] text-gray-500 group-hover:text-[#fca000]">Suporte</span>
      </button>

      <!-- Perfil -->
      <button class="flex flex-col items-center justify-center space-y-1 flex-1 group">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400 group-hover:text-[#fca000] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <span class="text-[10px] text-gray-500 group-hover:text-[#fca000]">Perfil</span>
      </button>

    </div>
  </nav>
</template>

<script setup>
</script>

<style scoped>
.pb-safe {
  padding-bottom: env(safe-area-inset-bottom);
}
</style>
