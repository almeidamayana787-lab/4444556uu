<template>
  <div class="min-h-screen bg-[#111111] text-white p-6 font-sans pb-32">
    <!-- Admin Login -->
    <div v-if="!isAdminLoggedIn && !isLoading" class="min-h-screen flex items-center justify-center p-4">
      <div class="max-w-md w-full bg-[#1a1a1a] p-8 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] border border-white/5 relative overflow-hidden group">
        <!-- Glow Effect -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-[#fca000]/10 blur-[80px] rounded-full group-hover:bg-[#fca000]/20 transition-all duration-700"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-[#fca000]/5 blur-[80px] rounded-full group-hover:bg-[#fca000]/10 transition-all duration-700"></div>

        <div class="relative z-10 flex flex-col items-center">
          <img src="/image-removebg-preview.png" alt="Logo" class="w-24 mb-6 drop-shadow-[0_0_15px_rgba(252,160,0,0.3)] transition-transform hover:scale-105" />
          <h2 class="text-3xl font-black text-white mb-2 tracking-tight">Painel de <span class="text-[#fca000]">Controle</span></h2>
          <p class="text-gray-500 text-sm mb-8 font-medium">Autenticação de Segurança Avançada</p>
          
          <div class="w-full space-y-5">
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Usuário de Acesso</label>
              <div class="relative">
                <input v-model="loginUser" type="text" class="w-full bg-black/40 border border-white/5 rounded-2xl h-14 px-5 focus:border-[#fca000]/50 outline-none transition-all placeholder:text-gray-700 text-white font-medium" placeholder="Digite seu login..." />
              </div>
            </div>
            
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Senha Secreta</label>
              <div class="relative">
                <input v-model="loginPass" type="password" @keyup.enter="handleLogin" class="w-full bg-black/40 border border-white/5 rounded-2xl h-14 px-5 focus:border-[#fca000]/50 outline-none transition-all placeholder:text-gray-700 text-white font-medium" placeholder="••••••••" />
              </div>
            </div>

            <button @click="handleLogin" :disabled="isLoggingIn" class="w-full h-14 bg-[#fca000] hover:bg-[#ffb326] text-black font-black text-lg rounded-2xl mt-8 shadow-[0_10px_30px_rgba(252,160,0,0.3)] hover:shadow-[0_15px_40px_rgba(252,160,0,0.4)] disabled:opacity-50 disabled:grayscale transition-all active:scale-95 flex items-center justify-center gap-3">
              <span v-if="isLoggingIn" class="w-5 h-5 border-3 border-black border-t-transparent rounded-full animate-spin"></span>
              <span v-else>Acessar Sistema</span>
            </button>

            <p v-if="loginError" class="text-red-500 text-sm text-center font-bold bg-red-500/10 p-4 rounded-2xl border border-red-500/20 animate-pulse">{{ loginError }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Admin Dashboard Main -->
    <div v-if="isAdminLoggedIn && !isLoading" class="flex min-h-screen bg-[#0a0a0a]">
      <!-- Sidebar Navigation -->
      <aside class="w-72 bg-[#111] border-r border-white/5 flex flex-col fixed inset-y-0 z-[100]">
        <div class="p-8 border-b border-white/5 flex flex-col items-center">
          <img src="/image-removebg-preview.png" alt="Logo" class="w-20 mb-4" />
          <h2 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em]">Management Console</h2>
        </div>

        <nav class="flex-1 p-4 space-y-2 overflow-y-auto mt-4">
          <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-[#fca000] text-black shadow-[0_10px_20px_rgba(252,160,0,0.2)]' : 'text-gray-400 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl transition-all group">
             <span class="text-xl">📊</span>
             <span class="font-bold">Visão Geral</span>
          </button>
          <button @click="activeTab = 'users'" :class="activeTab === 'users' ? 'bg-[#fca000] text-black shadow-[0_10px_20px_rgba(252,160,0,0.2)]' : 'text-gray-400 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl transition-all group">
             <span class="text-xl">👤</span>
             <span class="font-bold">Usuários</span>
          </button>
          <button @click="activeTab = 'games'" :class="activeTab === 'games' ? 'bg-[#fca000] text-black shadow-[0_10px_20px_rgba(252,160,0,0.2)]' : 'text-gray-400 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl transition-all group">
             <span class="text-xl">🎮</span>
             <span class="font-bold">Jogos & Provedores</span>
          </button>
          <button @click="activeTab = 'payments'" :class="activeTab === 'payments' ? 'bg-[#fca000] text-black shadow-[0_10px_20px_rgba(252,160,0,0.2)]' : 'text-gray-400 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl transition-all group">
             <span class="text-xl">💰</span>
             <span class="font-bold">Pagamentos & GGPIX</span>
          </button>
          <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-[#fca000] text-black shadow-[0_10px_20px_rgba(252,160,0,0.2)]' : 'text-gray-400 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl transition-all group">
             <span class="text-xl">⚙️</span>
             <span class="font-bold">Configuração Global</span>
          </button>
          <button @click="activeTab = 'support'" :class="activeTab === 'support' ? 'bg-[#fca000] text-black shadow-[0_10_20px_rgba(252,160,0,0.2)]' : 'text-gray-400 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl transition-all group">
             <span class="text-xl">🔗</span>
             <span class="font-bold">Suporte & Social</span>
          </button>
          <button @click="activeTab = 'retro'; fetchRetroGames()" :class="activeTab === 'retro' ? 'bg-purple-600 text-white shadow-[0_10px_20px_rgba(168,85,247,0.3)]' : 'text-gray-400 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl transition-all group">
             <span class="text-xl">🕹️</span>
             <span class="font-bold">Controle Retrô</span>
          </button>
          <button @click="activeTab = 'rtp'" :class="activeTab === 'rtp' ? 'bg-green-600 text-white shadow-[0_10px_20px_rgba(34,197,94,0.3)]' : 'text-gray-400 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl transition-all group">
             <span class="text-xl">⚖️</span>
             <span class="font-bold">Sist. Retribuição</span>
          </button>
          <button @click="activeTab = 'content'" :class="activeTab === 'content' ? 'bg-blue-600 text-white shadow-[0_10px_20px_rgba(37,99,235,0.3)]' : 'text-gray-400 hover:bg-white/5'" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl transition-all group">
             <span class="text-xl">📝</span>
             <span class="font-bold">Conteúdo Site</span>
          </button>
        </nav>

        <div class="p-4 border-t border-white/5">
          <button @click="$emit('close')" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl text-red-500 hover:bg-red-500/10 transition-all font-bold">
            <span class="text-xl">🚪</span>
            <span>Sair do Painel</span>
          </button>
        </div>
      </aside>

      <!-- Main Content Area -->
      <main class="flex-1 ml-72 p-10 max-w-7xl mx-auto w-full">
        <!-- Dashboard Header -->
        <header class="flex items-center justify-between mb-12">
          <div>
            <h1 class="text-4xl font-black text-white capitalize tracking-tight">{{ activeTab.replace('overview', 'Painel de Controle') }}</h1>
            <p class="text-gray-500 mt-2 font-medium">Gestão inteligente e centralizada da sua plataforma.</p>
          </div>
          <div class="flex items-center gap-3">
             <div class="bg-black/40 border border-white/5 px-6 py-3 rounded-2xl">
               <span class="text-xs text-gray-500 block uppercase font-black tracking-widest">Admin Sessão</span>
               <span class="text-white font-bold">Logado como Root</span>
             </div>
          </div>
        </header>

        <div class="space-y-10">
          <!-- Tab: Overview -->
          <div v-if="activeTab === 'overview'" class="animate-in fade-in slide-in-from-bottom-4 duration-500">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div class="bg-[#111] p-8 rounded-3xl border border-white/5 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                  <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 text-2xl">👤</div>
                  <span class="text-xs font-black text-gray-500 uppercase tracking-widest">Usuários</span>
                </div>
                <div class="text-4xl font-black text-white">{{ usersList.length }}+</div>
                <p class="text-gray-500 text-xs mt-2">Total de contas registradas</p>
              </div>
              <div class="bg-[#111] p-8 rounded-3xl border border-white/5 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                  <div class="w-12 h-12 bg-orange-500/10 rounded-2xl flex items-center justify-center text-orange-500 text-2xl">🎮</div>
                  <span class="text-xs font-black text-gray-500 uppercase tracking-widest">Jogos</span>
                </div>
                <div class="text-4xl font-black text-white">{{ totalGamesCount }}</div>
                <p class="text-gray-500 text-xs mt-2">Títulos ativos na plataforma</p>
              </div>
              <div class="bg-[#111] p-8 rounded-3xl border border-white/5 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                  <div class="w-12 h-12 bg-green-500/10 rounded-2xl flex items-center justify-center text-green-500 text-2xl">⚡</div>
                  <span class="text-xs font-black text-gray-500 uppercase tracking-widest">Provedores</span>
                </div>
                <div class="text-4xl font-black text-white">{{ apiProviders.length }}</div>
                <p class="text-gray-500 text-xs mt-2">Parceiros integrados</p>
              </div>
            </div>
            
            <div class="mt-10 bg-gradient-to-br from-[#1a1a1a] to-[#111] p-1 rounded-[40px] border border-white/5">
              <div class="bg-[#0a0a0a] rounded-[38px] p-10 flex flex-col md:flex-row items-center gap-10">
                <div class="flex-1 space-y-6">
                  <h2 class="text-3xl font-black text-white leading-tight">Mantenha sua plataforma <br/><span class="text-[#fca000]">atualizada e segura</span></h2>
                  <p class="text-gray-400 text-lg">Use as ferramentas laterais para gerenciar usuários, importar novos jogos e configurar gateways de pagamento.</p>
                  <div class="flex gap-4">
                    <button @click="activeTab = 'games'" class="px-8 py-3 bg-[#fca000] text-black font-black rounded-2xl shadow-lg hover:brightness-110 active:scale-95 transition-all">Importar Jogos</button>
                    <button @click="activeTab = 'users'" class="px-8 py-3 bg-[#222] text-white font-black rounded-2xl border border-white/10 hover:bg-white/5 transition-all">Ver Usuários</button>
                  </div>
                </div>
                <div class="w-72 h-72 bg-[#fca000]/5 rounded-full flex items-center justify-center border border-[#fca000]/10 shrink-0">
                  <div class="w-56 h-56 bg-[#fca000]/10 rounded-full flex items-center justify-center animate-pulse">
                    <img src="/image-removebg-preview.png" class="w-32 rotate-[10deg]" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Tab: Users -->
          <div v-if="activeTab === 'users'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-8">
            <div class="bg-[#111] p-8 rounded-3xl border border-white/5">
               <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                  <div>
                    <h3 class="text-2xl font-black text-white">Gestão de Jogadores</h3>
                    <p class="text-gray-500 text-sm">Controle saldos, permissões e histórico.</p>
                  </div>
                  <div class="flex gap-3">
                    <div class="relative group">
                      <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">🔍</span>
                      <input v-model="userSearch" @input="fetchUsers" type="text" placeholder="Nome ou E-mail..." class="bg-black/40 border border-white/10 rounded-2xl px-12 h-14 text-sm outline-none focus:border-[#fca000]/50 w-full md:w-80 transition-all" />
                    </div>
                    <button @click="openCreateUserModal" class="bg-[#fca000] text-black h-14 px-8 rounded-2xl text-sm font-black shadow-lg hover:brightness-110 active:scale-95 transition-all shrink-0">Novo Usuário</button>
                  </div>
               </div>

               <div class="overflow-hidden rounded-2xl border border-white/5">
                  <table class="w-full text-left text-sm">
                    <thead>
                      <tr class="bg-white/5 border-b border-white/5">
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Identificação</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Saldo Atual</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Status/Tipo</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Senha</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Controle</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                      <tr v-for="user in usersList" :key="user.id" class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-6 py-5">
                          <div class="font-black text-white">{{ user.name }}</div>
                          <div class="text-[11px] text-gray-500 font-medium">{{ user.email }}</div>
                        </td>
                        <td class="px-6 py-5">
                          <span class="text-green-500 font-bold bg-green-500/10 px-3 py-1.5 rounded-xl border border-green-500/20">R$ {{ Number(user.balance).toFixed(2) }}</span>
                        </td>
                        <td class="px-6 py-5">
                          <span v-if="user.is_demo" class="px-3 py-1 bg-purple-500/10 text-purple-400 rounded-xl text-[10px] font-black border border-purple-500/20 uppercase tracking-tighter">Influencer / Demo</span>
                          <span v-else class="px-3 py-1 bg-gray-500/10 text-gray-500 rounded-xl text-[10px] font-black border border-white/5 uppercase tracking-tighter">Normal</span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                           <span class="font-mono text-[11px] text-[#fca000] bg-[#fca000]/5 px-3 py-1.5 rounded-xl border border-[#fca000]/10 tracking-widest uppercase">
                             {{ user.password_plain || '********' }}
                           </span>
                         </td>
                        <td class="px-6 py-5 text-right">
                          <button @click="openEditUserModal(user)" class="bg-[#222] hover:bg-[#333] text-[#fca000] px-5 py-2.5 rounded-xl font-black text-xs transition-all border border-white/5">GERENCIAR</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
               </div>
               
               <div class="flex items-center justify-between mt-6">
                 <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Página {{ usersPage }}</p>
                 <div class="flex gap-2">
                   <button @click="prevUsersPage" :disabled="usersPage === 1" class="w-12 h-12 flex items-center justify-center bg-[#222] rounded-xl border border-white/5 text-white disabled:opacity-20">←</button>
                   <button @click="nextUsersPage" :disabled="!hasMoreUsers" class="w-12 h-12 flex items-center justify-center bg-[#222] rounded-xl border border-white/5 text-white disabled:opacity-20">→</button>
                 </div>
               </div>
            </div>
          </div>

          <!-- Tab: Settings / Design -->
          <div v-if="activeTab === 'settings'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-8 pb-20">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <!-- Site Appearance -->
              <div class="bg-[#111] p-8 rounded-3xl border border-white/5 space-y-8">
                <div class="flex items-center gap-4 border-b border-white/5 pb-4">
                  <span class="text-2xl">🖼️</span>
                  <h3 class="text-xl font-black text-white">Identidade Visual</h3>
                </div>
                
                <div class="space-y-4">
                  <label class="block text-xs font-black text-gray-500 uppercase tracking-widest">Background Principal (Full HD)</label>
                  <div class="bg-black/40 rounded-2xl p-6 border border-white/5 flex items-center justify-between gap-6">
                    <img v-if="settings.home_background" :src="settings.home_background" class="w-20 h-32 object-cover rounded-xl border border-white/10" />
                    <div class="flex-1">
                      <input type="file" @change="e => uploadFile(e, 'home_background', 'founde')" class="text-xs text-gray-400 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-[#fca000] file:text-black hover:file:brightness-110 cursor-pointer" />
                    </div>
                  </div>
                </div>

                <div class="space-y-4">
                  <label class="block text-xs font-black text-gray-500 uppercase tracking-widest">Banner Publicitários</label>
                  <div class="space-y-4">
                    <div v-for="(banner, index) in settings.home_banners" :key="index" class="relative group rounded-2xl overflow-hidden border border-white/10">
                      <img :src="banner" class="w-full h-24 object-cover" />
                      <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <button @click="removeBanner(index)" class="bg-red-500 text-white px-4 py-2 rounded-xl text-xs font-black shadow-lg">EXCLUIR BANNER</button>
                      </div>
                    </div>
                    <div class="bg-black/40 rounded-2xl p-6 border border-white/5">
                      <input type="file" @change="e => uploadBanner(e)" class="text-xs text-gray-400 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-[#222] file:text-white" />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Category Icons -->
              <div class="bg-[#111] p-8 rounded-3xl border border-white/5 space-y-8">
                <div class="flex items-center gap-4 border-b border-white/5 pb-4">
                  <span class="text-2xl">📂</span>
                  <h3 class="text-xl font-black text-white">Ícones de Categoria</h3>
                </div>
                
                <div class="space-y-6">
                  <div v-for="cat in ['popular', 'slot', 'retro']" :key="cat" class="flex items-center gap-6 p-4 rounded-2xl bg-black/20 border border-white/5">
                    <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center border border-white/5 overflow-hidden">
                       <img v-if="settings[`category_icon_${cat}`]" :src="settings[`category_icon_${cat}`]" class="w-12 h-12 object-contain" />
                    </div>
                    <div class="flex-1">
                       <p class="text-xs font-black text-[#fca000] uppercase tracking-widest mb-2">{{ cat }}</p>
                       <input type="file" @change="e => uploadFile(e, `category_icon_${cat}`, 'casino_icons')" class="text-[10px] text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#222] file:text-white" />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tiers -->
              <div class="md:col-span-2 bg-[#111] p-8 rounded-3xl border border-white/5 space-y-8">
                <div class="flex items-center justify-between border-b border-white/5 pb-4">
                  <div class="flex items-center gap-4">
                    <span class="text-2xl">🎁</span>
                    <h3 class="text-xl font-black text-white">Sistema de Bônus (Convite)</h3>
                  </div>
                  <button @click="addTier" class="px-6 py-2.5 bg-white/5 hover:bg-white/10 text-white font-black text-xs rounded-xl border border-white/10 transition-all">+ CRIAR NOVO TIER</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div v-for="(tier, index) in settings.invite_bonus_tiers" :key="index" class="bg-black/40 p-6 rounded-2xl border border-white/5 relative group">
                    <button @click="removeTier(index)" class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 rounded-full text-white text-[10px] font-bold shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">✕</button>
                    <div class="grid grid-cols-3 gap-4">
                      <div>
                        <label class="block text-[10px] text-gray-500 font-black uppercase mb-2">Pessoas</label>
                        <input v-model="tier.people" type="number" class="w-full bg-[#111] border border-white/10 rounded-xl h-10 px-3 text-sm text-white focus:border-[#fca000]" />
                      </div>
                      <div>
                        <label class="block text-[10px] text-gray-500 font-black uppercase mb-2">Apostas (R$)</label>
                        <input v-model="tier.bets" type="number" class="w-full bg-[#111] border border-white/10 rounded-xl h-10 px-3 text-sm text-white focus:border-[#fca000]" />
                      </div>
                      <div>
                        <label class="block text-[10px] text-gray-500 font-black uppercase mb-2">Prêmio (R$)</label>
                        <input v-model="tier.reward" type="number" class="w-full bg-[#111] border border-white/10 rounded-xl h-10 px-3 text-sm text-[#fca000] font-black focus:border-[#fca000]" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Popups Flutuantes -->
              <div class="md:col-span-2 bg-[#111] p-8 rounded-3xl border border-white/5 space-y-8">
                <div class="flex items-center justify-between border-b border-white/5 pb-4">
                  <div class="flex items-center gap-4">
                    <span class="text-2xl">🪄</span>
                    <h3 class="text-xl font-black text-white">Popups Flutuantes (Máx 3 por lado)</h3>
                  </div>
                  <button @click="addFloatingPopup" class="px-6 py-2.5 bg-white/5 hover:bg-white/10 text-white font-black text-xs rounded-xl border border-white/10 transition-all">+ ADICIONAR POPUP</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div v-for="(popup, index) in settings.floating_popups" :key="index" class="bg-black/40 p-6 rounded-2xl border border-white/5 relative group space-y-4">
                    <button @click="removeFloatingPopup(index)" class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 rounded-full text-white text-[10px] font-bold shadow-lg opacity-100 transition-opacity">✕</button>
                    
                    <div class="flex items-center gap-4">
                      <div class="w-16 h-16 bg-white/5 rounded-xl border border-white/10 overflow-hidden flex items-center justify-center shrink-0">
                        <img v-if="popup.image" :src="popup.image" class="w-full h-full object-contain" />
                        <span v-else class="text-[10px] text-gray-600 font-black uppercase">SEM MIDIA</span>
                      </div>
                      <div class="flex-1">
                        <label class="block text-[9px] text-gray-500 font-black uppercase tracking-widest mb-1">Mídia (AVIF/GIF/PNG)</label>
                        <input type="file" @change="e => uploadPopupImage(e, index)" class="text-[10px] text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#222] file:text-white cursor-pointer" />
                      </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                      <div class="col-span-2">
                        <label class="block text-[9px] text-gray-500 font-black uppercase tracking-widest mb-1">Link de Redirecionamento (Obrigatório)</label>
                        <input v-model="popup.link" type="url" class="w-full bg-[#111] border border-white/10 rounded-xl h-10 px-3 text-xs text-white focus:border-[#fca000] outline-none" placeholder="https://..." />
                      </div>
                      <div>
                        <label class="block text-[9px] text-gray-500 font-black uppercase tracking-widest mb-1">Posicionamento</label>
                        <select v-model="popup.side" class="w-full bg-[#111] border border-white/10 rounded-xl h-10 px-3 text-xs text-white focus:border-[#fca000] outline-none">
                          <option value="left">Esquerda</option>
                          <option value="right">Direita</option>
                        </select>
                      </div>
                      <div class="flex items-end">
                        <span class="text-[9px] font-black" :class="getPopupCount(popup.side) > 3 ? 'text-red-500' : 'text-gray-600'">
                          {{ getPopupCount(popup.side) }} / 3 neste lado
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Popups de Entrada -->
            <div class="md:col-span-2 bg-[#111] p-8 rounded-3xl border border-white/5 space-y-8 mt-8">
              <div class="flex items-center justify-between border-b border-white/5 pb-4">
                <div class="flex items-center gap-4">
                  <span class="text-2xl">🖼️</span>
                  <h3 class="text-xl font-black text-white">Popups de Entrada</h3>
                </div>
                <button @click="addEntryPopup" class="px-6 py-2.5 bg-white/5 hover:bg-white/10 text-white font-black text-xs rounded-xl border border-white/10 transition-all">+ ADICIONAR POPUP DE ENTRADA</button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="(popup, index) in settings.entry_popups" :key="index" class="bg-black/40 p-6 rounded-2xl border border-white/5 relative group space-y-4">
                  <button @click="removeEntryPopup(index)" class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 rounded-full text-white text-[10px] font-bold shadow-lg opacity-100 transition-opacity">✕</button>
                  
                  <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/5 rounded-xl border border-white/10 overflow-hidden flex items-center justify-center shrink-0">
                      <img v-if="popup.image" :src="popup.image" class="w-full h-full object-contain" />
                      <span v-else class="text-[10px] text-gray-600 font-black uppercase">SEM MIDIA</span>
                    </div>
                    <div class="flex-1">
                      <label class="block text-[9px] text-gray-500 font-black uppercase tracking-widest mb-1">Mídia Recom. 564x1002 (AVIF/GIF/PNG)</label>
                      <input type="file" @change="e => uploadEntryPopupImage(e, index)" class="text-[10px] text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#222] file:text-white cursor-pointer" />
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                      <label class="block text-[9px] text-gray-500 font-black uppercase tracking-widest mb-1">Deve aparecer em:</label>
                      <select v-model="popup.visibility" class="w-full bg-[#111] border border-white/10 rounded-xl h-10 px-3 text-xs text-white focus:border-[#fca000] outline-none">
                        <option value="all">Todos os Usuários</option>
                        <option value="logged_in">Apenas Cadastrados (Com Login)</option>
                        <option value="logged_out">Apenas Sem Cadastro (Sem Login)</option>
                      </select>
                    </div>
                    <div class="col-span-2">
                      <label class="block text-[9px] text-gray-500 font-black uppercase tracking-widest mb-1">Link ao Clicar (Opcional)</label>
                      <input v-model="popup.link" type="url" class="w-full bg-[#111] border border-white/10 rounded-xl h-10 px-3 text-xs text-white focus:border-[#fca000] outline-none" placeholder="https://..." />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Global Save Trigger -->
            <div class="fixed bottom-10 right-10 z-[101]">
              <button @click="saveSettings" :disabled="isSaving" class="px-10 py-5 bg-[#fca000] text-black font-black text-lg rounded-3xl shadow-[0_15px_40px_rgba(252,160,0,0.3)] hover:brightness-110 active:scale-95 transition-all flex items-center gap-4">
                <span v-if="isSaving" class="w-6 h-6 border-4 border-black border-t-transparent rounded-full animate-spin"></span>
                <span v-else>💾 SALVAR CONFIGURAÇÃO GLOBAL</span>
              </button>
            </div>
          </div>


          <!-- Tab: Payments (GGPIX) -->
          <div v-if="activeTab === 'payments'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-10 pb-20">
            <div class="bg-[#111] p-10 rounded-[40px] border border-white/5 shadow-2xl relative overflow-hidden">
               <div class="absolute -top-40 -right-40 w-96 h-96 bg-green-500/5 blur-[100px] rounded-full"></div>
            
               <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-10">
                  <div class="flex-1">
                    <div class="flex items-center gap-4 mb-4">
                      <div class="w-12 h-12 bg-green-500/10 rounded-2xl flex items-center justify-center text-green-500 text-2xl">💸</div>
                      <h3 class="text-3xl font-black text-white italic uppercase tracking-tighter">Gateway GGPIX</h3>
                    </div>
                    <p class="text-gray-400 text-lg max-w-xl">Configure suas credenciais de pagamento e gerencie o saldo de lucro da sua plataforma em tempo real.</p>
                  </div>
                  
                  <div v-if="ggpixBalance !== null" class="bg-black/60 border border-white/10 p-8 rounded-[32px] min-w-[300px] shadow-inner">
                     <span class="text-xs font-black text-gray-500 uppercase tracking-widest block mb-1">Saldo Disponível no Gateway</span>
                     <span class="text-5xl font-black text-green-500 font-mono tracking-tighter">R$ {{ Number(ggpixBalance.balance / 100).toFixed(2) }}</span>
                     <div class="mt-6 flex gap-3">
                        <button @click="fetchGGPIXBalance" :disabled="isFetchingBalance" class="flex-1 bg-white/5 h-12 rounded-xl text-white font-black text-xs border border-white/5 hover:bg-white/10 transition-all">RECARREGAR</button>
                        <button @click="showWithdrawProfitModal = true" class="flex-1 bg-green-500 h-12 rounded-xl text-black font-black text-xs hover:brightness-110 active:scale-95 transition-all">SACAR LUCRO</button>
                     </div>
                  </div>
                  <div v-else class="bg-black/60 border border-white/10 p-8 rounded-[32px] min-w-[300px] flex flex-col items-center justify-center gap-4">
                     <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Saldo não consultado</p>
                     <button @click="fetchGGPIXBalance" :disabled="isFetchingBalance" class="w-full bg-[#fca000] text-black h-12 rounded-xl font-black text-xs hover:brightness-110 transition-all">CONSULTAR AGORA</button>
                  </div>
               </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8 mt-12 pt-10 border-t border-white/5">
                  <div class="space-y-3">
                    <label class="text-xs font-black text-gray-500 uppercase tracking-widest ml-1">GGPIX API Key</label>
                    <input v-model="settings.ggpix_api_key" type="password" class="w-full bg-black/40 border border-white/10 rounded-2xl h-16 px-6 text-white focus:border-green-500/50 outline-none transition-all placeholder:text-gray-800 font-mono text-xs" placeholder="Bearer ..." />
                  </div>
                  <div class="space-y-3">
                    <label class="text-xs font-black text-gray-500 uppercase tracking-widest ml-1">Webhook Secret (Bearer Token)</label>
                    <input v-model="settings.ggpix_webhook_secret" type="password" class="w-full bg-black/40 border border-white/10 rounded-2xl h-16 px-6 text-white focus:border-green-500/50 outline-none transition-all placeholder:text-gray-800 font-mono text-xs" placeholder="seu_secret_shhh" />
                  </div>
               </div>

               <div class="flex justify-end mt-10">
                 <button @click="saveSettings" :disabled="isSaving" class="px-10 py-5 bg-green-600 text-white font-black text-lg rounded-3xl shadow-[0_15px_40px_rgba(0,255,100,0.1)] hover:brightness-110 active:scale-95 transition-all flex items-center gap-4">
                    <span v-if="isSaving" class="w-6 h-6 border-4 border-white border-t-transparent rounded-full animate-spin"></span>
                    <span v-else>💾 SALVAR CREDENCIAIS</span>
                 </button>
               </div>

               <!-- Gestão de Rollover -->
               <div class="md:col-span-2 bg-black/20 p-8 rounded-3xl border border-white/5 space-y-6 mt-10">
                 <div class="flex items-center gap-4 border-b border-white/5 pb-4">
                   <span class="text-2xl">🔄</span>
                   <h3 class="text-xl font-black text-white">Configuração de Rollover</h3>
                 </div>
                 
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                   <div>
                     <label class="block text-[10px] text-gray-500 font-black uppercase tracking-widest mb-2 text-green-500">Rollover de Depósito (Multiplicador)</label>
                     <div class="relative flex items-center">
                       <input v-model.number="settings.rollover_deposit_multiplier" type="number" step="1" min="1" class="w-full bg-[#111] border border-green-500/20 rounded-2xl h-14 px-4 text-center text-lg text-green-500 font-black focus:border-green-500/50 outline-none transition-all pr-8" />
                       <span class="absolute right-4 text-green-500 font-black text-lg">x</span>
                     </div>
                     <p class="text-[10px] text-gray-600 mt-2 italic uppercase">Exemplo: 1x (Depósito de R$ 10 exige R$ 10 em apostas)</p>
                   </div>
                   <div>
                     <label class="block text-[10px] text-gray-500 font-black uppercase tracking-widest mb-2 text-purple-500">Rollover de Bônus (Multiplicador)</label>
                     <div class="relative flex items-center">
                       <input v-model.number="settings.rollover_bonus_multiplier" type="number" step="1" min="1" class="w-full bg-[#111] border border-purple-500/20 rounded-2xl h-14 px-4 text-center text-lg text-purple-500 font-black focus:border-purple-500/50 outline-none transition-all pr-8" />
                       <span class="absolute right-4 text-purple-500 font-black text-lg">x</span>
                     </div>
                     <p class="text-[10px] text-gray-600 mt-2 italic uppercase">Exemplo: 20x (Bônus de R$ 10 exige R$ 200 em apostas)</p>
                   </div>
                 </div>
                 
                 <div class="flex justify-end pt-4">
                   <button @click="saveSettings" :disabled="isSaving" class="px-6 py-3 bg-[#fca000] text-black font-black text-xs rounded-xl hover:brightness-110 transition-all uppercase">
                     Salvar Rollover
                   </button>
                 </div>
               </div>

               <!-- Limites de Transação (MOVIDO) -->
               <div class="md:col-span-2 bg-black/20 p-8 rounded-3xl border border-white/5 space-y-6 mt-10">
                 <div class="flex items-center gap-4 border-b border-white/5 pb-4">
                   <span class="text-2xl">💰</span>
                   <h3 class="text-xl font-black text-white">Limites Financeiros</h3>
                 </div>
                 
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                   <div>
                     <label class="block text-[10px] text-gray-500 font-black uppercase tracking-widest mb-2 text-green-500">Depósito Mínimo</label>
                     <input type="text" :value="`R$ ${Number(settings.min_deposit || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`" @change="e => settings.min_deposit = parseFloat(e.target.value.replace('R$', '').trim().replace(/\./g, '').replace(',', '.')) || 0" class="w-full bg-[#111] border border-green-500/20 rounded-2xl h-14 px-4 text-center text-lg text-green-500 font-black focus:border-green-500/50 outline-none transition-all" />
                     <p class="text-[10px] text-gray-600 mt-2 italic uppercase">Formato esperado: R$ 0,00</p>
                   </div>
                   <div>
                     <label class="block text-[10px] text-gray-500 font-black uppercase tracking-widest mb-2 text-red-500">Saque Mínimo</label>
                     <input type="text" :value="`R$ ${Number(settings.min_withdrawal || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`" @change="e => settings.min_withdrawal = parseFloat(e.target.value.replace('R$', '').trim().replace(/\./g, '').replace(',', '.')) || 0" class="w-full bg-[#111] border border-red-500/20 rounded-2xl h-14 px-4 text-center text-lg text-red-500 font-black focus:border-red-500/50 outline-none transition-all" />
                     <p class="text-[10px] text-gray-600 mt-2 italic uppercase">Formato esperado: R$ 0,00</p>
                   </div>

                   <div class="col-span-1 md:col-span-2 pt-4">
                     <button @click="openBonusRulesModal" class="w-full h-14 bg-gradient-to-r from-purple-600/20 to-purple-800/10 border border-purple-500/30 rounded-2xl flex items-center justify-center gap-3 text-purple-400 font-black text-xs sm:text-sm hover:border-purple-500 transition-all uppercase tracking-widest">
                       <span class="text-xl">🎁</span> INSERIR / GERENCIAR BÔNUS DE DEPÓSITO
                     </button>
                   </div>
                 </div>
               </div>
            </div>
          </div>

          <!-- Tab: Games (MAX API) -->
          <div v-if="activeTab === 'games'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-10 pb-20">
            <div class="bg-[#111] p-10 rounded-[40px] border border-white/5 shadow-2xl relative overflow-hidden">
               <div class="absolute -top-40 -right-40 w-96 h-96 bg-purple-500/5 blur-[100px] rounded-full"></div>
            
               <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-10 border-b border-white/5 pb-10">
                  <div class="flex-1">
                    <div class="flex items-center gap-4 mb-4">
                      <div class="w-12 h-12 bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-500 text-2xl">🎮</div>
                      <h3 class="text-3xl font-black text-white uppercase tracking-tighter">MAX API GAMES</h3>
                    </div>
                    <p class="text-gray-400 text-lg max-w-xl">Gerencie sua integração de jogos. Importe catálogos inteiros de provedores líderes com um clique.</p>
                  </div>
                  
                  <div class="flex flex-wrap gap-3">
                    <button @click="testApiConnection" :disabled="apiLoading" class="h-14 px-8 bg-purple-600 text-white font-black rounded-2xl shadow-lg hover:brightness-110 active:scale-95 transition-all flex items-center gap-3">
                      <span v-if="apiLoading" class="w-4 h-4 border-3 border-white border-t-transparent rounded-full animate-spin"></span>
                      <span>TESTAR CONEXÃO</span>
                    </button>
                    <button @click="fetchGamesFromApi" :disabled="apiLoading" class="h-14 px-8 bg-black/40 border border-white/10 text-white font-black rounded-2xl hover:bg-white/5 transition-all flex items-center gap-3">
                      <span v-if="fetchingGames" class="w-4 h-4 border-3 border-white border-t-transparent rounded-full animate-spin"></span>
                      <span>SINCRIZAR JOGOS ({{ apiProviders.reduce((acc, p) => acc + (p.games?.length || 0), 0) }})</span>
                    </button>
                  </div>
               </div>

               <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
                  <div class="space-y-3">
                    <label class="text-xs font-black text-gray-500 uppercase tracking-widest ml-1">Agent Code</label>
                    <input v-model="apiAgentCode" type="text" class="w-full bg-black/40 border border-white/10 rounded-2xl h-14 px-5 text-white focus:border-purple-500/50 outline-none text-sm" placeholder="ag_..." />
                  </div>
                  <div class="space-y-3">
                    <label class="text-xs font-black text-gray-500 uppercase tracking-widest ml-1">API Token</label>
                    <input v-model="apiAgentToken" type="password" class="w-full bg-black/40 border border-white/10 rounded-2xl h-14 px-5 text-white focus:border-purple-500/50 outline-none text-sm" placeholder="tok_..." />
                  </div>
                  <div class="space-y-3">
                    <label class="text-xs font-black text-gray-500 uppercase tracking-widest ml-1">Webhook Secret</label>
                    <input v-model="apiWebhookSecret" type="password" class="w-full bg-black/40 border border-white/10 rounded-2xl h-14 px-5 text-white focus:border-purple-500/50 outline-none text-sm" placeholder="sec_..." />
                  </div>
               </div>

               <div v-if="apiMessage" class="mt-8 p-6 rounded-3xl text-sm font-bold border" :class="apiSuccess ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20'">
                  {{ apiMessage }}
               </div>
            </div>

            <div v-if="apiProviders.length > 0" class="space-y-6">
               <div class="flex items-center justify-between">
                 <h4 class="text-2xl font-black text-white italic uppercase tracking-tighter">Catálogo de Provedores</h4>
                 <button @click="deleteAllGames" class="text-red-500 text-xs font-black hover:underline uppercase tracking-widest">Wipe Data: Excluir Todos os Jogos</button>
               </div>

               <div class="grid grid-cols-1 gap-4">
                  <div v-for="provider in apiProviders" :key="provider.code" class="bg-[#111] rounded-3xl border border-white/5 overflow-hidden group">
                     <div @click="toggleProvider(provider.code)" class="p-6 flex items-center justify-between cursor-pointer hover:bg-white/[0.02] transition-all">
                        <div class="flex items-center gap-6">
                           <div class="w-16 h-16 bg-gradient-to-br from-[#222] to-[#111] rounded-2xl flex items-center justify-center text-purple-400 font-black text-xl border border-white/5">{{ provider.code.substring(0,2).toUpperCase() }}</div>
                           <div>
                              <h5 class="text-lg font-black text-white">{{ provider.name }}</h5>
                              <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">{{ provider.games_count || provider.games?.length || 0 }} Títulos disponíveis</p>
                           </div>
                        </div>
                        <div class="flex items-center gap-3">
                           <span v-if="provider.is_slot" class="px-3 py-1 bg-green-500/10 text-green-500 rounded-xl text-[10px] font-black border border-green-500/20 uppercase">Destaque em Slots</span>
                           <div class="relative">
                              <button @click.stop="toggleProviderMenu(provider.code)" class="w-10 h-10 flex items-center justify-center bg-[#222] rounded-xl text-white font-black text-lg">⋮</button>
                              <div v-if="openProviderMenu === provider.code" class="absolute right-0 top-12 bg-[#222] border border-white/10 rounded-2xl shadow-2xl z-[50] min-w-[220px] overflow-hidden backdrop-blur-md">
                                <button v-if="!provider.is_slot" @click.stop="sendProviderToSlots(provider)" class="w-full text-left px-5 py-4 hover:bg-white/5 text-xs font-black flex items-center gap-3 text-white transition-all uppercase tracking-tighter italic">🎰 Enviar para Slots</button>
                                <button v-else @click.stop="removeProviderFromSlots(provider)" class="w-full text-left px-5 py-4 hover:bg-white/5 text-xs font-black flex items-center gap-3 text-red-500 transition-all uppercase tracking-tighter italic">✕ Remover dos Slots</button>
                                <button @click.stop="openLogoModal(provider)" class="w-full text-left px-5 py-4 hover:bg-white/5 text-xs font-black flex items-center gap-3 text-white transition-all uppercase tracking-tighter italic border-t border-white/5">🖼️ Logo Lateral</button>
                              </div>
                           </div>
                           <span class="text-gray-600 group-hover:text-white transition-all ml-2" :class="expandedProviders.includes(provider.code) ? 'rotate-180' : ''">▼</span>
                        </div>
                     </div>

                     <div v-if="expandedProviders.includes(provider.code)" class="p-6 pt-0 border-t border-white/5 bg-black/20 animate-in slide-in-from-top-4 duration-300">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                           <div v-for="game in provider.games" :key="game.id" class="relative group aspect-[3/4] rounded-2xl overflow-hidden border border-white/10 bg-[#111]">
                              <img v-if="game.banner_local" :src="game.banner_local" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                              <div v-else class="w-full h-full flex items-center justify-center text-[10px] text-gray-700 font-black uppercase">Sem Capa</div>
                              <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black via-black/40 to-transparent p-4">
                                 <p class="text-[10px] font-black text-white truncate uppercase tracking-tighter italic">{{ game.game_name }}</p>
                                 <p v-if="game.is_popular" class="text-[8px] text-[#fca000] font-black tracking-widest uppercase mt-1">🔥 Popular</p>
                              </div>
                              <div class="absolute top-2 right-2 flex flex-col gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                 <button @click.stop="toggleGameMenu(game.id)" class="w-8 h-8 bg-black/80 rounded-lg flex items-center justify-center text-white text-xs border border-white/20">⋮</button>
                                 <div v-if="openGameMenu === game.id" class="absolute right-0 top-10 bg-[#222] border border-white/10 rounded-xl shadow-2xl z-[60] min-w-[180px] overflow-hidden">
                                    <button v-if="!game.is_popular" @click.stop="sendGameToPopular(game)" class="w-full text-left px-4 py-3 hover:bg-white/5 text-[10px] font-black text-[#fca000] uppercase italic tracking-tighter">⭐ Adicionar Popular</button>
                                    <button v-else @click.stop="removeGameFromPopular(game)" class="w-full text-left px-4 py-3 hover:bg-white/5 text-[10px] font-black text-red-500 uppercase italic tracking-tighter border-t border-white/5">✕ Remover Popular</button>
                                    
                                    <button v-if="!game.is_retro" @click.stop="sendGameToRetro(game)" class="w-full text-left px-4 py-3 hover:bg-white/5 text-[10px] font-black text-purple-400 uppercase italic tracking-tighter border-t border-white/5">🕹️ Adicionar ao Retrô</button>
                                    <button v-else @click.stop="removeGameFromRetro(game)" class="w-full text-left px-4 py-3 hover:bg-white/5 text-[10px] font-black text-red-500 uppercase italic tracking-tighter border-t border-white/5">✕ Remover do Retrô</button>
                                  </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
          </div>

          <!-- Tab: Social / Support -->
          <div v-if="activeTab === 'support'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-10 pb-20">
            <div class="bg-[#111] p-10 rounded-[40px] border border-white/5 shadow-2xl">
              <div class="flex items-center gap-4 border-b border-white/5 pb-6 mb-8">
                <span class="text-3xl">🔗</span>
                <h3 class="text-2xl font-black text-white uppercase tracking-tighter">Canais de Atendimento</h3>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div v-for="social in ['telegram', 'whatsapp', 'facebook', 'instagram']" :key="social" class="space-y-3">
                  <div class="flex items-center justify-between ml-1">
                    <label class="text-xs font-black text-gray-400 font-bold uppercase tracking-widest capitalize">Canal {{ social }}</label>
                  </div>
                  <div class="relative">
                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-xl filter grayscale">{{ social === 'telegram' ? '🔹' : social === 'whatsapp' ? '🟢' : social === 'facebook' ? '🔵' : '📷' }}</span>
                    <input v-model="settings[`support_${social}`]" type="url" class="w-full bg-black/40 border border-white/10 rounded-2xl h-16 pl-14 pr-6 text-white text-sm focus:border-[#fca000]/50 outline-none transition-all placeholder:text-gray-800" :placeholder="`https://${social}.com/seu_perfil`" />
                  </div>
                </div>
              </div>

              <div class="flex justify-end mt-12 border-t border-white/5 pt-10">
                <button @click="saveSettings" :disabled="isSaving" class="px-10 py-5 bg-[#fca000] text-black font-black text-lg rounded-3xl shadow-[0_15px_40px_rgba(252,160,0,0.3)] hover:brightness-110 active:scale-95 transition-all flex items-center gap-4">
                  <span v-if="isSaving" class="w-6 h-6 border-4 border-black border-t-transparent rounded-full animate-spin"></span>
                  <span v-else>💾 ATUALIZAR LINKS SOCIAIS</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Tab: Controle Retrô -->
          <div v-if="activeTab === 'retro'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-10 pb-20">
            <!-- Header -->
            <div class="bg-gradient-to-br from-purple-900/30 to-[#111] p-10 rounded-[40px] border border-purple-500/10 shadow-2xl relative overflow-hidden">
              <div class="absolute -top-40 -right-40 w-96 h-96 bg-purple-500/5 blur-[100px] rounded-full"></div>
              <div class="relative z-10">
                <div class="flex items-center gap-4 mb-4">
                  <div class="w-14 h-14 bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-400 text-3xl">🕹️</div>
                  <h3 class="text-3xl font-black text-white italic uppercase tracking-tighter">Controle Retrô</h3>
                </div>
                <p class="text-gray-400 text-lg">Gerencie jogos retrô, ajuste dificuldades e controle quais jogos aparecem na seção Retrô do site.</p>
              </div>
            </div>
          </div>

          <!-- Tab: Sistema de Retribuição -->
          <div v-if="activeTab === 'rtp'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-10 pb-20">
            <!-- Sistema de Retribuição (RTP Control) -->
            <div class="bg-gradient-to-r from-green-900/20 to-green-900/5 p-8 rounded-3xl border border-green-500/20 space-y-6">
              <div class="flex items-center gap-4 border-b border-green-500/20 pb-4">
                <span class="text-2xl drop-shadow-[0_0_10px_rgba(34,197,94,0.8)]">⚖️</span>
                <h3 class="text-xl font-black text-white italic tracking-tighter">Sistema de Retribuição (Controle de RTP Global)</h3>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Arrecadação -->
                <div class="space-y-3">
                  <label class="text-xs font-black text-green-400 uppercase tracking-widest ml-1">Arrecadação (Casa)</label>
                  <div class="relative">
                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-green-500 text-lg font-black italic">R$</span>
                    <input type="text" :value="`R$ ${Number(settings.system_arrecadacao || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`" @change="e => settings.system_arrecadacao = parseFloat(e.target.value.replace('R$', '').trim().replace(/\./g, '').replace(',', '.')) || 0" class="w-full bg-[#111] border border-green-500/30 rounded-2xl h-16 pl-14 px-6 text-white text-lg focus:border-green-500 outline-none transition-all" />
                  </div>
                  <p class="text-[10px] text-gray-500 italic mt-1 font-bold">Valor base esperado de lucro da plataforma.</p>
                </div>
                
                <!-- Distribuição -->
                <div class="space-y-3">
                  <label class="text-xs font-black text-purple-400 uppercase tracking-widest ml-1">Distribuição (Jogadores)</label>
                  <div class="relative">
                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-purple-500 text-lg font-black italic">R$</span>
                    <input type="text" :value="`R$ ${Number(settings.system_distribuicao || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`" @change="e => settings.system_distribuicao = parseFloat(e.target.value.replace('R$', '').trim().replace(/\./g, '').replace(',', '.')) || 0" class="w-full bg-[#111] border border-purple-500/30 rounded-2xl h-16 pl-14 px-6 text-white text-lg focus:border-purple-500 outline-none transition-all" />
                  </div>
                  <p class="text-[10px] text-gray-500 italic mt-1 font-bold">Valor base que retornará como ganho aos jogadores.</p>
                </div>
              </div>

              <!-- Calculadora RTP Automática -->
              <div class="bg-black/40 border border-white/5 rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
                <div>
                  <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest block mb-1">RTP Teórico Calculado (Return to Player)</span>
                  <div class="flex items-center gap-2">
                    <h2 class="text-4xl font-black italic tracking-tighter" :class="calculatedRtp > 98 ? 'text-red-500' : (calculatedRtp < 80 ? 'text-green-500' : 'text-yellow-500')">
                      {{ calculatedRtp.toFixed(2) }}%
                    </h2>
                    <span class="text-xs text-gray-500 font-bold uppercase" v-if="calculatedRtp > 98">(Risco de prejuízo)</span>
                  </div>
                </div>
                <button @click="saveSettingsAndSyncRTP" :disabled="isSaving" class="px-8 py-4 bg-green-600 text-white font-black text-sm rounded-xl shadow-[0_10px_30px_rgba(34,197,94,0.3)] hover:brightness-110 active:scale-95 transition-all w-full md:w-auto flex items-center justify-center gap-2">
                  <span v-if="isSaving" class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                  SALVAR E SINCRONIZAR RTP
                </button>
              </div>
            </div>
          </div>

          <!-- Tab: Conteúdo Site -->
          <div v-if="activeTab === 'content'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-10 pb-20">
            <div class="bg-[#111] p-10 rounded-[40px] border border-white/5 shadow-2xl space-y-12">
              <div class="flex items-center gap-4 border-b border-white/5 pb-6">
                <div class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-400 text-3xl">📝</div>
                <div>
                  <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">Gestão de Conteúdo</h3>
                  <p class="text-gray-500 text-sm">Personalize os textos das seções institucionais do site.</p>
                </div>
              </div>

              <!-- FAQ Content -->
              <div class="space-y-4">
                <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Conteúdo FAQ (HTML Permitido)</label>
                <textarea v-model="settings.faq_content" rows="6" class="w-full bg-black/40 border border-white/10 rounded-2xl p-6 text-white text-sm focus:border-blue-500/50 outline-none transition-all placeholder:text-gray-800" placeholder="Ex: <h3 class='text-white font-bold'>Como depositar?</h3><p>...</p>"></textarea>
              </div>

              <!-- About Us Content -->
              <div class="space-y-4">
                <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Conteúdo Sobre Nós (HTML Permitido)</label>
                <textarea v-model="settings.about_content" rows="6" class="w-full bg-black/40 border border-white/10 rounded-2xl p-6 text-white text-sm focus:border-blue-500/50 outline-none transition-all placeholder:text-gray-800" placeholder="Ex: <p>Nossa plataforma é foca em...</p>"></textarea>
              </div>

              <div class="flex justify-end pt-6">
                <button @click="saveSettings" :disabled="isSaving" class="px-10 py-5 bg-blue-600 text-white font-black text-lg rounded-3xl shadow-[0_15px_40px_rgba(37,99,235,0.3)] hover:brightness-110 active:scale-95 transition-all flex items-center gap-4">
                  <span v-if="isSaving" class="w-6 h-6 border-4 border-white border-t-transparent rounded-full animate-spin"></span>
                  <span v-else>💾 SALVAR CONTEÚDO</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Remaining Retro Tab Content -->
          <div v-if="activeTab === 'retro'" class="space-y-10">


            <!-- Game Cards with 3 dots menu -->
            <div class="bg-[#111] p-8 rounded-3xl border border-white/5 space-y-6">
              <div class="flex items-center gap-4 border-b border-white/5 pb-4">
                <span class="text-2xl">🎮</span>
                <h3 class="text-xl font-black text-white">Jogos Retro Disponíveis</h3>
              </div>
              <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <div v-for="game in retroGames" :key="game.id" class="relative group">
                  <div class="aspect-[3/4] rounded-2xl overflow-hidden border border-white/10 bg-[#222] relative">
                    <img :src="game.banner" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black via-black/60 to-transparent p-3">
                      <p class="text-[10px] font-black text-white truncate uppercase tracking-tighter italic">{{ game.name }}</p>
                      <span v-if="game.is_active" class="text-[8px] text-green-400 font-black uppercase">✓ Ativo na Seção Retrô</span>
                      <span v-else class="text-[8px] text-gray-500 font-black uppercase">Inativo</span>
                    </div>
                    <!-- 3 dots menu -->
                    <div class="absolute top-2 right-2">
                      <button @click.stop="retroMenuOpen = retroMenuOpen === game.id ? null : game.id" class="w-8 h-8 bg-black/80 rounded-lg flex items-center justify-center text-white text-xs border border-white/20 backdrop-blur-sm">⋮</button>
                      <div v-if="retroMenuOpen === game.id" class="absolute right-0 top-10 bg-[#222] border border-white/10 rounded-xl shadow-2xl z-[60] min-w-[180px] overflow-hidden">
                        <button @click.stop="openRetroEditModal(game)" class="w-full text-left px-4 py-3 hover:bg-white/5 text-[10px] font-black text-white uppercase italic tracking-tighter">✏️ Editar Nome e Capa</button>
                        <button @click.stop="toggleRetroGame(game)" class="w-full text-left px-4 py-3 hover:bg-white/5 text-[10px] font-black uppercase italic tracking-tighter border-t border-white/5" :class="game.is_active ? 'text-red-500' : 'text-green-400'">{{ game.is_active ? '✕ Remover do Retrô' : '🕹️ Enviar para Retrô' }}</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Ajustador de Dificuldade -->
            <div class="bg-[#111] p-8 rounded-3xl border border-purple-500/10 space-y-6">
              <div class="flex items-center gap-4 border-b border-white/5 pb-4">
                <span class="text-2xl">⚙️</span>
                <h3 class="text-xl font-black text-white">Ajustador de Dificuldade</h3>
              </div>
              <div v-if="retroDifficulty.length === 0" class="text-center py-10 text-gray-500 text-sm font-bold uppercase">Carregando configurações...</div>
              <div v-for="game in retroDifficulty" :key="game.id" class="bg-black/40 p-6 rounded-2xl border border-white/5">
                <div @click="retroDiffExpanded = retroDiffExpanded === game.id ? null : game.id" class="flex items-center justify-between cursor-pointer">
                  <div class="flex items-center gap-4">
                    <img :src="game.icon" class="w-10 h-10 rounded-xl object-cover border border-white/10" />
                    <h4 class="text-lg font-black text-white">{{ game.name }}</h4>
                  </div>
                  <span class="text-gray-500 transition-transform" :class="retroDiffExpanded === game.id ? 'rotate-180' : ''">▼</span>
                </div>
                <div v-if="retroDiffExpanded === game.id && game.settings.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-white/5">
                  <div v-for="s in game.settings" :key="s.key" class="space-y-2">
                    <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest">{{ s.label }}</label>
                    <input v-model.number="retroDiffValues[s.key]" type="number" step="0.01" class="w-full bg-[#111] border border-white/10 rounded-xl h-10 px-3 text-sm text-purple-400 font-black focus:border-purple-500/50 outline-none" />
                  </div>
                </div>
                <div v-else-if="retroDiffExpanded === game.id && game.settings.length === 0" class="text-gray-600 text-xs italic mt-4 pt-4 border-t border-white/5">Este jogo não possui configurações de dificuldade.</div>
              </div>

              <div class="flex justify-end mt-6">
                <button @click="saveRetroDifficulty" :disabled="isSaving" class="px-10 py-4 bg-purple-600 text-white font-black text-lg rounded-3xl shadow-[0_15px_40px_rgba(168,85,247,0.2)] hover:brightness-110 active:scale-95 transition-all flex items-center gap-3">
                  <span v-if="isSaving" class="w-5 h-5 border-4 border-white border-t-transparent rounded-full animate-spin"></span>
                  <span v-else>💾 SALVAR DIFICULDADES</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Modal: Editar Jogo Retrô -->
          <div v-if="showRetroEditModal" class="fixed inset-0 z-[120] flex items-center justify-center bg-black/90 backdrop-blur-md p-4">
            <div class="bg-[#0a0a0a] w-full max-w-md rounded-[32px] border border-white/10 shadow-2xl p-8 space-y-6">
              <h3 class="text-xl font-black text-white italic uppercase tracking-tighter">✏️ Editar Jogo Retrô</h3>
              <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Nome do Jogo</label>
                <input v-model="retroEditForm.name" type="text" class="w-full bg-[#111] border border-white/10 rounded-2xl h-12 px-4 text-white focus:border-purple-500/50 outline-none" />
              </div>
              <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Nova Capa do Jogo</label>
                <input type="file" @change="e => retroEditForm.bannerFile = e.target.files[0]" class="text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-purple-600 file:text-white" />
              </div>
              <div class="flex gap-4">
                <button @click="showRetroEditModal = false" class="flex-1 h-12 bg-[#1a1a1a] text-white font-black rounded-2xl">Cancelar</button>
                <button @click="saveRetroEdit" class="flex-1 h-12 bg-purple-600 text-white font-black rounded-2xl">Salvar</button>
              </div>
            </div>
          </div>

        </div>
      </main>
    </div>

    <!-- Loading Spinner -->
    <div v-if="isLoading" class="flex justify-center items-center h-screen">
      <div class="w-12 h-12 border-4 border-[#fca000] border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- MODAL: User Management -->
    <div v-if="showUserModal" class="fixed inset-0 z-[120] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4 overflow-y-auto py-20">
      <div class="bg-[#0a0a0a] w-full max-w-2xl rounded-[40px] border border-white/10 shadow-[0_0_100px_rgba(0,0,0,1)] overflow-hidden animate-in fade-in zoom-in duration-300 relative">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-[#fca000]/5 blur-[80px] rounded-full pointer-events-none"></div>
        
        <div class="relative z-10">
          <div class="p-8 border-b border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-4">
               <div class="w-14 h-14 bg-gradient-to-br from-[#222] to-[#111] rounded-2xl flex items-center justify-center text-3xl shadow-lg border border-white/5">👤</div>
               <div>
                  <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">{{ editingUser ? 'Configurar Jogador' : 'Novo Recruta' }}</h3>
                  <p class="text-gray-500 text-xs font-black uppercase tracking-widest">{{ editingUser ? `ID: #USER_${editingUser.id}` : 'Criando novo perfil no sistema' }}</p>
               </div>
            </div>
            <button @click="showUserModal = false" class="w-12 h-12 bg-white/5 rounded-full flex items-center justify-center hover:bg-white/10 transition-all text-white font-bold">✕</button>
          </div>

          <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Nome Completo</label>
                <input v-model="userForm.name" type="text" class="w-full bg-black/40 border border-white/5 rounded-2xl h-14 px-6 text-white focus:border-[#fca000]/50 outline-none transition-all placeholder:text-gray-800" placeholder="Nome do jogador" />
              </div>
              <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">E-mail de Acesso</label>
                <input v-model="userForm.email" type="email" class="w-full bg-black/40 border border-white/5 rounded-2xl h-14 px-6 text-white focus:border-[#fca000]/50 outline-none transition-all placeholder:text-gray-800" placeholder="exemplo@email.com" />
              </div>
              <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Telefone / Wallet</label>
                <input v-model="userForm.phone" type="text" class="w-full bg-black/40 border border-white/5 rounded-2xl h-14 px-6 text-white focus:border-[#fca000]/50 outline-none transition-all placeholder:text-gray-800" placeholder="+55 ..." />
              </div>
              <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Senha (Deixe vazio para manter)</label>
                <div class="relative">
                  <input v-model="userForm.password" :type="showUserPassword ? 'text' : 'password'" class="w-full bg-black/40 border border-white/5 rounded-2xl h-14 px-6 pr-14 text-white focus:border-[#fca000]/50 outline-none transition-all placeholder:text-gray-800 font-mono" :placeholder="userForm.password_plain || '********'" />
                  <button @click="showUserPassword = !showUserPassword" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-500 hover:text-[#fca000] transition-colors">
                    <svg v-if="!showUserPassword" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <div class="p-8 bg-gradient-to-br from-[#111] to-[#0a0a0a] rounded-[32px] border border-white/5 shadow-inner">
               <div class="flex items-center justify-between mb-6">
                 <div>
                   <h4 class="text-white font-black italic uppercase tracking-tighter">Carteira & Saldo</h4>
                   <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest">Controle financeiro direto</p>
                 </div>
                 <div class="text-right">
                   <span class="text-xs text-gray-500 block mb-1">Montante Atual</span>
                   <span class="text-3xl font-black text-[#fca000] font-mono tracking-tighter">R$ {{ Number(userForm.balance).toFixed(2) }}</span>
                 </div>
               </div>
               <div class="flex items-center gap-4">
                 <input v-model="userForm.balance" type="number" step="0.01" class="flex-1 bg-black/40 border border-white/10 rounded-2xl h-16 px-8 text-2xl font-black text-white focus:border-green-500/50 outline-none transition-all" />
                 <div class="flex-1 p-6 rounded-2xl border border-white/5 bg-black/20 flex items-center justify-between">
                   <span class="text-xs font-black text-gray-400 uppercase">Demo / Influencer</span>
                   <label class="relative inline-flex items-center cursor-pointer">
                      <input type="checkbox" v-model="userForm.is_demo" class="sr-only peer">
                      <div class="w-14 h-7 bg-white/5 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-[#333] after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
                   </label>
                 </div>
               </div>
            </div>

            <div v-if="editingUser" class="space-y-4">
               <button @click="showHistory = !showHistory" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-white transition-all flex items-center gap-2">
                 {{ showHistory ? '▼ Ocultar Histórico' : '▶ Ver Histórico de Saldo' }}
               </button>
               <div v-if="showHistory" class="max-h-60 overflow-y-auto rounded-3xl border border-white/5 bg-black/40 p-4 space-y-2 no-scrollbar">
                  <div v-for="tx in userHistory" :key="tx.id" class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5">
                     <div>
                        <p class="text-[11px] font-black text-white uppercase">{{ tx.type === 'deposit' ? '💰 Depósito' : tx.type === 'withdraw' ? '💸 Saque' : '🎮 Aposta' }}</p>
                        <p class="text-[9px] text-gray-500">{{ new Date(tx.created_at).toLocaleString() }}</p>
                     </div>
                     <span class="font-mono font-black" :class="tx.amount > 0 ? 'text-green-500' : 'text-red-500'">{{ tx.amount > 0 ? '+' : '' }}R$ {{ Math.abs(tx.amount).toFixed(2) }}</span>
                  </div>
               </div>
            </div>
          </div>

          <div class="p-8 border-t border-white/5 flex gap-4 bg-[#050505]">
            <button @click="showUserModal = false" class="flex-1 h-16 bg-[#1a1a1a] text-white font-black rounded-3xl hover:bg-[#222] transition-all uppercase italic tracking-tighter">Cancelar</button>
            <button @click="saveUser" class="flex-1 h-16 bg-[#fca000] text-black font-black rounded-3xl shadow-xl hover:brightness-110 active:scale-95 transition-all uppercase italic tracking-tighter">Confirmar Alterações</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL: Custom Logo/Cover -->
    <div v-if="showCoverModal" class="fixed inset-0 z-[120] flex items-center justify-center bg-black/90 backdrop-blur-md p-4">
       <div class="bg-[#111] w-full max-w-sm rounded-[32px] border border-white/10 p-8 text-center space-y-6 animate-in zoom-in duration-200">
          <div class="w-20 h-20 bg-purple-500/10 rounded-3xl flex items-center justify-center text-3xl mx-auto border border-purple-500/20">🖼️</div>
          <div>
            <h3 class="text-xl font-black text-white italic uppercase tracking-tighter">Alterar Imagem</h3>
            <p class="text-gray-500 text-xs mt-2 uppercase tracking-widest font-bold">{{ coverModalProvider?.name }}</p>
          </div>
          <div class="bg-black/40 p-10 rounded-2xl border-2 border-dashed border-white/10 hover:border-purple-500/50 transition-all relative">
             <input type="file" @change="handleCoverUpload" class="absolute inset-0 opacity-0 cursor-pointer" />
             <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Clique para selecionar <br/> ou arraste o arquivo</p>
          </div>
          <button @click="showCoverModal = false" class="w-full h-12 text-gray-500 font-black text-xs uppercase hover:text-white">Fechar Janela</button>
       </div>
    </div>

    <!-- Bonus Rules Modal -->
    <div v-if="showBonusRulesModal" class="fixed inset-0 z-[120] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4">
      <div class="bg-[#111] w-full max-w-2xl rounded-[40px] border border-white/10 shadow-[0_0_100px_rgba(0,0,0,1)] overflow-hidden relative flex flex-col max-h-[90vh]">
        <div class="absolute -top-40 -left-40 w-80 h-80 bg-purple-500/10 blur-[80px] rounded-full pointer-events-none"></div>
        <div class="p-8 border-b border-white/5 relative z-10 flex justify-between items-center shrink-0">
          <div>
            <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">Regras de Bônus</h3>
            <p class="text-gray-400 text-xs mt-1">Configuração de ganhos em depósitos automatizada</p>
          </div>
          <button @click="showBonusRulesModal = false" class="w-10 h-10 bg-white/5 hover:bg-white/10 rounded-full flex items-center justify-center text-gray-300 transition-all font-bold">✕</button>
        </div>
        
        <div class="p-8 overflow-y-auto no-scrollbar space-y-8 flex-1 relative z-10">
          
          <div class="bg-black/40 border border-white/5 p-6 rounded-3xl space-y-4">
            <h4 class="text-sm font-black text-white uppercase tracking-widest border-b border-white/5 pb-2">Nova Regra</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="sm:col-span-2">
                <label class="block text-[10px] text-gray-500 font-black uppercase mb-1">Nome de Identificação</label>
                <input v-model="newBonusRule.name" type="text" placeholder="Ex: Bônus de Fim de Semana" class="w-full bg-[#1a1a1a] border border-white/10 rounded-xl h-10 px-3 text-sm text-white focus:border-purple-500 outline-none transition-all" />
              </div>
              <div>
                <label class="block text-[10px] text-gray-500 font-black uppercase mb-1">Depósito Mínimo (R$)</label>
                <input v-model.number="newBonusRule.min_amount" type="number" min="0" step="1" class="w-full bg-[#1a1a1a] border border-white/10 rounded-xl h-10 px-3 text-sm text-white focus:border-purple-500 outline-none transition-all" />
              </div>
              <div>
                <label class="block text-[10px] text-gray-500 font-black uppercase mb-1">Depósito Máximo (R$)</label>
                <input v-model.number="newBonusRule.max_amount" type="number" min="0" step="1" class="w-full bg-[#1a1a1a] border border-white/10 rounded-xl h-10 px-3 text-sm text-white focus:border-purple-500 outline-none transition-all" />
              </div>
              <div class="sm:col-span-2">
                <label class="block text-[10px] text-purple-400 font-black uppercase mb-1">Valor de Bônus Concedido (R$)</label>
                <input v-model.number="newBonusRule.bonus_amount" type="number" min="0" step="1" class="w-full bg-purple-900/10 border border-purple-500/30 rounded-xl h-10 px-3 text-sm text-purple-400 font-black focus:border-purple-500 outline-none transition-all" />
              </div>
            </div>
            <button @click="saveBonusRule" :disabled="isSavingBonusRule" class="w-full h-12 bg-purple-600 text-white font-black rounded-xl hover:brightness-110 active:scale-95 transition-all text-xs flex items-center justify-center gap-2">
              <span v-if="isSavingBonusRule" class="w-4 h-4 border-2 border-white/50 border-t-white rounded-full animate-spin"></span>
              <span v-else>CRIAR REGRA AUTOMÁTICA</span>
            </button>
          </div>

          <div class="space-y-4">
            <h4 class="text-sm font-black text-white uppercase tracking-widest border-b border-white/5 pb-2">Regras Ativas</h4>
            
            <div v-if="bonusRules.length === 0" class="flex flex-col items-center justify-center p-10 text-center border border-white/5 rounded-3xl bg-black/20">
               <svg class="w-16 h-16 text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
               <p class="text-gray-400 text-sm font-black uppercase">Nenhuma regra encontrada</p>
               <p class="text-xs text-gray-600 mt-1">Crie a primeira regra no formulário acima para incentivar depósitos.</p>
            </div>
            
            <div v-else class="space-y-3">
              <div v-for="rule in bonusRules" :key="rule.id" class="bg-[#1a1a1a] border border-white/10 rounded-2xl p-4 flex justify-between items-center group hover:border-purple-500/50 transition-all">
                <div>
                  <h5 class="text-white font-bold text-sm">{{ rule.name }}</h5>
                  <p class="text-[10px] text-gray-500 uppercase mt-1">De R$ {{ rule.min_amount }} até R$ {{ rule.max_amount }}</p>
                  <span class="inline-block mt-2 px-2 py-1 bg-purple-500/20 text-purple-400 text-[9px] font-black rounded-md border border-purple-500/20 text-xs">GANHA + R$ {{ rule.bonus_amount }}</span>
                </div>
                <button @click="deleteBonusRule(rule.id)" class="w-10 h-10 bg-red-500/10 hover:bg-red-500/20 text-red-500 rounded-xl flex items-center justify-center transition-colors">🗑️</button>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </div>

    <!-- MODAL: Sacar Lucro GGPIX -->
    <div v-if="showWithdrawProfitModal" class="fixed inset-0 z-[120] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4">
      <div class="bg-[#0a0a0a] w-full max-w-md rounded-[40px] border border-white/10 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-10">
          <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-green-500/10 rounded-2xl flex items-center justify-center text-2xl">💰</div>
            <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter leading-tight">Sacar Lucro <br/> do Cassino</h3>
          </div>
          
          <div class="space-y-6">
            <div class="space-y-2">
              <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Valor do Saque (R$)</label>
              <input v-model="withdrawProfitForm.amount" type="number" step="0.01" class="w-full bg-black/40 border border-white/5 rounded-2xl h-14 px-6 text-white text-xl font-black focus:border-green-500/50 outline-none transition-all placeholder:text-gray-800" placeholder="0.00" />
            </div>
            <div class="space-y-2">
              <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Chave PIX</label>
              <input v-model="withdrawProfitForm.pix_key" type="text" class="w-full bg-black/40 border border-white/5 rounded-2xl h-14 px-6 text-white focus:border-green-500/50 outline-none transition-all placeholder:text-gray-800" placeholder="E-mail, CPF, Tel..." />
            </div>
            <div class="grid grid-cols-2 gap-4">
               <div class="space-y-2">
                 <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Tipo de Chave</label>
                 <select v-model="withdrawProfitForm.pix_key_type" class="w-full bg-black/40 border border-white/5 rounded-2xl h-14 px-4 text-white focus:border-green-500/50 outline-none">
                    <option value="CPF">CPF</option>
                    <option value="CNPJ">CNPJ</option>
                    <option value="PHONE">Telefone</option>
                    <option value="EMAIL">E-mail</option>
                    <option value="EVP">Aleatória</option>
                 </select>
               </div>
               <div class="space-y-2">
                 <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">CPF/CNPJ Beneficiário</label>
                 <input v-model="withdrawProfitForm.recipient_document" type="text" class="w-full bg-black/40 border border-white/5 rounded-2xl h-14 px-4 text-white focus:border-green-500/50 outline-none" placeholder="Só números" />
               </div>
            </div>
          </div>
          <div class="flex gap-4 mt-10">
            <button @click="showWithdrawProfitModal = false" class="flex-1 h-14 bg-[#1a1a1a] text-white font-black rounded-2xl hover:bg-[#222] transition-all uppercase italic tracking-tighter">Voltar</button>
            <button @click="handleWithdrawProfit" class="flex-1 h-14 bg-green-500 text-black font-black rounded-2xl shadow-lg hover:brightness-110 active:scale-95 transition-all uppercase italic tracking-tighter">Sacar Agora</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const emit = defineEmits(['close']);

// Header / Navigation
const activeTab = ref('overview'); // overview, users, games, payments, settings, support
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
  home_banners: [],
  category_icon_popular: '',
  category_icon_slot: '',
  category_icon_retro: '',
  support_telegram: '', support_whatsapp: '', support_facebook: '', support_instagram: '',
  invite_bonus_tiers: [],
  floating_popups: [], // { image, link, side: 'left' | 'right' }
  entry_popups: [], // { image, link, visibility: 'all' | 'logged_in' | 'logged_out' }
  ggpix_api_key: '',
  ggpix_webhook_secret: '',
  min_deposit: 1,
  min_withdrawal: 10,
  rollover_deposit_multiplier: 1,
  rollover_bonus_multiplier: 20
});

// Stats computed
const totalGamesCount = computed(() => {
  return apiProviders.value.reduce((acc, p) => acc + (p.games?.length || 0), 0);
});

const calculatedRtp = computed(() => {
    let arr = parseFloat(settings.value.system_arrecadacao) || 0;
    let dist = parseFloat(settings.value.system_distribuicao) || 0;
    if (arr + dist === 0) return 0;
    return (dist / (arr + dist)) * 100;
});

const saveSettingsAndSyncRTP = async () => {
    isSaving.value = true;
    try {
        const dataToSave = { ...settings.value };
        const res = await fetchWithAuth('/api/admin/sync-rtp', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dataToSave)
        });
        if (res.ok) {
            alert("Sistema de Retribuição atualizado e RTP sincronizado com sucesso!");
        } else {
            const err = await res.json().catch(() => ({}));
            alert("Erro na sincronização: " + (err.message || res.statusText));
        }
    } catch (e) {
        console.error(e);
        alert("Erro fatal: " + e.message);
    }
    isSaving.value = false;
};

// API de Jogos
const apiAgentCode = ref('');
const apiAgentToken = ref('');
const apiWebhookSecret = ref('');

// Gateway GGPIX
const ggpixBalance = ref(null);
const isFetchingBalance = ref(false);
const showWithdrawProfitModal = ref(false);
const withdrawProfitForm = ref({
  amount: 0,
  pix_key: '',
  pix_key_type: 'CPF',
  recipient_document: ''
});

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
const logoModalProvider = ref(null); // Added this as it was missing but used in openLogoModal
const showLogoModal = ref(false); // Added this as it was missing but used in openLogoModal

// User Management State
const usersList = ref([]);
const userSearch = ref('');
const usersPage = ref(1);
const hasMoreUsers = ref(false);
const showUserModal = ref(false);
const showUserPassword = ref(false);
const editingUser = ref(null);
const userForm = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  balance: 0,
  is_demo: false
});
const userHistory = ref([]);
const showHistory = ref(false);

