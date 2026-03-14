```vue
<script setup>
import { ref, onMounted } from 'vue';
import Header from './components/Header.vue';
import NotificationTicker from './components/NotificationTicker.vue';
import CategoryMenu from './components/CategoryMenu.vue';
import GameGrid from './components/GameGrid.vue';
import BottomNav from './components/BottomNav.vue';
import OffersView from './components/OffersView.vue';
import RegisterView from './components/RegisterView.vue';
import SupportView from './components/SupportView.vue';
import ProfileView from './components/ProfileView.vue';

const isLoaded = ref(false);
const currentView = ref('home'); // home, offers, register, support, profile
const isLoggedIn = ref(false);
const userProfile = ref(null);

onMounted(() => {
  // Simulate loading screen
  setTimeout(() => {
    isLoaded.value = true;
  }, 1000);
});

const handleNavigate = (view) => {
  currentView.value = view;
  // window.scrollTo(0, 0); // Removed as per instruction, but not explicitly stated to remove. Keeping it for now.
  window.scrollTo(0, 0);
};

const handleRegisterSuccess = (user) => {
  userProfile.value = user;
  isLoggedIn.value = true;
  currentView.value = 'profile'; // Optional: open profile after registration
};
</script>

<template>
  <div class="min-h-screen bg-[#111111] text-white font-sans w-full overflow-x-hidden relative">
    
    <!-- App Container -->
    <div v-if="isLoaded" class="fade-in max-w-[528px] mx-auto bg-[#111111] min-h-screen relative shadow-2xl border-x border-yellow-900/10">
      
      <!-- Faded Dragon Background -->
      <div class="absolute inset-0 z-0 bg-cover bg-top bg-no-repeat bg-fixed pointer-events-none opacity-40 mix-blend-luminosity" style="background-image: url('/founde/drag.jpg');"></div>

      <!-- Content wrapper -->
      <div class="relative z-10">
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
      <OffersView v-else-if="currentView === 'offers'" @close="currentView = 'home'" />

      <!-- Register View -->
      <RegisterView v-else-if="currentView === 'register'" @close="currentView = 'home'" @register-success="handleRegisterSuccess" />

      <!-- Support View -->
      <SupportView v-else-if="currentView === 'support'" @close="currentView = 'home'" />

      <!-- Profile View -->
      <ProfileView v-else-if="currentView === 'profile'" :user="userProfile" @close="currentView = 'home'" @request-register="currentView = 'register'" />

      </div> <!-- End Content wrapper -->
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
        <img src="/image-removebg-preview.png" alt="Logo" class="w-72 animate-pulse drop-shadow-[0_0_15px_rgba(252,160,0,0.5)] mb-4" />
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
