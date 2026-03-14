<script setup>
import { ref, onMounted } from 'vue';
import Header from './components/Header.vue';
import NotificationTicker from './components/NotificationTicker.vue';
import CategoryMenu from './components/CategoryMenu.vue';
import GameGrid from './components/GameGrid.vue';

const isLoaded = ref(false);

onMounted(() => {
  // Simulate loading screen
  setTimeout(() => {
    isLoaded.value = true;
  }, 1000);
});
</script>

<template>
  <div class="min-h-screen bg-[#111111] text-white font-sans w-full overflow-x-hidden">
    
    <!-- Main content container -->
    <div v-if="isLoaded" class="fade-in max-w-[480px] mx-auto bg-[#1a1a1a] min-h-screen relative shadow-2xl border-x border-gray-900 pb-20">
      <Header />
      
      <!-- Banner -->
      <div class="p-3 pt-0">
        <img src="/banner/banner.avif" alt="Banner" class="w-full h-auto rounded-lg shadow-lg" />
      </div>

      <NotificationTicker />
      
      <!-- Category Menu -->
      <CategoryMenu />

      <!-- Games Grids -->
      <GameGrid title="Popular" iconSrc="/icons/popular.avif" sectionId="popular" />
      <GameGrid title="Slots" iconSrc="/icons/slots.avif" sectionId="slots" />
      <GameGrid title="Retrô" iconSrc="/icons/retro.png" sectionId="retro" />

    </div>

    <!-- Loading Screen -->
    <div v-else class="fixed inset-0 z-50 flex items-center justify-center bg-[#1a1a1a] loading-fade">
      <div class="flex flex-col items-center">
        <img src="/image-removebg-preview.png" alt="Logo" class="w-48 animate-pulse drop-shadow-[0_0_15px_rgba(252,160,0,0.5)] mb-4" />
        <div class="w-12 h-12 border-4 border-[#fca000] border-t-transparent rounded-full animate-spin"></div>
      </div>
    </div>
  </div>
</template>

<style>
/* Global CSS additions */
:root {
  --primary-color: #fca000;
  --bg-color: #111111;
  --surface-color: #1a1a1a;
}

body {
  background-color: var(--bg-color);
  color: #fff;
}

.fade-in {
  animation: fadeIn 0.5s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}

.loading-fade {
  transition: opacity 0.5s ease;
}
</style>