// Retro Games State
const retroGames = ref([]);
const retroDifficulty = ref([]);
const retroDiffValues = ref({});
const retroDiffExpanded = ref(null);
const retroMenuOpen = ref(null);
const showRetroEditModal = ref(false);
const retroEditForm = ref({ game_id: '', name: '', bannerFile: null });

const showBonusRulesModal = ref(false);
const bonusRules = ref([]);
const newBonusRule = ref({ name: '', min_amount: 0, max_amount: 0, bonus_amount: 0 });
const isSavingBonusRule = ref(false);

const fetchBonusRules = async () => {
    try {
        const res = await fetchWithAuth('/api/admin/bonus-rules');
        if (res.ok) bonusRules.value = await res.json();
    } catch(e) { console.error("Error fetching bonus rules:", e); }
};

const openBonusRulesModal = () => {
    fetchBonusRules();
    showBonusRulesModal.value = true;
};

const saveBonusRule = async () => {
    if(!newBonusRule.value.name || newBonusRule.value.min_amount <= 0 || newBonusRule.value.max_amount <= newBonusRule.value.min_amount) {
        alert("Preencha corretamente os campos da regra. O valor mínimo de depósito precisa ser maior que 0 e o máximo maior que o mínimo.");
        return;
    }
    isSavingBonusRule.value = true;
    try {
        const res = await fetchWithAuth('/api/admin/bonus-rules', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(newBonusRule.value)
        });
        if (res.ok) {
            newBonusRule.value = { name: '', min_amount: 0, max_amount: 0, bonus_amount: 0 };
            fetchBonusRules();
        } else {
            const err = await res.json();
            alert('Erro ao salvar regra: ' + (err.message || 'Desconhecido'));
        }
    } catch(e) {
        alert('Erro ao salvar: ' + e.message);
    }
    isSavingBonusRule.value = false;
};

