<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  url: { type: String, required: true }
});

const emit = defineEmits(['close']);
const isLoading = ref(true);

const handleIframeLoad = () => {
  isLoading.value = false;
};

const handleMessage = (event) => {
  if (event.data && (event.data.type === 'game_win' || event.data.type === 'game_loss')) {
    emit('close', event.data.type);
  }
};

onMounted(() => {
  window.addEventListener('message', handleMessage);
});

onUnmounted(() => {
  window.removeEventListener('message', handleMessage);
});


const handleBack = () => {
  emit('close');
};
</script>

<template>
  <div class="fixed inset-0 z-[100] bg-black flex flex-col w-full h-full overflow-hidden">
    <!-- Overlay/Header with Back Button -->
    <div class="absolute top-4 left-4 z-[110]">
      <button 
        @click="handleBack" 
        class="group flex items-center justify-center w-12 h-12 bg-black/50 backdrop-blur-md border border-white/10 rounded-full hover:bg-[#fca000] hover:border-[#fca000] transition-all active:scale-95 shadow-2xl"
        title="Voltar para o Início"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white group-hover:text-black transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
    </div>

    <!-- Loading Screen -->
    <div v-if="isLoading" class="absolute inset-0 z-[105] bg-[#050505] flex flex-col items-center justify-center">
      <div class="w-16 h-16 border-4 border-[#fca000]/20 border-t-[#fca000] rounded-full animate-spin"></div>
      <p class="mt-4 text-gray-400 font-medium animate-pulse">Preparando sua diversão...</p>
    </div>

    <!-- Game Iframe -->
    <iframe 
      :src="url" 
      class="w-full h-full border-none"
      allow="fullscreen; autoplay; encrypted-media; gyroscope; accelerometer"
      @load="handleIframeLoad"
    ></iframe>
  </div>
</template>

<style scoped>
iframe {
  background: #000;
}
</style>
