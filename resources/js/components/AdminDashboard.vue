<template>
  <div class="min-h-screen bg-[#111111] text-white p-6 font-sans pb-32">
    <!-- Admin Login -->
    <div v-if="!isAdminLoggedIn && !isLoading" class="max-w-md mx-auto mt-20 bg-[#1a1a1a] p-8 rounded-2xl shadow-2xl border border-gray-800">
      <div class="flex justify-center mb-8">
        <img src="/image-removebg-preview.png" alt="Logo" class="w-32" />
      </div>
      <h2 class="text-2xl font-bold text-center text-[#fca000] mb-6">Painel Administrativo</h2>
      <div class="space-y-4">
        <div>
          <label class="block text-sm text-gray-400 mb-1">Usuário</label>
          <input v-model="loginUser" type="text" class="w-full bg-[#121212] border border-gray-700 rounded-lg h-12 px-4 focus:border-[#fca000] outline-none transition-colors" placeholder="Nome de admin" />
        </div>
        <div>
          <label class="block text-sm text-gray-400 mb-1">Senha</label>
          <input v-model="loginPass" type="password" class="w-full bg-[#121212] border border-gray-700 rounded-lg h-12 px-4 focus:border-[#fca000] outline-none transition-colors" placeholder="••••••••" />
        </div>
        <button @click="handleLogin" :disabled="isLoggingIn" class="w-full h-12 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-bold rounded-lg mt-6 hover:brightness-110 active:scale-95 transition-all">
          <span v-if="isLoggingIn">Entrando...</span>
          <span v-else>Entrar</span>
        </button>
        <p v-if="loginError" class="text-red-500 text-sm text-center mt-4">{{ loginError }}</p>
      </div>
    </div>

    <!-- Admin Dashboard Main -->
    <div v-if="isAdminLoggedIn && !isLoading" class="max-w-4xl mx-auto space-y-8">
      <div class="flex items-center justify-between mb-8 border-b border-gray-800 pb-4 mt-6">
        <div>
          <h1 class="text-3xl font-black text-[#fca000] tracking-tight">Painel de Configurações Gerais</h1>
          <p class="text-gray-400 text-sm mt-1">Gerencie imagens, bônus, jogos e links de suporte em tempo real.</p>
        </div>
        <button @click="$emit('close')" class="flex items-center gap-2 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition">Sair</button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- SECTION 1: Appearance -->
        <div class="bg-[#1a1a1a] p-6 rounded-2xl border border-gray-800 shadow-xl space-y-6">
          <div class="flex gap-3 items-center border-b border-gray-800 pb-3">
            <div class="w-8 h-8 rounded-lg bg-yellow-900/30 flex items-center justify-center text-[#fca000]">🖼️</div>
            <h3 class="text-xl font-bold text-white">Aparência do Site</h3>
          </div>
          <div>
            <label class="block text-sm text-gray-300 font-medium mb-2">Fundo Principal (1080x1920)</label>
            <div class="flex items-center gap-4">
              <img v-if="settings.home_background" :src="settings.home_background" class="w-16 h-24 object-cover rounded-lg border border-gray-700 bg-black" />
              <input type="file" @change="e => uploadFile(e, 'home_background', 'founde')" class="text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#fca000] file:text-black hover:file:bg-[#e08e00] transition" />
            </div>
          </div>
          <div class="pt-4 border-t border-gray-800">
            <label class="block text-sm text-gray-300 font-medium mb-2">Banner Principal (1411x418)</label>
            <div class="flex flex-col gap-4">
              <div v-for="(banner, index) in settings.home_banners" :key="index" class="relative group">
                <img :src="banner" class="w-full h-24 object-cover rounded-lg border border-gray-700 bg-black" />
                <button @click="removeBanner(index)" class="absolute top-2 right-2 bg-red-600 text-white p-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-lg">✕</button>
              </div>
              <input type="file" @change="e => uploadBanner(e)" class="text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-gray-700 transition" />
            </div>
          </div>
        </div>

        <!-- SECTION 2: Category Icons -->
        <div class="bg-[#1a1a1a] p-6 rounded-2xl border border-gray-800 shadow-xl space-y-6">
          <div class="flex gap-3 items-center border-b border-gray-800 pb-3">
            <div class="w-8 h-8 rounded-lg bg-yellow-900/30 flex items-center justify-center text-[#fca000]">📂</div>
            <h3 class="text-xl font-bold text-white">Menu de Categorias</h3>
          </div>
          <div v-for="cat in ['popular', 'slot', 'retro']" :key="cat" class="flex items-center justify-between pb-4 border-b border-gray-800 last:border-0 last:pb-0">
            <div>
              <p class="text-sm font-semibold capitalize text-gray-200">{{ cat }}</p>
              <img v-if="settings[`category_icon_${cat}`]" :src="settings[`category_icon_${cat}`]" class="w-10 h-10 object-contain mt-2" />
            </div>
            <input type="file" @change="e => uploadFile(e, `category_icon_${cat}`, 'casino_icons')" class="w-48 text-[10px] text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-gray-800 file:text-white" />
          </div>
        </div>

        <!-- SECTION 3: Invite Bonuses -->
        <div class="bg-[#1a1a1a] p-6 rounded-2xl border border-gray-800 shadow-xl space-y-6 md:col-span-2">
          <div class="flex gap-3 items-center border-b border-gray-800 pb-3">
            <div class="w-8 h-8 rounded-lg bg-yellow-900/30 flex items-center justify-center text-[#fca000]">💰</div>
            <h3 class="text-xl font-bold text-white">Bônus de Convite (Tiers)</h3>
          </div>
          <div class="space-y-4">
            <div v-for="(tier, index) in settings.invite_bonus_tiers" :key="index" class="flex flex-wrap md:flex-nowrap gap-4 items-center bg-[#111111] p-4 rounded-xl border border-gray-800">
              <div class="w-full md:w-32">
                <label class="text-[10px] text-gray-500">Pessoas p/ Baú</label>
                <input v-model="tier.people" type="number" class="w-full bg-[#1a1a1a] border border-gray-700 rounded h-10 px-3 text-sm" />
              </div>
              <div class="w-full md:w-32">
                <label class="text-[10px] text-gray-500">Apostas Totais (R$)</label>
                <input v-model="tier.bets" type="number" class="w-full bg-[#1a1a1a] border border-gray-700 rounded h-10 px-3 text-sm" />
              </div>
              <div class="w-full md:w-32">
                <label class="text-[10px] text-gray-500">Prêmio (R$)</label>
                <input v-model="tier.reward" type="number" class="w-full bg-[#1a1a1a] border border-gray-700 rounded h-10 px-3 text-sm" />
              </div>
              <button @click="removeTier(index)" class="ml-auto bg-red-900/30 text-red-500 hover:bg-red-600 hover:text-white p-2 rounded-lg transition mt-4 md:mt-0">Remover</button>
            </div>
            <button @click="addTier" class="w-full border-2 border-dashed border-gray-700 text-gray-400 font-bold py-3 rounded-xl hover:border-[#fca000] hover:text-[#fca000] transition">+ Adicionar Novo Baú (Tier)</button>
          </div>
        </div>

        <!-- SECTION 4: Social Links -->
        <div class="bg-[#1a1a1a] p-6 rounded-2xl border border-gray-800 shadow-xl space-y-6 md:col-span-2">
          <div class="flex gap-3 items-center border-b border-gray-800 pb-3">
            <div class="w-8 h-8 rounded-lg bg-yellow-900/30 flex items-center justify-center text-[#fca000]">🔗</div>
            <h3 class="text-xl font-bold text-white">Suporte & Redes Sociais</h3>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="social in ['telegram', 'whatsapp', 'facebook', 'instagram']" :key="social">
              <label class="block text-sm text-gray-300 font-medium capitalize mb-1">Link do {{ social }}</label>
              <input v-model="settings[`support_${social}`]" type="url" class="w-full bg-[#121212] border border-gray-700 rounded-lg h-12 px-4 focus:border-[#fca000] outline-none text-sm placeholder:text-gray-600" placeholder="https://" />
            </div>
          </div>
        </div>

        <!-- SECTION 5: API de Jogos -->
        <div class="bg-[#1a1a1a] p-6 rounded-2xl border border-gray-800 shadow-xl space-y-6 md:col-span-2">
          <div class="flex gap-3 items-center border-b border-gray-800 pb-3">
            <div class="w-8 h-8 rounded-lg bg-purple-900/30 flex items-center justify-center text-purple-400">🎮</div>
            <h3 class="text-xl font-bold text-white">API de Jogos (MAX API)</h3>
          </div>

          <!-- Credentials -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm text-gray-300 font-medium mb-1">Agent User</label>
              <input v-model="apiAgentCode" type="text" class="w-full bg-[#121212] border border-gray-700 rounded-lg h-12 px-4 focus:border-purple-500 outline-none text-sm" placeholder="ag_main_001" />
            </div>
            <div>
              <label class="block text-sm text-gray-300 font-medium mb-1">API Token</label>
              <input v-model="apiAgentToken" type="password" class="w-full bg-[#121212] border border-gray-700 rounded-lg h-12 px-4 focus:border-purple-500 outline-none text-sm" placeholder="tok_8f..." />
            </div>
            <div>
              <label class="block text-sm text-gray-300 font-medium mb-1">Webhook Secret</label>
              <input v-model="apiWebhookSecret" type="password" class="w-full bg-[#121212] border border-gray-700 rounded-lg h-12 px-4 focus:border-purple-500 outline-none text-sm" placeholder="sec_..." />
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-wrap gap-3">
            <button @click="testApiConnection" :disabled="apiLoading" class="px-6 py-3 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition flex items-center gap-2">
              <div v-if="apiLoading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              {{ apiLoading ? 'Testando...' : '🔌 Testar Conexão' }}
            </button>
            <button @click="fetchGamesFromApi" :disabled="apiLoading" class="px-6 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition flex items-center gap-2">
              <div v-if="fetchingGames" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              {{ fetchingGames ? 'Importando...' : `📥 Extrair Jogos da API` }}
            </button>
            <button @click="deleteAllGames" :disabled="apiLoading" class="px-6 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition">🗑️ Excluir Jogos</button>
          </div>

          <!-- Status Message -->
          <div v-if="apiMessage" class="p-3 rounded-lg text-sm" :class="apiSuccess ? 'bg-green-900/30 text-green-400 border border-green-800' : 'bg-red-900/30 text-red-400 border border-red-800'">
            {{ apiMessage }}
          </div>

          <!-- Games Grouped by Provider -->
          <div v-if="apiProviders.length > 0" class="space-y-4 mt-6">
            <h4 class="text-lg font-bold text-gray-200">Provedores e Jogos Importados ({{ totalGamesCount }})</h4>
            
            <div v-for="provider in apiProviders" :key="provider.code" class="bg-[#111111] rounded-xl border border-gray-800 overflow-hidden">
              <!-- Provider Header -->
              <div class="flex items-center justify-between p-4 cursor-pointer hover:bg-[#1a1a1a] transition" @click="toggleProvider(provider.code)">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-purple-900/30 rounded-lg flex items-center justify-center text-purple-400 font-black text-sm">{{ provider.code.substring(0,2) }}</div>
                  <div>
                    <p class="font-bold text-white">{{ provider.name }}</p>
                    <p class="text-xs text-gray-500">{{ provider.games_count || provider.games?.length || 0 }} jogos</p>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <!-- Provider 3-dots menu -->
                  <div class="relative">
                    <button @click.stop="toggleProviderMenu(provider.code)" class="p-2 hover:bg-gray-800 rounded-lg transition text-gray-400">⋮</button>
                    <div v-if="openProviderMenu === provider.code" class="absolute right-0 top-10 bg-[#222] border border-gray-700 rounded-lg shadow-xl z-50 min-w-[200px]">
                      <button @click.stop="sendProviderToSlots(provider)" class="w-full text-left px-4 py-3 hover:bg-gray-700 text-sm flex items-center gap-2 rounded-t-lg">🎰 Enviar para Slots</button>
                      <button @click.stop="removeProviderFromSlots(provider)" class="w-full text-left px-4 py-3 hover:bg-gray-700 text-sm flex items-center gap-2 text-red-400 rounded-b-lg">✕ Remover dos Slots</button>
                    </div>
                  </div>
                  <span class="text-gray-500 text-lg">{{ expandedProviders.includes(provider.code) ? '▼' : '▶' }}</span>
                </div>
              </div>

              <!-- Provider Games List -->
              <div v-if="expandedProviders.includes(provider.code)" class="border-t border-gray-800">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 p-4">
                  <div v-for="game in provider.games" :key="game.id" class="relative group bg-[#1a1a1a] rounded-lg overflow-hidden border border-gray-800 hover:border-purple-500 transition">
                    <img v-if="game.banner_local" :src="game.banner_local" class="w-full aspect-[3/4] object-cover" />
                    <div v-else class="w-full aspect-[3/4] bg-[#222] flex items-center justify-center text-xs text-gray-600">Sem capa</div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-2">
                      <p class="text-[10px] font-bold text-white truncate">{{ game.game_name }}</p>
                      <p v-if="game.is_popular" class="text-[8px] text-[#fca000] font-bold">⭐ Popular</p>
                    </div>
                    <!-- Game 3-dots menu -->
                    <div class="absolute top-1 right-1">
                      <button @click.stop="toggleGameMenu(game.id)" class="p-1 bg-black/50 hover:bg-black/80 rounded text-white text-xs opacity-0 group-hover:opacity-100 transition">⋮</button>
                      <div v-if="openGameMenu === game.id" class="absolute right-0 top-7 bg-[#222] border border-gray-700 rounded-lg shadow-xl z-50 min-w-[180px]">
                        <button @click.stop="sendGameToPopular(game)" class="w-full text-left px-3 py-2 hover:bg-gray-700 text-[11px] flex items-center gap-2">⭐ Enviar para Populares</button>
                        <button @click.stop="removeGameFromPopular(game)" class="w-full text-left px-3 py-2 hover:bg-gray-700 text-[11px] flex items-center gap-2 text-red-400">✕ Remover dos Populares</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Slot Provider Cover Upload Modal -->
          <div v-if="showCoverModal" class="fixed inset-0 bg-black/70 z-[100] flex items-center justify-center p-4" @click.self="showCoverModal = false">
            <div class="bg-[#1a1a1a] rounded-2xl p-6 max-w-md w-full border border-gray-700 space-y-4">
              <h3 class="text-lg font-bold text-white">Selecionar Capa para "{{ coverModalProvider?.name }}"</h3>
              <p class="text-xs text-gray-400">Tamanho recomendado: 330×440px</p>
              <input type="file" @change="handleCoverUpload" accept="image/*" class="text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-600 file:text-white" />
              <div class="flex justify-end gap-2">
                <button @click="showCoverModal = false" class="px-4 py-2 bg-gray-700 rounded-lg text-sm">Cancelar</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Save Button -->
      <div class="sticky bottom-6 mt-8 p-4 bg-[#1a1a1a]/90 backdrop-blur-md rounded-2xl border border-[#fca000]/30 shadow-[0_0_30px_rgba(252,160,0,0.1)] flex justify-end">
        <button @click="saveSettings" :disabled="isSaving" class="px-8 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-black text-lg rounded-xl hover:brightness-110 transition shadow-lg flex items-center gap-2">
          <div v-if="isSaving" class="w-5 h-5 border-2 border-black border-t-transparent rounded-full animate-spin"></div>
          {{ isSaving ? 'Salvando...' : '✔ Salvar Alterações Globais' }}
        </button>
      </div>
    </div>

    <!-- Loading Spinner -->
    <div v-if="isLoading" class="flex justify-center items-center h-screen">
      <div class="w-12 h-12 border-4 border-[#fca000] border-t-transparent rounded-full animate-spin"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const emit = defineEmits(['close']);