const deleteBonusRule = async (id) => {
    if (!confirm('Excluir regra de bônus?')) return;
    try {
        const res = await fetchWithAuth('/api/admin/bonus-rules/' + id, { method: 'DELETE' });
        if (res.ok) fetchBonusRules();
    } catch(e) {
        alert('Erro ao excluir: ' + e.message);
    }
};


const adminToken = ref(localStorage.getItem('admin_token') || '');

const fetchWithAuth = async (url, options = {}) => {
  const headers = {
    ...options.headers,
    'Accept': 'application/json',
  };
  if (adminToken.value) {
    headers['Authorization'] = `Bearer ${adminToken.value}`;
  }
  return fetch(url, { ...options, headers });
};

// Login
const handleLogin = async () => {
  if (!loginUser.value || !loginPass.value) return;
  isLoggingIn.value = true;
  loginError.value = '';
  try {
    const res = await fetch('/api/login', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ login: loginUser.value, password: loginPass.value }) });
    if (res.ok) {
      const data = await res.json();
      if (data.user?.is_admin) { 
        if (data.token) {
          adminToken.value = data.token;
          localStorage.setItem('admin_token', data.token);
        }
        isAdminLoggedIn.value = true; 
        fetchSettings(); 
      } 
      else { loginError.value = 'Usuário não tem privilégios de administrador.'; }
    } else { loginError.value = 'Login falhou. Verifique suas credenciais.'; }
  } catch (err) { loginError.value = 'Erro ao contatar o servidor.'; }
  finally { isLoggingIn.value = false; }
};

