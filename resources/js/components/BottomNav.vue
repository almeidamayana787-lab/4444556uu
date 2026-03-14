<template>
  <!-- Fixed container that always spans viewport width -->
  <nav class="fixed bottom-0 left-0 right-0 z-[100] flex justify-center translate-y-[1px]">
    <!-- Centered bar that mimics the App.vue max-width -->
    <div class="w-full max-w-[440px] bg-[#0d0d0d] border-t border-[#fca000]/20 flex items-center justify-around h-20 px-2 shadow-[0_-8px_20px_rgba(0,0,0,0.8)] pointer-events-auto rounded-t-xl mb-0">
      
      <!-- Começar -->
      <button 
        @click="$emit('navigate', 'home')"
        class="flex flex-col items-center justify-center flex-1 transition-all active:scale-95 group"
      >
        <div class="relative mb-0.5">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" :class="currentView === 'home' ? 'text-[#fca000] drop-shadow-[0_0_8px_rgba(252,160,0,0.5)]' : 'text-gray-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
        </div>
        <span class="text-[12px] uppercase tracking-wider font-bold" :class="currentView === 'home' ? 'text-[#fca000]' : 'text-gray-500'">Começar</span>
      </button>

      <!-- Ofertas -->
      <button 
        @click="$emit('navigate', 'offers')"
        class="flex flex-col items-center justify-center flex-1 transition-all active:scale-95 group"
      >
        <div class="mb-0.5" :class="currentView === 'offers' ? 'text-[#fca000]' : 'text-gray-500'">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
          </svg>
        </div>
        <span class="text-[12px] font-medium" :class="currentView === 'offers' ? 'text-[#fca000] font-bold' : 'text-gray-500'">Ofertas</span>
      </button>

      <!-- Registro (Hidden if logged in) -->
      <button 
        @click="$emit('navigate', 'register')"
        v-if="!isLoggedIn"
        class="flex flex-col items-center justify-center flex-1 transition-all active:scale-95 group"
      >
        <div class="relative mb-0.5">
           <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" :class="currentView === 'register' ? 'text-[#fca000]' : 'text-gray-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
          </svg>
          <span class="absolute -top-2 -right-3 bg-[#00e676] text-[9px] text-black px-1.5 py-0.5 rounded-full font-black shadow-lg animate-bounce ring-1 ring-black">2-9</span>
        </div>
        <span class="text-[12px] font-medium" :class="currentView === 'register' ? 'text-[#fca000] font-bold' : 'text-gray-500'">Registro</span>
      </button>

      <!-- Suporte -->
      <button 
        @click="$emit('navigate', 'support')"
        class="flex flex-col items-center justify-center flex-1 transition-all active:scale-95 group"
      >
        <div class="mb-0.5" :class="currentView === 'support' ? 'text-[#fca000]' : 'text-gray-500 group-hover:text-gray-300'">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 18v-6a9 9 0 0118 0v6M3 18a3 3 0 106 0v-5M21 18a3 3 0 11-6 0v-5m-3 12h.01" />
          </svg>
        </div>
        <span class="text-[12px] font-medium" :class="currentView === 'support' ? 'text-[#fca000] font-bold' : 'text-gray-500 group-hover:text-gray-300'">Suporte</span>
      </button>

      <!-- Perfil -->
      <button 
        @click="$emit('navigate', 'profile')"
        class="flex flex-col items-center justify-center flex-1 transition-all active:scale-95 group"
      >
        <div class="mb-0.5" :class="currentView === 'profile' ? 'text-[#fca000]' : 'text-gray-500 group-hover:text-gray-300'">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
        <span class="text-[12px] font-medium" :class="currentView === 'profile' ? 'text-[#fca000] font-bold' : 'text-gray-500 group-hover:text-gray-300'">Perfil</span>
      </button>

    </div>
  </nav>
</template>

<script setup>
defineProps({
  currentView: String,
  isLoggedIn: Boolean
});
defineEmits(['navigate']);
</script>
