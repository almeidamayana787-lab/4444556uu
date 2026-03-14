<template>
  <div class="flex items-center justify-between bg-[#111] px-4 py-2 my-2 rounded-full mx-3 shadow-inner border border-yellow-900/20 text-sm overflow-hidden h-10">
    
    <!-- Left Icon (Speaker) -->
    <div class="z-10 bg-[#111] pr-3 flex items-center shrink-0">
      <img src="/casino_icons/sound_icon.avif" alt="Sound" class="w-5 h-5 object-contain" />
    </div>

    <!-- Ticker container (Marquee style) -->
    <div class="flex-1 overflow-hidden relative h-full flex items-center">
      <div class="marquee-content flex items-center space-x-12">
        <div v-for="(msg, index) in messages" :key="index" class="flex items-center shrink-0">
          <span class="text-[#fca000] font-bold mr-1.5">{{ msg.user }}</span>
          <span class="text-gray-400">ganhou</span>
          <span class="ml-1.5 text-[#00e676] font-extrabold">{{ msg.value }} R$</span>
          <img src="https://artpoin.com/wp-content/uploads/2023/09/artpoin-logo-pix.png" alt="Pix" class="w-4 h-4 ml-2 inline-block object-contain" />
        </div>
        <!-- Duplicate for seamless loop -->
        <div v-for="(msg, index) in messages" :key="'dup-'+index" class="flex items-center shrink-0">
          <span class="text-[#fca000] font-bold mr-1.5">{{ msg.user }}</span>
          <span class="text-gray-400">ganhou</span>
          <span class="ml-1.5 text-[#00e676] font-extrabold">{{ msg.value }} R$</span>
          <img src="https://artpoin.com/wp-content/uploads/2023/09/artpoin-logo-pix.png" alt="Pix" class="w-4 h-4 ml-2 inline-block object-contain" />
        </div>
      </div>
    </div>

    <!-- Right Icon (Message Box) : FIXED ICON -->
    <div class="z-10 bg-[#111] pl-3 flex items-center shrink-0">
      <img src="/casino_icons/mensagem_caixa.avif" alt="Message Box" class="w-5 h-5 object-contain" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const messages = [
  { user: 'user_482***', value: '100.00' },
  { user: 'user_912***', value: '50.00' },
  { user: 'user_034***', value: '250.00' },
  { user: 'user_777***', value: '500.00' },
  { user: 'user_159***', value: '1,000.00' },
];
</script>

<style scoped>
.marquee-content {
  display: flex;
  width: max-content;
  animation: marquee 25s linear infinite;
}

@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.shrink-0 {
  flex-shrink: 0;
}
</style>
