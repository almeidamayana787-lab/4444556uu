<template>
  <div :id="sectionId" class="px-3 mt-6">
    <div class="flex items-center space-x-2 mb-3">
      <img :src="iconSrc" :alt="title" class="w-6 h-6 object-contain" />
      <h2 class="text-lg font-bold text-gray-100">{{ title }}</h2>
    </div>
    
    <div class="grid grid-cols-3 gap-2">
      <div 
        v-for="game in displayGames" 
        :key="game.id || game.game_code"
        @click="launchGame(game)"
        class="bg-[#222] rounded-lg overflow-hidden aspect-[3/4] relative group cursor-pointer shadow-lg border border-gray-800 hover:border-[#fca000] hover:-translate-y-1 transition-all duration-300"
      >
        <img v-if="game.banner_local" :src="game.banner_local" class="absolute inset-0 w-full h-full object-cover" />
        <div v-else class="absolute inset-0 bg-[#333] flex items-center justify-center">
          <span class="text-gray-600 text-xs text-center px-1">{{ game.game_name || 'Game' }}</span>
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10"></div>
        <div class="absolute bottom-1 left-2 z-20">
          <span class="text-[10px] text-[#fca000] font-bold">{{ game.game_name }}</span>
        </div>
      </div>

      <!-- Fallback mock games if no real data -->
      <template v-if="!games || games.length === 0">
        <div v-for="i in 9" :key="'mock-'+i" class="bg-[#222] rounded-lg overflow-hidden aspect-[3/4] relative group cursor-pointer shadow-lg border border-gray-800 hover:border-[#fca000] hover:-translate-y-1 transition-all duration-300">
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10"></div>
          <div class="absolute inset-0 bg-[#333] flex items-center justify-center">
            <span class="text-gray-600 text-xs">Game {{ i }}</span>
          </div>
          <div class="absolute bottom-1 left-2 z-20">
            <span class="text-[10px] text-[#fca000] font-bold">{{ title }} {{ i }}</span>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  title: String,
  iconSrc: String,
  sectionId: String,
  games: { type: Array, default: () => [] }
});

const displayGames = computed(() => {
  if (props.games && props.games.length > 0) return props.games.slice(0, 9);
  return [];
});

const launchGame = async (game) => {
  if (!game.game_code) return;
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
      alert('Erro ao iniciar o jogo: ' + (data.msg || 'Desconhecido'));
    }
  } catch(e) {
    alert('Erro de conexão ao lançar o jogo.');
  }
};
</script>
