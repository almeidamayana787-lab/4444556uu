<template>
  <div class="flex flex-col min-h-screen text-white relative z-10">
    <!-- Header -->
    <header class="flex items-center px-4 py-3 bg-[#121212]/80 backdrop-blur-md sticky top-0 z-50 border-b border-[#fca000]/10">
      <button @click="$emit('close')" class="p-1 mr-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      <h1 class="text-base font-bold tracking-tight flex-1 text-center">Slots</h1>
    </header>

    <!-- Search Bar -->
    <div class="px-4 py-3">
      <div class="flex items-center bg-[#1a1a1a] rounded-lg border border-gray-800 px-3 h-10">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input v-model="searchTerm" type="text" placeholder="Pesquisar" class="bg-transparent outline-none text-sm text-gray-300 flex-1" />
      </div>
    </div>

    <div class="flex flex-1 overflow-hidden">
      <!-- Left Sidebar: Provider Tabs -->
      <div class="w-16 bg-[#0d0d0d]/50 backdrop-blur-sm border-r border-gray-800 overflow-y-auto flex flex-col items-center py-2 space-y-1 scrollbar-hide">
        <button 
          @click="selectedProvider = null"
          class="w-12 h-12 rounded-lg flex flex-col items-center justify-center text-[8px] font-bold transition flex-shrink-0"
          :class="selectedProvider === null ? 'bg-[#fca000] text-black shadow-[0_0_10px_rgba(252,160,0,0.3)]' : 'bg-[#1a1a1a] text-gray-400 hover:bg-[#222]'"
        >
          <span class="text-sm">🎰</span>
          <span>Slots</span>
        </button>
        <button 
          v-for="prov in providers" :key="prov.code"
          @click="selectedProvider = prov.code"
          class="w-12 h-12 rounded-lg flex flex-col items-center justify-center text-[8px] font-bold transition flex-shrink-0 overflow-hidden"
          :class="selectedProvider === prov.code ? 'bg-[#fca000] text-black shadow-[0_0_10px_rgba(252,160,0,0.3)]' : 'bg-[#1a1a1a] text-gray-400 hover:bg-[#222]'"
        >
          <img v-if="prov.logo" :src="prov.logo" class="w-full h-full object-contain p-1" />
          <template v-else>
            <span class="text-sm font-black">{{ prov.code.substring(0,2) }}</span>
            <span class="truncate w-full text-center px-0.5">{{ prov.name || prov.code }}</span>
          </template>
        </button>
      </div>

      <!-- Main Content -->
      <div class="flex-1 overflow-y-auto pb-24">
        <!-- Filter Tabs -->
        <div class="flex gap-2 px-3 py-3 overflow-x-auto">
          <button v-for="tab in ['Tudo', 'Popular', 'Recente', 'Favoritos']" :key="tab"
            @click="activeTab = tab"
            class="px-4 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition"
            :class="activeTab === tab ? 'bg-[#fca000] text-black' : 'bg-[#1a1a1a] text-gray-400 border border-gray-700'"
          >{{ tab }}</button>
        </div>

        <!-- Games Grid -->
        <div class="grid grid-cols-3 gap-2 px-3 pb-4">
          <div 
            v-for="game in filteredGames" :key="game.id"
            @click="launchGame(game)"
            class="bg-[#222] rounded-lg overflow-hidden aspect-[3/4] relative group cursor-pointer shadow-lg border border-gray-800 hover:border-[#fca000] hover:-translate-y-0.5 transition-all duration-300"
          >
            <img v-if="game.banner_local" :src="game.banner_local" class="absolute inset-0 w-full h-full object-cover" />
            <div v-else class="absolute inset-0 bg-[#333] flex items-center justify-center">
              <span class="text-gray-600 text-[10px] text-center px-1">{{ game.game_name }}</span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10"></div>
            <div class="absolute bottom-1 left-2 z-20">
              <span class="text-[9px] text-white font-bold drop-shadow">{{ game.game_name }}</span>
            </div>
          </div>
        </div>

        <p v-if="filteredGames.length === 0" class="text-center text-gray-600 text-sm mt-8">Nenhum jogo encontrado.</p>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="flex items-center justify-center gap-2 py-4">
          <button @click="currentPage = Math.max(1, currentPage - 1)" class="w-8 h-8 bg-[#1a1a1a] rounded text-xs text-gray-400">‹</button>
          <button v-for="p in visiblePages" :key="p" @click="currentPage = p"
            class="w-8 h-8 rounded text-xs font-bold transition"
            :class="currentPage === p ? 'bg-[#fca000] text-black' : 'bg-[#1a1a1a] text-gray-400'"
          >{{ p }}</button>
          <button @click="currentPage = Math.min(totalPages, currentPage + 1)" class="w-8 h-8 bg-[#1a1a1a] rounded text-xs text-gray-400">›</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';

const props = defineProps({
  providerCode: { type: String, default: null }
});
defineEmits(['close']);

const providers = ref([]);
const allGames = ref([]);
const selectedProvider = ref(props.providerCode);
const searchTerm = ref('');
const activeTab = ref('Tudo');
const currentPage = ref(1);
const perPage = 12;

onMounted(async () => {
  // Fetch all providers
  try {
    const res = await fetch('/api/games/providers/all');
    if (res.ok) providers.value = await res.json();
  } catch(e) {}

  // Load games for initial provider or all
  loadGames();
});

watch(selectedProvider, () => { currentPage.value = 1; loadGames(); });

const loadGames = async () => {
  try {
    let url = selectedProvider.value 
      ? `/api/games/provider/${encodeURIComponent(selectedProvider.value)}`
      : '/api/games/providers/all';
    
    if (selectedProvider.value) {
      const res = await fetch(url);
      if (res.ok) {
        const data = await res.json();
        allGames.value = data.games || [];
      }
    } else {
      // Fetch all games - load from each provider
      allGames.value = [];
      for (const prov of providers.value) {
        const res = await fetch(`/api/games/provider/${encodeURIComponent(prov.code)}`);
        if (res.ok) {
          const data = await res.json();
          allGames.value = [...allGames.value, ...(data.games || [])];
        }
      }
    }
  } catch(e) { console.error(e); }
};

const filteredGames = computed(() => {
  let games = allGames.value;
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase();
    games = games.filter(g => g.game_name.toLowerCase().includes(term));
  }
  if (activeTab.value === 'Popular') {
    games = games.filter(g => g.is_popular);
  }
  // Pagination
  const start = (currentPage.value - 1) * perPage;
  return games.slice(start, start + perPage);
});

const totalPages = computed(() => {
  let games = allGames.value;
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
  try {
    const res = await fetch('/api/game/launch', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ game_code: game.game_code })
    });
    const data = await res.json();
    if (data.status === 1 && data.launch_url) {
      window.open(data.launch_url, '_blank');
    } else {
      alert('Erro: ' + (data.msg || 'Desconhecido'));
    }
  } catch(e) { alert('Erro de conexão.'); }
};
</script>
