<template>
  <div class="flex items-center justify-between bg-[#1f1f1f] px-3 py-2 my-2 rounded-lg mx-3 shadow-md border border-gray-800 text-sm overflow-hidden relative">
    
    <!-- Left Icon -->
    <img src="/casino_icons/sound_icon.avif" alt="Sound" class="w-5 h-5 z-10 bg-[#1f1f1f]" />

    <!-- Ticker container -->
    <div class="flex-1 overflow-hidden relative h-5 mx-2 flex items-center">
      <div 
        class="whitespace-nowrap flex items-center transition-transform duration-500 ease-in-out absolute left-0 text-gray-300 w-full"
        :style="{ transform: `translateY(${offset}px)`, opacity: opacity }"
      >
        <span class="mr-1 text-[#fca000] font-semibold">{{ currentMessage.user }}</span> 
        ganhou 
        <span class="ml-1 text-[#00e676] font-bold">{{ currentMessage.value }} R$</span>
        <img src="https://artpoin.com/wp-content/uploads/2023/09/artpoin-logo-pix.png" alt="Pix" class="w-4 h-4 ml-1 inline-block" />
      </div>
    </div>

    <!-- Right Icon -->
    <img src="/casino_icons/sound_icon.avif" alt="Message Box" class="w-5 h-5 z-10 bg-[#1f1f1f]" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const messages = [
  { user: 'João***', value: '100.00' },
  { user: 'Maria***', value: '50.00' },
  { user: 'Carlos***', value: '250.00' },
  { user: 'Pedro***', value: '500.00' },
  { user: 'Ana***', value: '1,000.00' },
];

const currentIndex = ref(0);
const currentMessage = ref(messages[0]);
const offset = ref(0);
const opacity = ref(1);
let interval = null;

const nextMessage = () => {
  opacity.value = 0;
  offset.value = -10;
  
  setTimeout(() => {
    currentIndex.value = (currentIndex.value + 1) % messages.length;
    currentMessage.value = messages[currentIndex.value];
    offset.value = 10;
    
    setTimeout(() => {
      opacity.value = 1;
      offset.value = 0;
    }, 50);
  }, 300);
};

onMounted(() => {
  interval = setInterval(nextMessage, 4000);
});

onUnmounted(() => {
  clearInterval(interval);
});
</script>

<style scoped>
/* Smooth transition is handled inline with Vue data binding. */
</style>
