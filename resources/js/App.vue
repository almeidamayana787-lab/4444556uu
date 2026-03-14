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
import ProviderPage from './components/ProviderPage.vue';

const isLoaded = ref(false);
const currentView = ref('home');
const isLoggedIn = ref(false);
const userProfile = ref(null);
const selectedProviderCode = ref(null);

// Global settings
const globalSettings = ref({
  home_background: '/founde/eu7.png',
  home_banners: ['/banner/banner.avif'],
  category_icon_popular: '/casino_icons/popular.avif',
  category_icon_slot: '/casino_icons/slots.avif',
  category_icon_retro: '/casino_icons/retro.png',
  support_telegram: '', support_whatsapp: '', support_facebook: '', support_instagram: '',
  invite_bonus_tiers: []
});

// Real game data
const popularGames = ref([]);
const slotProviders = ref([]);

onMounted(async () => {
  if (window.location.hash === '#admin') currentView.value = 'admin';

  // Fetch settings
  try {
    const res = await fetch('/api/settings');
    if (res.ok) {
      const data = await res.json();
      if (data.home_banners) try { data.home_banners = JSON.parse(data.home_banners); } catch(e) {}
      if (data.invite_bonus_tiers) try { data.invite_bonus_tiers = JSON.parse(data.invite_bonus_tiers); } catch(e) {}
      globalSettings.value = { ...globalSettings.value, ...data };
    }
  } catch (err) { console.error('Settings fetch failed', err); }

  // Fetch popular games
  try {
    const res = await fetch('/api/games/popular');
    if (res.ok) popularGames.value = await res.json();
  } catch(e) {}

  // Fetch slot providers
  try {
    const res = await fetch('/api/games/providers');
    if (res.ok) slotProviders.value = await res.json();
  } catch(e) {}

  setTimeout(() => { isLoaded.value = true; }, 1000);
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

const openProviderPage = (providerCode) => {
  selectedProviderCode.value = providerCode;
  currentView.value = 'provider';
};
</script>

<template>
  <div class="min-h-screen bg-[#111111] text-white font-sans w-full overflow-x-hidden relative">

    <!-- Admin Panel -->
    <AdminDashboard v-if="currentView === 'admin'" @close="currentView = 'home'" />

    <!-- Provider Detail Page -->
    <ProviderPage v-else-if="currentView === 'provider'" :providerCode="selectedProviderCode" @close="currentView = 'home'" />

    <!-- App Container -->
    <div v-else-if="isLoaded" class="fade-in max-w-[440px] mx-auto bg-[#111111] min-h-screen relative shadow-2xl border-x border-yellow-900/10">

      <!-- Background -->
      <div class="absolute inset-0 z-0 bg-cover bg-top bg-no-repeat pointer-events-none" :style="{ backgroundImage: `url('${globalSettings.home_background}')` }"></div>

      <!-- Content wrapper -->
      <div class="relative z-10">
        <!-- Home View -->
        <div v-if="currentView === 'home'" class="pb-32">
          <Header />

          <!-- Banners -->
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

          <!-- Popular Games (real data from admin) -->
          <GameGrid title="Popular" :iconSrc="globalSettings.category_icon_popular" sectionId="popular" :games="popularGames" />

          <!-- Slots Section: Provider Cards -->
          <div id="slots" class="px-3 mt-6">
            <div class="flex items-center space-x-2 mb-3">
              <img :src="globalSettings.category_icon_slot" alt="Slots" class="w-6 h-6 object-contain" />
              <h2 class="text-lg font-bold text-gray-100">Slots</h2>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <!-- Real Slot Provider Cards -->
              <div 
                v-for="prov in slotProviders" :key="prov.id"
                @click="openProviderPage(prov.code)"
                class="bg-[#222] rounded-lg overflow-hidden aspect-[3/4] relative group cursor-pointer shadow-lg border border-gray-800 hover:border-[#fca000] hover:-translate-y-1 transition-all duration-300"
              >
                <img v-if="prov.cover_image" :src="prov.cover_image" class="absolute inset-0 w-full h-full object-cover" />
                <div v-else class="absolute inset-0 bg-gradient-to-br from-purple-900/40 to-[#222] flex items-center justify-center">
                  <span class="text-2xl font-black text-purple-400">{{ prov.code }}</span>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10"></div>
                <div class="absolute bottom-2 left-2 z-20">
                  <span class="text-xs text-white font-bold drop-shadow">{{ prov.name }}</span>
                </div>
              </div>
              <!-- Fallback mocks if no providers -->
              <template v-if="slotProviders.length === 0">
                <div v-for="i in 9" :key="'mock-slot-'+i" class="bg-[#222] rounded-lg overflow-hidden aspect-[3/4] relative group cursor-pointer shadow-lg border border-gray-800">
                  <div class="absolute inset-0 bg-[#333] flex items-center justify-center"><span class="text-gray-600 text-xs">Slot {{ i }}</span></div>
                  <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10"></div>
                  <div class="absolute bottom-1 left-2 z-20"><span class="text-[10px] text-[#fca000] font-bold">Slots {{ i }}</span></div>
                </div>
              </template>
            </div>
          </div>

          <!-- Retrô (keeps mock for now) -->
          <GameGrid title="Retrô" :iconSrc="globalSettings.category_icon_retro" sectionId="retro" />
        </div>

        <!-- Offers -->
        <OffersView v-else-if="currentView === 'offers'" :settings="globalSettings" @close="currentView = 'home'" />

        <!-- Register -->
        <RegisterView v-else-if="currentView === 'register'" @close="currentView = 'home'" @register-success="handleRegisterSuccess" />

        <!-- Support -->
        <SupportView v-else-if="currentView === 'support'" :settings="globalSettings" @close="currentView = 'home'" />

        <!-- Profile -->
        <ProfileView v-else-if="currentView === 'profile'" :user="userProfile" @close="currentView = 'home'" @request-register="currentView = 'register'" />

      </div>
    </div>

    <!-- Bottom Nav -->
    <BottomNav
      v-if="isLoaded && currentView !== 'admin' && currentView !== 'provider'"
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
:root { --primary-color: #fca000; --bg-color: #111111; --surface-color: #1a1a1a; }
html, body { background-color: var(--bg-color); color: #fff; scrollbar-width: none; margin: 0; padding: 0; }
body::-webkit-scrollbar { display: none; }
.fade-in { animation: fadeIn 0.4s ease-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.loading-fade { transition: opacity 0.5s ease; }
</style>
