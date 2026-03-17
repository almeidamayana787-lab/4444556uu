<script setup>
import { ref, onMounted, computed, provide, watch } from 'vue';
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
import GamePlayer from './components/GamePlayer.vue';

const isLoaded = ref(false);
const currentView = ref('home');
const isLoggedIn = ref(false);
const userProfile = ref(null);
const selectedProviderCode = ref(null);
const lobbyInitialMode = ref('slots'); // 'popular', 'slots', 'retro'
const currentGameUrl = ref('');
const previousView = ref('home');

// Global settings
const globalSettings = ref({
  home_background: '/founde/eu7.png',
  home_banners: ['/banner/banner.avif'],
  category_icon_popular: '/casino_icons/popular.avif',
  category_icon_slot: '/casino_icons/slots.avif',
  category_icon_retro: '/casino_icons/retro.png',
  support_telegram: '', support_whatsapp: '', support_facebook: '', support_instagram: '',
  invite_bonus_tiers: [],
  entry_popups: [],
  floating_popups: [],
  min_deposit: 1,
  min_withdrawal: 10
});

const closedPopups = ref(JSON.parse(localStorage.getItem('closed_popups') || '[]'));
const activePopups = computed(() => {
  return (globalSettings.value.floating_popups || []).filter((p, index) => {
    return !closedPopups.value.includes(`popup_${index}`);
  });
});

const closePopup = (index) => {
  closedPopups.value.push(`popup_${index}`);
  localStorage.setItem('closed_popups', JSON.stringify(closedPopups.value));
};

const closedEntryPopups = ref(JSON.parse(localStorage.getItem('closed_entry_popups') || '[]'));
const activeEntryPopups = computed(() => {
  return (globalSettings.value.entry_popups || []).filter((p, index) => {
    if (closedEntryPopups.value.includes(`entry_popup_${index}`)) return false;
    if (p.visibility === 'logged_in' && !isLoggedIn.value) return false;
    if (p.visibility === 'logged_out' && isLoggedIn.value) return false;
    return true;
  });
});

const closeEntryPopup = (index) => {
  closedEntryPopups.value.push(`entry_popup_${index}`);
  localStorage.setItem('closed_entry_popups', JSON.stringify(closedEntryPopups.value));
};

// Real game data
const popularGames = ref([]);
const slotProviders = ref([]);
const retroGamesPublic = ref([]);

// Retro Launch State
const showRetroBetModal = ref(false);
const selectedRetroGame = ref(null);
const retroBetAmount = ref(1.00);
const isLaunchingRetro = ref(false);

const fetchPublicRetroGames = async () => {
  console.log('[RetroDebug] Fetching public retro games...');
  try {
    const res = await fetch('/api/retro/games');
    if (res.ok) {
      const all = await res.json();
      console.log('[RetroDebug] API returned total games:', all.length);
      const active = all.filter(g => g.is_active);
      console.log('[RetroDebug] Active games found:', active.length, active.map(g => g.id));
      retroGamesPublic.value = active.map(g => ({
        id: g.id,
        game_code: g.game_code,
        game_name: g.name,
        banner_local: g.banner,
        play_url: g.play_url,
        is_retro: !g.is_database_game,
        is_database_game: g.is_database_game
      }));
    } else {
      console.error('[RetroDebug] Fetch failed with status:', res.status);
    }
  } catch(e) { console.error('[RetroDebug] Fetch exception:', e); }
};