// Fetch settings 
// Popup Management
const addFloatingPopup = () => {
  if (settings.value.floating_popups.length >= 6) {
    alert("Limite máximo de 6 popups (3 por lado) atingido.");
    return;
  }
  settings.value.floating_popups.push({ image: '', link: '', side: 'left' });
};

const removeFloatingPopup = (index) => {
  settings.value.floating_popups.splice(index, 1);
};

const getPopupCount = (side) => {
  return settings.value.floating_popups.filter(p => p.side === side).length;
};

const uploadPopupImage = async (event, index) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('image', file);
  formData.append('folder', 'popups');

  try {
    const res = await fetchWithAuth('/api/admin/upload', {
      method: 'POST',
      body: formData
    });
    if (res.ok) {
      const data = await res.json();
      settings.value.floating_popups[index].image = data.url;
    } else {
      alert("Falha no upload do popup");
    }
  } catch (err) {
    alert("Erro ao fazer upload da imagem do popup");
  }
};

const addEntryPopup = () => {
  if (settings.value.entry_popups.length >= 5) {
    alert("Limite máximo de 5 popups de entrada atingido.");
    return;
  }
  settings.value.entry_popups.push({ image: '', link: '', visibility: 'all' });
};

const removeEntryPopup = (index) => {
  settings.value.entry_popups.splice(index, 1);
};

