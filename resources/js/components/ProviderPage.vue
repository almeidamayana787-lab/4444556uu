<template>
  <div class="flex flex-col flex-1 min-h-0 text-white relative z-10 bg-[#0a0a0a]">
    <!-- Header: Ultra Clean & Modern -->
    <header class="flex items-center px-4 h-16 bg-[#0a0a0a]/90 backdrop-blur-xl border-b border-white/5 sticky top-0 z-[60]">
      <button @click="$emit('close')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 transition-all active:scale-95">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      <h1 class="text-sm font-black uppercase tracking-[0.2em] flex-1 text-center text-gray-100">{{ lobbyTitle }}</h1>
      <div class="w-10"></div> <!-- Spacer for centering -->
    </header>

    <!-- Search Section: Glowing & Integrated -->
    <div class="px-4 py-4 bg-[#0a0a0a]">
      <div class="relative group">
        <div class="absolute inset-0 bg-yellow-500/5 blur-xl group-focus-within:bg-yellow-500/10 transition-all duration-500"></div>
        <div class="relative flex items-center bg-[#151515] rounded-2xl border border-white/5 px-4 h-12 focus-within:border-[#fca000]/40 transition-all duration-300 shadow-xl">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input v-model="searchTerm" type="text" placeholder="Encontre seu jogo favorito..." class="bg-transparent outline-none text-sm text-gray-200 flex-1 placeholder:text-gray-600 font-medium" />
        </div>
      </div>
    </div>

    <div class="flex flex-1 overflow-hidden relative">
      <!-- Left Sidebar: Unified for all modes -->
      <div class="w-[72px] bg-[#0a0a0a] border-r border-white/5 overflow-y-auto flex flex-col items-center py-4 gap-4 scrollbar-hide z-20">
        <button 
          @click="selectedProvider = null"
          class="w-12 h-12 rounded-2xl flex flex-col items-center justify-center transition-all duration-500 relative group flex-shrink-0"
          :class="selectedProvider === null ? 'bg-[#fca000] text-black shadow-[0_4px_20px_rgba(252,160,0,0.4)] scale-110' : 'bg-[#151515] text-gray-500 hover:bg-[#202020] border border-white/5'"
        >
          <span class="text-lg">✨</span>
          <div v-if="selectedProvider === null" class="absolute -left-1 w-1 h-6 bg-[#fca000] rounded-full shadow-[0_0_10px_#fca000]"></div>
        </button>

        <button 
          v-for="prov in providers" :key="prov.code"
          @click="selectedProvider = prov.code"
          class="w-12 h-12 rounded-2xl flex flex-col items-center justify-center transition-all duration-500 relative group flex-shrink-0 overflow-hidden"
          :class="selectedProvider === prov.code ? 'bg-white text-black shadow-xl scale-110' : 'bg-[#151515] text-gray-500 hover:bg-[#202020] border border-white/5'"
        >
          <img v-if="prov.logo" :src="prov.logo" class="w-full h-full object-contain p-2" />
          <template v-else>
            <span class="text-xs font-black uppercase tracking-tighter">{{ prov.code.substring(0,2) }}</span>
          </template>
          <div v-if="selectedProvider === prov.code" class="absolute -left-1 w-1 h-6 bg-white rounded-full shadow-[0_0_10px_white]"></div>
        </button>

        <!-- Virtual Provider: RT (Retrô) -->
        <button 
          @click="selectedProvider = 'RT'"
          class="w-12 h-12 rounded-2xl flex flex-col items-center justify-center transition-all duration-500 relative group flex-shrink-0 overflow-hidden"
          :class="selectedProvider === 'RT' ? 'bg-[#fca000] text-black shadow-xl scale-110' : 'bg-[#151515] text-gray-500 hover:bg-[#202020] border border-white/5'"
        >
          <span class="text-xs font-black uppercase tracking-tighter">RT</span>
          <div v-if="selectedProvider === 'RT'" class="absolute -left-1 w-1 h-6 bg-[#fca000] rounded-full shadow-[0_0_10px_#fca000]"></div>
        </button>
      </div>

      <!-- Main Content Area: Responsive & Clean -->
      <div class="flex-1 overflow-y-auto pb-32 bg-[#0a0a0a] relative z-10">
        <!-- Background Glow -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-yellow-500/5 blur-[120px] rounded-full pointer-events-none"></div>

        <!-- Filter Tabs: Apple-Style Pills -->
        <div class="flex gap-2 px-4 py-4 overflow-x-auto no-scrollbar mask-gradient-x">
          <button v-for="tab in ['Tudo', 'Favoritos']" :key="tab"
            @click="activeTab = tab"
            class="px-6 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all duration-300 whitespace-nowrap border"
            :class="activeTab === tab ? 'bg-[#fca000] text-black border-[#fca000] shadow-lg shadow-yellow-500/20' : 'bg-[#1a1a1a] text-gray-500 border-white/5 hover:border-white/10'"
          >{{ tab }}</button>
        </div>

        <!-- Games Grid: High-Fidelity Cards -->
        <div class="grid grid-cols-3 gap-3 px-4 pb-10">
          <div 
            v-for="game in filteredGames" :key="game.id"
            class="relative aspect-[312/416] rounded-2xl overflow-hidden group shadow-2xl border border-white/5 hover:border-[#fca000]/50 transition-all duration-500 active:scale-95"
          >
            <!-- Favorite Star -->
            <button 
              @click.stop="toggleFavorite(game.game_code)"
              class="absolute top-2 right-2 z-30 p-1.5 rounded-lg bg-black/40 backdrop-blur-md border border-white/5 hover:scale-110 active:scale-90 transition-all"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" :class="isFavorite(game.game_code) ? 'text-yellow-400 fill-current' : 'text-gray-400'" viewBox="0 0 24 24" stroke="currentColor" fill="none">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
              </svg>
            </button>

            <!-- Game Click Area -->
            <div @click="launchGame(game)" class="absolute inset-0 cursor-pointer">
              <!-- Game Image with Overlay -->
              <img v-if="game.banner_local" :src="game.banner_local" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
              <div v-else class="absolute inset-0 bg-[#151515] flex items-center justify-center">
                <span class="text-gray-700 text-[10px] text-center px-1 font-black">{{ game.game_name }}</span>
              </div>
              
              <!-- Hover Gradient -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-60 group-hover:opacity-0 transition-opacity duration-300"></div>
              
              <!-- Play Icon on Hover -->
              <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <div class="w-10 h-10 bg-[#fca000] rounded-full flex items-center justify-center shadow-2xl shadow-yellow-500/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-black fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredGames.length === 0" class="flex flex-col items-center justify-center py-20 px-6 text-center">
          <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-4">
            <span class="text-3xl opacity-30">🔍</span>
          </div>
          <h3 class="text-lg font-bold text-gray-300">Nenhum jogo encontrado</h3>
          <p class="text-xs text-gray-500 mt-1 max-w-[200px]">Tente ajustar sua pesquisa ou filtrar por outro provedor.</p>
        </div>

        <!-- Pagination: Apple-Style -->
        <div v-if="totalPages > 1" class="flex items-center justify-center gap-1.5 py-6">
          <button @click="currentPage = Math.max(1, currentPage - 1)" 
            class="w-10 h-10 flex items-center justify-center bg-[#151515] rounded-xl text-gray-400 border border-white/5 active:scale-90 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button v-for="p in visiblePages" :key="p" @click="currentPage = p"
            class="w-10 h-10 rounded-xl text-xs font-black transition-all duration-300 border"
            :class="currentPage === p ? 'bg-[#fca000] text-black border-[#fca000] shadow-lg shadow-yellow-500/20 scale-110' : 'bg-[#151515] text-gray-500 border-white/5'"
          >{{ p }}</button>
          <button @click="currentPage = Math.min(totalPages, currentPage + 1)" 
            class="w-10 h-10 flex items-center justify-center bg-[#151515] rounded-xl text-gray-400 border border-white/5 active:scale-90 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';

