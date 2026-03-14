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
          <p class="text-gray-400 text-sm mt-1">Gerencie imagens, bônus e links de suporte de todo o sistema em tempo real.</p>
        </div>
        <button @click="$emit('close')" class="flex items-center gap-2 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
            </svg>
          Sair
        </button>
      </div>

      <!-- Settings Sections -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- SECTION 1: Appearance -->
        <div class="bg-[#1a1a1a] p-6 rounded-2xl border border-gray-800 shadow-xl space-y-6">
          <div class="flex gap-3 items-center border-b border-gray-800 pb-3">
             <div class="w-8 h-8 rounded-lg bg-yellow-900/30 flex items-center justify-center text-[#fca000]">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
             </div>
            <h3 class="text-xl font-bold text-white">Aparência do Site</h3>
          </div>

          <!-- Background Upload -->
          <div>
            <label class="block text-sm text-gray-300 font-medium mb-2">Fundo Principal (1080x1920)</label>
            <div class="flex items-center gap-4">
              <img v-if="settings.home_background" :src="settings.home_background" class="w-16 h-24 object-cover rounded-lg border border-gray-700 bg-black" />
              <input type="file" @change="e => uploadFile(e, 'home_background', 'founde')" class="text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#fca000] file:text-black hover:file:bg-[#e08e00] transition" />
            </div>
          </div>

          <!-- Banner Upload -->
          <div class="pt-4 border-t border-gray-800">
            <label class="block text-sm text-gray-300 font-medium mb-2">Banner Principal (1411x418)</label>
            <div class="flex flex-col gap-4">
              <div v-for="(banner, index) in settings.home_banners" :key="index" class="relative group">
                <img :src="banner" class="w-full h-24 object-cover rounded-lg border border-gray-700 bg-black" />
                <button @click="removeBanner(index)" class="absolute top-2 right-2 bg-red-600 text-white p-1.5 rounded-md opacity-0 group-hover:opacity-100 transition shadow-lg">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
              <input type="file" @change="e => uploadBanner(e)" class="text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-gray-700 transition" />
            </div>
          </div>
        </div>

        <!-- SECTION 2: Category Icons -->
        <div class="bg-[#1a1a1a] p-6 rounded-2xl border border-gray-800 shadow-xl space-y-6">
          <div class="flex gap-3 items-center border-b border-gray-800 pb-3">
             <div class="w-8 h-8 rounded-lg bg-yellow-900/30 flex items-center justify-center text-[#fca000]">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
             </div>
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
             <div class="w-8 h-8 rounded-lg bg-yellow-900/30 flex items-center justify-center text-[#fca000]">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
             </div>
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
              <button @click="removeTier(index)" class="ml-auto bg-red-900/30 text-red-500 hover:bg-red-600 hover:text-white p-2 rounded-lg transition mt-4 md:mt-0">
                Remover
              </button>
            </div>
            
            <button @click="addTier" class="w-full border-2 border-dashed border-gray-700 text-gray-400 font-bold py-3 rounded-xl hover:border-[#fca000] hover:text-[#fca000] transition">
              + Adicionar Novo Baú (Tier)
            </button>
          </div>
        </div>

        <!-- SECTION 4: Social Links -->
        <div class="bg-[#1a1a1a] p-6 rounded-2xl border border-gray-800 shadow-xl space-y-6 md:col-span-2">
          <div class="flex gap-3 items-center border-b border-gray-800 pb-3">
             <div class="w-8 h-8 rounded-lg bg-yellow-900/30 flex items-center justify-center text-[#fca000]">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
             </div>
            <h3 class="text-xl font-bold text-white">Suporte & Redes Sociais</h3>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="social in ['telegram', 'whatsapp', 'facebook', 'instagram']" :key="social">
              <label class="block text-sm text-gray-300 font-medium capitalize mb-1">Link do {{ social }}</label>
              <input v-model="settings[`support_${social}`]" type="url" class="w-full bg-[#121212] border border-gray-700 rounded-lg h-12 px-4 focus:border-[#fca000] outline-none text-sm placeholder:text-gray-600" placeholder="https://" />
            </div>
          </div>
        </div>

      </div>

      <!-- Save Button -->
      <div class="sticky bottom-6 mt-8 p-4 bg-[#1a1a1a]/90 backdrop-blur-md rounded-2xl border border-[#fca000]/30 shadow-[0_0_30px_rgba(252,160,0,0.1)] flex justify-end">
        <button @click="saveSettings" :disabled="isSaving" class="px-8 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-black text-lg rounded-xl hover:brightness-110 transition shadow-lg flex items-center gap-2">
          <svg v-if="!isSaving" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <div v-else class="w-5 h-5 border-2 border-black border-t-transparent rounded-full animate-spin"></div>
          {{ isSaving ? 'Salvando...' : 'Salvar Alterações Globais' }}
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
import { ref, onMounted } from 'vue';