const uploadEntryPopupImage = async (event, index) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('image', file);
  formData.append('folder', 'entry_popups');

  try {
    const res = await fetchWithAuth('/api/admin/upload', {
      method: 'POST',
      body: formData
    });
    if (res.ok) {
      const data = await res.json();
      settings.value.entry_popups[index].image = data.url;
    } else {
      alert("Falha no upload do popup de entrada");
    }
  } catch (err) {
    alert("Erro ao fazer upload da imagem do popup de entrada");
  }
};

const fetchSettings = async () => {
  isLoading.value = true;
  try {
    const res = await fetchWithAuth('/api/settings');
    if (res.ok) {
      const data = await res.json();
      if (data.home_banners) try { data.home_banners = JSON.parse(data.home_banners); } catch(e) {}
      if (data.invite_bonus_tiers) try { data.invite_bonus_tiers = JSON.parse(data.invite_bonus_tiers); } catch(e) {}
      if (data.floating_popups) try { data.floating_popups = JSON.parse(data.floating_popups); } catch(e) {}
      if (data.entry_popups) try { data.entry_popups = JSON.parse(data.entry_popups); } catch(e) {}
      settings.value = { ...settings.value, ...data };
      if (data.api_agent_code) apiAgentCode.value = data.api_agent_code;
      if (data.api_agent_token) apiAgentToken.value = data.api_agent_token;
      if (data.api_webhook_secret) apiWebhookSecret.value = data.api_webhook_secret;
    }
  } catch (err) { console.error(err); }
  
  // Also fetch existing games
  try {
    const res = await fetchWithAuth('/api/admin/games-grouped');
    if (res.ok) { apiProviders.value = await res.json(); }
  } catch(e) {}
  
  isLoading.value = false;
};