const props = defineProps({
  providerCode: { type: String, default: null },
  initialMode: { type: String, default: 'slots' }, // 'popular', 'slots', 'retro'
  isLoggedIn: { type: Boolean, default: false },
  user: { type: Object, default: () => ({ balance: 0 }) }
});
const emit = defineEmits(['close', 'request-auth', 'launch-url']);

const currentMode = ref(props.initialMode || 'slots');
const providers = ref([]);
const allGames = ref([]);
const selectedProvider = ref(props.providerCode);
const searchTerm = ref('');
const activeTab = ref('Tudo');
const currentPage = ref(1);
const perPage = 18;
const loading = ref(false);
const favorites = ref(JSON.parse(localStorage.getItem('casino_favorites') || '[]'));

const lobbyTitle = computed(() => {
  if (selectedProvider.value === 'RT') return 'Jogos Retrô';
  if (currentMode.value === 'popular') return 'Jogos Populares';
  if (currentMode.value === 'retro') return 'Lobby Retrô';
  return 'Lobby de Slots';
});

onMounted(async () => {
  loading.value = true;
  await loadData();
  loading.value = false;
});

watch(selectedProvider, () => { 
  currentPage.value = 1; 
});

const toggleFavorite = (gameCode) => {
  const index = favorites.value.indexOf(gameCode);
  if (index === -1) {
    favorites.value.push(gameCode);
  } else {
    favorites.value.splice(index, 1);
  }
  localStorage.setItem('casino_favorites', JSON.stringify(favorites.value));
};

