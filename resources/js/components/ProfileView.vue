<template>
  <div class="flex flex-col flex-1 bg-[#0f0f0f] text-white pb-32 overflow-y-auto min-h-screen relative font-sans">
    
    <!-- Sophisticated Background Gradient -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-[500px] bg-gradient-to-b from-yellow-500/5 to-transparent"></div>
        <div class="absolute top-[20%] right-[-10%] w-[400px] h-[400px] bg-yellow-500/5 blur-[120px] rounded-full"></div>
    </div>

    <!-- Sticky Top Header -->
    <div class="sticky top-0 z-[50] bg-[#0f0f0f]/80 backdrop-blur-md px-6 py-4 flex items-center justify-between border-b border-white/[0.03]">
      <button @click="$emit('close')" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center active:scale-95 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <div class="flex items-center space-x-3">
        <!-- Gift Icon with Badge -->
        <div class="relative cursor-pointer active:scale-90 transition group mr-1">
            <img src="/casino_icons/presente.avif" class="w-10 h-10 object-contain drop-shadow-[0_0_10px_rgba(234,179,8,0.3)]" />
            <div class="absolute -top-1 -right-1 bg-[#00ff00] text-black text-[9px] font-black px-1.5 py-0.5 rounded-full border-2 border-[#0f0f0f] shadow-lg">2-9</div>
        </div>

        <template v-if="!user">
            <div class="flex bg-white/5 p-1 rounded-xl border border-white/5">
                <button @click="$emit('request-register', 'login')" class="bg-yellow-500 text-black font-black text-[12px] px-4 py-2 rounded-lg shadow-lg active:scale-95 transition-all">Login</button>
                <button @click="$emit('request-register', 'register')" class="text-gray-400 font-bold text-[12px] px-4 py-2 rounded-lg active:text-white transition-all">Registro</button>
            </div>
        </template>
        <button v-else @click="logout" class="bg-white/5 text-gray-400 font-bold text-[12px] px-4 py-2 rounded-lg active:scale-95 transition border border-white/5">Sair</button>
      </div>
    </div>

    <!-- User Section -->
    <div class="relative z-10 px-6 mt-6">
        <div class="flex items-center space-x-4 bg-gradient-to-r from-white/[0.03] to-transparent p-4 rounded-3xl border border-white/[0.05]">
            <div class="relative">
                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-[#1a1a1a] shadow-2xl border border-white/10 ring-4 ring-yellow-500/10">
                    <img v-if="user" :src="user.avatar || '/casino_icons/perfil-user/homemperfil.png'" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center bg-[#2a2a2a]">
                        <svg class="w-8 h-8 text-white/20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                    </div>
                </div>
                <div v-if="user" class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#00ff00] border-2 border-[#0f0f0f] rounded-full shadow-lg"></div>
            </div>
            <div class="flex flex-col flex-1">
                <div v-if="user" class="flex flex-col">
                    <span class="text-white font-black text-lg tracking-tight leading-none uppercase">{{ user.name }}</span>
                    <div class="flex items-center mt-2">
                        <span class="text-gray-500 text-[11px] font-bold tracking-widest uppercase bg-white/5 px-2 py-0.5 rounded-md border border-white/5">ID: {{ user.id }}</span>
                    </div>
                </div>
                <div v-else class="flex flex-col">
                    <span class="text-gray-400 font-bold text-[14px]">Bem-vindo, visitante!</span>
                    <span class="text-white text-[12px] opacity-60 mt-0.5">Faça login para começar a jogar</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance / Quick Actions Card -->
    <div class="relative z-10 px-6 mt-6">
        <div class="bg-[#1a1a1a] rounded-[2.5rem] border border-white/10 p-6 shadow-2xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500/5 blur-3xl rounded-full -translate-y-1/2 translate-x-1/2"></div>
            
            <div class="flex flex-col items-center mb-4">
                <span class="text-[10px] text-gray-500 font-black uppercase tracking-[0.2em] mb-1">Saldo Total em Jogo</span>
                <div class="flex items-baseline space-x-1 text-center">
                    <span class="text-white text-3xl font-black tracking-tighter">R$ {{ (parseFloat(user?.balance || 0) + parseFloat(user?.bonus_balance || 0)).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6 relative z-10">
                <!-- Saldo Real -->
                <div class="bg-black/40 border border-white/[0.05] p-4 rounded-3xl flex flex-col items-center justify-center relative overflow-hidden group">
                    <div class="absolute inset-0 bg-green-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <span class="text-[9px] text-gray-500 font-black uppercase tracking-[0.2em] mb-1 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-green-500 shadow-[0_0_5px_#22c55e]"></span> Saldo Real</span>
                    <div class="flex items-baseline space-x-1">
                        <span class="text-green-500 text-sm font-black italic">R$</span>
                        <span class="text-2xl font-black text-white tracking-tighter">{{ (user?.balance || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                    </div>
                </div>
                
                <!-- Saldo Bônus -->
                <div class="bg-black/40 border border-white/[0.05] p-4 rounded-3xl flex flex-col items-center justify-center relative overflow-hidden group">
                    <div class="absolute inset-0 bg-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <span class="text-[9px] text-gray-500 font-black uppercase tracking-[0.2em] mb-1 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-purple-500 shadow-[0_0_5px_#a855f7]"></span> Saldo Bônus</span>
                    <div class="flex items-baseline space-x-1">
                        <span class="text-purple-500 text-sm font-black italic">R$</span>
                        <span class="text-2xl font-black text-white tracking-tighter">{{ parseFloat(user?.bonus_balance || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
                    </div>
                </div>
            </div>

            <!-- Rollover Tracking -->
            <div class="space-y-3 mb-6 relative z-10" v-if="user">
                <!-- Rollover Deposit -->
                <div class="bg-black/40 border border-white/[0.05] p-4 rounded-2xl relative overflow-hidden">
                    <div class="flex justify-between items-end mb-3">
                        <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest leading-tight">Rollover de Saque<br><span class="text-green-500 font-bold opacity-80 mt-0.5 inline-block">(Depósitos)</span></span>
                        <span class="text-sm font-black text-white tracking-tighter">{{ user.rollover_deposit_target > 0 ? Math.min(100, (user.rollover_deposit_current / user.rollover_deposit_target) * 100).toFixed(0) : '100' }}%</span>
                    </div>
                    <div class="w-full h-2 bg-white/5 rounded-full overflow-hidden shadow-inner relative">
                        <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-green-600 to-green-400 rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(34,197,94,0.5)]" :style="{ width: (user.rollover_deposit_target > 0 ? Math.min(100, (user.rollover_deposit_current / user.rollover_deposit_target) * 100) : 100) + '%' }"></div>
                    </div>
                    <div class="text-[9px] text-gray-500 text-right mt-2 font-black uppercase tracking-widest">Apostado: <span class="text-gray-300">R$ {{ Number(user.rollover_deposit_current).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}</span> / R$ {{ Number(user.rollover_deposit_target).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}</div>
                </div>

                <!-- Rollover Bonus -->
                <div class="bg-black/40 border border-white/[0.05] p-4 rounded-2xl relative overflow-hidden">
                    <div class="flex justify-between items-end mb-3">
                        <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest leading-tight">Rollover de Bônus<br><span class="text-purple-500 font-bold opacity-80 mt-0.5 inline-block">(Ganhos de Bônus)</span></span>
                        <span class="text-sm font-black text-white tracking-tighter">{{ user.rollover_bonus_target > 0 ? Math.min(100, (user.rollover_bonus_current / user.rollover_bonus_target) * 100).toFixed(0) : '100' }}%</span>
                    </div>
                    <div class="w-full h-2 bg-white/5 rounded-full overflow-hidden shadow-inner relative">
                        <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-purple-600 to-purple-400 rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(168,85,247,0.5)]" :style="{ width: (user.rollover_bonus_target > 0 ? Math.min(100, (user.rollover_bonus_current / user.rollover_bonus_target) * 100) : 100) + '%' }"></div>
                    </div>
                    <div class="text-[9px] text-gray-500 text-right mt-2 font-black uppercase tracking-widest">Apostado: <span class="text-gray-300">R$ {{ Number(user.rollover_bonus_current).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}</span> / R$ {{ Number(user.rollover_bonus_target).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button @click="openModal('withdrawal')" class="bg-white/5 border border-white/[0.08] p-4 rounded-2xl flex flex-col items-center group active:scale-95 transition hover:bg-white/[0.08]">
                    <div class="w-10 h-10 bg-yellow-500/10 rounded-xl flex items-center justify-center mb-2">
                         <svg viewBox="0 0 24 24" fill="none" class="w-6 h-6 text-yellow-500">
                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M8 12H16M16 12L13 9M16 12L13 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="text-white text-xs font-black uppercase tracking-wider">Saques</span>
                </button>
                <button @click="openModal('deposit')" class="bg-yellow-500 p-4 rounded-2xl flex flex-col items-center active:scale-95 transition shadow-lg shadow-yellow-500/20">
                    <div class="w-10 h-10 bg-black/10 rounded-xl flex items-center justify-center mb-2">
                        <img src="/casino_icons/perfil-user/deposito.avif" class="w-7 h-7 object-contain" />
                    </div>
                    <span class="text-black text-xs font-black uppercase tracking-wider">Depósito</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Navigation List -->
    <div class="relative z-10 px-6 mt-10 space-y-2">
        
        <div class="text-[11px] text-gray-500 font-black uppercase tracking-[0.2em] mb-4 ml-2 opacity-50">Atividade e Gestão</div>
        
        <div class="space-y-1">
            <template v-for="(item, idx) in menuItems" :key="item.id">
                
                <!-- Divider/Section for specific items -->
                <div v-if="item.id === 'promo'" class="pt-8 pb-4">
                    <div class="text-[11px] text-gray-500 font-black uppercase tracking-[0.2em] ml-2 opacity-50">Geral e Info</div>
                </div>

                <button @click="handleItemClick(item)" class="w-full flex items-center justify-between p-4 rounded-2xl border border-transparent hover:bg-white/[0.03] active:bg-white/[0.05] transition group overflow-hidden relative">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-white/[0.05] flex items-center justify-center text-yellow-500 transition group-hover:scale-110">
                            <div v-html="item.svg" class="w-5 h-5"></div>
                        </div>
                        <div class="flex flex-col items-start leading-tight">
                            <span class="text-gray-200 text-[14px] font-bold tracking-tight">{{ item.title }}</span>
                            <span v-if="item.subtitle" class="text-gray-600 text-[10px] opacity-80 max-w-[180px] break-words">{{ item.subtitle }}</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span v-if="item.badge" class="hidden xs:block text-yellow-500 text-[10px] font-black uppercase bg-yellow-500/5 px-2 py-1 rounded-lg border border-yellow-500/10">{{ item.badge }}</span>
                        <svg class="h-4 w-4 text-gray-700 transition group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>
            </template>
        </div>

        <div class="h-10"></div>
        <button v-if="user" @click="logout" class="w-full p-5 rounded-2xl bg-red-500/5 border border-red-500/10 text-red-500 font-black text-xs uppercase tracking-[0.15em] active:bg-red-500/10 transition mt-6">
            Sair da conta
        </button>
    </div>

    <!-- Subpage Overlays (Redesigned) -->
    <div v-if="activeSubpage" class="fixed inset-0 z-[110] bg-[#0f0f0f] flex flex-col animate-slide-in">
        <div class="sticky top-0 z-[120] bg-[#0f0f0f]/80 backdrop-blur-md px-6 py-4 flex items-center border-b border-white/[0.03]">
            <button @click="activeSubpage = null" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                 </svg>
            </button>
            <h2 class="ml-4 text-lg font-black tracking-tight">{{ subpageTitle }}</h2>
        </div>
        <div class="flex-1 overflow-y-auto p-6 relative">
            <!-- Dynamic Subpage Content -->
            <transition name="fade" mode="out-in">
                <div :key="activeSubpage">
                    <div v-if="activeSubpage === 'history'" class="space-y-3">
                        <div v-if="loading" class="flex flex-col items-center py-20">
                            <div class="w-8 h-8 border-2 border-yellow-500 border-t-transparent rounded-full animate-spin"></div>
                            <span class="mt-4 text-xs text-gray-500 uppercase font-bold tracking-widest">Carregando...</span>
                        </div>
                        <div v-else-if="subpageData.length === 0" class="text-center py-20 text-gray-600 font-bold">Nenhum registro encontrado.</div>
                        <div v-for="tx in subpageData" :key="tx.id" class="bg-white/5 p-5 rounded-2xl border border-white/[0.05] flex justify-between items-center group">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="tx.amount > 0 ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500'">
                                    <svg v-if="tx.amount > 0" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold capitalize">{{ tx.type === 'deposit' ? 'Depósito' : (tx.type === 'withdrawal' ? 'Saque' : tx.type) }}</span>
                                    <span class="text-[10px] text-gray-600 font-bold uppercase tracking-wider">{{ new Date(tx.created_at).toLocaleDateString('pt-BR') }} • {{ new Date(tx.created_at).toLocaleTimeString('pt-BR', {hour:'2-digit', minute:'2-digit'}) }}</span>
                                </div>
                            </div>
                            <div :class="tx.amount > 0 ? 'text-green-500' : 'text-red-500'" class="text-lg font-black tracking-tighter">
                                {{ tx.amount > 0 ? '+' : '-' }}R$ {{ Math.abs(tx.amount).toFixed(2) }}
                            </div>
                        </div>
                    </div>

                    <!-- Security / FAQ Content -->
                    <div v-if="activeSubpage === 'security'" class="space-y-6">
                        <div class="bg-white/5 p-6 rounded-3xl border border-white/[0.05]">
                            <h3 class="text-white font-black mb-4 uppercase tracking-widest text-xs">Alterar Senha</h3>
                            <div class="space-y-4">
                                <input type="password" placeholder="Senha Atual" class="w-full bg-[#111] border border-white/10 rounded-xl p-4 text-sm text-white focus:border-yellow-500 outline-none transition" />
                                <input type="password" placeholder="Nova Senha" class="w-full bg-[#111] border border-white/10 rounded-xl p-4 text-sm text-white focus:border-yellow-500 outline-none transition" />
                                <button class="w-full bg-yellow-500 text-black font-black py-4 rounded-xl active:scale-95 transition mt-2 hover:brightness-110">Salvar Alteração</button>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeSubpage === 'faq'" class="space-y-4">
                        <div v-if="settings?.faq_content" class="bg-white/5 p-5 rounded-2xl border border-white/[0.05] text-gray-400 text-xs leading-relaxed" v-html="settings.faq_content"></div>
                        <div v-else v-for="(item, i) in (subpageData.length > 0 ? subpageData : [{question: 'Como faço um depósito?', answer: 'Clique no botão Depósito no menu principal e escolha o valor.'}, {question: 'Por que não consigo sacar?', answer: 'Certifique-se de que todas as suas metas de Rollover de Bônus e Depósito mostrem 100% nas barras de progresso do perfil.'}, {question: 'Esqueci minha senha', answer: 'Entre em contato com nosso suporte oficial no Telegram ou e-mail de recuperação.'}])" :key="i" class="bg-white/5 p-5 rounded-2xl border border-white/[0.05]">
                            <h3 class="text-white font-bold text-sm mb-2">{{ item.question }}</h3>
                            <p class="text-gray-400 text-xs leading-relaxed">{{ item.answer }}</p>
                        </div>
                    </div>

                    <div v-if="activeSubpage === 'find-us'" class="space-y-4">
                        <a :href="settings?.support_telegram || '#'" target="_blank" class="block w-full bg-[#2AABEE]/10 border border-[#2AABEE]/30 p-5 rounded-2xl flex items-center justify-between group hover:bg-[#2AABEE]/20 transition">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">✈️</span>
                                <span class="text-[#2AABEE] font-black uppercase text-sm">Canal Oficial Telegram</span>
                            </div>
                            <span class="text-[#2AABEE] group-hover:translate-x-1 transition">➔</span>
                        </a>
                        <a :href="settings?.support_email || 'mailto:suporte@exemplo.com'" class="block w-full bg-yellow-500/5 border border-yellow-500/20 p-5 rounded-2xl flex items-center justify-between group hover:bg-yellow-500/10 transition">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">📧</span>
                                <span class="text-yellow-500 font-black uppercase text-sm">Suporte via E-mail</span>
                            </div>
                            <span class="text-yellow-500 group-hover:translate-x-1 transition">➔</span>
                        </a>
                        <div class="bg-white/5 p-6 rounded-3xl border border-white/5 mt-6">
                            <h3 class="text-white font-black text-xs uppercase tracking-widest mb-4 flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Nossas Redes</h3>
                            <div class="grid grid-cols-2 gap-3">
                                <a href="#" class="bg-white/5 p-4 rounded-2xl flex items-center justify-center text-gray-400 hover:text-white transition">Instagram</a>
                                <a href="#" class="bg-white/5 p-4 rounded-2xl flex items-center justify-center text-gray-400 hover:text-white transition">Twitter / X</a>
                            </div>
                        </div>
                    </div>

                    <div v-if="activeSubpage === 'about'" class="space-y-4">
                        <div class="bg-white/5 p-8 rounded-[2.5rem] border border-white/5 relative overflow-hidden">
                            <div class="absolute -top-10 -right-10 w-40 h-40 bg-yellow-500/5 blur-3xl rounded-full"></div>
                            <div v-if="settings?.about_content" class="text-gray-400 text-sm leading-relaxed" v-html="settings.about_content"></div>
                            <template v-else>
                                <h3 class="text-white font-black text-xl mb-4 italic">Excelência em Jogos</h3>
                                <p class="text-gray-400 text-sm leading-relaxed mb-6">Somos uma plataforma líder em entretenimento digital, comprometida em oferecer a experiência de jogo mais segura, justa e emocionante do mercado. Fundada em 2024, nossa missão é conectar entusiastas de jogos a tecnologia de ponta e vitórias reais.</p>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5">
                                        <span class="text-2xl">🛡️</span>
                                        <div>
                                            <h4 class="text-white font-bold text-xs uppercase">Segurança Total</h4>
                                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Protocolos de criptografia bancária</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5">
                                        <span class="text-2xl">⚡</span>
                                        <div>
                                            <h4 class="text-white font-bold text-xs uppercase">Saques Instantâneos</h4>
                                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Pagamentos via PIX em segundos</p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div v-if="activeSubpage === 'promo'" class="space-y-6">
                        <div class="bg-gradient-to-r from-purple-900/40 to-black p-6 rounded-3xl border border-purple-500/30">
                           <h3 class="text-white font-black text-xl mb-2 italic">Indique & Ganhe</h3>
                           <p class="text-gray-400 text-sm mb-4">Ganhe bônus reais indicando amigos para a nossa plataforma. Acesse a Central de Ofertas para pegar seu link!</p>
                           <button @click="$emit('close'); setTimeout(() => $emit('navigate', 'offers'), 100);" class="bg-purple-600 text-white px-5 py-3 rounded-xl font-bold text-xs uppercase hover:bg-purple-500 transition shadow-[0_5px_20px_rgba(147,51,234,0.3)]">IR PARA OFERTAS</button>
                        </div>
                    </div>
                    <div v-if="activeSubpage === 'suggest'" class="space-y-6">
                        <div class="bg-gradient-to-br from-yellow-500/10 to-black p-6 rounded-3xl border border-yellow-500/20 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500/10 blur-3xl rounded-full -translate-y-12 translate-x-12"></div>
                            <h3 class="text-white font-black text-xl mb-2 italic">Indique & Ganhe</h3>
                            <p class="text-gray-400 text-sm mb-6">Convide seus amigos e ganhe prêmios incríveis por cada cadastro e depósito realizado através do seu link.</p>
                            
                            <div class="space-y-4">
                                <div class="bg-black/40 p-4 rounded-2xl border border-white/5">
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-2 block">Seu Link de Afiliado</span>
                                    <div class="flex items-center gap-2">
                                        <input readonly :value="`${window.location.origin}/register?ref=${user?.name}`" class="flex-1 bg-transparent text-white text-xs font-mono outline-none" />
                                        <button @click="copyLink" class="bg-yellow-500 text-black px-3 py-2 rounded-lg text-[10px] font-black uppercase">Copiar</button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <button @click="shareSocial('whatsapp')" class="bg-[#25D366]/10 border border-[#25D366]/20 p-4 rounded-2xl flex flex-col items-center gap-2 group active:scale-95 transition">
                                        <span class="text-xl">💬</span>
                                        <span class="text-[#25D366] text-[10px] font-black uppercase">WhatsApp</span>
                                    </button>
                                    <button @click="shareSocial('facebook')" class="bg-[#1877F2]/10 border border-[#1877F2]/20 p-4 rounded-2xl flex flex-col items-center gap-2 group active:scale-95 transition">
                                        <span class="text-xl">👥</span>
                                        <span class="text-[#1877F2] text-[10px] font-black uppercase">Facebook</span>
                                    </button>
                                    <button @click="shareSocial('telegram')" class="bg-[#26A5E4]/10 border border-[#26A5E4]/20 p-4 rounded-2xl flex flex-col items-center gap-2 group active:scale-95 transition">
                                        <span class="text-xl">✈️</span>
                                        <span class="text-[#26A5E4] text-[10px] font-black uppercase">Telegram</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/5 p-6 rounded-3xl border border-white/5">
                            <h4 class="text-white font-black text-xs uppercase tracking-widest mb-4">Como funciona?</h4>
                            <div class="space-y-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-8 h-8 rounded-lg bg-yellow-500/10 flex items-center justify-center text-yellow-500 font-black text-sm shrink-0">1</div>
                                    <p class="text-gray-400 text-xs leading-relaxed">Compartilhe seu link exclusivo com seus amigos e conhecidos.</p>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="w-8 h-8 rounded-lg bg-yellow-500/10 flex items-center justify-center text-yellow-500 font-black text-sm shrink-0">2</div>
                                    <p class="text-gray-400 text-xs leading-relaxed">Eles se cadastram e começam a jogar na nossa plataforma.</p>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="w-8 h-8 rounded-lg bg-yellow-500/10 flex items-center justify-center text-yellow-500 font-black text-sm shrink-0">3</div>
                                    <p class="text-gray-400 text-xs leading-relaxed">Você recebe comissões e bônus baseados no desempenho dos seus indicados!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>

    <!-- Custom Modals (Refined) -->
    <!-- Same Modal Logic as before but with cleaner styling -->
    <div v-if="activeModal" class="fixed inset-0 z-[150] flex items-end justify-center bg-[#000]/90 backdrop-blur-md p-4" @click.self="activeModal = null">
      <div class="w-full max-w-lg bg-[#0f0f0f] rounded-t-[3.5rem] p-10 animate-slide-up border-t border-white/[0.08] relative overflow-hidden">
        
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-20 h-1.5 bg-white/10 rounded-full mt-4"></div>

        <div class="flex items-center justify-between mb-10 pt-4">
          <h3 class="text-xl font-black tracking-tight text-white uppercase">{{ activeModal === 'deposit' ? 'Depósito Rápido' : 'Solicitar Saque' }}</h3>
          <button @click="activeModal = null" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-gray-500 active:scale-90 transition">✕</button>
        </div>

        <!-- Deposit Content -->
        <div v-if="activeModal === 'deposit'" class="space-y-8">
            <div v-if="!pixData" class="space-y-8">
                <div class="grid grid-cols-3 gap-3">
                    <button v-for="val in [20, 50, 100, 200, 500, 1000]" :key="val" @click="depositAmount = val" class="py-5 rounded-2xl border-2 text-sm font-black transition-all flex items-center justify-center" :class="depositAmount === val ? 'bg-yellow-500 text-black border-yellow-500 rotate-1' : 'bg-white/5 text-gray-400 border-white/5 hover:border-white/10'">R$ {{ val }}</button>
                </div>
                <div class="relative">
                    <input v-model="depositAmount" type="number" class="w-full bg-white/[0.03] border border-white/10 rounded-3xl p-6 pl-14 text-white text-lg font-black outline-none focus:border-yellow-500 transition-all shadow-inner" />
                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-yellow-500 text-lg font-black italic">R$</span>
                    <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] text-gray-500 font-bold uppercase tracking-wider">Mínimo R$ {{ settings.min_deposit || '1,00' }}</span>
                </div>
                <button @click="generatePix" :disabled="loading" class="w-full bg-yellow-500 text-black font-black py-6 rounded-3xl shadow-2xl shadow-yellow-500/20 active:scale-[0.98] transition-all text-base uppercase tracking-widest disabled:opacity-50">
                    {{ loading ? 'Sincronizando...' : 'Gerar QR Code Pix' }}
                </button>
            </div>
            <div v-else class="flex flex-col items-center space-y-8 py-4 animate-fade-in">
                <div class="bg-white p-8 rounded-[3rem] shadow-[0_0_50px_rgba(255,255,255,0.1)] relative group">
                    <div class="absolute -inset-4 bg-yellow-500/20 blur-2xl rounded-full scale-0 group-hover:scale-100 transition-transform"></div>
                    <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(pixData.pixCopyPaste)}`" class="w-48 h-48 relative z-10" />
                </div>
                <div class="w-full bg-white/5 p-6 rounded-2xl border border-white/10 break-all text-[10px] text-gray-500 font-mono text-center leading-relaxed">{{ pixData.pixCopyPaste }}</div>
                <button @click="copyPix" class="w-full bg-white text-black font-black uppercase text-xs py-5 rounded-2xl transition active:scale-95">Copiar Código PIX</button>
            </div>
        </div>

        <!-- Withdrawal Content -->
        <div v-if="activeModal === 'withdrawal'" class="space-y-8">
            <div class="bg-white/[0.03] p-8 rounded-[2.5rem] border border-white/5 flex flex-col items-center shadow-inner relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-yellow-500/10 blur-2xl rounded-full -translate-y-10 translate-x-10"></div>
                <span class="text-[10px] text-gray-500 font-black uppercase tracking-[0.3em] mb-2 opacity-60">Disponível</span>
                <p class="text-4xl font-black text-white italic tracking-tighter">R$ {{ (user?.balance || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}</p>
            </div>
            
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="relative w-1/3">
                        <select v-model="pixType" class="w-full bg-white/[0.03] border border-white/10 rounded-2xl p-5 text-white text-[11px] font-black uppercase outline-none focus:border-yellow-500/50 appearance-none">
                            <option value="CPF">CPF</option>
                            <option value="EMAIL">E-mail</option>
                            <option value="PHONE">Tel</option>
                            <option value="EVP">Aleatória</option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none opacity-30">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                        </div>
                    </div>
                    <input v-model="pixKey" type="text" placeholder="Chave PIX" class="flex-1 bg-white/[0.03] border border-white/10 rounded-2xl p-5 text-white text-sm font-bold outline-none focus:border-yellow-500/50" />
                </div>
                <div class="relative">
                    <input v-model="withdrawalAmount" type="number" placeholder="Quanto deseja sacar?" class="w-full bg-white/[0.03] border border-white/10 rounded-2xl p-6 pl-14 text-white text-lg font-black outline-none focus:border-yellow-500 transition-all shadow-inner" />
                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-yellow-500 text-lg font-black italic">R$</span>
                    <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] text-gray-500 font-bold uppercase tracking-wider">Mínimo R$ {{ settings.min_withdrawal || '10,00' }}</span>
                </div>
            </div>

            <button @click="executeWithdrawal" :disabled="loading" class="w-full bg-white text-black font-black py-6 rounded-3xl shadow-2xl active:scale-[0.98] transition-all text-sm uppercase tracking-[0.2em] disabled:opacity-50 mt-4">
                {{ loading ? 'Validando...' : 'Confirmar Saque' }}
            </button>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({ 
  user: { type: Object, default: null },
  settings: { type: Object, default: () => ({ min_deposit: 1, min_withdrawal: 10 }) }
});
const emit = defineEmits(['close', 'request-register', 'navigate']);

const activeModal = ref(null);
const activeSubpage = ref(null);
const subpageTitle = ref('');
const subpageData = ref([]);
const loading = ref(false);

const depositAmount = ref(50);
const pixData = ref(null);
const pixType = ref('CPF');
const pixKey = ref('');
const withdrawalAmount = ref('');

const menuItems = [
  { id: 'history', title: 'Meus Registros', subtitle: 'Histórico detalhado de depósitos, saques e apostas.', svg: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><circle cx="12" cy="14" r="3"/><path d="M12 11v3l2 2"/></svg>` },
  { id: 'withdrawals', title: 'Gestão Retiradas', subtitle: 'Acompanhe o status dos seus pedidos de saque.', svg: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><path d="M7 15h.01"/><path d="M17 15h.01"/></svg>`, action: () => openModal('withdrawal') },
  { id: 'promo', title: 'Promoção', badge: 'Renda Extra', svg: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>` },
  { id: 'security', title: 'Segurança', svg: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>` },
  { id: 'find-us', title: 'Encontre-nos', badge: 'Telegram', svg: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>` },
  { id: 'faq', title: 'FAQ', svg: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>` },
  { id: 'suggest', title: 'Indique e Ganhe', svg: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>`, action: () => emit('navigate', 'offers') },
  { id: 'about', title: 'Sobre Nós', svg: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>` }
];

const openModal = (type) => { if (!props.user) return emit('request-register', 'login'); activeModal.value = type; pixData.value = null; };
const handleItemClick = (item) => { if (item.action) return item.action(); goToSubpage(item.id); };
const goToSubpage = async (id) => {
    if (!props.user && id !== 'faq' && id !== 'about') return emit('request-register', 'login');
    activeSubpage.value = id;
    const item = menuItems.find(i => i.id === id);
    subpageTitle.value = item ? item.title : 'Detalhes';
    loading.value = true;
    try {
        let endpoint = `/api/profile/${id}`;
        if (id === 'faq') endpoint = '/api/faq';
        const res = await fetch(endpoint, { headers: { 'Authorization': `Bearer ${localStorage.getItem('casino_token')}` } });
        const json = await res.json();
        subpageData.value = json.data || [];
    } catch(e) { subpageData.value = []; } finally { loading.value = false; }
};
const logout = () => { localStorage.removeItem('casino_token'); window.location.reload(); };

const copyLink = () => {
    const url = `${window.location.origin}/register?ref=${props.user?.name}`;
    navigator.clipboard.writeText(url);
    alert('Link de convite copiado!');
};

const shareSocial = (platform) => {
    const url = encodeURIComponent(`${window.location.origin}/register?ref=${props.user?.name}`);
    const text = encodeURIComponent('Venha jogar comigo e ganhe bônus de boas-vindas!');
    
    let shareUrl = '';
    switch(platform) {
        case 'whatsapp': shareUrl = `https://wa.me/?text=${text}%20${url}`; break;
        case 'facebook': shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`; break;
        case 'telegram': shareUrl = `https://t.me/share/url?url=${url}&text=${text}`; break;
    }
    
    if (shareUrl) window.open(shareUrl, '_blank');
};

const generatePix = async () => {
    const min = parseFloat(props.settings.min_deposit || 1);
    if (depositAmount.value < min) return alert(`Mínimo R$ ${min.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`);
    loading.value = true;
    try {
        const res = await fetch('/api/pix/deposit', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${localStorage.getItem('casino_token')}` }, body: JSON.stringify({ amount: depositAmount.value }) });
        const data = await res.json();
        if (data.status === 'success') pixData.value = data;
        else alert(data.error || 'Erro ao gerar Pix');
    } catch(e) { alert('Erro de conexão'); } finally { loading.value = false; }
};
const copyPix = () => { if (pixData.value) { navigator.clipboard.writeText(pixData.value.pixCopyPaste); alert('Código copiado!'); } };
const executeWithdrawal = async () => {
    const min = parseFloat(props.settings.min_withdrawal || 10);
    if (!withdrawalAmount.value || withdrawalAmount.value < min) return alert(`Mínimo R$ ${min.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`);
    if (!pixKey.value) return alert('Informe a chave PIX');
    loading.value = true;
    try {
        const res = await fetch('/api/pix/withdraw', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${localStorage.getItem('casino_token')}` }, body: JSON.stringify({ amount: withdrawalAmount.value, pix_key: pixKey.value, pix_key_type: pixType.value }) });
        const data = await res.json();
        if (data.status === 'success') { alert('Saque solicitado!'); activeModal.value = null; window.location.reload(); }
        else alert(data.error || 'Erro ao sacar');
    } catch(e) { alert('Erro de conexão'); } finally { loading.value = false; }
};
</script>

<style scoped>
@keyframes slideUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.animate-slide-in { animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
