<template>
  <header class="flex items-center justify-between px-4 py-3 bg-[#111111] sticky top-0 z-40 shadow-sm border-b border-gray-800">
    <!-- Logo area -->
    <div class="flex items-center space-x-2 relative z-50 cursor-pointer" @click="$emit('navigate', 'home')">
      <img src="/image-removebg-preview.png" alt="Logo" class="w-24 h-auto" />
    </div>

    <!-- Auth Buttons or Balance -->
    <div class="flex items-center space-x-2">
      <template v-if="!isLoggedIn">
        <button @click="$emit('navigate', 'register')" class="px-4 py-1.5 text-sm font-semibold rounded-md border border-gray-600 text-gray-300 active:scale-95 transition-all">
          Entrar
        </button>
        <button @click="$emit('navigate', 'register')" class="px-4 py-1.5 text-sm font-semibold rounded-md bg-[#fca000] text-[#111111] active:scale-95 transition-all shadow-[0_0_10px_rgba(252,160,0,0.3)]">
          Registrar
        </button>
      </template>
      <template v-else>
        <!-- Balance Display -->
        <div class="flex items-center bg-[#222] rounded-full pl-3 pr-1 py-1 border border-gray-800 space-x-2">
          <div class="flex items-baseline space-x-1" title="Saldo Total: Real + Bônus">
            <span class="text-[#fca000] text-[10px] font-bold">R$</span>
            <span class="text-white text-sm font-black">{{ (Number(user?.balance || 0) + Number(user?.bonus_balance || 0)).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
          </div>
          <button @click="$emit('navigate', 'profile')" class="w-8 h-8 rounded-full overflow-hidden border border-yellow-500/30 active:scale-90 transition">
             <img :src="user?.avatar || '/casino_icons/perfil-user/homemperfil.png'" class="w-full h-full object-cover" />
          </button>
        </div>
      </template>
    </div>
  </header>
</template>

<script setup>
const props = defineProps({
  isLoggedIn: Boolean,
  user: Object
});
defineEmits(['navigate']);
</script>