// GGPIX Balance
const fetchGGPIXBalance = async () => {
  isFetchingBalance.value = true;
  try {
    const res = await fetchWithAuth('/api/admin/ggpix/balance');
    if (res.ok) {
      ggpixBalance.value = await res.json();
    } else {
      alert("Erro ao consultar saldo. Verifique as credenciais.");
    }
  } catch(e) { console.error(e); }
  finally { isFetchingBalance.value = false; }
};

const handleWithdrawProfit = async () => {
  try {
    const res = await fetchWithAuth('/api/admin/ggpix/withdraw-profit', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(withdrawProfitForm.value)
    });
    const data = await res.json();
    if (res.ok) {
      apiMessage.value = data.message;
      apiSuccess.value = true;
      showWithdrawProfitModal.value = false;
      fetchGGPIXBalance();
    } else {
      alert(data.error || "Erro ao realizar saque");
    }
  } catch(e) { console.error(e); }
};

// Upload
const uploadFile = async (event, settingKey, folder) => {
  const file = event.target.files[0]; if (!file) return;
  const fd = new FormData(); fd.append('image', file); fd.append('folder', folder);
  try {
    const res = await fetchWithAuth('/api/admin/upload', { method: 'POST', body: fd });
    if (res.ok) { const d = await res.json(); settings.value[settingKey] = d.url; }
    else alert("Falha no upload");
  } catch(e) { console.error(e); }
};
const uploadBanner = async (event) => {
  const file = event.target.files[0]; if (!file) return;
  const fd = new FormData(); fd.append('image', file); fd.append('folder', 'banner');
  try {
    const res = await fetchWithAuth('/api/admin/upload', { method: 'POST', body: fd });
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
    if (dataToSave.floating_popups) dataToSave.floating_popups = JSON.stringify(dataToSave.floating_popups);
    if (dataToSave.entry_popups) dataToSave.entry_popups = JSON.stringify(dataToSave.entry_popups);
    if (dataToSave.invite_bonus_tiers) dataToSave.invite_bonus_tiers = JSON.stringify(dataToSave.invite_bonus_tiers);
    if (dataToSave.home_banners) dataToSave.home_banners = JSON.stringify(dataToSave.home_banners);
    
    const res = await fetchWithAuth('/api/admin/settings', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(dataToSave) });
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
    const res = await fetchWithAuth('/api/admin/test-api', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ agent_code: apiAgentCode.value, agent_token: apiAgentToken.value, webhook_secret: apiWebhookSecret.value }) });
    const data = await res.json();
    apiSuccess.value = data.success;
    apiMessage.value = data.message + (data.agent ? ` | Saldo: R$ ${data.agent.balance}` : '');
  } catch(e) { apiSuccess.value = false; apiMessage.value = 'Erro: ' + e.message; }
  apiLoading.value = false;
};

