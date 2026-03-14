<script setup>
import { ref, onMounted } from 'vue';
import Header from './components/Header.vue';
import NotificationTicker from './components/NotificationTicker.vue';
import CategoryMenu from './components/CategoryMenu.vue';
import GameGrid from './components/GameGrid.vue';
import BottomNav from './components/BottomNav.vue';
import OffersView from './components/OffersView.vue';
import RegisterView from './components/RegisterView.vue';

const isLoaded = ref(false);
const currentView = ref('home'); // 'home', 'offers', 'register'
const isLoggedIn = ref(false);

onMounted(() => {
  // Simulate loading screen
  setTimeout(() => {
    isLoaded.value = true;
  }, 1000);
});

const handleNavigate = (view) => {
  currentView.value = view;
  window.scrollTo(0, 0);
};

const handleRegister = () => {
  isLoggedIn.value = true;
  currentView.value = 'home';
};
</script>

<template>
  <div class="min-h-screen bg-[#111111] text-white font-sans w-full overflow-x-hidden relative">
    
    <!-- App Container -->
    <div v-if="isLoaded" class="fade-in max-w-[528px] mx-auto bg-[#1a1a1a] min-h-screen relative shadow-2xl border-x border-yellow-900/10">
      
      <!-- Home View -->
      <div v-if="currentView === 'home'" class="pb-32">
        <Header />
        
        <!-- Banner -->
        <div class="p-3 pt-0">
          <img src="/banner/banner.avif" alt="Banner" class="w-full h-auto rounded-xl shadow-[0_10px_20px_rgba(0,0,0,0.4)] border border-yellow-900/10" />
        </div>

        <NotificationTicker />
        
        <!-- Category Menu -->
        <CategoryMenu />

        <!-- Games Grids -->
        <GameGrid title="Popular" iconSrc="/casino_icons/popular.avif" sectionId="popular" />
        <GameGrid title="Slots" iconSrc="/casino_icons/slots.avif" sectionId="slots" />
        <GameGrid title="Retrô" iconSrc="/casino_icons/retro.png" sectionId="retro" />
      </div>

      <!-- Offers View -->
      <OffersView v-else-if="currentView === 'offers'" @close="handleNavigate('home')" />

      <!-- Register View -->
      <RegisterView v-else-if="currentView === 'register'" @close="handleNavigate('home')" @registered="handleRegister" />

    </div>

    <!-- Bottom Navigation - Placed top-level -->
    <BottomNav 
      v-if="isLoaded" 
      :currentView="currentView" 
      :isLoggedIn="isLoggedIn"
      @navigate="handleNavigate" 
    />

    <!-- Loading Screen -->
    <div v-else class="fixed inset-0 z-[200] flex items-center justify-center bg-[#1a1a1a] loading-fade">
      <div class="flex flex-col items-center">
        <img src="/image-removebg-preview.png" alt="Logo" class="w-60 animate-pulse drop-shadow-[0_0_15px_rgba(252,160,0,0.5)] mb-4" />
        <div class="w-12 h-12 border-4 border-[#fca000] border-t-transparent rounded-full animate-spin"></div>
      </div>
    </div>
  </div>
</template>

<style>
/* Global CSS */
:root {
  --primary-color: #fca000;
  --bg-color: #111111;
  --surface-color: #1a1a1a;
}

html, body {
  background-color: var(--bg-color);
  color: #fff;
  scrollbar-width: none;
  margin: 0;
  padding: 0;
}

body::-webkit-scrollbar {
  display: none;
}

.fade-in {
  animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}

.loading-fade {
  transition: opacity 0.5s ease;
}
</style>
