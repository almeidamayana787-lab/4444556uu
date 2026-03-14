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
          <!-- QR Code Area -->
          <div class="flex flex-col items-center">
            <div class="bg-white p-2 rounded-lg mb-2">
              <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://30win.org" alt="QR Code" class="w-20 h-20" />
            </div>
            <button class="bg-[#fca000] text-black text-[10px] font-bold py-1 px-3 rounded-full shadow-lg">Salvar código de..</button>
          </div>

          <!-- Share Area -->
          <div class="flex-1 space-y-3">
            <div class="text-xs text-gray-500 mb-1">Link de convite</div>
            <div class="flex items-center bg-[#1a1a1a] rounded-lg border border-gray-800 p-2 relative h-10 px-3">
              <span class="text-xs text-gray-400 truncate flex-1">https://30win.org</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
              <button class="ml-2 text-[#fca000]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
              </button>
            </div>

            <!-- Social Icons -->
            <div class="flex justify-between items-center pt-1 px-1">
              <div v-for="(social, i) in socials" :key="i" class="flex flex-col items-center space-y-1">
                <div :class="social.bg" class="w-10 h-10 rounded-full flex items-center justify-center shadow-md">
                   <img :src="social.icon" class="w-6 h-6 object-contain" />
                </div>
                <span class="text-[9px] text-gray-500">{{ social.name }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-4 flex items-center justify-center space-x-2 text-[10px]">
          <span class="text-gray-500">Subordinados válidos <span class="text-[#fca000] font-bold">0</span> pessoas</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-[#fca000] animate-spin-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span class="text-[#fca000]">Detalhes</span>
        </div>
      </div>

      <!-- Description Section -->
      <div class="bg-[#121212] rounded-xl border border-yellow-900/10 p-4 space-y-4">
        <h2 class="text-center text-gray-400 text-sm font-medium italic underline decoration-[#fca000]/30 offset-2">O que é o número válido de indicados?</h2>
        <div class="text-[11px] text-gray-500 space-y-2 leading-relaxed">
          <p>O subordinado Depósito Total <span class="text-gray-300 font-bold">≥20</span>, Total de Apostas Válidas <span class="text-gray-300 font-bold">≥300</span></p>
          <p class="text-gray-600 italic">As apostas válidas para membros de nível inferior não incluem as seguintes plataformas:</p>
          <div class="grid grid-cols-1 gap-1">
            <span class="flex justify-between"><span>Pescaria: <span class="text-blue-400">Baison(Gold Toad Fishing)</span></span></span>
            <span class="flex justify-between"><span>Slots: <span class="text-gray-400">WG(Lucky Dog)...</span></span> <span class="text-[#fca000]">Mais</span></span>
            <span class="flex justify-between"><span>Evento Blockchain: <span class="text-gray-400">WG(Circle)...</span></span> <span class="text-[#fca000]">Mais</span></span>
          </div>
        </div>
      </div>

      <!-- Tiers Grid -->
      <div class="grid grid-cols-4 gap-2">
        <div v-for="tier in tiers" :key="tier.people" class="bg-[#222] rounded-lg p-2 flex flex-col items-center border border-gray-800/50">
          <div class="w-full aspect-[4/3] bg-gradient-to-br from-gray-700 to-gray-800 rounded flex items-center justify-center relative mb-1 overflow-hidden group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white/20 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
            </svg>
            <div class="absolute bottom-1 text-[7px] text-gray-400 uppercase font-black tracking-tighter">{{ tier.people }} Pessoas</div>
          </div>
          <span class="text-[10px] font-bold text-gray-400">{{ tier.reward }},00</span>
        </div>
      </div>

      <!-- Footer Info -->
      <div class="bg-[#121212] rounded-xl border border-yellow-900/10 overflow-hidden">
        <div class="bg-gradient-to-r from-[#fca000] to-[#ffd700] px-4 py-2 flex items-center">
           <span class="text-black font-black text-xs italic uppercase tracking-widest">Descrição da atividade</span>
           <div class="w-0 h-0 border-t-[10px] border-t-transparent border-l-[10px] border-l-[#ffd700] border-b-[10px] border-b-transparent -mr-2"></div>
        </div>
        <div class="p-4 space-y-4 text-[11px] text-gray-400 leading-relaxed">
          <p>1. Contanto que você convide novos usuários para ingressar no 30win, cada membro deposite pelo menos 20 reais e aposte cumulativamente mais de 300 em jogos eletrônicos, você pode abrir a caixa do tesouro e ganhar 30 reais. Quanto mais baús de tesouro, mais bônus você ganha!</p>
          <p>2. Este evento é válido por um longo período e pode ser participado em conjunto com outros descontos. Recomende a seus amigos que participem;</p>
          <p>3. Se você esquecer a senha da sua conta, use seu número de celular/e-mail para obter o código de verificação para fazer login ou entre em contato com nosso atendimento ao cliente 30win;</p>
          <p>4. A plataforma 30win.org reserva-se o direito de interpretação final deste evento</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineEmits(['close']);

const socials = [
  { name: 'Partilhar', bg: 'bg-orange-500', icon: 'https://cdn-icons-png.flaticon.com/512/1358/1358023.png' },
  { name: 'Facebook', bg: 'bg-blue-600', icon: 'https://cdn-icons-png.flaticon.com/512/733/733547.png' },
  { name: 'WhatsApp', bg: 'bg-green-500', icon: 'https://cdn-icons-png.flaticon.com/512/733/733585.png' },
  { name: 'Telegram', bg: 'bg-blue-400', icon: 'https://cdn-icons-png.flaticon.com/512/2111/2111646.png' },
  { name: 'Insta', bg: 'bg-pink-600', icon: 'https://cdn-icons-png.flaticon.com/512/174/174855.png' },
];

const tiers = [
  { people: '1', reward: '30' },
  { people: '2', reward: '30' },
  { people: '3', reward: '30' },
  { people: '4', reward: '30' },
  { people: '5', reward: '30' },
  { people: '10', reward: '150' },
  { people: '15', reward: '150' },
  { people: '20', reward: '150' },
  { people: '30', reward: '300' },
  { people: '40', reward: '300' },
  { people: '50', reward: '300' },
  { people: '60', reward: '300' },
];
</script>

<style scoped>
.animate-spin-slow {
  animation: spin 3s linear infinite;
}
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