const emit = defineEmits(['close']);

// Initial auth states
const isAdminLoggedIn = ref(false);
const loginUser = ref('');
const loginPass = ref('');
const isLoggingIn = ref(false);
const loginError = ref('');

// Global Settings State
const isLoading = ref(false);
const isSaving = ref(false);
const settings = ref({
  home_background: '/founde/eu7.png',
  home_banners: ['/banner/banner.avif'],
  category_icon_popular: '/casino_icons/popular.avif',
  category_icon_slot: '/casino_icons/slots.avif',
  category_icon_retro: '/casino_icons/retro.png',
  support_telegram: 'https://telegram.org',
  support_whatsapp: 'https://whatsapp.com',
  support_facebook: 'https://facebook.com',
  support_instagram: 'https://instagram.com',
  invite_bonus_tiers: [
    { people: 1, bets: 300, reward: 50 },
    { selected: false, people: 20, bets: 300, reward: 500 } // Example defaults
  ]
});

// Authentication logic
const handleLogin = async () => {
  if (!loginUser.value || !loginPass.value) return;
  isLoggingIn.value = true;
  loginError.value = '';
  try {
    const res = await fetch('/api/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ login: loginUser.value, password: loginPass.value })
    });
    
    if (res.ok) {
      const data = await res.json();
      if (data.user && data.user.is_admin) {
        isAdminLoggedIn.value = true;
        fetchSettings();
      } else {
        loginError.value = 'Usuário não tem privilégios de administrador.';
      }
    } else {
      loginError.value = 'Login falhou. Verifique suas credenciais.';
    }
  } catch (err) {
    loginError.value = 'Erro ao contatar o servidor.';
  } finally {
    isLoggingIn.value = false;
  }
};

// Fetch real settings from server
const fetchSettings = async () => {
  isLoading.value = true;
  try {
    const res = await fetch('/api/settings');
    if (res.ok) {
      const data = await res.json();
      
      // Parse JSON arrays safely
      if(data.home_banners) data.home_banners = JSON.parse(data.home_banners);
      if(data.invite_bonus_tiers) data.invite_bonus_tiers = JSON.parse(data.invite_bonus_tiers);
      
      settings.value = { ...settings.value, ...data };
    }
  } catch (err) {
    console.error("Error fetching admin settings", err);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  // We don't fetch settings here directly because we need admin auth first to view this panel
});

// Upload Handlers
const uploadFile = async (event, settingKey, folder) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('image', file);
  formData.append('folder', folder);

  try {
    const res = await fetch('/api/admin/upload', {
      method: 'POST',
      headers: { 'Accept': 'application/json' },
      body: formData
    });
    
    if (res.ok) {
      const data = await res.json();
      settings.value[settingKey] = data.url;
    } else {
      alert("Falha no upload da imagem");
    }
  } catch (err) {
    console.error("Upload error", err);
  }
};

const uploadBanner = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('image', file);
  formData.append('folder', 'banner');

  try {
    const res = await fetch('/api/admin/upload', {
      method: 'POST',
      headers: { 'Accept': 'application/json' },
      body: formData
    });
    
    if (res.ok) {
      const data = await res.json();
      settings.value.home_banners.push(data.url);
    }
  } catch (err) {
    console.error("Upload banner error", err);
  }
};

const removeBanner = (index) => {
  settings.value.home_banners.splice(index, 1);
};

// Tiers Logic
const addTier = () => {
  settings.value.invite_bonus_tiers.push({ people: 1, bets: 300, reward: 100 });
};

const removeTier = (index) => {
  settings.value.invite_bonus_tiers.splice(index, 1);
};

// Save Settings to Database
const saveSettings = async () => {
  isSaving.value = true;
  try {
    const res = await fetch('/api/admin/settings', {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Accept': 'application/json' 
      },
      body: JSON.stringify(settings.value)
    });
    
    if (res.ok) {
      alert("Configurações atualizadas com sucesso! A página principal será afetada imediatamente.");
      // Optional: window.location.reload();
    } else {
      console.error(await res.text());
      alert("Erro ao salvar opções.");
    }
  } catch(err) {
    console.error("Save settings error", err);
  } finally {
    isSaving.value = false;
  }
};
</script>
