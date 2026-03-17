<template>
  <div class="flex flex-col flex-1 bg-[#1a1a1a] text-white pb-32 overflow-y-auto">
    <!-- Top Banner -->
    <div class="relative w-full">
      <img src="/casino_icons/bannerganhereg.avif" alt="Promo" class="w-full h-auto" />
      <button @click="$emit('close')" class="absolute top-4 left-4 p-2 bg-black/40 rounded-full active:scale-95 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-gray-800 px-6 mt-4">
      <button 
        @click="isLoginMode = false"
        class="flex-1 pb-3 text-sm font-bold transition-all"
        :class="!isLoginMode ? 'border-b-2 border-[#fca000] text-[#fca000]' : 'text-gray-500'"
      >Registro</button>
      <button 
        @click="isLoginMode = true"
        class="flex-1 pb-3 text-sm font-bold transition-all"
        :class="isLoginMode ? 'border-b-2 border-[#fca000] text-[#fca000]' : 'text-gray-500'"
      >Login</button>
    </div>

    <!-- Form -->
    <div class="p-6 space-y-6">
      
      <div v-if="errorMessage" class="bg-red-500/10 border border-red-500/20 text-red-500 p-3 rounded-lg text-xs font-bold animate-pulse">
        {{ errorMessage }}
      </div>

      <!-- Phone/Login Input -->
      <div class="space-y-2">
        <label class="text-[11px] text-gray-400 font-medium tracking-wide">
          Número do Celular ou E-mail
        </label>
        <div class="flex items-center bg-[#121212] rounded-lg border border-gray-800 h-12 px-3 focus-within:border-[#fca000]/50 transition-colors">
          <div class="flex items-center space-x-2 pr-2 border-r border-gray-800">
            <img src="/casino_icons/brasilband.png" alt="BR" class="w-6 h-4 object-contain" />
            <span class="text-sm font-medium text-gray-300">+55</span>
          </div>
          <input 
            v-model="formLogin"
            type="text" 
            placeholder="Digite o Celular ou E-mail" 
            class="bg-transparent flex-1 outline-none px-3 text-sm placeholder:text-gray-600"
          />
        </div>
      </div>

      <!-- Password Input -->
      <div class="space-y-2">
        <div class="flex items-center justify-between">
          <label class="flex items-center text-[11px] text-[#fca000] font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
            </svg>
            Senha
          </label>
        </div>
        <div class="bg-[#121212] rounded-lg border border-gray-800 h-12 px-3 flex items-center focus-within:border-[#fca000]/50 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
          <input 
            v-model="formPassword"
            :type="showPassword ? 'text' : 'password'" 
            placeholder="Insira a senha" 
            class="bg-transparent flex-1 outline-none px-3 text-sm placeholder:text-gray-600"
          />
          <button @click="showPassword = !showPassword" class="text-gray-600">
            <svg v-if="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
          </button>
        </div>
      </div>

      <!-- Confirm Password (Only Register) -->
      <div v-if="!isLoginMode" class="bg-[#121212] rounded-lg border border-gray-800 h-12 px-3 flex items-center focus-within:border-[#fca000]/50 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
        <input 
          v-model="formPasswordConfirm"
          :type="showPassword ? 'text' : 'password'" 
          placeholder="Confirmar senha" 
          class="bg-transparent flex-1 outline-none px-3 text-sm placeholder:text-gray-600"
        />
      </div>

      <!-- Agreement -->
      <div v-if="!isLoginMode" class="flex items-start space-x-3">
        <div class="w-5 h-5 rounded bg-green-600 flex items-center justify-center shrink-0 mt-0.5">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
             <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
           </svg>
        </div>
        <p class="text-[11px] text-white leading-tight">
          Tenho mais de 18 anos, li e concordo com <span class="text-[#fca000]">«Acordo de Usuário»</span>
        </p>
      </div>

      <!-- Auth Button With Gift Icon -->
      <div class="relative w-full mt-6">
        <button 
          @click="handleSubmit" 
          :disabled="isLoading" 
          class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-bold py-3 rounded-xl shadow-lg hover:brightness-110 active:scale-[0.98] transition flex justify-center items-center disabled:opacity-50"
        >
          <span v-if="isLoading">{{ isLoginMode ? 'Entrando...' : 'Criando Conta...' }}</span>
          <span v-else>{{ isLoginMode ? 'Entrar Agora' : 'Registrar Agora' }}</span>
        </button>
        <img v-if="!isLoginMode" src="/casino_icons/presente.avif" alt="Gift" class="absolute -top-6 left-1/2 transform -translate-x-1/2 w-10 animate-bounce pointer-events-none drop-shadow-lg" />
      </div>

      <p v-if="isLoginMode" class="text-center text-xs text-gray-500 mt-4">
        Não tem uma conta? <span @click="isLoginMode = false" class="text-[#fca000] font-bold cursor-pointer">Registre-se</span>
      </p>
      <p v-else class="text-center text-xs text-gray-500 mt-4">
        Já tem uma conta? <span @click="isLoginMode = true" class="text-[#fca000] font-bold cursor-pointer">Entre aqui</span>
      </p>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
const emit = defineEmits(['close', 'register-success']);

const isLoginMode = ref(false);
const showPassword = ref(false);
const formLogin = ref('');
const formPassword = ref('');
const formPasswordConfirm = ref('');
const isLoading = ref(false);
const errorMessage = ref('');

const handleSubmit = async () => {
    errorMessage.value = '';
    if (!formLogin.value || !formPassword.value) {
        errorMessage.value = 'Preencha todos os campos.';
        return;
    }
    
    if (!isLoginMode.value && formPassword.value !== formPasswordConfirm.value) {
        errorMessage.value = 'As senhas não coincidem.';
        return;
    }

    isLoading.value = true;
    const endpoint = isLoginMode.value ? '/api/login' : '/api/register';
    const payload = isLoginMode.value 
        ? { login: formLogin.value, password: formPassword.value }
        : { email: formLogin.value.includes('@') ? formLogin.value : null, phone: !formLogin.value.includes('@') ? formLogin.value : null, password: formPassword.value, password_confirmation: formPasswordConfirm.value };

    try {
        const res = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        
        const data = await res.json();
        
        if (res.ok && data.status === 1) {
            localStorage.setItem('casino_token', data.token);
            emit('register-success', data.user);
        } else {
            errorMessage.value = data.message || 'Erro ao processar solicitação.';
        }
    } catch (err) {
        errorMessage.value = 'Erro de conexão com o servidor.';
        console.error(err);
    } finally {
        isLoading.value = false;
    }
};
</script>