onMounted(async () => {
  if (window.location.hash === '#admin') currentView.value = 'admin';

  // Check Auth
  const token = localStorage.getItem('casino_token');
  if (token) {
    try {
      const res = await fetch('/api/me', {
        headers: { 'Authorization': `Bearer ${token}` }
      });
      if (res.ok) {
        userProfile.value = await res.json();
        isLoggedIn.value = true;
      } else {
        localStorage.removeItem('casino_token');
      }
    } catch (err) { console.error('Auth check failed', err); }
  }

  // Fetch initial data
  try {
    const res = await fetch('/api/settings');
    if (res.ok) {
      const data = await res.json();
      if (data.home_banners) {
        try {
          data.home_banners = typeof data.home_banners === 'string' ? JSON.parse(data.home_banners) : data.home_banners;
        } catch(e) { console.error('Error parsing home_banners', e); }
      }
      if (data.invite_bonus_tiers) {
        try {
          data.invite_bonus_tiers = typeof data.invite_bonus_tiers === 'string' ? JSON.parse(data.invite_bonus_tiers) : data.invite_bonus_tiers;
        } catch(e) { console.error('Error parsing invite_bonus_tiers', e); }
      }
      if (data.floating_popups) {
        try {
          data.floating_popups = typeof data.floating_popups === 'string' ? JSON.parse(data.floating_popups) : data.floating_popups;
        } catch(e) { console.error('Error parsing floating_popups', e); }
      }
      if (data.entry_popups) {
        try {
          data.entry_popups = typeof data.entry_popups === 'string' ? JSON.parse(data.entry_popups) : data.entry_popups;
        } catch(e) { console.error('Error parsing entry_popups', e); }
      }
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

  // Fetch active retro games
  await fetchPublicRetroGames();

  setTimeout(() => { isLoaded.value = true; }, 1000);
});

// Refresh data when navigating back to home
watch(currentView, (newView) => {
  console.log('[RetroDebug] View changed to:', newView);
  if (newView === 'home' || newView === 'lobby') { // Support both just in case
    fetchPublicRetroGames();
    // Also refresh other dynamic data
    fetch('/api/games/popular').then(r => r.ok && r.json().then(d => popularGames.value = d));
    fetch('/api/games/providers').then(r => r.ok && r.json().then(d => slotProviders.value = d));
  }
});

const handleNavigate = (view) => {
  currentView.value = view;
  window.scrollTo(0, 0);
};

const handleRegisterSuccess = (user) => {
  userProfile.value = user;
  isLoggedIn.value = true;
  currentView.value = 'home'; // Go home or profile
};

const openProviderPage = (providerCode) => {
  selectedProviderCode.value = providerCode;
  lobbyInitialMode.value = 'slots';
  currentView.value = 'provider';
};

const handleSeeMore = (sectionId) => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
  // Map section IDs to lobby modes
  if (sectionId === 'popular') {
    lobbyInitialMode.value = 'popular';
    selectedProviderCode.value = null;
  } else if (sectionId === 'slots') {
    lobbyInitialMode.value = 'slots';
    selectedProviderCode.value = null;
  } else if (sectionId === 'retro') {
    lobbyInitialMode.value = 'slots';
    selectedProviderCode.value = 'RT'; // Special virtual provider for Retro
  }
  currentView.value = 'provider';
};

const handleGameLaunch = (url) => {
  currentGameUrl.value = url;
  previousView.value = currentView.value;
  currentView.value = 'game-player';
};

const handleRetroLaunchRequest = (game) => {
  console.log('[RetroDebug] User requested to launch:', game.id, game.game_name);
  if (!isLoggedIn.value) {
    alert('Faça login ou cadastre-se para jogar e ganhar prêmios reais!');
    handleNavigate('register');
    return;
  }
  selectedRetroGame.value = game;
  showRetroBetModal.value = true;
};

const confirmRetroLaunch = async () => {
  console.log('[RetroDebug] Confirming bet for:', selectedRetroGame.value.id, 'Amount:', retroBetAmount.value);
  if (retroBetAmount.value <= 0) {
    alert('Por favor, insira um valor de aposta válido.');
    return;
  }
  if (userProfile.value.balance < retroBetAmount.value) {
    alert('Saldo insuficiente para esta aposta.');
    return;
  }

  isLaunchingRetro.value = true;
  try {
    const res = await fetch('/games/start', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('casino_token')}`
      },
      body: JSON.stringify({
        jogo: selectedRetroGame.value.id,
        aposta: retroBetAmount.value
      })
    });
    console.log('[RetroDebug] /games/start response status:', res.status);
    const data = await res.json();
    console.log('[RetroDebug] /games/start data:', data);

    if (res.ok && data.url) {
      showRetroBetModal.value = false;
      // Re-fetch profile to update balance
      const meRes = await fetch('/api/me', {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('casino_token')}` }
      });
      if (meRes.ok) {
        userProfile.value = await meRes.json();
        console.log('[RetroDebug] Profile updated, new balance:', userProfile.value.balance);
      }
      
      // Pass the token to the launcher route
      const launchUrl = `/play/${selectedRetroGame.value.id}/p?token=${localStorage.getItem('casino_token')}`;
      console.log('[RetroDebug] Launching iframe with URL:', launchUrl);
      handleGameLaunch(launchUrl);
    } else {
      console.error('[RetroDebug] Failed to start game:', data.error);
      alert(data.error || 'Erro ao iniciar o jogo.');
    }
  } catch (e) {
    console.error('[RetroDebug] Exception during launch:', e);
    alert('Erro de conexão ao iniciar o jogo.');
  } finally {
    isLaunchingRetro.value = false;
  }
};