const fetchGamesFromApi = async () => {
  fetchingGames.value = true; apiLoading.value = true; apiMessage.value = '';
  try {
    const res = await fetchWithAuth('/api/admin/fetch-games', { method: 'POST' });
    const data = await res.json();
    apiSuccess.value = data.success;
    apiMessage.value = data.message;
    if (data.success && data.providers) apiProviders.value = data.providers;
    // Reload grouped data
    const res2 = await fetchWithAuth('/api/admin/games-grouped');
    if (res2.ok) apiProviders.value = await res2.json();
  } catch(e) { apiSuccess.value = false; apiMessage.value = 'Erro: ' + e.message; }
  fetchingGames.value = false; apiLoading.value = false;
};

const deleteAllGames = async () => {
  if (!confirm('Tem certeza que deseja excluir todos os jogos importados?')) return;
  try {
    const res = await fetchWithAuth('/api/admin/delete-games', { method: 'POST' });
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
    const res = await fetchWithAuth('/api/admin/set-popular', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ game_ids: currentPopular.slice(0, 9) }) });
    if (res.ok) { game.is_popular = true; apiMessage.value = `"${game.game_name}" adicionado aos Populares!`; apiSuccess.value = true; }
  } catch(e) { console.error(e); }
};
const removeGameFromPopular = async (game) => {
  openGameMenu.value = null;
  const currentPopular = [];
  apiProviders.value.forEach(p => p.games?.forEach(g => { if (g.is_popular && g.id !== game.id) currentPopular.push(g.id); }));
  try {
    const res = await fetchWithAuth('/api/admin/set-popular', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ game_ids: currentPopular }) });
    if (res.ok) { game.is_popular = false; apiMessage.value = `"${game.game_name}" removido dos Populares.`; apiSuccess.value = true; }
  } catch(e) { console.error(e); }
};

