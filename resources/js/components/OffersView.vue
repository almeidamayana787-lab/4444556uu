<template>
  <div class="flex flex-col flex-1 bg-[#1a1a1a] text-white pb-32 overflow-y-auto">
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
        <div class="flex flex-col space-y-4">
          <div class="flex items-center space-x-4">
            <div class="flex flex-col items-center flex-shrink-0">
              <div class="bg-white p-1 rounded-lg mb-1.5">
                <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(inviteLink)}`" alt="QR Code" class="w-16 h-16" />
              </div>
              <a :href="`https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(inviteLink)}`" target="_blank" class="bg-[#fca000] text-black text-[9px] font-black py-1 px-2.5 rounded-full shadow-lg whitespace-nowrap">SALVAR QR CODE</a>
            </div>
            
            <div class="flex-1 space-y-2 min-w-0">
              <div class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Link de convite</div>
              <div class="flex items-center bg-[#1a1a1a] rounded-lg border border-gray-800 p-2 relative h-9 px-3">
                <span class="text-[11px] text-[#fca000] truncate font-medium">{{ inviteLink }}</span>
              </div>
            </div>
          </div>

          <!-- Social Share Bar (Fixed overlap) -->
          <div class="flex justify-between items-center bg-[#0a0a0a]/50 p-2 rounded-xl border border-white/5">
            <button v-for="(social, i) in socials" :key="i" @click="handleShare(social.name)" class="flex flex-col items-center space-y-1 group outline-none">
              <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-md overflow-hidden transition-transform group-active:scale-90" :class="social.bg">
                <svg v-if="social.name === 'Copiar'" viewBox="0 0 24 24" class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                <img v-else :src="social.icon" class="w-full h-full object-cover" />
              </div>
              <span class="text-[8px] text-gray-500 font-bold">{{ social.name }}</span>
            </button>
          </div>
        </div>
        <div class="mt-4 flex items-center justify-center space-x-2 text-[10px]">
          <span class="text-gray-500">Subordinados válidos <span class="text-[#fca000] font-bold">{{ affiliateData.valid_referrals || 0 }}</span> pessoas</span>
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
import { computed, ref, onMounted } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  user: { type: Object, default: null } // Received from App.vue
});

defineEmits(['close']);

const affiliateData = ref({ valid_referrals: 0, pending_referrals: 0 });

const inviteLink = computed(() => {
  if (!props.user) return 'Faça login para gerar seu link';
  return `${window.location.origin}/#register?ref=${props.user.id}`;
});

onMounted(async () => {
  if (props.user) {
    try {
      const res = await fetch('/api/profile/affiliate', {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('casino_token')}` }
      });
      if(res.ok) {
        const data = await res.json();
        affiliateData.value.valid_referrals = data.valid_count || 0;
      }
    } catch(e) {}
  }
});

const handleShare = (network) => {
  const url = encodeURIComponent(inviteLink.value);
  const text = encodeURIComponent("Jogue comigo na melhor plataforma! Registre-se agora.");
  
  if (network === 'Copiar') {
    navigator.clipboard.writeText(inviteLink.value);
    alert('Link de convite copiado!');
    return;
  }
  
  if (network === 'WhatsApp') {
    window.open(`whatsapp://send?text=${text}%20${url}`, '_blank');
  } else if (network === 'Telegram') {
    window.open(`tg://msg?text=${text}%20${url}`, '_blank');
  } else if (network === 'Facebook') {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
  } else if (network === 'Insta') {
    // Only share intent for modern browsers
    if (navigator.share) {
      navigator.share({ title: 'Afiliados', text: "Jogue comigo na melhor plataforma!", url: inviteLink.value });
    } else {
      alert("O compartilhamento nativo não é suportado pelo seu navegador.");
    }
  }
};

const socials = [
  { name: 'Copiar', bg: 'bg-[#ff9800]', icon: '' },
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