// Auth
const isAdminLoggedIn = ref(false);
const loginUser = ref('');
const loginPass = ref('');
const isLoggingIn = ref(false);
const loginError = ref('');

// Settings
const isLoading = ref(false);
const isSaving = ref(false);
const settings = ref({
  home_background: '/founde/eu7.png',
  home_banners: ['/banner/banner.avif'],
  category_icon_popular: '/casino_icons/popular.avif',
  category_icon_slot: '/casino_icons/slots.avif',
  category_icon_retro: '/casino_icons/retro.png',
  support_telegram: '', support_whatsapp: '', support_facebook: '', support_instagram: '',
  invite_bonus_tiers: [{ people: 1, bets: 300, reward: 50 }, { people: 20, bets: 300, reward: 500 }]
});

// API de Jogos
const apiAgentCode = ref('');
const apiAgentToken = ref('');
const apiWebhookSecret = ref('');
const apiLoading = ref(false);
const fetchingGames = ref(false);
const apiMessage = ref('');
const apiSuccess = ref(false);
const apiProviders = ref([]);
const expandedProviders = ref([]);
const openProviderMenu = ref(null);
const openGameMenu = ref(null);
const showCoverModal = ref(false);
const coverModalProvider = ref(null);

const totalGamesCount = computed(() => apiProviders.value.reduce((acc, p) => acc + (p.games?.length || 0), 0));