const closeGamePlayer = (result) => {
  currentView.value = previousView.value;
  currentGameUrl.value = '';
  
  if (result === 'game_loss') {
    alert('Poxa, você perdeu :( \nBoa sorte na próxima!');
    // If we want to reopen the bet modal, we would need the game object.
    // For now, returning to lobby is the standard behavior.
  } else if (result === 'game_win') {
    alert('Parabéns! Você ganhou! 🎉');
  }
};
</script>

<template>
  <div class="min-h-screen bg-[#050505] text-white font-sans w-full flex justify-center selection:bg-[#fca000] selection:text-black">

    <!-- Admin Panel (Full Screen) -->
    <AdminDashboard v-if="currentView === 'admin'" @close="currentView = 'home'" />

    <!-- App Container (Unique Size) -->
    <div v-else-if="isLoaded" 
      class="fade-in w-full sm:max-w-[617px] bg-[#1a1a1a] relative shadow-[0_0_100px_rgba(0,0,0,0.8)] sm:border-x border-yellow-900/10 flex flex-col overflow-hidden" 
      :style="currentView === 'game-player' ? { width: '100%', maxWidth: '100%', height: '100vh' } : { zoom: 0.7, height: '142.85vh', maxWidth: '617px' }"
    >

      <!-- FIXED HEADER AT TOP -->
      <Header v-if="currentView !== 'admin' && currentView !== 'provider' && currentView !== 'game-player'" @navigate="handleNavigate" :isLoggedIn="isLoggedIn" :user="userProfile" class="w-full sticky top-0 z-50 shadow-2xl" />

      <!-- Provider Detail Page -->
      <ProviderPage v-if="currentView === 'provider'" :providerCode="selectedProviderCode" :initialMode="lobbyInitialMode" :isLoggedIn="isLoggedIn" :user="userProfile" @close="currentView = 'home'" @request-auth="handleNavigate('register')" @launch-url="handleGameLaunch" class="flex-1" />

      <!-- Background (Only for Home) -->
      <div v-if="currentView === 'home'" class="absolute inset-0 z-0 bg-cover bg-top bg-no-repeat pointer-events-none opacity-40" :style="{ backgroundImage: `url('${globalSettings.home_background}')` }"></div>

      <!-- Main Layout -->
      <div v-if="currentView === 'home'" class="flex-1 overflow-y-auto no-scrollbar pb-32 z-10 scroll-smooth">
        
        <div class="px-3 pt-4"> <!-- Added top padding to separate from fixed header -->
          <!-- Hero Section Banners (TOP) -->
          <div class="rounded-2xl overflow-hidden shadow-2xl relative aspect-[1000/300] mb-6 border border-white/10">
             <img :src="globalSettings.home_banners[0]" class="w-full h-full object-cover" />
          </div>

          <NotificationTicker class="mt-4" />

          <CategoryMenu class="mt-6" />

          <!-- Popular Games -->
          <GameGrid title="Popular" :iconSrc="globalSettings.category_icon_popular" sectionId="popular" :games="popularGames" :isLoggedIn="isLoggedIn" :user="userProfile" @see-more="handleSeeMore" @request-auth="handleNavigate('register')" @launch-url="handleGameLaunch" />

          <!-- Slots Section: Provider Cards -->
          <div id="slots" class="px-3 mt-6">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center space-x-2">
                <img :src="globalSettings.category_icon_slot" alt="Slots" class="w-6 h-6 object-contain" />
                <h2 class="text-lg font-bold text-gray-100">Slots</h2>
              </div>
              <button @click="handleSeeMore('slots')" class="hover:scale-105 active:scale-95 transition-transform flex items-center justify-center">
                <img src="/casino_icons/SLOTS_ICONS/button-vermais.png" alt="Ver Mais" class="h-[84px] w-auto object-contain" />
              </button>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <div 
                v-for="prov in slotProviders" 
                :key="prov.code" 
                @click="openProviderPage(prov.code)"
                class="bg-[#222] rounded-lg p-2 flex flex-col items-center justify-center aspect-[312/416] border border-gray-800 hover:border-[#fca000] hover:bg-[#2a2a2a] transition-all cursor-pointer shadow-lg overflow-hidden relative"
              >
                <img v-if="prov.cover_image || prov.logo" :src="prov.cover_image || prov.logo" class="w-full h-full object-cover" />
                <template v-else>
                   <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10"></div>
                   <div class="absolute bottom-2 left-2 z-20">
                     <span class="text-[10px] text-white font-bold drop-shadow">{{ prov.name }}</span>
                   </div>
                </template>
              </div>
            </div>
          </div>

          <!-- Retrô -->
          <GameGrid title="Retrô" :iconSrc="globalSettings.category_icon_retro" sectionId="retro" :games="retroGamesPublic" :isLoggedIn="isLoggedIn" :user="userProfile" @see-more="handleSeeMore" @request-auth="handleNavigate('register')" @launch-url="handleGameLaunch" @retro-launch="handleRetroLaunchRequest" />
        </div>
      </div>

      <!-- Retro Bet Modal -->
      <div v-if="showRetroBetModal" class="fixed inset-0 z-[150] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4">
        <div class="bg-[#111] w-full max-w-md rounded-[40px] border border-white/10 shadow-[0_0_100px_rgba(0,0,0,1)] overflow-hidden animate-in fade-in zoom-in duration-300 relative">
          <div class="absolute -top-40 -left-40 w-80 h-80 bg-purple-600/10 blur-[80px] rounded-full"></div>
          
          <div class="p-8 space-y-8 relative z-10">
            <div class="text-center space-y-4">
               <div class="w-24 h-24 mx-auto rounded-3xl overflow-hidden border-2 border-purple-500/30 shadow-2xl">
                 <img :src="selectedRetroGame.banner_local" class="w-full h-full object-cover" />
               </div>
               <div>
                  <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">{{ selectedRetroGame.game_name }}</h3>
                  <p class="text-purple-400 text-xs font-black uppercase tracking-widest">Defina sua aposta para começar</p>
               </div>
            </div>

            <div class="space-y-4">
              <div class="bg-black/40 p-6 rounded-3xl border border-white/5 space-y-2 text-center">
                <label class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">VALOR DA APOSTA (R$)</label>
                <div class="flex items-center justify-center gap-4">
                  <button @click="retroBetAmount = Math.max(1, retroBetAmount - 1)" class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-2xl font-black hover:bg-white/10 transition-all">-</button>
                  <input v-model.number="retroBetAmount" type="number" step="1" class="bg-transparent text-4xl font-black text-center text-white w-32 outline-none border-b-2 border-purple-500/30 focus:border-purple-500" />
                  <button @click="retroBetAmount += 1" class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-2xl font-black hover:bg-white/10 transition-all">+</button>
                </div>
              </div>

              <div class="grid grid-cols-3 gap-2">
                <button v-for="val in [1, 2, 5, 10, 20, 50]" :key="val" @click="retroBetAmount = val" class="py-3 bg-white/5 rounded-xl text-xs font-black hover:bg-purple-600 transition-all" :class="retroBetAmount === val ? 'bg-purple-600' : ''">R$ {{ val }}</button>
              </div>
            </div>

            <div class="flex gap-4">
              <button @click="showRetroBetModal = false" class="flex-1 py-5 bg-white/5 text-white font-black rounded-3xl hover:bg-white/10 transition-all uppercase italic tracking-tighter">Cancelar</button>
              <button @click="confirmRetroLaunch" :disabled="isLaunchingRetro" class="flex-2 py-5 bg-purple-600 text-white font-black rounded-3xl shadow-[0_15px_40px_rgba(168,85,247,0.3)] hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-3">
                <span v-if="isLaunchingRetro" class="w-5 h-5 border-4 border-white/30 border-t-white rounded-full animate-spin"></span>
                <span v-else class="uppercase italic tracking-tighter">Iniciar Rodada 🕹️</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Offers -->
      <OffersView v-if="currentView === 'offers'" :settings="globalSettings" :user="userProfile" @close="currentView = 'home'" class="flex-1 overflow-y-auto no-scrollbar" />

      <!-- Register -->
      <RegisterView v-else-if="currentView === 'register'" @close="currentView = 'home'" @register-success="handleRegisterSuccess" class="flex-1 overflow-y-auto no-scrollbar" />

      <!-- Support -->
      <SupportView v-else-if="currentView === 'support'" :settings="globalSettings" @close="currentView = 'home'" class="flex-1 overflow-y-auto no-scrollbar" />

      <!-- Profile -->
      <ProfileView v-else-if="currentView === 'profile'" :user="userProfile" :settings="globalSettings" @close="currentView = 'home'" @request-register="currentView = 'register'" @navigate="v => currentView = v" class="flex-1 overflow-y-auto no-scrollbar" />

      <BottomNav
        v-if="isLoaded && currentView !== 'admin' && currentView !== 'game-player'"
        :currentView="currentView"
        :isLoggedIn="isLoggedIn"
        @navigate="handleNavigate"
        class="absolute bottom-0 left-0 right-0 z-[100] bg-[#0d0d0d] border-t border-[#fca000]/10"
      />

      <!-- Entry Popups -->
      <div v-if="currentView === 'home' && isLoaded && activeEntryPopups.length > 0" class="fixed inset-0 z-[120] flex items-center justify-center bg-black/80 backdrop-blur-md p-4 animate-in fade-in duration-500">
        <div class="relative max-w-[90%] sm:max-w-[400px] animate-in zoom-in duration-300">
           <!-- The close button -->
           <button @click="closeEntryPopup(globalSettings.entry_popups.indexOf(activeEntryPopups[0]))" class="absolute -top-4 -right-4 w-10 h-10 bg-[#333] border border-white/20 rounded-full text-white font-black shadow-2xl z-10 hover:bg-[#555] active:scale-90 transition-all flex items-center justify-center">✕</button>
           <a :href="activeEntryPopups[0].link || '#'" :target="activeEntryPopups[0].link ? '_blank' : ''" @click="!activeEntryPopups[0].link && $event.preventDefault()" class="block cursor-pointer">
             <img :src="activeEntryPopups[0].image" class="w-full h-auto rounded-[2rem] object-contain shadow-[0_20px_50px_rgba(0,0,0,0.8)] border border-white/10" />
           </a>
        </div>
      </div>

      <!-- Floating Popups (Lado Esquerdo) -->
      <div class="fixed left-4 bottom-32 z-[110] flex flex-col gap-6 pointer-events-none">
        <div v-for="(popup, index) in activePopups.filter(p => p.side === 'left').slice(0, 3)" :key="`left_${index}`" 
             class="relative group animate-in slide-in-from-left duration-500 pointer-events-auto">
          <a :href="popup.link" target="_blank" class="block transition-transform hover:scale-110 active:scale-95">
            <img :src="popup.image" class="w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-[0_10px_20px_rgba(0,0,0,0.5)]" />
          </a>
          <button @click.stop="closePopup(globalSettings.floating_popups.indexOf(popup))" 
                  class="absolute -top-2 -right-2 w-6 h-6 bg-black/60 backdrop-blur-md rounded-full text-white text-[10px] flex items-center justify-center border border-white/20 shadow-lg group-hover:bg-red-500 transition-colors">✕</button>
        </div>
      </div>

      <!-- Floating Popups (Lado Direito) -->
      <div class="fixed right-4 bottom-32 z-[110] flex flex-col gap-6 pointer-events-none">
        <div v-for="(popup, index) in activePopups.filter(p => p.side === 'right').slice(0, 3)" :key="`right_${index}`" 
             class="relative group animate-in slide-in-from-right duration-500 pointer-events-auto">
          <a :href="popup.link" target="_blank" class="block transition-transform hover:scale-110 active:scale-95">
            <img :src="popup.image" class="w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-[0_10px_20px_rgba(0,0,0,0.5)]" />
          </a>
          <button @click.stop="closePopup(globalSettings.floating_popups.indexOf(popup))" 
                  class="absolute -top-2 -left-2 w-6 h-6 bg-black/60 backdrop-blur-md rounded-full text-white text-[10px] flex items-center justify-center border border-white/20 shadow-lg group-hover:bg-red-500 transition-colors">✕</button>
        </div>
      </div>
    </div>

    <!-- In-App Game Player (OUTSIDE container for true fullscreen) -->
    <GamePlayer v-if="currentView === 'game-player'" :url="currentGameUrl" @close="closeGamePlayer" />

    <!-- Loading Screen -->
    <div v-if="!isLoaded && currentView !== 'admin'" class="fixed inset-0 z-[200] flex items-center justify-center bg-[#111111] loading-fade">
      <div class="flex flex-col items-center">
        <img src="/image-removebg-preview.png" alt="Logo" class="w-64 animate-pulse drop-shadow-[0_0_25px_rgba(252,160,0,0.6)] mb-6" />
        <div class="w-10 h-10 border-4 border-[#fca000] border-t-transparent rounded-full animate-spin"></div>
      </div>
    </div>
  </div>
</template>

<style>
:root { --primary-color: #fca000; --bg-color: #111111; --surface-color: #1a1a1a; }
html, body { 
  background-color: var(--bg-color); 
  color: #fff; 
  margin: 0; 
  padding: 0;
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none;  /* IE and Edge */
}
/* Hide scrollbar for Chrome, Safari and Opera */
*::-webkit-scrollbar {
  display: none;
}
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
.fade-in { animation: fadeIn 0.4s ease-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.loading-fade { transition: opacity 0.5s ease; }
</style>
