<template>
  <div :id="sectionId" class="px-3 mt-6">
    <div class="flex items-center justify-between mb-3">
      <div class="flex items-center space-x-2">
        <img :src="iconSrc" :alt="title" class="w-6 h-6 object-contain" />
        <h2 class="text-lg font-bold text-gray-100">{{ title }}</h2>
      </div>
      <button @click="$emit('see-more', sectionId)" class="hover:scale-105 active:scale-95 transition-transform flex items-center justify-center">
        <img src="/casino_icons/SLOTS_ICONS/button-vermais.png" alt="Ver Mais" class="h-[84px] w-auto object-contain" />
      </button>
    </div>
    
    <div class="grid grid-cols-3 gap-2">
      <div 
        v-for="game in displayGames" 
        :key="game.id || game.game_code"
        @click="launchGame(game)"
        class="bg-[#222] rounded-lg overflow-hidden relative group cursor-pointer shadow-lg border border-gray-800 hover:border-[#fca000] hover:-translate-y-1 transition-all duration-300 w-full aspect-[312/416]"
      >
        <img v-if="game.banner_local" :src="game.banner_local" class="absolute inset-0 w-full h-full object-cover" />
        <div v-else class="absolute inset-0 bg-[#333] flex items-center justify-center">
          <span class="text-gray-600 text-[10px] text-center px-1">{{ game.game_name || 'Game' }}</span>
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent z-10 transition-opacity group-hover:opacity-0"></div>
      </div>

      <!-- Fallback mock games if no real data -->
      <template v-if="!games || games.length === 0">
        <div v-for="i in 9" :key="'mock-'+i" class="bg-[#222] rounded-lg overflow-hidden relative group cursor-pointer shadow-lg border border-gray-800 hover:border-[#fca000] hover:-translate-y-1 transition-all duration-300 w-full aspect-[312/416]">
          <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent z-10"></div>
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
  games: { type: Array, default: () => [] },
  isLoggedIn: { type: Boolean, default: false },
  user: { type: Object, default: () => ({ balance: 0 }) }
});
const emit = defineEmits(['see-more', 'request-auth', 'launch-url', 'retro-launch']);

const displayGames = computed(() => {
  if (props.games && props.games.length > 0) return props.games.slice(0, 9);
  return [];
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

  // Retro games need a bet selection first
  if (game.is_retro) {
    emit('retro-launch', game);
    return;
  }

  // Standard MAX API games
  if (!game.game_code) return;

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