// Login
const handleLogin = async () => {
  if (!loginUser.value || !loginPass.value) return;
  isLoggingIn.value = true;
  loginError.value = '';
  try {
    const res = await fetch('/api/login', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ login: loginUser.value, password: loginPass.value }) });
    if (res.ok) {
      const data = await res.json();
      if (data.user?.is_admin) { isAdminLoggedIn.value = true; fetchSettings(); } 
      else { loginError.value = 'Usuário não tem privilégios de administrador.'; }
    } else { loginError.value = 'Login falhou. Verifique suas credenciais.'; }
  } catch (err) { loginError.value = 'Erro ao contatar o servidor.'; }
  finally { isLoggingIn.value = false; }
};

// Fetch settings 
const fetchSettings = async () => {
  isLoading.value = true;
  try {
    const res = await fetch('/api/settings');
    if (res.ok) {
      const data = await res.json();
      if (data.home_banners) try { data.home_banners = JSON.parse(data.home_banners); } catch(e) {}
      if (data.invite_bonus_tiers) try { data.invite_bonus_tiers = JSON.parse(data.invite_bonus_tiers); } catch(e) {}
      settings.value = { ...settings.value, ...data };
      if (data.api_agent_code) apiAgentCode.value = data.api_agent_code;
      if (data.api_agent_token) apiAgentToken.value = data.api_agent_token;
      if (data.api_webhook_secret) apiWebhookSecret.value = data.api_webhook_secret;
    }
  } catch (err) { console.error(err); }
  
  // Also fetch existing games
  try {
    const res = await fetch('/api/admin/games-grouped', { headers: { 'Accept': 'application/json' }});
    if (res.ok) { apiProviders.value = await res.json(); }
  } catch(e) {}
  
  isLoading.value = false;
};

