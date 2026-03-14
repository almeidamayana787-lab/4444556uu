<template>
  <div class="flex flex-col min-h-screen bg-[#1a1a1a] text-white pb-24">
    <!-- Header -->
    <header class="flex items-center justify-between px-4 py-3 bg-[#121212] sticky top-0 z-50 border-b border-[#fca000]/10">
      <button @click="$emit('close')" class="p-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      <h1 class="text-base font-bold tracking-tight">BÔNUS DE CONVITE <span class="bg-[#2a2a2a] rounded-full w-4 h-4 inline-flex items-center justify-center text-[10px] text-gray-400 border border-gray-600">i</span></h1>
      <button class="text-[#fca000] text-sm font-medium">Histórico</button>
    </header>

    <div class="p-4 space-y-4">
      <!-- Invite Card -->
      <div class="bg-[#121212] rounded-xl p-4 border border-yellow-900/10 shadow-lg relative overflow-hidden">
        <div class="flex items-start space-x-4">
          <div class="flex flex-col items-center">
            <div class="bg-white p-2 rounded-lg mb-2">
              <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://398.win.mooo.com" alt="QR Code" class="w-20 h-20" />
            </div>
            <button class="bg-[#fca000] text-black text-[10px] font-bold py-1 px-3 rounded-full shadow-lg">Salvar código de..</button>
          </div>
          <div class="flex-1 space-y-3">
            <div class="text-xs text-gray-500 mb-1">Link de convite</div>
            <div class="flex items-center bg-[#1a1a1a] rounded-lg border border-gray-800 p-2 relative h-10 px-3">
              <span class="text-xs text-gray-400 truncate flex-1">https://398.win.mooo.com</span>
            </div>
            <div class="flex justify-between items-center pt-1 px-1">
              <div v-for="(social, i) in socials" :key="i" class="flex flex-col items-center space-y-1">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shadow-md overflow-hidden" :class="social.bg">
                   <svg v-if="social.name === 'Partilhar'" viewBox="0 0 40 40" class="w-6 h-6 fill-white">
                      <path d="M39.084,11.043a9.472,9.472,0,0,1-.628.812q-4.419,4.614-8.851,9.219a1.176,1.176,0,0,1-1.126.451,1.1,1.1,0,0,1-.868-1.194c0-1.368,0-2.738,0-4.107v-.347c-.042-.031-.059-.054-.078-.055a12.329,12.329,0,0,0-9.671,3.139,12.019,12.019,0,0,0-2.493,3.316,1.08,1.08,0,0,1-1.481.576.968.968,0,0,1-.6-.867,18.273,18.273,0,0,1,.6-6.3A13.785,13.785,0,0,1,24.744,6a22.662,22.662,0,0,1,2.5-.254c.109-.011.219-.012.364-.02V5.343c0-1.3.019-2.6-.008-3.9A1.36,1.36,0,0,1,28.4,0h.537A6.223,6.223,0,0,1,29.7.582q4.389,4.546,8.759,9.112a9.284,9.284,0,0,1,.627.811ZM40,33.7V19.919a1.852,1.852,0,0,0-3.7,0V33.7A2.6,2.6,0,0,1,33.7,36.3H6.3A2.6,2.6,0,0,1,3.7,33.7V12.6A2.6,2.6,0,0,1,6.3,10.011h5.469a1.852,1.852,0,0,0,0-3.7H6.3A6.3,6.3,0,0,0,0,12.6V33.7A6.3,6.3,0,0,0,6.3,40H33.7A6.3,6.3,0,0,0,40,33.7Z" />
                   </svg>
                   <img v-else :src="social.icon" class="w-full h-full object-cover" />
                </div>
                <span class="text-[9px] text-gray-500">{{ social.name }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-4 flex items-center justify-center space-x-2 text-[10px]">
          <span class="text-gray-500">Subordinados válidos <span class="text-[#fca000] font-bold">0</span> pessoas</span>
          <span class="text-[#fca000]">Detalhes</span>
        </div>
      </div>

      <!-- Description -->
      <div class="bg-[#121212] rounded-xl border border-yellow-900/10 p-4 space-y-4">
        <h2 class="text-center text-gray-400 text-sm font-medium italic underline decoration-[#fca000]/30 offset-2">O que é o número válido de indicados?</h2>
        <div class="text-[11px] text-gray-500 space-y-2 leading-relaxed">
          <p>O subordinado Depósito Total <span class="text-gray-300 font-bold">≥20</span>, Total de Apostas Válidas <span class="text-gray-300 font-bold">≥300</span></p>
        </div>
      </div>

      <!-- Dynamic Tiers Grid -->
      <div class="grid grid-cols-4 gap-2">
        <div v-for="tier in computedTiers" :key="tier.people" class="bg-[#222] rounded-lg p-2 flex flex-col items-center border border-gray-800/50">
          <div class="w-full aspect-[4/3] bg-gradient-to-br from-[#2a2a2a] to-[#121212] rounded flex items-center justify-center relative mb-1 overflow-hidden">
            <svg viewBox="0 0 24 24" class="w-10 h-10 drop-shadow-[0_2px_5px_rgba(252,160,0,0.4)]" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M4 10C4 8.89543 4.89543 8 6 8H18C19.1046 8 20 8.89543 20 10V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V10Z" fill="#fca000" fill-opacity="0.2" stroke="#fca000" stroke-width="1.5"/>
              <path d="M12 11V14M12 14C11.4477 14 11 13.5523 11 13C11 12.4477 11.4477 12 12 12C12.5523 12 13 12.4477 13 13C13 13.5523 12.5523 14 12 14Z" stroke="#fca000" stroke-width="1.5" stroke-linecap="round"/>
              <path d="M4 10L12 8L20 10" stroke="#fca000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M18 8V6C18 5.44772 17.5523 5 17 5H7C6.44772 5 6 5.44772 6 6V8" stroke="#fca000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <rect x="9" y="11" width="6" height="3" rx="1.5" fill="#fca000" fill-opacity="0.3"/>
            </svg>
            <div class="absolute bottom-1 text-[7px] text-[#fca000]/60 uppercase font-black tracking-tighter">{{ tier.people }} Pessoas</div>
          </div>
          <span class="text-[10px] font-bold text-gray-400">{{ tier.reward }},00</span>
        </div>
      </div>

      <!-- Footer Info -->
      <div class="bg-[#121212] rounded-xl border border-yellow-900/10 overflow-hidden">
        <div class="bg-gradient-to-r from-[#fca000] to-[#ffd700] px-4 py-2 flex items-center">
           <span class="text-black font-black text-xs italic uppercase tracking-widest">Descrição da atividade</span>
        </div>
        <div class="p-4 space-y-4 text-[11px] text-gray-400 leading-relaxed">
          <p>1. Contanto que você convide novos usuários para ingressar no 30win, cada membro deposite pelo menos 20 reais e aposte cumulativamente mais de 300 em jogos eletrônicos, você pode abrir a caixa do tesouro e ganhar bônus. Quanto mais baús de tesouro, mais bônus você ganha!</p>
          <p>2. Este evento é válido por um longo período e pode ser participado em conjunto com outros descontos.</p>
          <p>3. A plataforma 30win.org reserva-se o direito de interpretação final deste evento.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) }
});

defineEmits(['close']);

const socials = [
  { name: 'Partilhar', bg: 'bg-[#ff9800]', icon: '' },
  { name: 'Facebook', bg: 'bg-transparent', icon: '/casino_icons/img_facebook.png' },
  { name: 'WhatsApp', bg: 'bg-transparent', icon: '/casino_icons/img_wa.png' },
  { name: 'Telegram', bg: 'bg-transparent', icon: '/casino_icons/img_tg.png' },
  { name: 'Insta', bg: 'bg-transparent', icon: '/casino_icons/instamg.avif' },
];

// Use dynamic tiers from settings, fallback to defaults
const computedTiers = computed(() => {
  let tiers = props.settings?.invite_bonus_tiers;
  if (typeof tiers === 'string') {
    try { tiers = JSON.parse(tiers); } catch(e) { tiers = null; }
  }
  if (Array.isArray(tiers) && tiers.length > 0) return tiers;
  return [
    { people: '1', reward: '30' }, { people: '2', reward: '30' }, { people: '3', reward: '30' }, { people: '4', reward: '30' },
    { people: '5', reward: '30' }, { people: '10', reward: '150' }, { people: '15', reward: '150' }, { people: '20', reward: '150' },
  ];
});
</script>
