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
import AdminDashboard from './components/AdminDashboard.vue';

const isLoaded = ref(false);
const currentView = ref('home');
const isLoggedIn = ref(false);
const userProfile = ref(null);

// Global settings fetched from database
const globalSettings = ref({
  home_background: '/founde/eu7.png',
  home_banners: ['/banner/banner.avif'],
  category_icon_popular: '/casino_icons/popular.avif',
  category_icon_slot: '/casino_icons/slots.avif',
  category_icon_retro: '/casino_icons/retro.png',
  support_telegram: '',
  support_whatsapp: '',
  support_facebook: '',
  support_instagram: '',
  invite_bonus_tiers: []
});

onMounted(async () => {
  // Check if URL has #admin hash
  if (window.location.hash === '#admin') {
    currentView.value = 'admin';
  }

  // Fetch global settings
  try {
    const res = await fetch('/api/settings');
    if (res.ok) {
      const data = await res.json();
      if (data.home_banners) data.home_banners = JSON.parse(data.home_banners);
      if (data.invite_bonus_tiers) data.invite_bonus_tiers = JSON.parse(data.invite_bonus_tiers);
      globalSettings.value = { ...globalSettings.value, ...data };
    }
  } catch (err) {
    console.error('Settings fetch failed', err);
  }

  setTimeout(() => {
    isLoaded.value = true;
  }, 1000);
});

const handleNavigate = (view) => {
  currentView.value = view;
  window.scrollTo(0, 0);
};

const handleRegisterSuccess = (user) => {
  userProfile.value = user;
  isLoggedIn.value = true;
  currentView.value = 'profile';
};
</script>

<template>
  <div class="min-h-screen bg-[#111111] text-white font-sans w-full overflow-x-hidden relative">

    <!-- Admin Panel (full-screen, no bottom nav) -->
    <AdminDashboard v-if="currentView === 'admin'" @close="currentView = 'home'" />

    <!-- App Container -->
    <div v-else-if="isLoaded" class="fade-in max-w-[528px] mx-auto bg-[#111111] min-h-screen relative shadow-2xl border-x border-yellow-900/10">

      <!-- Main Background Image (Raw) -->
      <div class="absolute inset-0 z-0 bg-cover bg-top bg-no-repeat pointer-events-none" :style="{ backgroundImage: `url('${globalSettings.home_background}')` }"></div>

      <!-- Content wrapper -->
      <div class="relative z-10">
        <!-- Home View -->
        <div v-if="currentView === 'home'" class="pb-32">
          <Header />

          <!-- Banner Carousel -->
          <div class="p-3 pt-0">
            <img v-for="(banner, i) in globalSettings.home_banners" :key="i" :src="banner" alt="Banner" class="w-full h-auto rounded-xl shadow-[0_10px_20px_rgba(0,0,0,0.4)] border border-yellow-900/10 mb-2" />
          </div>

          <NotificationTicker />

          <!-- Category Menu -->
          <CategoryMenu
            :popularIcon="globalSettings.category_icon_popular"
            :slotsIcon="globalSettings.category_icon_slot"
            :retroIcon="globalSettings.category_icon_retro"
          />

          <!-- Games Grids -->
          <GameGrid title="Popular" :iconSrc="globalSettings.category_icon_popular" sectionId="popular" />
          <GameGrid title="Slots" :iconSrc="globalSettings.category_icon_slot" sectionId="slots" />
          <GameGrid title="Retrô" :iconSrc="globalSettings.category_icon_retro" sectionId="retro" />
        </div>

        <!-- Offers View -->
        <OffersView v-else-if="currentView === 'offers'" :settings="globalSettings" @close="currentView = 'home'" />

        <!-- Register View -->
        <RegisterView v-else-if="currentView === 'register'" @close="currentView = 'home'" @register-success="handleRegisterSuccess" />

        <!-- Support View -->
        <SupportView v-else-if="currentView === 'support'" :settings="globalSettings" @close="currentView = 'home'" />

        <!-- Profile View -->
        <ProfileView v-else-if="currentView === 'profile'" :user="userProfile" @close="currentView = 'home'" @request-register="currentView = 'register'" />

      </div> <!-- End Content wrapper -->
    </div>

    <!-- Bottom Navigation -->
    <BottomNav
      v-if="isLoaded && currentView !== 'admin'"
      :currentView="currentView"
      :isLoggedIn="isLoggedIn"
      @navigate="handleNavigate"
    />

    <!-- Loading Screen -->
    <div v-if="!isLoaded && currentView !== 'admin'" class="fixed inset-0 z-[200] flex items-center justify-center bg-[#1a1a1a] loading-fade">
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