// Upload
const uploadFile = async (event, settingKey, folder) => {
  const file = event.target.files[0]; if (!file) return;
  const fd = new FormData(); fd.append('image', file); fd.append('folder', folder);
  try {
    const res = await fetch('/api/admin/upload', { method: 'POST', headers: { 'Accept': 'application/json' }, body: fd });
    if (res.ok) { const d = await res.json(); settings.value[settingKey] = d.url; }
    else alert("Falha no upload");
  } catch(e) { console.error(e); }
};
const uploadBanner = async (event) => {
  const file = event.target.files[0]; if (!file) return;
  const fd = new FormData(); fd.append('image', file); fd.append('folder', 'banner');
  try {
    const res = await fetch('/api/admin/upload', { method: 'POST', headers: { 'Accept': 'application/json' }, body: fd });
    if (res.ok) { const d = await res.json(); settings.value.home_banners.push(d.url); }
  } catch(e) { console.error(e); }
};
const removeBanner = (i) => settings.value.home_banners.splice(i, 1);

// Tiers
const addTier = () => settings.value.invite_bonus_tiers.push({ people: 1, bets: 300, reward: 100 });
const removeTier = (i) => settings.value.invite_bonus_tiers.splice(i, 1);

// Save settings
const saveSettings = async () => {
  isSaving.value = true;
  try {
    const dataToSave = {
      ...settings.value,
      api_agent_code: apiAgentCode.value,
      api_agent_token: apiAgentToken.value,
      api_webhook_secret: apiWebhookSecret.value
    };
    const res = await fetch('/api/admin/settings', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(dataToSave) });
    if (res.ok) alert("Configurações atualizadas com sucesso!");
    else {
      const errorData = await res.json().catch(() => ({}));
      alert("Erro ao salvar: " + (errorData.message || res.statusText));
    }
  } catch(e) { 
    console.error(e); 
    alert("Erro na requisição: " + e.message);
  }
  isSaving.value = false;
};

