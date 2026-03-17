<template>
  <div class="flex flex-col flex-1 bg-[#1a1a1a] text-white pb-32 overflow-y-auto">
    <!-- Header -->
    <header class="flex items-center justify-between px-4 py-3 bg-[#121212] sticky top-0 z-50 border-b border-[#fca000]/10">
      <button @click="$emit('close')" class="p-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      <h1 class="text-base font-bold tracking-tight">Atendimento ao Cl...</h1>
      <div class="w-6"></div> <!-- Spacer for centering -->
    </header>

    <div class="p-4 space-y-4">
      
      <!-- Top Support Card -->
      <div class="bg-[#242424] rounded-xl p-4 flex flex-col space-y-4 shadow-lg border border-gray-800">
        <div class="flex items-center space-x-4">
          <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-transparent bg-gradient-to-br from-gray-700 to-gray-900 shrink-0">
            <img src="/casino_icons/suporte.avif" alt="Support" class="w-full h-full object-cover" />
          </div>
          <div>
            <h2 class="text-white font-bold text-lg leading-tight">Apoio online 24/7</h2>
            <p class="text-gray-400 text-[11px] mt-1 leading-snug">Converse com o serviço profissional de apoio ao cliente online para resolver os seus problemas</p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <!-- Serviço Online -->
          <button class="border border-[#fca000] border-opacity-60 rounded-lg p-2 flex items-center justify-center space-x-2 bg-[#2a2a2a] hover:bg-[#333] transition-colors">
            <img src="/casino_icons/suporte.avif" class="w-5 h-5 rounded-full object-cover" />
            <span class="text-[#fca000] font-bold text-[11px]">Serviço On-line</span>
          </button>
          
          <!-- Telegram -->
          <button class="border border-[#fca000] border-opacity-60 rounded-lg p-2 flex items-center justify-center space-x-2 bg-[#2a2a2a] hover:bg-[#333] transition-colors">
            <img src="/casino_icons/img_tg.png" class="w-5 h-5 object-contain" />
            <span class="text-[#fca000] font-bold text-[11px]">Telegram</span>
          </button>

          <!-- WhatsApp -->
          <button class="border border-[#fca000] border-opacity-60 rounded-lg p-2 flex items-center justify-center space-x-2 bg-[#242424] hover:bg-[#333] transition-colors">
            <img src="/casino_icons/img_wa.png" class="w-5 h-5 object-contain" />
            <span class="text-[#fca000] font-bold text-[11px]">WhatsApp</span>
          </button>

          <!-- Facebook -->
          <button class="border border-[#fca000] border-opacity-60 rounded-lg p-2 flex items-center justify-center space-x-2 bg-[#242424] hover:bg-[#333] transition-colors">
            <img src="/casino_icons/img_facebook.png" class="w-5 h-5 object-contain" />
            <span class="text-[#fca000] font-bold text-[11px]">Facebook</span>
          </button>
          
          <!-- Instagram -->
          <button class="border border-[#fca000] border-opacity-60 rounded-lg p-2 flex items-center justify-center space-x-2 bg-[#242424] hover:bg-[#333] transition-colors">
            <img src="/casino_icons/instamg.avif" class="w-5 h-5 object-contain" />
            <span class="text-[#fca000] font-bold text-[11px]">Instagram</span>
          </button>
        </div>
      </div>

      <!-- Channels Section -->
      <div class="bg-[#242424] rounded-xl overflow-hidden border border-gray-800">
        <!-- Tabs -->
        <div class="flex items-center overflow-x-auto border-b border-gray-700 hide-scrollbar px-2 bg-[#1a1a1a]">
          <button class="flex items-center space-x-2 px-4 py-3 border-b-2 border-[#fca000] shrink-0">
            <img src="/casino_icons/img_tg.png" class="w-4 h-4 object-contain" />
            <span class="text-[#fca000] text-[11px] font-bold">Telegram Suporte</span>
          </button>
          <button class="flex items-center space-x-2 px-4 py-3 shrink-0 opacity-50">
            <img src="/casino_icons/img_facebook.png" class="w-4 h-4 object-contain" />
            <span class="text-white text-[11px] font-bold">Facebook Suporte</span>
          </button>
          <button class="flex items-center space-x-2 px-4 py-3 shrink-0 opacity-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
            </svg>
            <span class="text-white text-[11px] font-bold">Outros</span>
          </button>
        </div>

        <!-- List -->
        <div class="p-4 space-y-3">
          
          <div class="bg-[#333] rounded-lg p-3 flex items-center justify-between shadow-inner">
            <div class="flex items-center space-x-3">
              <img src="/casino_icons/img_tg.png" class="w-10 h-10 object-contain drop-shadow" />
              <div class="flex flex-col">
                <span class="text-gray-400 text-[10px] font-medium">30win</span>
                <span class="text-white text-xs font-bold">aa30win</span>
              </div>
            </div>
            <button class="bg-gradient-to-r from-[#ffd700] to-[#fca000] text-black text-[10px] font-bold px-3 py-2 rounded-md shadow-md active:scale-95 transition-transform">
              Contactar agora
            </button>
          </div>

          <div class="bg-[#333] rounded-lg p-3 flex items-center justify-between shadow-inner">
            <div class="flex items-center space-x-3">
              <img src="/casino_icons/img_tg.png" class="w-10 h-10 object-contain drop-shadow" />
              <div class="flex flex-col">
                <span class="text-gray-400 text-[10px] font-medium">30win.pro | Canal Oficial ®</span>
                <span class="text-white text-xs font-bold truncate max-w-[120px]">addlist/xJQ9JBad...</span>
              </div>
            </div>
            <button class="bg-gradient-to-r from-[#ffd700] to-[#fca000] text-black text-[10px] font-bold px-3 py-2 rounded-md shadow-md active:scale-95 transition-transform">
              Contactar agora
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineEmits(['close']);
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
