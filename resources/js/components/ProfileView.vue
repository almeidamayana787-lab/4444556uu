<template>
  <div class="flex flex-col min-h-screen bg-[#1a1a1a] text-white overflow-y-auto pb-24">
    
    <!-- Header banner area -->
    <div class="relative w-full h-44 overflow-hidden rounded-b-3xl">
      <!-- Background curve/gradient -->
      <div class="absolute inset-0 bg-gradient-to-br from-[#2a2a2a] to-[#111]">
        <svg class="absolute bottom-0 w-full h-auto text-[#ffffff05]" viewBox="0 0 1440 320" preserveAspectRatio="none">
          <path fill="currentColor" fill-opacity="1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,197.3C672,192,768,160,864,154.7C960,149,1056,171,1152,192C1248,213,1344,235,1392,245.3L1440,256L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
      </div>

      <div class="relative z-10 p-4 h-full flex flex-col justify-between">
        <button @click="$emit('close')" class="flex items-center space-x-1 mt-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <div class="flex items-center justify-between mt-auto mb-2">
          <div class="flex items-center space-x-3">
            <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-200 border-2 border-transparent">
              <!-- Avatar or placeholder -->
              <img v-if="user" :src="user.avatar" class="w-full h-full object-cover" />
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-full w-full text-gray-400 bg-white" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="flex flex-col">
              <template v-if="user">
                <span class="text-white font-bold text-sm">{{ user.name }}</span>
                <span class="text-gray-400 text-xs mt-1 bg-black/30 px-2 py-0.5 rounded shadow-inner">ID: {{ user.id }}</span>
              </template>
              <template v-else>
                <div class="w-28 text-[12px] leading-tight text-gray-200">Por favor, primeiro Login Ou Registro</div>
              </template>
            </div>
          </div>
          
          <div v-if="!user" class="flex space-x-2">
            <button @click="$emit('request-register', 'login')" class="bg-[#fca000] text-black font-bold text-xs px-3 py-1.5 rounded shadow">Login</button>
            <div class="relative">
              <button @click="$emit('request-register', 'register')" class="border border-[#fca000] text-[#fca000] font-bold text-xs px-3 py-1.5 rounded shadow bg-black/20">Registro</button>
              <img src="/casino_icons/presente.avif" class="absolute -top-4 -right-2 w-6 h-6 animate-bounce" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Saques / Deposito -->
    <div class="px-4 -mt-2 relative z-20">
      <div class="flex justify-between bg-[#242424] rounded-xl shadow-xl overflow-hidden divide-x divide-gray-800">
        <button class="flex flex-col items-center justify-center p-3 w-1/2 hover:bg-[#2a2a2a] transition">
          <img src="/casino_icons/perfil-user/deposito.avif" class="w-10 h-10 object-contain mb-1 drop-shadow-md" />
          <span class="text-gray-300 text-xs font-semibold">Saques</span>
        </button>
        <button class="flex flex-col items-center justify-center p-3 w-1/2 hover:bg-[#2a2a2a] transition">
          <!-- Using a standard wallet icon for Deposito as visual fallback -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-400 drop-shadow-md mb-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
          </svg>
          <span class="text-gray-300 text-xs font-semibold">Depósito</span>
        </button>
      </div>
    </div>

    <!-- Main Menu List -->
    <div class="mt-4 flex flex-col space-y-[1px] bg-gray-800">

      <button class="flex items-center justify-between p-4 bg-[#1a1a1a] hover:bg-[#222] transition w-full">
        <div class="flex items-center space-x-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#fca000]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
          <span class="text-gray-200 text-sm font-bold">Meus Registros</span>
        </div>
        <div class="flex items-center space-x-2">
          <span class="text-gray-500 text-xs">Detalhes, apostas, relatórios...</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </div>
      </button>

      <button class="flex items-center justify-between p-4 bg-[#1a1a1a] hover:bg-[#222] transition w-full">
        <div class="flex items-center space-x-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
          <span class="text-gray-200 text-sm font-bold">Gestão Retiradas</span>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
      </button>

      <div class="h-4 bg-[#111]"></div> <!-- Divider -->

      <button class="flex items-center justify-between p-4 bg-[#1a1a1a] hover:bg-[#222] transition w-full">
        <div class="flex items-center space-x-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#fca000]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
          <span class="text-gray-200 text-sm font-bold">Promoção</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="text-[#fca000] text-xs font-medium">Renda mensal de um milhão</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </div>
      </button>

      <button class="flex items-center justify-between p-4 bg-[#1a1a1a] hover:bg-[#222] transition w-full">
        <div class="flex items-center space-x-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#fca000]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
          <span class="text-gray-200 text-sm font-bold">Segurança</span>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
      </button>

      <button class="flex items-center justify-between p-4 bg-[#1a1a1a] hover:bg-[#222] transition w-full">
        <div class="flex items-center space-x-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#fca000]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
          <span class="text-gray-200 text-sm font-bold">Encontre-nos</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="text-gray-500 text-xs">Evite que abra</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </div>
      </button>

      <button class="flex items-center justify-between p-4 bg-[#1a1a1a] hover:bg-[#222] transition w-full">
        <div class="flex items-center space-x-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#fca000]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          <span class="text-gray-200 text-sm font-bold">FAQ</span>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
      </button>
      
      <button class="flex items-center justify-between p-4 bg-[#1a1a1a] hover:bg-[#222] transition w-full">
        <div class="flex items-center space-x-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#fca000]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
          <span class="text-gray-200 text-sm font-bold">Bônus de Sugestão</span>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
      </button>

      <button class="flex items-center justify-between p-4 bg-[#1a1a1a] hover:bg-[#222] transition w-full">
        <div class="flex items-center space-x-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#fca000]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
          <span class="text-gray-200 text-sm font-bold">Faça login no dispositivo</span>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
      </button>

      <button class="flex items-center justify-between p-4 bg-[#1a1a1a] hover:bg-[#222] transition w-full">
        <div class="flex items-center space-x-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#fca000]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          <span class="text-gray-200 text-sm font-bold">Sobre Nós</span>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  user: {
    type: Object,
    default: null
  }
});
defineEmits(['close', 'request-register']);
</script>