// === API de Jogos ===
const testApiConnection = async () => {
  apiLoading.value = true; apiMessage.value = '';
  try {
    const res = await fetch('/api/admin/test-api', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ agent_code: apiAgentCode.value, agent_token: apiAgentToken.value, webhook_secret: apiWebhookSecret.value }) });
    const data = await res.json();
    apiSuccess.value = data.success;
    apiMessage.value = data.message + (data.agent ? ` | Saldo: R$ ${data.agent.balance}` : '');
  } catch(e) { apiSuccess.value = false; apiMessage.value = 'Erro: ' + e.message; }
  apiLoading.value = false;
};

const fetchGamesFromApi = async () => {
  fetchingGames.value = true; apiLoading.value = true; apiMessage.value = '';
  try {
    const res = await fetch('/api/admin/fetch-games', { method: 'POST', headers: { 'Accept': 'application/json' }});
    const data = await res.json();
    apiSuccess.value = data.success;
    apiMessage.value = data.message;
    if (data.success && data.providers) apiProviders.value = data.providers;
    // Reload grouped data
    const res2 = await fetch('/api/admin/games-grouped', { headers: { 'Accept': 'application/json' }});
    if (res2.ok) apiProviders.value = await res2.json();
  } catch(e) { apiSuccess.value = false; apiMessage.value = 'Erro: ' + e.message; }
  fetchingGames.value = false; apiLoading.value = false;
};