const isFavorite = (gameCode) => {
  return favorites.value.includes(gameCode);
};

const loadData = async () => {
  // Try to load from cache first for immediate display
  const cached = localStorage.getItem('casino_games_cache');
  if (cached) {
    try {
      const data = JSON.parse(cached);
      providers.value = data;
      let games = [];
      data.forEach(p => {
        if (p.games) p.games.forEach(g => games.push({ ...g, provider_name: p.name }));
      });
      allGames.value = games;
    } catch(e) {}
  }

  try {
    const res = await fetch('/api/games/all-grouped');
    if (res.ok) {
      const data = await res.json();
      providers.value = data;
      localStorage.setItem('casino_games_cache', JSON.stringify(data));
      
      let games = [];
      data.forEach(p => {
        if (p.games) p.games.forEach(g => games.push({ ...g, provider_name: p.name }));
      });
      allGames.value = games;
    }
  } catch(e) { console.error(e); }
};

const filteredGames = computed(() => {
  let games = allGames.value;
  
  // Mode-based filtering first
  if (currentMode.value === 'popular') {
    games = games.filter(g => g.is_popular);
  } else if (currentMode.value === 'retro') {
    games = games.filter(g => g.is_retro);
  }
  
  // Tab-based filtering
  if (activeTab.value === 'Favoritos') {
    games = games.filter(g => isFavorite(g.game_code));
  } else if (currentMode.value === 'slots' || currentMode.value === 'popular' || currentMode.value === 'retro') {
    // Filter by provider if selected 
    if (selectedProvider.value === 'RT') {
       games = allGames.value.filter(g => g.is_retro);
    } else if (selectedProvider.value) {
      games = games.filter(g => g.provider_code === selectedProvider.value);
    }
  }

  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase();
    games = games.filter(g => g.game_name.toLowerCase().includes(term));
  }

  // Pagination
  const start = (currentPage.value - 1) * perPage;
  return games.slice(start, start + perPage);
});

const totalPages = computed(() => {
  let games = allGames.value;
  
  if (currentMode.value === 'popular') {
    games = games.filter(g => g.is_popular);
  } else if (currentMode.value === 'retro') {
    games = games.filter(g => g.is_retro);
  }
  
  if (activeTab.value === 'Favoritos') {
    games = games.filter(g => isFavorite(g.game_code));
  } else if (currentMode.value === 'slots' && selectedProvider.value) {
    games = games.filter(g => g.provider_code === selectedProvider.value);
  }
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase();
    games = games.filter(g => g.game_name.toLowerCase().includes(term));
  }
  return Math.ceil(games.length / perPage);
});

const visiblePages = computed(() => {
  const pages = [];
  const start = Math.max(1, currentPage.value - 2);
  const end = Math.min(totalPages.value, start + 4);
  for (let i = start; i <= end; i++) pages.push(i);
  return pages;
});

const launchGame = async (game) => {
  if (!props.isLoggedIn) {
    alert('Faça login ou cadastre-se para jogar e ganhar prêmios reais!');
    emit('request-auth');
    return;
  }

  if ((props.user?.balance || 0) <= 0) {
    alert('Saldo insuficiente para iniciar o jogo. Por favor, realize um depósito para desfrutar da experiência completa e concorrer a prêmios reais.');
    return;
  }

  try {
    const res = await fetch('/api/game/launch', {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json', 
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('casino_token')}`
      },
      body: JSON.stringify({ game_code: game.game_code })
    });
    const data = await res.json();
    if (data.status === 1 && data.launch_url) {
      emit('launch-url', data.launch_url);
    } else {
      alert(data.msg || 'Erro ao iniciar o jogo.');
    }
  } catch(e) {
    alert('Erro de conexão ao lançar o jogo.');
  }
};
</script>