// Send game to Retro
const sendGameToRetro = async (game) => {
  openGameMenu.value = null;
  try {
    const currentRetro = [];
    apiProviders.value.forEach(p => p.games?.forEach(g => { if (g.is_retro) currentRetro.push(g.id); }));
    if (!currentRetro.includes(game.id)) currentRetro.push(game.id);
    const res = await fetchWithAuth('/api/admin/set-retro', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ game_ids: currentRetro }) });
    if (res.ok) { game.is_retro = true; apiMessage.value = `"${game.game_name}" enviado para o Retrô!`; apiSuccess.value = true; }
  } catch(e) { console.error(e); }
};
const removeGameFromRetro = async (game) => {
  openGameMenu.value = null;
  const currentRetro = [];
  apiProviders.value.forEach(p => p.games?.forEach(g => { if (g.is_retro && g.id !== game.id) currentRetro.push(g.id); }));
  try {
    const res = await fetchWithAuth('/api/admin/set-retro', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ game_ids: currentRetro }) });
    if (res.ok) { game.is_retro = false; apiMessage.value = `"${game.game_name}" removido do Retrô.`; apiSuccess.value = true; }
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
    const res = await fetchWithAuth('/api/admin/set-slot-provider', { method: 'POST', body: fd });
    if (res.ok) {
      const data = await res.json();
      coverModalProvider.value.is_slot = true;
      apiMessage.value = `"${coverModalProvider.value.name}" adicionado aos Slots!`;
      apiSuccess.value = true;
      showCoverModal.value = false;
      fetchGamesGrouped();
    }
  } catch(e) { console.error(e); }
};

const openLogoModal = (provider) => {
  logoModalProvider.value = provider;
  showLogoModal.value = true;
  openProviderMenu.value = null;
};

const handleLogoUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const fd = new FormData();
  fd.append('logo', file);
  fd.append('provider_code', logoModalProvider.value.code);

  try {
    const res = await fetchWithAuth('/api/admin/set-provider-logo', {
      method: 'POST',
      body: fd
    });
    const data = await res.json();
    if (data.success) {
      apiMessage.value = 'Logo atualizada com sucesso!';
      apiSuccess.value = true;
      showLogoModal.value = false;
      fetchGamesGrouped();
    }
  } catch (err) {
    console.error(err);
    apiMessage.value = 'Erro ao enviar logo.';
    apiSuccess.value = false;
  }
};
const removeProviderFromSlots = async (provider) => {
  openProviderMenu.value = null;
  try {
    await fetchWithAuth('/api/admin/remove-slot-provider', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ provider_code: provider.code }) });
    provider.is_slot = false; provider.cover_image = null;
    apiMessage.value = `"${provider.name}" removido dos Slots.`; apiSuccess.value = true;
  } catch(e) { console.error(e); }
};
// User Management Logic
const fetchUsers = async () => {
  try {
    const res = await fetchWithAuth(`/api/admin/users?page=${usersPage.value}&search=${userSearch.value}`);
    const data = await res.json();
    usersList.value = data.data;
    hasMoreUsers.value = !!data.next_page_url;
  } catch (e) {
    console.error(e);
  }
};

const nextUsersPage = () => { if(hasMoreUsers.value) { usersPage.value++; fetchUsers(); } };
const prevUsersPage = () => { if(usersPage.value > 1) { usersPage.value--; fetchUsers(); } };

const openCreateUserModal = () => {
  editingUser.value = null;
  userForm.value = { name: '', email: '', phone: '', password: '', balance: 0, is_demo: false };
  showUserModal.value = true;
  showUserPassword.value = false;
  showHistory.value = false;
};

const openEditUserModal = async (user) => {
  editingUser.value = user;
  userForm.value = { ...user, password: '' };
  showUserModal.value = true;
  showUserPassword.value = false;
  showHistory.value = false;
  fetchUserHistory(user.id);
};

const fetchUserHistory = async (id) => {
  try {
    const res = await fetchWithAuth(`/api/admin/users/${id}/history`);
    userHistory.value = await res.json();
  } catch (e) { console.error(e); }
};

const saveUser = async () => {
  try {
    const url = editingUser.value ? `/api/admin/users/${editingUser.value.id}` : '/api/admin/users';
    const res = await fetchWithAuth(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(userForm.value)
    });
    const data = await res.json();
    if(data.id || data.success) {
      apiMessage.value = 'Usuário salvo com sucesso!';
      apiSuccess.value = true;
      showUserModal.value = false;
      fetchUsers();
    }
  } catch (e) {
    apiMessage.value = 'Erro ao salvar usuário.';
    apiSuccess.value = false;
  }
};

const fetchGamesGrouped = async () => {
  try {
    const res = await fetchWithAuth('/api/admin/games-grouped');
    if (res.ok) { apiProviders.value = await res.json(); }
  } catch(e) {}
};

// Retro Games State
const fetchRetroGames = async () => {
  try {
    const res = await fetchWithAuth('/api/admin/retro/games');
    if (res.ok) retroGames.value = await res.json();
    const res2 = await fetchWithAuth('/api/admin/retro/difficulty');
    if (res2.ok) {
      retroDifficulty.value = await res2.json();
      retroDifficulty.value.forEach(g => {
        g.settings.forEach(s => {
          retroDiffValues.value[s.key] = s.value;
        });
      });
    }
  } catch(e) { console.error(e); }
};

const toggleRetroGame = async (game) => {
  console.log('[RetroDebug] Toggling game:', game.id, 'Current status:', game.is_active);
  try {
    const res = await fetchWithAuth('/api/admin/retro/toggle', {
      method: 'POST',
      body: JSON.stringify({ game_id: game.id, active: !game.is_active }),
    });
    console.log('[RetroDebug] Response status:', res.status);
    if (res.ok) {
      const data = await res.json();
      console.log('[RetroDebug] Success data:', data);
      game.is_active = !game.is_active;
      retroMenuOpen.value = null;
      apiMessage.value = 'Status atualizado!';
      apiSuccess.value = true;
    } else {
      const err = await res.json().catch(() => ({}));
      console.error('[RetroDebug] Error response:', err);
    }
  } catch(e) {
    console.error('[RetroDebug] Exception:', e);
  }
};

const openRetroEditModal = (game) => {
  retroEditForm.value = { game_id: game.id, name: game.name, bannerFile: null };
  showRetroEditModal.value = true;
  retroMenuOpen.value = null;
};

const saveRetroEdit = async () => {
  try {
    const fd = new FormData();
    fd.append('game_id', retroEditForm.value.game_id);
    fd.append('name', retroEditForm.value.name);
    if (retroEditForm.value.bannerFile) fd.append('banner', retroEditForm.value.bannerFile);
    const res = await fetch('/api/admin/retro/update-game', {
      method: 'POST',
      headers: { 'Authorization': 'Bearer ' + adminToken.value },
      body: fd
    });
    if (res.ok) {
      showRetroEditModal.value = false;
      fetchRetroGames();
      apiMessage.value = 'Jogo atualizado!';
      apiSuccess.value = true;
    }
  } catch(e) { console.error(e); }
};

const saveRetroDifficulty = async () => {
  isSaving.value = true;
  try {
    const res = await fetchWithAuth('/api/admin/retro/difficulty', {
      method: 'POST',
      body: JSON.stringify({ settings: retroDiffValues.value }),
    });
    if (res.ok) {
      apiMessage.value = 'Configurações de dificuldade salvas!';
      apiSuccess.value = true;
    }
  } catch(e) { console.error(e); }
  isSaving.value = false;
};

onMounted(() => {
  if (adminToken.value) {
    isAdminLoggedIn.value = true;
    fetchSettings();
    fetchUsers();
  }
});
</script>