const deleteAllGames = async () => {
  if (!confirm('Tem certeza que deseja excluir todos os jogos importados?')) return;
  try {
    const res = await fetch('/api/admin/delete-games', { method: 'POST', headers: { 'Accept': 'application/json' }});
    const data = await res.json();
    apiSuccess.value = data.success; apiMessage.value = data.message;
    apiProviders.value = [];
  } catch(e) { apiMessage.value = 'Erro: ' + e.message; }
};

// Provider/Game menus
const toggleProvider = (code) => {
  const i = expandedProviders.value.indexOf(code);
  i >= 0 ? expandedProviders.value.splice(i, 1) : expandedProviders.value.push(code);
};
const toggleProviderMenu = (code) => { openProviderMenu.value = openProviderMenu.value === code ? null : code; openGameMenu.value = null; };
const toggleGameMenu = (id) => { openGameMenu.value = openGameMenu.value === id ? null : id; openProviderMenu.value = null; };

// Send game to Popular
const sendGameToPopular = async (game) => {
  openGameMenu.value = null;
  try {
    const currentPopular = [];
    apiProviders.value.forEach(p => p.games?.forEach(g => { if (g.is_popular) currentPopular.push(g.id); }));
    if (!currentPopular.includes(game.id)) currentPopular.push(game.id);
    const res = await fetch('/api/admin/set-popular', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ game_ids: currentPopular.slice(0, 9) }) });
    if (res.ok) { game.is_popular = true; apiMessage.value = `"${game.game_name}" adicionado aos Populares!`; apiSuccess.value = true; }
  } catch(e) { console.error(e); }
};
const removeGameFromPopular = async (game) => {
  openGameMenu.value = null;
  const currentPopular = [];
  apiProviders.value.forEach(p => p.games?.forEach(g => { if (g.is_popular && g.id !== game.id) currentPopular.push(g.id); }));
  try {
    const res = await fetch('/api/admin/set-popular', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ game_ids: currentPopular }) });
    if (res.ok) { game.is_popular = false; apiMessage.value = `"${game.game_name}" removido dos Populares.`; apiSuccess.value = true; }
  } catch(e) { console.error(e); }
};

// Send provider to Slots
const sendProviderToSlots = (provider) => {
  openProviderMenu.value = null;
  coverModalProvider.value = provider;
  showCoverModal.value = true;
};
const handleCoverUpload = async (event) => {
  const file = event.target.files[0]; if (!file) return;
  const fd = new FormData();
  fd.append('cover_image', file);
  fd.append('provider_code', coverModalProvider.value.code);
  try {
    const res = await fetch('/api/admin/set-slot-provider', { method: 'POST', headers: { 'Accept': 'application/json' }, body: fd });
    if (res.ok) {
      const data = await res.json();
      coverModalProvider.value.is_slot = true;
      coverModalProvider.value.cover_image = data.provider?.cover_image;
      apiMessage.value = `"${coverModalProvider.value.name}" adicionado aos Slots!`;
      apiSuccess.value = true;
    }
  } catch(e) { console.error(e); }
  showCoverModal.value = false;
};
const removeProviderFromSlots = async (provider) => {
  openProviderMenu.value = null;
  try {
    await fetch('/api/admin/remove-slot-provider', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ provider_code: provider.code }) });
    provider.is_slot = false; provider.cover_image = null;
    apiMessage.value = `"${provider.name}" removido dos Slots.`; apiSuccess.value = true;
  } catch(e) { console.error(e); }
};
</script>
